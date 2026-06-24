<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\ReminderRule;
use App\Models\ReminderLog;
use App\Notifications\ContractsExpiringSummaryNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendExpiringContractsNotification extends Command
{
    protected $signature = 'contracts:send-expiring';
    protected $description = 'Send daily summary of expiring employee contracts to HRD (Hybrid with DB Rules)';

    public function handle()
    {
        $today = Carbon::today();

        // 1. Dapatkan aturan hari dinamis dari database untuk "contract"
        $ruleDays = ReminderRule::where('active', 1)
                        ->where('entity', ReminderRule::ENTITY_CONTRACT)
                        ->pluck('days_before')
                        ->toArray();

        $targetDates = [];
        foreach ($ruleDays as $day) {
            $targetDates[] = $today->copy()->addDays($day)->format('Y-m-d');
        }
        
        $criticalThreshold = $today->copy()->addDays(7)->format('Y-m-d');

        // Fetch only the relevant employees from the database
        $employees = Employee::whereNotNull('contract_end')
            ->where(function ($query) use ($targetDates, $criticalThreshold) {
                $query->whereDate('contract_end', '<=', $criticalThreshold);
                if (!empty($targetDates)) {
                    $query->orWhereIn('contract_end', $targetDates);
                }
            })
            ->get();

        $critical = collect();
        $warnings = [];
        
        // Siapkan array kosong untuk setiap rule
        foreach ($ruleDays as $day) {
            $warnings[$day] = collect();
        }

        $hasDataToSend = false;

        foreach ($employees as $emp) {
            // Karena employee difilter by DB, diffInDays aman secara memori
            $diff = clone $today;
            $diff = $diff->diffInDays($emp->contract_end, false); 

            // Cek Critical <= 7
            if ($diff <= 7) {
                // Untuk "critical spam", kita selalu tangkap dan laporkan
                $critical->push($emp);
                $hasDataToSend = true;
            } 
            // Cek apakah hari ini murni menyentuh angka peringatan di DB
            elseif (in_array($diff, $ruleDays)) {
                // Kita harus periksa ReminderLog untuk mencegah duplikasi misal cron jalan 2x di satu hari
                $alreadyLogged = ReminderLog::where([
                    'entity' => 'contract',
                    'entity_id' => $emp->id,
                    'target_date' => $emp->contract_end->format('Y-m-d'),
                    'rule_days' => $diff,
                    'status' => 'sent',
                ])->exists();

                if (!$alreadyLogged) {
                    $warnings[$diff]->push($emp);
                    $hasDataToSend = true;
                }
            }
        }

        if (!$hasDataToSend) {
            $this->info('Tidak ada jadwal notifikasi kontrak untuk hari ini.');
            return;
        }

        // Kirim 1 Email Terpusat ke HRD
        $hrEmail = env('MAIL_HR_SUMMARY_ADDRESS', 'jaddlyn@gmail.com');
        Notification::route('mail', $hrEmail)
            ->notify(new ContractsExpiringSummaryNotification($critical, $warnings));

        $this->info('Summary email kontrak dikirim ke HRD!');

        // Lakukan pencatatan (Log) agar tidak dobel besoknya untuk array rule
        DB::transaction(function () use ($warnings) {
            foreach ($warnings as $diffDays => $kumpulanEmp) {
                foreach ($kumpulanEmp as $emp) {
                    ReminderLog::create([
                        'entity' => 'contract',
                        'entity_id' => $emp->id,
                        'target_date' => $emp->contract_end->format('Y-m-d'),
                        'rule_days' => $diffDays,
                        'recipient' => 'jaddlyn@gmail.com (HRD Summary)',
                        'channel' => 'email',
                        'status' => 'sent',
                        'meta' => ['name' => $emp->name, 'nip' => $emp->nip],
                    ]);
                }
            }
        });
        
        // Kita juga bisa melog critical spam harian jika dibutuhkan sejarah setiap spam.
        // Tapi umumnya tidak masalah karena critical spam tidak peduli sudah dilog atau belum (dia sengaja bypass).
        $this->info('Pencatatan Audit Log Kontrak berhasil!');
    }
}

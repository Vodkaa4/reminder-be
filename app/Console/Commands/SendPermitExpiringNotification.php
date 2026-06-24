<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permit;
use App\Models\ReminderRule;
use App\Models\ReminderLog;
use App\Notifications\PermitExpiringNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendPermitExpiringNotification extends Command
{
    protected $signature = 'permits:send-expiring';
    protected $description = 'Send daily summary of expiring permits to HRD (Hybrid with DB Rules)';

    public function handle()
    {
        $today = Carbon::today();

        // 1. Dapatkan aturan hari dinamis dari database untuk "permit"
        $ruleDays = ReminderRule::where('active', 1)
                        ->where('entity', ReminderRule::ENTITY_PERMIT)
                        ->pluck('days_before')
                        ->toArray();

        // Ambil izin yang masih "active" dan "renewal" (jangan ambil yang sudah dimatikan/diarsipkan sepenuhnya)
        // Kita includekan "expired" karena kita mau terus ngespam sampai HRD beresin.
        $targetDates = [];
        foreach ($ruleDays as $day) {
            $targetDates[] = $today->copy()->addDays($day)->format('Y-m-d');
        }
        
        $criticalThreshold = $today->copy()->addDays(7)->format('Y-m-d');

        // Fetch only relevant permits from DB
        $permits = Permit::whereIn('status', ['active', 'renewal', 'expired'])
            ->whereNotNull('expires_at')
            ->where(function ($query) use ($targetDates, $criticalThreshold) {
                $query->whereDate('expires_at', '<=', $criticalThreshold);
                if (!empty($targetDates)) {
                    $query->orWhereIn('expires_at', $targetDates);
                }
            })
            ->get();

        $critical = collect();
        $warnings = [];
        
        // Siapkan array
        foreach ($ruleDays as $day) {
            $warnings[$day] = collect();
        }

        $hasDataToSend = false;

        foreach ($permits as $permit) {
            $diff = clone $today;
            $diff = $diff->diffInDays($permit->expires_at, false);

            if ($diff <= 7) {
                // Spam Kritis
                $critical->push($permit);
                $hasDataToSend = true;
            } 
            elseif (in_array($diff, $ruleDays)) {
                // Cek log biar gak dobel run
                $alreadyLogged = ReminderLog::where([
                    'entity' => 'permit',
                    'entity_id' => $permit->id,
                    'target_date' => $permit->expires_at->format('Y-m-d'),
                    'rule_days' => $diff,
                    'status' => 'sent',
                ])->exists();

                if (!$alreadyLogged) {
                    $warnings[$diff]->push($permit);
                    $hasDataToSend = true;
                }
            }
        }

        if (!$hasDataToSend) {
            $this->info('Tidak ada jadwal notifikasi dokumen perizinan untuk hari ini.');
            return;
        }

        // Kirim ke HRD
        $hrEmail = env('MAIL_HR_SUMMARY_ADDRESS', 'jaddlyn@gmail.com');
        Notification::route('mail', $hrEmail)
            ->notify(new PermitExpiringNotification($critical, $warnings));

        $this->info('Summary email izin dikirim ke HRD!');

        // Catat ke log
        DB::transaction(function () use ($warnings) {
            foreach ($warnings as $diffDays => $kumpulanPermit) {
                foreach ($kumpulanPermit as $permit) {
                    ReminderLog::create([
                        'entity' => 'permit',
                        'entity_id' => $permit->id,
                        'target_date' => $permit->expires_at->format('Y-m-d'),
                        'rule_days' => $diffDays,
                        'recipient' => 'jaddlyn@gmail.com (HRD Summary)',
                        'channel' => 'email',
                        'status' => 'sent',
                        'meta' => ['number' => $permit->number, 'type' => $permit->type],
                    ]);
                }
            }
        });
        
        $this->info('Pencatatan Audit Log Izin berhasil!');
    }
}

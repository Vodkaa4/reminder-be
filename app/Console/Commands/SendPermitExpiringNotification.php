<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Permit;
use App\Notifications\PermitExpiringNotification;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class SendPermitExpiringNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permits:send-expiring {--days=30 : Days before expiry to send notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications for permits expiring soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        // Get permits expiring within specified days
        $expiringPermits = Permit::where('expires_at', '<=', Carbon::now()->addDays($days))
            ->where('expires_at', '>=', Carbon::now())
            ->where('status', 'active')
            ->whereNotNull('pic')
            ->get();

        if ($expiringPermits->isEmpty()) {
            $this->info("Tidak ada izin yang akan berakhir dalam {$days} hari ke depan.");
            return;
        }

        // Group permits by PIC email
        $permitsByPic = $expiringPermits->groupBy('pic');

        $totalEmailsSent = 0;

        foreach ($permitsByPic as $picEmail => $permits) {
            try {
                // Send notification to PIC
                Notification::route('mail', $picEmail)
                    ->notify(new PermitExpiringNotification($permits, $picEmail));

                $this->info("Email pengingat dikirim ke {$picEmail} untuk {$permits->count()} izin.");
                $totalEmailsSent++;
            } catch (\Exception $e) {
                $this->error("Gagal mengirim email ke {$picEmail}: " . $e->getMessage());
            }
        }

        // Also send to admin email
        try {
            Notification::route('mail', 'jaddlyn@gmail.com')
                ->notify(new PermitExpiringNotification($expiringPermits, 'Admin'));
            
            $this->info("Email summary dikirim ke admin (jaddlyn@gmail.com) untuk {$expiringPermits->count()} izin.");
            $totalEmailsSent++;
        } catch (\Exception $e) {
            $this->error("Gagal mengirim email ke admin: " . $e->getMessage());
        }

        $this->info("Total {$totalEmailsSent} email pengingat berhasil dikirim!");
    }
}

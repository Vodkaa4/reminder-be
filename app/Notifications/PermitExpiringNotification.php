<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PermitExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Collection $critical;
    public array $warnings;

    /**
     * @param Collection $critical
     * @param array $warnings
     */
    public function __construct(Collection $critical, array $warnings)
    {
        $this->critical = $critical;
        $this->warnings = $warnings;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('⚠️ Laporan Status Dokumen Perizinan - PT Eksonindo MPI')
            ->greeting('Halo HRD,')
            ->line('Berikut adalah rekap harian untuk dokumen perizinan yang mendekati atau telah melewati batas masa berlaku berdasarkan aturan sistem:')
            ->line('');

        // Critical (<= 7)
        $mail->line('🔴 **PERIZINAN KRITIS (≤ 7 Hari / Kadaluarsa):**');
        if ($this->critical->isEmpty()) {
            $mail->line('- ✅ Aman (Tidak ada)');
        } else {
            foreach ($this->critical as $permit) {
                $days = today()->diffInDays($permit->expires_at, false);
                $statusText = $days < 0 ? "Lewat " . abs($days) . " hari" : "Sisa {$days} hari";
                $mail->line("- **{$permit->number} ({$permit->type})** - Holder: {$permit->holder} | Jatuh Tempo: {$permit->expires_at->format('d M Y')} [{$statusText}]");
            }
        }
        $mail->line('');

        // Dynamic warnings loop
        ksort($this->warnings);
        foreach ($this->warnings as $days => $collection) {
            if ($collection->isNotEmpty()) {
                $mail->line("🟡 **PERINGATAN {$days} HARI:**");
                foreach ($collection as $permit) {
                    $mail->line("- **{$permit->number} ({$permit->type})** - Holder: {$permit->holder} | Jatuh Tempo: {$permit->expires_at->format('d M Y')}");
                }
                $mail->line('');
            }
        }

        $mail->line('📝 **Tindakan yang diperlukan:**')
            ->line('- Izin di kategori KRITIS harus segera diurus karena email akan dikirim setiap hari sebagai reminder.')
            ->line('- Silakan follow up ke PIC masing-masing izin.');

        return $mail->action('Buka Dashboard Admin', config('app.url').'/admin')
            ->line('')
            ->line('Terima kasih.')
            ->line('')
            ->line('*Email otomatis pengingat Dokumen Perizinan - Sistem PT Eksonindo MPI.*');
    }
}

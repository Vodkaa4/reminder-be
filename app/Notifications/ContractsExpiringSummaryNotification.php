<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class ContractsExpiringSummaryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Collection $expired;
    public Collection $upcoming;

    public function __construct(Collection $expired, Collection $upcoming)
    {
        $this->expired = $expired;
        $this->upcoming = $upcoming;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('📋 Laporan Kontrak Karyawan - PT Eksonindo MPI')
            ->greeting('Halo HR,')
            ->line('Berikut laporan kontrak karyawan yang memerlukan perhatian:')
            ->line('');

        // List A - Expired
        $mail->line('🔴 **KONTRAK SUDAH BERAKHIR:**');
        if ($this->expired->isEmpty()) {
            $mail->line('- ✅ Tidak ada kontrak yang sudah berakhir');
        } else {
            foreach ($this->expired as $emp) {
                $daysOverdue = today()->diffInDays($emp->contract_end);
                $mail->line("- **{$emp->name}** (NIP: {$emp->nip}) - Berakhir: {$emp->contract_end->format('d F Y')} ({$daysOverdue} hari terlambat)");
            }
        }

        $mail->line('');

        // List B - Upcoming
        $mail->line('🟡 **KONTRAK AKAN BERAKHIR (≤15 hari):**');
        if ($this->upcoming->isEmpty()) {
            $mail->line('- ✅ Tidak ada kontrak yang akan berakhir');
        } else {
            foreach ($this->upcoming as $emp) {
                $daysLeft = today()->diffInDays($emp->contract_end);
                $mail->line("- **{$emp->name}** (NIP: {$emp->nip}) - Berakhir: {$emp->contract_end->format('d F Y')} ({$daysLeft} hari lagi)");
            }
        }

        $mail->line('')
            ->line('📝 **Tindakan yang diperlukan:**')
            ->line('- Segera proses perpanjangan kontrak')
            ->line('- Hubungi karyawan terkait')
            ->line('- Update status di sistem');

        return $mail->action('Buka Dashboard Admin', config('app.url').'/admin')
            ->line('')
            ->line('Terima kasih atas perhatiannya.')
            ->line('')
            ->line('*Email ini dikirim secara otomatis dari sistem PT Eksonindo MPI.*');
    }
}

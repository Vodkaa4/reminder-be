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
            ->subject('Laporan Kontrak Karyawan')
            ->greeting('Halo HR,')
            ->line('Berikut laporan kontrak karyawan:')
            ->line('');

        // List A
        $mail->line('📌 List A (Expired):');
        if ($this->expired->isEmpty()) {
            $mail->line('- Tidak ada');
        } else {
            foreach ($this->expired as $emp) {
                $mail->line("- {$emp->name} (ended: {$emp->contract_end->format('d-m-Y')})");
            }
        }

        $mail->line('');

        // List B
        $mail->line('📌 List B (Akan habis ≤15 hari):');
        if ($this->upcoming->isEmpty()) {
            $mail->line('- Tidak ada');
        } else {
            foreach ($this->upcoming as $emp) {
                $mail->line("- {$emp->name} (ends: {$emp->contract_end->format('d-m-Y')})");
            }
        }

        return $mail->line('Mohon ditindaklanjuti.');
    }
}

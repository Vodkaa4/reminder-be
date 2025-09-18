<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class PermitExpiringNotification extends Notification
{
    use Queueable;

    protected Collection $permits;
    protected string $picEmail;

    /**
     * Create a new notification instance.
     */
    public function __construct(Collection $permits, string $picEmail)
    {
        $this->permits = $permits;
        $this->picEmail = $picEmail;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('⚠️ Pengingat Izin Mendekati Masa Berakhir - PT Eksonindo MPI')
            ->greeting('Halo,')
            ->line('Berikut adalah daftar izin yang akan segera berakhir dan memerlukan perpanjangan:')
            ->line('');

        foreach ($this->permits as $permit) {
            $mail->line("**{$permit->type} - {$permit->number}**")
                ->line("Pemegang: {$permit->holder}")
                ->line("Lokasi: {$permit->asset_location}")
                ->line("Tanggal Berakhir: {$permit->expires_at->format('d F Y')}")
                ->line("Sisa Waktu: {$permit->expires_at->diffForHumans()}");
            
            if ($permit->notes) {
                $mail->line("Catatan: {$permit->notes}");
            }
            
            $mail->line('---');
        }

        $mail->line('**Tindakan yang Diperlukan:**')
            ->line('- Segera proses perpanjangan izin yang akan berakhir')
            ->line('- Persiapkan dokumen yang diperlukan')
            ->line('- Hubungi pihak terkait untuk proses perpanjangan')
            ->line('- Update status izin setelah diperpanjang')
            ->line('')
            ->line("**PIC:** {$this->picEmail}")
            ->line("**Tanggal Pengingat:** " . now()->format('d F Y H:i'));

        return $mail->action('Buka Dashboard Admin', config('app.url').'/admin')
            ->line('')
            ->line('Terima kasih atas perhatiannya.')
            ->line('')
            ->line('*Email ini dikirim secara otomatis dari sistem PT Eksonindo MPI.*');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'permits_count' => $this->permits->count(),
            'pic_email' => $this->picEmail,
        ];
    }
}

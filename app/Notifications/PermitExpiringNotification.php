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
        return (new MailMessage)
            ->subject('⚠️ Pengingat Izin Mendekati Masa Berakhir - PT Eksonindo MPI')
            ->view('emails.permit-expiring', [
                'permits' => $this->permits,
                'picEmail' => $this->picEmail,
            ]);
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

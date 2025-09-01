<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReminderMail extends Mailable
{
   
    use Queueable, SerializesModels;

    public function __construct(
        public string $entity,     // 'contract'
        public string $title,      // nama karyawan
        public string $targetDate, // YYYY-MM-DD
        public int    $daysBefore  // 30 / ...
    ) {}

    public function build()
    {
        return $this->subject("[Reminder][".strtoupper($this->entity)." H-{$this->daysBefore}] {$this->title}")
            ->markdown('emails.reminder');
    }
}

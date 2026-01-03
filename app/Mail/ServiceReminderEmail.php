<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\ReservationItem;

class ServiceReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $hours;
    public $recipientType; // 'client', 'provider', 'admin'

    public function __construct(ReservationItem $item, $hours, $recipientType)
    {
        $this->item = $item;
        $this->hours = $hours;
        $this->recipientType = $recipientType;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Upcoming Trip Reminder ({$this->hours} Hours) - #{$this->item->reservation->booking_ref}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service_reminder',
        );
    }
}

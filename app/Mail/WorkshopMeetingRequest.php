<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopMeetingRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $meetLink;
    public $service;

    public function __construct($data, $meetLink, $service)
    {
        $this->data = $data;
        $this->meetLink = $meetLink;
        $this->service = $service;
    }

    public function build()
    {
        return $this->subject('Confirmación de Reunión - Conecta Contigo')
            ->view('emails.workshops.meeting_confirmation');
    }
}

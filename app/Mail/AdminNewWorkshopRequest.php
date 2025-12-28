<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewWorkshopRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $service;

    public function __construct($data, $service)
    {
        $this->data = $data;
        $this->service = $service;
    }

    public function build()
    {
        return $this->subject('Nueva Solicitud de Taller - ' . $this->data['name'])
            ->view('emails.admin.new_workshop_request');
    }
}

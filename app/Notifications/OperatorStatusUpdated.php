<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ReservationItem;

class OperatorStatusUpdated extends Notification
{
    use Queueable;

    public $item;
    public $status;
    public $operatorName;

    public function __construct(ReservationItem $item, $status, $operatorName)
    {
        $this->item = $item;
        $this->status = $status;
        $this->operatorName = $operatorName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Status Update: ' . $this->item->service_name)
            ->line("Operator {$this->operatorName} has updated the status.")
            ->line("Booking Ref: {$this->item->reservation->booking_ref}")
            ->line("New Status: " . strtoupper($this->status))
            ->action('View Service', url('/dashboard'))
            ->line('Please monitor this service if necessary.');
    }
}

<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewServiceAssignment extends Notification
{
    use Queueable;

    public $serviceOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Service Assigned: #' . $this->serviceOrder->folio)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned a new service order.')
            ->line('Service: ' . $this->serviceOrder->reservationItem->service_name)
            ->line('Date: ' . $this->serviceOrder->reservationItem->date->format('Y-m-d'))
            ->line('Pickup: ' . $this->serviceOrder->reservationItem->pickup_time)
            ->action('View Assignment', url('/driver/dashboard')) // Link to be created in Phase 3
            ->line('Please accept or reject this assignment via the Driver Portal.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'service_order_id' => $this->serviceOrder->id,
            'folio' => $this->serviceOrder->folio,
            'message' => 'New service assignment #' . $this->serviceOrder->folio,
        ];
    }
}

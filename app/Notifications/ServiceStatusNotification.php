<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceStatusNotification extends Notification
{
    use Queueable;

    public $serviceOrder;
    public $status; // 'at_pickup', 'on_board', 'finished'

    /**
     * Create a new notification instance.
     */
    public function __construct(ServiceOrder $serviceOrder, string $status)
    {
        $this->serviceOrder = $serviceOrder;
        $this->status = $status;
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
        $order = $this->serviceOrder;
        $driverName = $order->driver->name ?? 'Your Driver';
        $vehicle = $order->vehicle ? "({$order->vehicle->model} - {$order->vehicle->plate})" : '';
        $trackingUrl = route('tracking.show', $order->id);
        // $surveyUrl = route('reviews.create', $order->reservationItem->token); // Assuming review token exists
        $surveyUrl = url('/'); // Placeholder

        $mail = (new MailMessage)->subject('Update on your transfer #' . $order->folio);

        switch ($this->status) {
            case 'at_pickup':
                $mail->greeting('Your Driver is Here!')
                    ->line("Good news! $driverName has arrived at the pickup location.")
                    ->line("Look for: $vehicle")
                    ->action('Track Driver', $trackingUrl);
                break;

            case 'on_board':
                $mail->greeting('Trip Started')
                    ->line("You are on your way to " . $order->reservationItem->dropoff_location)
                    ->line("Sit back and relax.")
                    ->action('Track Trip', $trackingUrl);
                break;

            case 'finished':
                $mail->subject('Trip Completed - Receipt #' . $order->folio)
                    ->greeting('You Arrived!')
                    ->line("Your trip to " . $order->reservationItem->dropoff_location . " has ended.")
                    ->line("Thank you for riding with us.")
                    ->action('Rate Your Experience', $surveyUrl);
                break;

            case 'en_route_base':
                $mail->greeting('Driver En Route')
                    ->line("$driverName is on the way to pick you up.")
                    ->action('Track Driver', $trackingUrl);
                break;
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendServiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:send-reminders';
    protected $description = 'Send 24h and 2h reminders for upcoming services';

    public function handle()
    {
        // 24 Hours Reminder
        $this->processReminders(24);

        // 2 Hours Reminder
        $this->processReminders(2);
    }

    protected function processReminders($hours)
    {
        $targetTime = now()->addHours($hours)->startOfHour();
        $endTime = $targetTime->copy()->endOfHour();

        // Find items that match the target time window
        // Note: For real-time precision, we might run this every minute and check ranges,
        // but for hourly cron, this window works.
        $items = \App\Models\ReservationItem::whereBetween('date', [$targetTime, $endTime])
            ->with(['reservation.user', 'assignedProvider', 'organization'])
            ->get();

        foreach ($items as $item) {
            $this->info("Sending {$hours}h reminder for Item #{$item->id}");

            // Notify Client
            if ($item->reservation->user) {
                \Illuminate\Support\Facades\Mail::to($item->reservation->user->email)
                    ->send(new \App\Mail\ServiceReminderEmail($item, $hours, 'client'));
            }

            // Notify Provider
            if ($item->assignedProvider && $item->assignedProvider->email) {
                \Illuminate\Support\Facades\Mail::to($item->assignedProvider->email)
                    ->send(new \App\Mail\ServiceReminderEmail($item, $hours, 'provider'));
            }

            // Notify Admin (of the organization)
            // Ideally we find the Organization's Admin user. For now, sending to org generic email or root if configured.
            if ($item->organization) {
                // Simplification: Send to root or a configured org email.
                // \Illuminate\Support\Facades\Mail::to('admin@example.com')...
            }
        }
    }
}

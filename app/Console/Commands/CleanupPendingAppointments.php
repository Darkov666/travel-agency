<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;

class CleanupPendingAppointments extends Command
{
    protected $signature = 'appointments:cleanup';
    protected $description = 'Cancel pending appointments that have expired (older than 24h)';

    public function handle()
    {
        $expired = Appointment::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $appointment) {
            $appointment->update(['status' => 'cancelled']);
            $this->info("Cancelled appointment ID: {$appointment->id}");
        }

        $this->info('Cleanup completed.');
    }
}

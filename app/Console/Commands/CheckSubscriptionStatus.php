<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Organization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionSuspended;

class CheckSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue subscriptions and suspend organizations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking subscription statuses...');

        $overdueOrgs = Organization::where('subscription_status', 'active')
            ->where('next_payment_date', '<', now())
            ->get();

        if ($overdueOrgs->isEmpty()) {
            $this->info('No overdue subscriptions found.');
            return;
        }

        foreach ($overdueOrgs as $org) {
            $formattedDate = \Carbon\Carbon::parse($org->next_payment_date)->format('Y-m-d');
            $this->warn("Suspending organization: {$org->name} (ID: {$org->id}) - Due: {$formattedDate}");

            $org->update([
                'subscription_status' => 'suspended',
                'is_active' => false,
            ]);

            // Optional: Send Email Notification
            // Mail::to($org->representative_email)->send(new SubscriptionSuspended($org));
            Log::info("Organization {$org->id} suspended due to non-payment.");
        }

        $this->info("Suspended {$overdueOrgs->count()} organizations.");
    }
}

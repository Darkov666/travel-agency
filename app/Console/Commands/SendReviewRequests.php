<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\Review;
use App\Mail\ReviewRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendReviewRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:send-requests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send review request emails to customers after their trip is completed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Criteria:
        // 1. Status is 'completed' (or we check end dates)
        // 2. No review exists (or token not generated)
        // 3. Trip finished X hours ago (e.g., 2 hours)

        // For simplicity, we'll fetch confirmed reservations where the last item's return date/time has passed.
        // And ensure we haven't already created a Review model for it.

        $reservations = Reservation::where('status', 'confirmed') // Or 'completed' if you have a status updater
            ->whereDoesntHave('review')
            ->with('items')
            ->get();

        $count = 0;

        foreach ($reservations as $reservation) {
            // Find the latest end time of the trip
            $latestItem = $reservation->items->sortByDesc(function ($item) {
                // Return date takes precedence, otherwise date
                return $item->return_date ?? $item->date;
            })->first();

            if (!$latestItem)
                continue;

            $endDate = $latestItem->return_date ?? $latestItem->date;
            $endTime = $latestItem->return_time ?? $latestItem->time ?? '23:59:00';

            $finishTime = \Carbon\Carbon::parse("$endDate $endTime");

            // If finish time was more than 2 hours ago
            if ($finishTime->addHours(2)->isPast()) {

                // Create Review Placeholder with Token
                $token = Str::random(32);

                Review::create([
                    'reservation_id' => $reservation->id,
                    'token' => $token,
                    'rating' => 0, // 0 indicates not submitted
                    'content' => '',
                    'reviewer_name' => $reservation->contact_name,
                ]);

                // Send Email
                Mail::to($reservation->contact_email)->send(new ReviewRequest($reservation, $token));

                $this->info("Sent review request to {$reservation->contact_email} (Ref: {$reservation->booking_ref})");
                $count++;
            }
        }

        $this->info("Processed {$count} review requests.");
    }
}

<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Review;
use App\Mail\ReviewActionRequired;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ReviewRequestService
{
    public function sendRequest(Reservation $reservation)
    {
        // Check if review already exists
        if ($reservation->review) {
            return $reservation->review;
        }

        // Create incomplete review
        $review = Review::create([
            'reservation_id' => $reservation->id,
            'token' => Str::random(32),
            'rating' => 0, // Placeholder
            'content' => '',
            'reviewer_name' => $reservation->contact_name . ' ' . substr($reservation->contact_surname, 0, 1) . '.',
            'is_approved' => false,
        ]);

        // Send Email
        // Assuming 'contact_email' exists on Reservation
        if ($reservation->contact_email) {
            Mail::to($reservation->contact_email)->send(new ReviewActionRequired($review));
        }

        return $review;
    }
}

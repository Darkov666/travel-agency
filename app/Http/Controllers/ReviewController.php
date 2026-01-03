<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    // Show the public review form
    public function show($token)
    {
        $review = Review::where('token', $token)->firstOrFail();

        if ($review->rating) {
            return redirect()->route('welcome')->with('message', 'Review already submitted.');
        }

        $reservation = $review->reservation;

        return Inertia::render('Reviews/Submit', [
            'token' => $token,
            'customerName' => $reservation->contact_name, // Default, editable
        ]);
    }

    // Process public review submission
    public function store(Request $request, $token)
    {
        $review = Review::where('token', $token)->firstOrFail();

        if ($review->rating) {
            abort(403, 'Review already submitted.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:1000',
            'reviewer_name' => 'required|string|max:255',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'reviewer_name' => $validated['reviewer_name'],
            'is_approved' => false, // Requires admin approval
        ]);

        // Notify Admin (TODO)

        return redirect()->route('welcome')->with('success', 'Thank you for your feedback! It will be published shortly.');
    }
}

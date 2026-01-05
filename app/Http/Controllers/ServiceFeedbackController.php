<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceFeedbackController extends Controller
{
    public function show($token)
    {
        $review = Review::where('token', $token)->firstOrFail();

        if ($review->rating) {
            return redirect('/')->with('info', 'You have already submitted this review. Thank you!');
        }

        return Inertia::render('Feedback/ServiceForm', [
            'token' => $token,
            'reservation' => $review->reservation()->with(['service', 'vehicle'])->first(),
        ]);
    }

    public function store(Request $request, $token)
    {
        $review = Review::where('token', $token)->firstOrFail();

        if ($review->rating) {
            return back()->with('error', 'Review already submittted.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'nullable|string|max:1000',
            'reviewer_name' => 'required|string|max:255',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'reviewer_name' => $validated['reviewer_name'],
            'is_approved' => false, // Requires moderation
        ]);

        return redirect('/')->with('success', 'Thank you for your review! It will be posted after moderation.');
    }
}

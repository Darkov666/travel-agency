<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\WebsiteFeedback;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'website'); // 'website' or 'service'

        if ($type === 'service') {
            $feedbacks = Review::with(['reservation.service'])
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else {
            $feedbacks = WebsiteFeedback::with('user')
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return Inertia::render('Admin/Feedback/Index', [
            'type' => $type,
            'feedbacks' => $feedbacks,
        ]);
    }

    // Service Reviews Actions
    public function approveReview(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved.');
    }

    public function rejectReview(Review $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Review rejected/hidden.');
    }

    public function destroyReview(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }

    // Website Feedback Actions (Read-only mostly, but can delete)
    public function markReviewed(WebsiteFeedback $feedback)
    {
        $feedback->update(['is_reviewed' => true]);
        return back()->with('success', 'Marked as reviewed.');
    }

    public function destroyWebsiteFeedback(WebsiteFeedback $feedback)
    {
        $feedback->delete();
        return back()->with('success', 'Feedback deleted.');
    }
}

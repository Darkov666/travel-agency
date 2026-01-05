<?php

namespace App\Http\Controllers;

use App\Models\WebsiteFeedback;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebsiteFeedbackController extends Controller
{
    public function show($token)
    {
        $feedback = WebsiteFeedback::where('token', $token)->firstOrFail();

        if ($feedback->rating) {
            return redirect('/')->with('info', 'You have already submitted this feedback. Thank you!');
        }

        return Inertia::render('Feedback/WebsiteForm', [
            'token' => $token,
        ]);
    }

    public function store(Request $request, $token)
    {
        $feedback = WebsiteFeedback::where('token', $token)->firstOrFail();

        if ($feedback->rating) {
            return back()->with('error', 'Feedback already submitted.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000',
        ]);

        $feedback->update([
            'rating' => $validated['rating'],
            'comments' => $validated['comments'] ?? null,
        ]);

        return redirect('/')->with('success', 'Thank you for your feedback!');
    }
}

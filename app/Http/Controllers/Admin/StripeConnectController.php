<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeService;
use Illuminate\Support\Facades\Auth;

class StripeConnectController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function connect(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOrgAdmin()) {
            abort(403);
        }

        $organization = $user->organization;

        // 1. Create Stripe Account if not exists
        if (!$organization->stripe_connect_id) {
            try {
                $account = $this->stripe->createConnectAccount($organization);
                $organization->update(['stripe_connect_id' => $account->id]);
            } catch (\Exception $e) {
                return back()->with('error', 'Failed to create Stripe Account: ' . $e->getMessage());
            }
        }

        // 2. Generate Account Link
        try {
            $link = $this->stripe->createAccountLink(
                $organization->stripe_connect_id,
                route('admin.stripe.connect'), // Refresh URL (loop back here to retry)
                route('admin.stripe.return')   // Return URL
            );

            return \Inertia\Inertia::location($link->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate onboarding link: ' . $e->getMessage());
        }
    }

    public function handleReturn()
    {
        // User returned from Stripe. We assume they completed (or skipped) some steps.
        // We should just redirect them to Dashboard with a success/info message.
        // Real status updates come via Webhooks.
        return redirect()->route('admin.dashboard')->with('success', 'Stripe onboarding flow completed. Please check status.');
    }
}

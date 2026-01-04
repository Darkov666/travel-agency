<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        if (empty($endpoint_secret)) {
            // If secret is not configured, we might verify conceptually or just log warning
            // For dev/test without CLI forwarding, it might be empty.
            // But strictly, we should require it for security.
            Log::warning('Stripe Webhook Secret not configured.');
        }

        $event = null;

        try {
            if ($endpoint_secret) {
                $event = Webhook::constructEvent(
                    $payload,
                    $sig_header,
                    $endpoint_secret
                );
            } else {
                // Fallback for dev without verification (Not recommended for prod)
                $event = \Stripe\Event::constructFrom(
                    json_decode($payload, true)
                );
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'account.updated':
                $account = $event->data->object;
                $this->handleAccountUpdated($account);
                break;
            default:
            // Unexpected event type
            // echo 'Received unknown event type ' . $event->type;
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleAccountUpdated($account)
    {
        Log::info('Stripe Account Updated: ' . $account->id);

        $organization = Organization::where('stripe_connect_id', $account->id)->first();

        if ($organization) {
            $chargesEnabled = $account->charges_enabled;
            $payoutsEnabled = $account->payouts_enabled;
            // $detailsSubmitted = $account->details_submitted;

            $status = ($chargesEnabled && $payoutsEnabled);

            $organization->update([
                'stripe_connect_enabled' => $status,
                // We could also store more details if needed
            ]);

            Log::info("Organization {$organization->id} Stripe Connect Status updated to: " . ($status ? 'Enabled' : 'Disabled'));
        } else {
            Log::warning('Organization not found for Stripe Account: ' . $account->id);
        }
    }
}

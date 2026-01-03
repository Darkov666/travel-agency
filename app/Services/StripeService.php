<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a Checkout Session for SaaS Subscription
     */
    public function createSubscriptionCheckoutSession($organization, $priceId)
    {
        return $this->stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'customer_email' => $organization->representative_email,
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ]
            ],
            'success_url' => route('partner.payment.success', ['organization' => $organization->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('partner.payment.show', ['organization' => $organization->id]),
            'metadata' => [
                'organization_id' => $organization->id,
                'type' => 'saas_subscription'
            ],
        ]);
    }

    /**
     * Create a Connect Account for the Organization
     */
    public function createConnectAccount($organization)
    {
        return $this->stripe->accounts->create([
            'type' => 'express',
            'country' => 'MX',
            'email' => $organization->representative_email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            'business_type' => 'company',
            'company' => [
                'name' => $organization->razon_social,
                'tax_id' => $organization->rfc,
            ],
            'metadata' => [
                'organization_id' => $organization->id,
            ]
        ]);
    }

    public function createAccountLink($accountId, $refreshUrl, $returnUrl)
    {
        return $this->stripe->accountLinks->create([
            'account' => $accountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);
    }
    /**
     * Create Checkout Session for Booking (Split Payment)
     */
    public function createBookingCheckoutSession($reservation, $amountToCharge, $destinationAccountId = null, $feeAmount = 0, $successUrl, $cancelUrl)
    {
        $params = [
            'mode' => 'payment',
            'customer_email' => $reservation->contact_email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd', // Or mxn, configurable
                        'product_data' => [
                            'name' => 'Booking Reference: ' . $reservation->booking_ref,
                        ],
                        'unit_amount' => (int) ($amountToCharge * 100), // In cents
                    ],
                    'quantity' => 1,
                ]
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'reservation_id' => $reservation->id,
                'booking_ref' => $reservation->booking_ref,
                'type' => 'booking_payment'
            ],
        ];

        // Apply Connect Split if Destination is provided
        if ($destinationAccountId) {
            $params['payment_intent_data'] = [
                'transfer_data' => [
                    'destination' => $destinationAccountId,
                ],
                // Fee must be less than amountToCharge
                'application_fee_amount' => (int) ($feeAmount * 100),
            ];
        }

        return $this->stripe->checkout->sessions->create($params);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('checkout.details');
    }

    public function details(Request $request)
    {
        $cart = $this->getCart($request);
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $cart->load(['items.providerService.service', 'items.providerService.zone', 'items.providerService.provider']);

        return Inertia::render('Shop/Checkout/Details', [
            'cart' => $cart,
            'user' => Auth::user(),
        ]);
    }

    public function storeDetails(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_surname' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => ['nullable', 'string', 'regex:/^[0-9]{10,14}$/'],
            // Holder override validation?
            'items' => 'array', // For per-item holder info matches
            // Validate at least one adult per item
            'passengers.*.list' => function ($attribute, $value, $fail) {
                $hasAdult = collect($value)->contains('type', 'adult');
                if (!$hasAdult) {
                    $fail('Each service must have at least one adult passenger.');
                }
            },
        ]);

        // Create Draft Reservation
        // ... Logic to convert Cart to Reservation (Pending Payment) ... 
        // For simplicity, we store inputs in session and move to payment OR create DB record now.
        // Creating DB record is safer.

        $cart = $this->getCart($request);
        $cart->load(['items.providerService.provider', 'items.providerService.service', 'items.providerService.zone']);

        $totalAmount = 0;
        foreach ($cart->items as $item) {
            $totalAmount += $item->price; // Assumed price is total for line item
        }

        $reservation = \App\Models\Reservation::create([
            'booking_ref' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => Auth::id(),
            'contact_name' => $validated['contact_name'],
            'contact_surname' => $validated['contact_surname'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'total_amount' => $totalAmount,
            'status' => 'draft'
        ]);

        foreach ($cart->items as $item) {
            // Determine Holder: Default to Contact, unless override provided
            // Frontend should pass 'holders' array keyed by cart_item_id or similar handling
            $itemHolder = $request->input("holders.{$item->id}") ?? $validated['contact_name'] . ' ' . $validated['contact_surname'];

            $resItem = \App\Models\ReservationItem::create([
                'reservation_id' => $reservation->id,
                'provider_service_id' => $item->provider_service_id,
                'service_name' => $item->providerService->service->title ?? $item->providerService->name,
                'provider_name' => $item->providerService->provider->name,
                'zone_name' => $item->providerService->zone->name ?? 'N/A',
                'quantity' => $item->quantity,
                'units' => $item->units,
                'pax' => $item->pax,
                'date' => $item->date,
                'return_date' => $item->return_date,
                'holder_name' => $itemHolder,
                'passengers_data' => $request->input("passengers.{$item->id}.list"),
                'unit_price' => $item->price / max(1, $item->quantity),
                'total_price' => $item->price,
                // Flight Data
                'airline' => $request->input("flights.{$item->id}.arrival_airline"),
                'arrival_flight_number' => $request->input("flights.{$item->id}.arrival_number"),
                'arrival_time' => $request->input("flights.{$item->id}.arrival_time"),
                'arrival_terminal' => $request->input("flights.{$item->id}.arrival_terminal"),

                'departure_airline' => $request->input("flights.{$item->id}.departure_airline"),
                'departure_flight_number' => $request->input("flights.{$item->id}.departure_number"),
                'departure_time' => $request->input("flights.{$item->id}.departure_time"),
                'departure_terminal' => $request->input("flights.{$item->id}.departure_terminal"),
                'flight_type' => $request->input("flights.{$item->id}.type") ?? 'international',
            ]);

            // Calculate Pickup Time if Departure exists
            if ($item->return_date && $request->input("flights.{$item->id}.departure_time")) {
                $fltType = $request->input("flights.{$item->id}.type") ?? 'international';
                $bufferMinutes = ($fltType === 'local') ? 120 : 180; // 2h vs 3h

                // Get Zone Transfer Time
                $zoneTime = $item->providerService->zone->transfer_time_minutes ?? 60;

                // Parse Departure DateTime
                try {
                    $departureDateTime = \Carbon\Carbon::parse($item->return_date . ' ' . $request->input("flights.{$item->id}.departure_time"));

                    // Calculation: Dept - Buffer - Transfer
                    $pickupDateTime = $departureDateTime->copy()->subMinutes($bufferMinutes)->subMinutes($zoneTime);

                    $resItem->pickup_time = $pickupDateTime;
                    $resItem->save();
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Pickup calculation error: " . $e->getMessage());
                }
            }
        }

        // Redirect to Payment with Reservation ID (hashed or UUID)
        return redirect()->route('checkout.payment', ['reservation' => $reservation->booking_ref]);
    }

    public function payment(Request $request, $bookingRef)
    {
        $reservation = \App\Models\Reservation::with('items')->where('booking_ref', $bookingRef)->firstOrFail();

        if ($reservation->status !== 'draft' && $reservation->status !== 'pending') {
            // Already processed?
        }

        return Inertia::render('Shop/Checkout/Payment', [
            'reservation' => $reservation,
            'paypalClientId' => env('PAYPAL_CLIENT_ID')
        ]);
    }

    public function processPayment(Request $request, $bookingRef)
    {
        $reservation = \App\Models\Reservation::where('booking_ref', $bookingRef)->firstOrFail();

        $validated = $request->validate([
            'payment_method' => 'required|in:paypal,transfer,cash',
            'payment_choice' => 'nullable|in:full,deposit',
        ]);

        $method = $validated['payment_method'];
        $choice = $validated['payment_choice'] ?? 'deposit';

        $reservation->payment_method = $method;
        $reservation->payment_choice = $choice;

        // Calculate Paid Amount
        $total = $reservation->total_amount;
        $paid = 0;

        if ($method === 'cash') {
            // Cash always deposit only effectively
            $paid = 0; // Or standard commission? Let's assume 0 confirmed until "paid" via other channel? 
            // Usually cash booking = Pay Commission Online? 
            // User said: "charged upfront commission... rest in cash".
            // So if choosing "Cash", user pays Commission NOW (via transfer/paypal?).
            // Contradiction: "Payment Method" usually implies HOW they pay NOW.
            // If they select "Cash", usually means "I will pay Cash". 
            // But if commission is REQUIRED upfront, "Cash" option implies "Pay Commission Now, Rest Cash Later".
            // Let's assume "Cash" means "Pay Deposit Now".
            $paid = 0; // Pending deposit confirmation
        } elseif ($choice === 'full') {
            $paid = $total;
        } else {
            // Deposit (20%)
            $paid = $total * 0.20;
        }

        if ($method === 'paypal') {
            // SIMULATION: Assume success
            $reservation->payment_status = ($choice === 'full') ? 'paid' : 'partial';
            $reservation->amount_paid = $paid;
            $reservation->balance_due = $total - $paid;
            $reservation->status = 'confirmed';
            $reservation->save();

            // Send Confirmation Email
            $this->sendConfirmationEmail($reservation);

            // Clear Cart
            $cart = $this->getCart($request);
            if ($cart)
                $cart->items()->delete();

            return redirect('/')->with('success', 'Reservation confirmed! Check your email.');
        } else {
            // Transfer or Cash (Deposit via Transfer)
            $reservation->payment_status = 'pending';
            $reservation->amount_paid = 0; // Nothing paid yet
            $reservation->balance_due = $total; // Full amount due until proof verified
            $reservation->status = 'pending';
            $reservation->save();

            // Clear Cart (Reservation is saved)
            $cart = $this->getCart($request);
            if ($cart)
                $cart->items()->delete();

            // Send Confirmation/Instruction Email for pending payment
            $this->sendConfirmationEmail($reservation);

            return redirect('/')->with('success', 'Reservation placed! Please check your email for payment instructions.');
        }
    }

    public function pending(Request $request, $bookingRef)
    {
        $reservation = \App\Models\Reservation::where('booking_ref', $bookingRef)->firstOrFail();
        return Inertia::render('Shop/Checkout/Pending', [
            'reservation' => $reservation
        ]);
    }

    private function sendConfirmationEmail($reservation)
    {
        try {
            // To Customer
            \Illuminate\Support\Facades\Mail::to($reservation->contact_email)->send(new \App\Mail\BookingConfirmation($reservation));

            // To Admin (Root)
            // Pass extra data? The Mailable constructor might need update or we can use with()
            // Let's generic Mailable doesn't support extra flags easily without changing signature.
            // But we can attach data to the Mailable instance if public.

            $adminMail = new \App\Mail\BookingConfirmation($reservation);
            $adminMail->isAdminCopy = true;

            \Illuminate\Support\Facades\Mail::to('root@example.com')->send($adminMail);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Email sending failed: " . $e->getMessage());
        }
    }

    private function getCart(Request $request)
    {
        // ... (Keep existing)
        $user = Auth::user();
        if ($user)
            return Cart::firstOrCreate(['user_id' => $user->id]);
        return Cart::firstOrCreate(['session_id' => $request->session()->getId()]);
    }
    public function confirmPayment(Request $request, $bookingRef)
    {
        // Protected by Admin middleware in real app
        $reservation = \App\Models\Reservation::where('booking_ref', $bookingRef)->firstOrFail();

        if ($reservation->status === 'confirmed') {
            return redirect()->back()->with('message', 'Already confirmed.');
        }

        $reservation->status = 'confirmed';
        // Set payment status if pending was full/deposit?
        // Let's assume manual confirmation implies payment was received as per choice logic
        // If choice was full, mark paid. If deposit, mark partial. 
        // We need to check payment_choice.

        $choice = $reservation->payment_choice ?? 'deposit';
        $reservation->payment_status = ($choice === 'full') ? 'paid' : 'partial';
        $reservation->amount_paid = ($choice === 'full') ? $reservation->total_amount : ($reservation->total_amount * 0.20);
        $reservation->balance_due = $reservation->total_amount - $reservation->amount_paid;

        $reservation->save();

        // Send Confirmation Email
        $this->sendConfirmationEmail($reservation);

        return redirect()->back()->with('success', 'Reservation confirmed and email sent.');
    }
}

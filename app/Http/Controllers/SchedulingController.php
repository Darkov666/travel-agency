<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class SchedulingController extends Controller
{
    private function getCart()
    {
        $user = Auth::user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        } else {
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate(['session_id' => $sessionId]);
        }
        return $cart;
    }

    public function index()
    {
        $cart = $this->getCart();
        $cart->load('items.service');

        // Filter items that require scheduling and explode by quantity
        $items = collect();

        foreach ($cart->items as $item) {
            if ($item->service->requires_scheduling) {
                for ($i = 0; $i < $item->quantity; $i++) {
                    $items->push([
                        'id' => $item->id . '_' . $i, // Unique ID for frontend keying
                        'cart_item_id' => $item->id,
                        'service' => $item->service,
                        'index' => $i + 1,
                        'total' => $item->quantity,
                        'title' => $item->service->title . ($item->quantity > 1 ? " (Sesión " . ($i + 1) . " de " . $item->quantity . ")" : "")
                    ]);
                }
            }
        }

        if ($items->isEmpty()) {
            return redirect()->route('checkout.index');
        }

        return Inertia::render('Shop/Scheduling', [
            'items' => $items
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'appointments' => 'required|array',
            // Allow date/time to be nullable for "Pending Date" feature if we implement it.
            // But current requirement implies managing order.
            // Let's allow nullable if we trust frontend validation or want to implement "Open Date".
            // Implementation Plan said: "Option A: Pick distinct dates... Option B: Schedule 1st... Pending Rest".
            // Let's support nullable here to enable "Pending".
            'appointments.*.date' => 'nullable|date|after_or_equal:today',
            'appointments.*.time' => 'nullable',
            // Participants validation (optional array)
            'appointments.*.participants' => 'nullable|array',
            'appointments.*.participants.*.name' => 'required_with:appointments.*.participants|string',
            'appointments.*.participants.*.surname' => 'required_with:appointments.*.participants|string',
            'appointments.*.participants.*.gender' => 'required_with:appointments.*.participants|string',
        ]);

        // Store scheduling data in session to be picked up by Checkout
        // Structure: [ 'item_id_0' => { date, time }, 'item_id_1' => ... ]
        session(['booking_appointments' => $request->appointments]);

        return redirect()->route('checkout.index');
    }
}

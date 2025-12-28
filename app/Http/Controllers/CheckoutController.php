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
        $user = Auth::user();
        $sessionId = $request->session()->getId();

        $cart = Cart::with('items.service')->where('user_id', $user ? $user->id : null)
            ->orWhere('session_id', $sessionId)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // Check if scheduling is required
        $requiresScheduling = $cart->items->contains(fn($item) => $item->service->requires_scheduling);
        $appointments = session('booking_appointments', []);

        if ($requiresScheduling && empty($appointments)) {
            return redirect()->route('scheduling.index');
        }

        return Inertia::render('Shop/Checkout', [
            'cart' => $cart,
            'appointments' => $appointments,
            'user' => $user
        ]);
    }

    private function getCart(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return Cart::firstOrCreate(['user_id' => $user->id]);
        }
        $sessionId = $request->session()->getId();
        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,transfer',
            'name' => 'required_without:user_id|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required_without:user_id|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|string',
            'session_type' => 'required|in:online,in_person',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Handle User Auto-creation or Retrieval if not logged in
        if (!$user) {
            // Mimicking BookingController behavior: Find or Create
            $user = \App\Models\User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name . ($request->surname ? ' ' . $request->surname : ''),
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)), // Random password
                    'role' => 'patient',
                    'phone' => $request->phone,
                    'gender' => $request->gender,
                ]
            );

            // If user existed, maybe update phone/gender if missing? 
            // For now, respect existing data if found.

            Auth::login($user);

            // Migrate Cart
            $sessionId = $request->session()->getId();
            Cart::where('session_id', $sessionId)->update(['user_id' => $user->id, 'session_id' => null]);
        }

        // Handle Profile Photo Upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Re-fetch cart for the user
        $cart = Cart::with('items.service')->where('user_id', $user->id)->first();

        if (!$cart) {
            return redirect('/')->with('error', 'Carrito no encontrado o expirado.');
        }

        $appointmentsData = session('booking_appointments', []);
        $paymentMethod = $request->payment_method;

        // Initial Payment Status
        $paymentStatus = ($paymentMethod === 'confirmed' || $paymentMethod === 'paypal') ? 'paid' : 'pending';

        $createdAppointments = []; // Collect for emails
        $digitalProducts = [];

        foreach ($cart->items as $item) {
            // Loop by Quantity (create N records)
            for ($i = 0; $i < $item->quantity; $i++) {

                if ($item->service->requires_scheduling) {
                    // It's a service (Therapy)
                    $schedKey = $item->id . '_' . $i; // Matches SchedulingController key
                    $data = $appointmentsData[$schedKey] ?? null;

                    $scheduledAt = null;
                    $endTime = null;
                    $apptStatus = 'pending'; // Default

                    if ($data && !empty($data['date']) && !empty($data['time'])) {
                        // Date Selected
                        $scheduledAt = $data['date'] . ' ' . $data['time'];
                        $endTime = \Carbon\Carbon::parse($scheduledAt)->addMinutes($item->service->duration_minutes);
                        $apptStatus = ($paymentStatus === 'paid') ? 'confirmed' : 'pending';
                    } else {
                        // Open Date (Pending Scheduling)
                        // Status remains 'pending' (or custom 'open')?
                        // If paid effectively, it's a "Credit".
                        $apptStatus = 'pending';
                    }

                    $participants = $data['participants'] ?? null;

                    $appt = Appointment::create([
                        'user_id' => $user->id,
                        'service_id' => $item->service_id,
                        'scheduled_at' => $scheduledAt,
                        'end_time' => $endTime,
                        'status' => $apptStatus,
                        'payment_method' => $paymentMethod,
                        'payment_status' => $paymentStatus,
                        'customer_name' => $user->name, // Fix Capitalization
                        'customer_email' => $user->email,
                        'customer_phone' => $request->phone ?? $user->phone,
                        'session_type' => $request->session_type,
                        'token' => \Illuminate\Support\Str::uuid(),
                        'expires_at' => now()->addHours(24),
                        'participants_data' => $participants ? json_encode($participants) : null,
                    ]);
                    $createdAppointments[] = $appt;

                } else {
                    // Digital Product / Non-Scheduled Logic
                    // Create a record to track purchase
                    $appt = Appointment::create([
                        'user_id' => $user->id,
                        'service_id' => $item->service_id,
                        'scheduled_at' => null,
                        'end_time' => null,
                        'status' => 'confirmed', // Confirmed as "delivered" if paid?
                        'payment_method' => $paymentMethod,
                        'payment_status' => $paymentStatus,
                        'customer_Name' => $user->name,
                        'customer_email' => $user->email,
                        'customer_phone' => $request->phone ?? $user->phone,
                        'session_type' => 'online', // Default or N/A
                        'token' => \Illuminate\Support\Str::uuid(),
                    ]);
                    $digitalProducts[] = $appt;
                }
            }
        }

        // Clear cart and session
        $cart->items()->delete();
        $cart->delete();
        session()->forget('booking_appointments');

        // Send Emails
        // Logic: Send 1 email summary or multiple?
        // Improved: Batch Admin Email
        if ($paymentMethod === 'transfer') {
            foreach ($createdAppointments as $appt) {
                // If Open Date, maybe different email? "BookingPending" works.
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\BookingPendingClientNotification($appt));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Client Email Fail: ' . $e->getMessage());
                }
            }
            // Digital products also pending transfer?
            foreach ($digitalProducts as $prod) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\BookingPendingClientNotification($prod));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Client Email Fail: ' . $e->getMessage());
                }
            }

            // ADMIN BATCH EMAIL
            try {
                // Merge all items for Admin summary
                $allAdminItems = array_merge($createdAppointments, $digitalProducts);
                \Illuminate\Support\Facades\Log::info('Batching Admin Email', ['count' => count($allAdminItems)]);

                if (!empty($allAdminItems)) {
                    \Illuminate\Support\Facades\Mail::to('terapia.test@gmail.com')->send(new \App\Mail\NewBookingAdminSummaryNotification($allAdminItems));
                    \Illuminate\Support\Facades\Log::info('Admin Email Sent Successfully to terapia.test@gmail.com');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin Email Fail: ' . $e->getMessage());
                \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            }

        } elseif ($paymentMethod === 'paypal') {
            foreach ($createdAppointments as $appt) {
                if ($appt->scheduled_at) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReservationConfirmedClientNotification($appt));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Client Email Fail: ' . $e->getMessage());
                    }
                } else {
                    // Open Date Confirmed - "You have a credit"
                    try {
                        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReservationConfirmedClientNotification($appt));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Client Email Fail: ' . $e->getMessage());
                    }
                }
            }
            foreach ($digitalProducts as $prod) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReservationConfirmedClientNotification($prod));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Client Email Fail: ' . $e->getMessage());
                }
            }

            // ADMIN BATCH EMAIL
            try {
                $allAdminItems = array_merge($createdAppointments, $digitalProducts);
                if (!empty($allAdminItems)) {
                    \Illuminate\Support\Facades\Mail::to('terapia.test@gmail.com')->send(new \App\Mail\NewBookingAdminSummaryNotification($allAdminItems));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin Email Fail: ' . $e->getMessage());
            }
        }

        $message = 'Compra realizada con éxito. ' . ($paymentMethod == 'transfer' ? 'Esperando confirmación de transferencia. Revisa tu correo.' : 'Pago confirmado. Revisa tu correo.');
        return redirect('/')->with('success', $message);
    }
}

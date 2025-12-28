<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewBookingAdminNotification;
use App\Mail\BookingPendingClientNotification;
// use Spatie\GoogleCalendar\Event; // Removed unused/unknown class
use Carbon\Carbon;

class BookingController extends Controller
{
    public function showIndividualForm(Request $request)
    {
        $service = null;
        if ($request->has('service_id')) {
            $service = Service::find($request->service_id);
        }
        return Inertia::render('Booking/IndividualForm', [
            'service' => $service
        ]);
    }

    public function showGroupForm(Request $request)
    {
        $service = null;
        if ($request->has('service_id')) {
            $service = Service::find($request->service_id);
        }
        return Inertia::render('Booking/GroupForm', [
            'service' => $service
        ]);
    }

    public function storeIndividual(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^\d{10}$/', // 10 digits exact
            'gender' => 'required|string',
            'session_type' => 'required|in:online,in_person',
            'photo' => 'required|image|max:2048', // Mandatory
            'service_id' => 'nullable|exists:services,id',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'patient',
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
            ]
        );

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'] ?? 1,
            'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'pending',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $validated['phone'],
            'session_type' => $validated['session_type'],
            'token' => Str::uuid(),
            'expires_at' => now()->addHours(24),
        ]);

        // Send Emails (Moved to after payment/confirmation or keep here? 
        // Better to keep here so they have the link, but maybe update email content to say "Pick a date")
        // Actually, let's NOT send emails yet. Let's send them after they pick a date.
        // Or send them now but with "Pending Date" status?
        // The requirement says "Reserva -> Calendario -> Pago".
        // So we shouldn't send "Payment Pending" email until they pick a date.

        return redirect()->route('booking.date', $appointment->id);
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|size:10|regex:/^[0-9]+$/',
            'gender' => 'required|string',
            'photo' => 'required|image|max:2048',
            'service_id' => 'nullable|exists:services,id',
            'participants' => 'required|array|min:1',
            'participants.*.name' => 'required|string',
            'participants.*.surname' => 'required|string',
            'participants.*.gender' => 'required|string',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'patient',
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
            ]
        );

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'] ?? 1,
            'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0), // Temporary
            'end_time' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'pending',
            'participants_data' => json_encode($validated['participants']),
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'token' => Str::uuid(),
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->route('booking.date', $appointment->id);
    }

    public function showSpecialForm(Request $request)
    {
        $service = null;
        if ($request->has('service_id')) {
            $service = Service::find($request->service_id);
        }
        return Inertia::render('Booking/SpecialForm', [
            'service' => $service
        ]);
    }

    public function storeSpecial(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'service_id' => 'nullable|exists:services,id',
            'notes' => 'required|string|max:1000', // Requirements
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'patient',
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
            ]
        );

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'] ?? 1,
            'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'pending',
            'notes' => $validated['notes'],
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'token' => Str::uuid(),
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->route('booking.date', $appointment->id);
    }

    public function selectDate(Request $request, Appointment $appointment)
    {
        $date = $request->input('date', now()->addDay()->format('Y-m-d'));

        $duration = 60;
        if ($appointment->service && in_array($appointment->service->type, ['workshop', 'conference', 'talk', 'training'])) {
            $duration = 45;
        } else if ($appointment->service) {
            $duration = $appointment->service->duration_minutes ?? 60;
        }

        $calendarService = new \App\Services\GoogleCalendarService();
        $slots = $calendarService->getAvailableSlots($date, $duration);

        return Inertia::render('Booking/SelectDate', [
            'appointment' => $appointment->load('service'),
            'availableSlots' => $slots,
            'currentDate' => $date,
        ]);
    }

    public function saveDate(Request $request, Appointment $appointment)
    {
        \Illuminate\Support\Facades\Log::info('saveDate called', ['request' => $request->all(), 'appointment_id' => $appointment->id]);

        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        $start = Carbon::parse($validated['date'] . ' ' . $validated['time']);

        // Duration Logic
        $service = $appointment->service;
        $duration = 60; // Default
        if ($service && in_array($service->type, ['workshop', 'conference', 'talk', 'training'])) {
            $duration = 45;
        } else if ($service) {
            $duration = $service->duration_minutes ?? 60;
        }

        $end = $start->copy()->addMinutes($duration);

        $appointment->update([
            'scheduled_at' => $start,
            'end_time' => $end,
        ]);

        \Illuminate\Support\Facades\Log::info('saveDate success, redirecting to payment');

        // Check if service is a workshop to skip payment
        $service = $appointment->service;
        if ($service && in_array($service->type, ['workshop', 'conference', 'talk', 'training'])) {
            $appointment->status = 'pending'; // Pending Admin Approval
            $appointment->save();

            // Send Notification Emails (Admin only or Client too?)
            // Requirement: "el psicologo debe aprobar la fecha seleccionada"
            // "se sigue el proceso de notificaciones tanto al psicologo como al cliente"

            try {
                Mail::to('terapia.test@gmail.com')->send(new NewBookingAdminNotification($appointment));
                Mail::to($appointment->customer_email)->send(new BookingPendingClientNotification($appointment));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Email Error: ' . $e->getMessage());
            }

            return redirect('/')->with('success', 'Solicitud recibida. Espera la confirmación del psicólogo.');
        }

        return redirect()->route('booking.payment', $appointment->id);
    }

    public function payment(Appointment $appointment)
    {
        return Inertia::render('Booking/Payment', [
            'appointment' => $appointment,
            'service' => $appointment->service
        ]);
    }

    public function processPayment(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:stripe,transfer,cash,paypal',
        ]);

        $appointment->payment_method = $validated['payment_method'];

        if ($validated['payment_method'] === 'paypal') {
            // Simulate PayPal success
            $appointment->payment_status = 'paid';
            $appointment->status = 'confirmed';
            $appointment->payment_status = 'paid';
            // Generate unique folio: FOLIO-YYYYMMDD-XXXX
            $appointment->folio = 'FOLIO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            $appointment->save();

            // Create Calendar Event
            $calendarService = new \App\Services\GoogleCalendarService();
            $calendarService->createEvent(
                'Cita con ' . $appointment->customer_name,
                $appointment->scheduled_at,
                $appointment->end_time
            );

            // Send Confirmation Emails
            Mail::to($appointment->customer_email)->send(new \App\Mail\ReservationConfirmedClientNotification($appointment));
            Mail::to('terapia.test@gmail.com')->send(new NewBookingAdminNotification($appointment));
        } elseif ($validated['payment_method'] === 'transfer') {
            $appointment->payment_status = 'pending';
            $appointment->status = 'pending';
            $appointment->save();

            // Send Pending Emails
            Mail::to($appointment->customer_email)->send(new BookingPendingClientNotification($appointment));
            Mail::to('terapia.test@gmail.com')->send(new NewBookingAdminNotification($appointment));
        }

        return redirect('/')->with('success', 'Reserva procesada. Por favor revisa tu correo.');
    }

    public function acceptReservation(Request $request, Appointment $appointment)
    {
        // Admin accepts reservation
        $appointment->status = 'confirmed';
        $appointment->payment_status = 'paid';
        $appointment->folio = 'FOLIO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $appointment->save();

        // Create Calendar Event (Catch errors)
        try {
            $calendarService = new \App\Services\GoogleCalendarService();
            $calendarService->createEvent(
                'Cita con ' . $appointment->customer_name,
                $appointment->scheduled_at,
                $appointment->end_time
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Calendar Error: ' . $e->getMessage());
        }

        // Send Confirmation Email
        Mail::to($appointment->customer_email)->send(new \App\Mail\ReservationConfirmedClientNotification($appointment));

        return back()->with('success', 'Reserva aceptada y cliente notificado.');
    }

    public function acceptReservationSigned(Request $request, Appointment $appointment)
    {
        // Wrapper for GET request from email
        return $this->acceptReservation($request, $appointment);
    }

    public function confirm($token)
    {
        // ... existing confirm logic (maybe deprecated or used for email links?)
        // Keeping it for now but acceptReservation is the main admin action
        $appointment = Appointment::where('token', $token)->firstOrFail();

        if ($appointment->status !== 'pending') {
            return "Esta reserva ya ha sido procesada.";
        }

        $appointment->update(['status' => 'confirmed']);
        $appointment->folio = 'FOLIO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        $appointment->save();

        // Create Google Calendar Event
        try {
            // $event = new Event;
            // $event->name = 'Cita con ' . $appointment->customer_name;
            // $event->startDateTime = Carbon::parse($appointment->scheduled_at);
            // $event->endDateTime = Carbon::parse($appointment->end_time);
            // $event->save();

            // $appointment->update(['google_event_id' => $event->id]);

            // Use Service instead if available
            $calendarService = new \App\Services\GoogleCalendarService();
            $calendarService->createEvent(
                'Cita con ' . $appointment->customer_name,
                $appointment->scheduled_at,
                $appointment->end_time
            );
        } catch (\Exception $e) {
            // Log error but continue
        }

        Mail::to($appointment->customer_email)->send(new \App\Mail\ReservationConfirmedClientNotification($appointment));

        return "Reserva confirmada exitosamente.";
    }

    public function reject($token)
    {
        $appointment = Appointment::where('token', $token)->firstOrFail();

        if ($appointment->status !== 'pending') {
            return "Esta reserva ya ha sido procesada.";
        }

        $appointment->update(['status' => 'cancelled']);

        // Send Rejection Email to Client (TODO)

        return "Reserva rechazada.";
    }
    public function showWorkshopForm(Request $request)
    {
        $service = null;
        if ($request->has('service_id')) {
            $service = Service::find($request->service_id);
        }
        return Inertia::render('Booking/WorkshopForm', [
            'service' => $service
        ]);
    }

    public function storeWorkshop(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'organization_type' => 'required|string',
            'organization_other' => 'nullable|string',
            'photo' => 'required|image|max:2048',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'password' => Hash::make(Str::random(16)),
                'role' => 'patient',
                'phone' => $validated['phone'],
            ]
        );

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
            $user->save();
        }

        if (!Auth::check()) {
            Auth::login($user);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'service_id' => $validated['service_id'] ?? 1,
            'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
            'end_time' => now()->addDay()->setHour(11)->setMinute(0),
            'status' => 'pending',
            'organization_type' => $validated['organization_type'],
            'organization_other' => $validated['organization_other'],
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $validated['phone'],
            'token' => Str::uuid(),
            'expires_at' => now()->addHours(24),
        ]);

        return redirect()->route('booking.date', $appointment->id);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class WorkshopController extends Controller
{
    public function index()
    {
        // Fetch workshop-related services
        $workshops = Service::where('is_active', true)
            ->whereIn('type', ['workshop', 'conference', 'talk', 'training'])
            ->get();

        return Inertia::render('Workshops/Index', [
            'workshops' => $workshops
        ]);
    }

    public function requestMeeting(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'gender' => 'required|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required',
        ]);

        $service = Service::find($validated['service_id']);

        // Generate Google Meet Link (Simulated for now, or use Service)
        // detailed implementation would use GoogleCalendarService
        $meetLink = 'https://meet.google.com/abc-defg-hij';

        // Send Emails
        // 1. To Admin
        try {
            Mail::to('terapia.test@gmail.com')->send(new \App\Mail\AdminNewWorkshopRequest($validated, $service));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Admin Workshop Email: ' . $e->getMessage());
        }

        // 2. To User
        try {
            Mail::to($validated['email'])->send(new \App\Mail\WorkshopMeetingRequest($validated, $meetLink, $service));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send User Workshop Email: ' . $e->getMessage());
        }

        // For MVP, just return success
        return redirect()->back()->with('success', 'Solicitud enviada. Te hemos enviado un correo con el enlace de la reunión.');
    }
}

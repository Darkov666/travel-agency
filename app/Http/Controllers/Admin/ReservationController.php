<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservationItem;
use App\Models\ProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProviderNewBooking;
use App\Mail\AdminCancellationNotification;

class ReservationController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Simple list for now to manage items
        $query = ReservationItem::with(['reservation', 'providerService.provider', 'assignedProvider']);

        // Scope by organization if not root
        if ($user->role !== 'root' && $user->organization_id) {
            $query->whereHas('reservation', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        $items = $query->latest()->paginate(20);

        return Inertia::render('Admin/Reservations/Index', [
            'items' => $items
        ]);
    }

    public function create()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $orgId = $user->organization_id;

        // Fetch services available for this org (or all if root?)
        // Assuming we pick from ProviderServices directly?
        // Or we pick from "Service Catalog" (Service model).
        // Let's pick from ProviderService where provider belongs to org? 
        // Or ProviderService where org is implicit?

        // For SaaS, we want to book a service offered by the current tenant.
        // Tenant -> Providers -> ProviderServices.

        $providerServices = \App\Models\ProviderService::with(['provider', 'service', 'zone'])
            ->when($orgId, function ($q) use ($orgId) {
                // Providers belonging to Org OR assigned to Org
                $q->whereHas('provider', function ($p) use ($orgId) {
                    $p->where('organization_id', $orgId)
                        ->orWhereHas('assignedOrganizations', function ($ass) use ($orgId) {
                            $ass->where('organization_id', $orgId);
                        });
                });
            })
            ->get()
            ->map(function ($ps) {
                return [
                    'id' => $ps->id,
                    'name' => $ps->service_name_full, // accessor? or build string
                    'label' => ($ps->zone ? $ps->zone->name . ' - ' : '') . ($ps->service ? $ps->service->title : $ps->name) . ' ($' . $ps->price_public . ')',
                    'price' => $ps->price_public
                ];
            });

        return Inertia::render('Admin/Reservations/Create', [
            'providerServices' => $providerServices
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string',
            'client_email' => 'required|email',
            'client_phone' => 'nullable|string',
            'provider_service_id' => 'required|exists:provider_services,id',
            'date' => 'required|date',
            'time' => 'required',
            'pax' => 'required|integer|min:1',
            'pickup_location' => 'nullable|string', // Details could be complex
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $ps = \App\Models\ProviderService::with('provider', 'service', 'zone')->find($validated['provider_service_id']);

        // Create Reservation Header
        $reservation = \App\Models\Reservation::create([
            'booking_ref' => 'MAN-' . Str::upper(Str::random(6)),
            'user_id' => $user->id, // Admin created
            'contact_name' => $validated['client_name'],
            'contact_email' => $validated['client_email'],
            'contact_phone' => $validated['client_phone'],
            'total_amount' => $ps->price_public * 1, // Quantity 1 logic for now
            'status' => 'confirmed', // Admin manual entry
            'payment_status' => 'pending', // or paid?
            'organization_id' => $user->organization_id,
            'currency' => 'USD' // Default
        ]);

        // Create Item
        \App\Models\ReservationItem::create([
            'reservation_id' => $reservation->id,
            'provider_service_id' => $ps->id,
            'service_name' => ($ps->service ? $ps->service->title : $ps->name) ?? 'Custom Service',
            'provider_name' => $ps->provider->name,
            'zone_id' => $ps->zone_id,
            'zone_name' => $ps->zone ? $ps->zone->name : 'General',
            'quantity' => 1, // Default to 1 unit
            'units' => 1,
            'unit_price' => $ps->price_public,
            'total_price' => $ps->price_public,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'pax' => $validated['pax'],
            'pickup_time' => $validated['time'], // same as time?
            'passengers_data' => ['pickup_location' => $validated['pickup_location'] ?? ''],
            'assigned_provider_id' => $ps->provider_id, // Default to the service provider
            'vendor_status' => 'pending', // Notify them?
        ]);

        // Send Notifications
        try {
            // 1. To Client
            if ($validated['client_email']) {
                Mail::to($validated['client_email'])->send(new \App\Mail\BookingConfirmation($reservation));
            }

            // 2. To Admin (Notification of created booking?) - Maybe skip since Admin created it.
            // But if created by Supervisor, Admin might want to know.
            // Mail::to($user->email)... ?? 

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Manual Reservation Email Failed: " . $e->getMessage());
        }

        return redirect()->route('admin.reservations.index')->with('success', 'Manual reservation created.');
    }

    public function assignProvider(Request $request, ReservationItem $item)
    {
        $request->validate([
            'provider_id' => 'required|exists:providers,id'
        ]);

        $providerId = $request->provider_id;

        // Logic: Update item with assigned provider and set status to pending
        // Generate token
        $token = Str::random(32);

        $item->update([
            'assigned_provider_id' => $providerId,
            'vendor_status' => 'pending',
            'vendor_confirmation_token' => $token,
            // 'cost' => ? We should look up the cost from ProviderService or a generic cost table?
            // For now assuming the cost is dynamic or entered manually? 
            // Or we assume the provider linked on 'provider_service_id' IS the provider?
            // Actually, provider_service_id is usually tied to a specific provider.
            // If we are reassiging, we might be changing the provider_service_id or just the "assigned_provider" override?
            // Let's assume we are just confirming the provider associated with the service OR switching it.
            // If switching, we should theoretically update provider_service_id too if the pricelist changes.
            // For simplicity/MVP: We assume we are assigning the provider ALREADY LINKED or a new one.
        ]);

        // Send Email
        // We need the provider's email.
        $provider = \App\Models\Provider::find($providerId);
        if ($provider && $provider->email) {
            Mail::to($provider->email)->send(new ProviderNewBooking($item, $token));
        }

        return redirect()->back()->with('success', 'Provider assigned and notified.');
    }

    public function cancelProvider(Request $request, ReservationItem $item)
    {
        // Check current status
        $oldStatus = $item->vendor_status;
        $providerId = $item->assigned_provider_id;

        $item->update([
            'vendor_status' => 'cancelled',
            'vendor_confirmation_token' => null,
            'assigned_provider_id' => null // Optional: clear it? Or keep history? Better keep null if we want to reassign.
        ]);

        // Notify old provider if they were pending or accepted
        if (in_array($oldStatus, ['pending', 'accepted']) && $providerId) {
            $provider = \App\Models\Provider::find($providerId);
            if ($provider && $provider->email) {
                Mail::to($provider->email)->send(new AdminCancellationNotification($item)); // Passing item with NULL provider might be issue if template relies on it. 
                // Actually template relies on item details.
            }
        }

        return redirect()->back()->with('success', 'Assignment cancelled. Provider notified.');
    }
}

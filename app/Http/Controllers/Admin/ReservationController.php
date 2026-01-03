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

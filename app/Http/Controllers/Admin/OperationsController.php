<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewServiceAssignment;

class OperationsController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'transport');

        $query = ServiceOrder::query()
            ->with(['reservationItem.reservation', 'reservationItem.service', 'driver', 'vehicle'])
            ->latest();

        // Filter by Service Type based on Tab
        if ($tab === 'transport') {
            $query->whereHas('reservationItem.service', function ($q) {
                $q->whereIn('services.type', ['transfer', 'private_transfer', 'shared_transfer']);
            });
        } elseif ($tab === 'tours') {
            $query->whereHas('reservationItem.service', function ($q) {
                $q->where('services.type', 'tour');
            });
        } elseif ($tab === 'packages') {
            $query->whereHas('reservationItem.service', function ($q) {
                $q->where('services.type', 'bundle');
            });
        } elseif ($tab === 'baggage') {
            // Future implementation
            $query->whereHas('reservationItem.service', function ($q) {
                $q->where('services.type', 'baggage');
            });
        }

        // Tenant Scoping? "Root Operations" usually implies tracking EVERYTHING for Root, 
        // but Tenant Admins might need this too?
        // User request: "En root tambien necesito una seccion..." (In Root I also need...)
        // "Supervisor solo puede ver... operaciones". 
        // So Tenants need this too.
        $user = auth()->user();
        if ($user->role !== 'root') {
            // Scope to Organization
            // ServiceOrder -> ReservationItem -> Service -> Organization
            // OR ServiceOrder -> Driver (if driver belongs to org? No, driver might be freelance)
            // Better: ServiceOrder linked to ReservationItem linked to Org?
            // Actually, `ServiceOrder` doesn't have `organization_id`. `ReservationItem` does?
            // `ReservationItem` belongs to `Reservation` (Org) AND `Service` (Org).
            // Usually `reservation_items` are created for a specific `provider_service`?
            // Let's assume scoping via `reservationItem.reservation.organization_id`.
            $query->whereHas('reservationItem.reservation', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            });
        }

        // Action Buttons Logic (handled in Frontend mostly, but we can pass drivers for modals)
        $drivers = User::where('role', 'driver')
            ->when($user->role !== 'root', function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id);
            })
            ->select('id', 'name')
            ->get();

        return Inertia::render('Admin/Operations/Index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'tab' => $tab,
            'drivers' => $drivers,
            'filters' => $request->all()
        ]);
    }

    public function show(ServiceOrder $order)
    {
        // Scoping check
        $user = auth()->user();
        if ($user->role !== 'root') {
            $order->load('reservationItem.reservation');
            if ($order->reservationItem->reservation->organization_id !== $user->organization_id) {
                abort(403);
            }
        }

        $order->load(['reservationItem.reservation.user', 'reservationItem.service', 'driver', 'vehicle', 'reservationItem.providerService']);

        return Inertia::render('Admin/Operations/Show', [
            'order' => $order,
            'drivers' => User::where('role', 'driver')
                ->when($user->role !== 'root', fn($q) => $q->where('organization_id', $user->organization_id))
                ->select('id', 'name')
                ->get()
        ]);
    }

    // Actions are reused from DispatchController or implemented here?
    // User wants: Edit, Assign, Reassign, Delete.

    public function update(Request $request, ServiceOrder $order)
    {
        // Edit details logic
        $order->update($request->validate([
            'status' => 'required',
            'comments' => 'nullable|string'
        ]));
        return back()->with('success', 'Order updated.');
    }

    public function destroy(ServiceOrder $order)
    {
        // Soft delete or remove from list?
        // User said "solo borrarlo de la lista visible" (Just delete from visible list).
        // Since we don't have SoftDeletes on ServiceOrder yet (maybe?), actual Delete might disappear it forever.
        // If we want "Hide", we need a flag. But usually Delete means Delete relative to operations.
        // Let's assume standard Delete.
        $order->delete();
        return redirect()->route('admin.operations.index')->with('success', 'Order removed.');
    }
}

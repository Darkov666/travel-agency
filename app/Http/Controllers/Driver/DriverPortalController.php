<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DriverPortalController extends Controller
{
    /**
     * Dashboard: List assigned orders.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        $query = ServiceOrder::query()
            ->with(['reservationItem.reservation', 'vehicle'])
            ->where('driver_id', $user->id)
            ->orderByRaw("FIELD(status, 'assigned', 'accepted', 'en_route_base', 'at_pickup', 'on_board', 'finished', 'cancelled', 'rejected')");

        // Optional: Filter by status tab (Active vs History)
        if ($request->has('tab') && $request->tab === 'history') {
            $query->whereIn('status', ['finished', 'cancelled', 'rejected']);
        } else {
            // Default: Active or Pending Acceptance
            $query->whereNotIn('status', ['finished', 'cancelled', 'rejected']);
        }

        $orders = $query->paginate(10)->withQueryString();

        return Inertia::render('Driver/Dashboard', [
            'orders' => $orders,
            'tab' => $request->tab ?? 'active'
        ]);
    }

    /**
     * Show Order Details.
     * Accessible by: Assigned Driver (even if rejected), Admin, Root.
     */
    public function show(ServiceOrder $order)
    {
        $user = Auth::user();

        // Authorization Check
        if ($user->role !== 'driver') {
            // Let Admins pass (assuming middleware handles basic auth)
            if (!$user->isPlatformAdmin() && !$user->isOrgAdmin()) {
                abort(403, 'Unauthorized access to this service order.');
            }
        } else {
            // Driver Check
            if ($order->driver_id !== $user->id) {
                // Determine if we should show if it WAS assigned? 
                // For now, simple check: Strict filtering usually safer.
                // If "rejected", driver_id might still be set if we don't clear it.
                // DispatchController logic: unassign clears it. Reject logic needs to decide.
                // If reject keeps driver_id but changes status, this works.
                abort(403);
            }
        }

        $order->load(['reservationItem.reservation', 'vehicle', 'reservationItem.providerService']);

        return Inertia::render('Driver/ServiceOrder/Show', [
            'order' => $order,
            'is_driver' => $user->role === 'driver'
        ]);
    }

    /**
     * Accept the assignment.
     */
    public function accept(ServiceOrder $order)
    {
        $this->authorizeDriver($order);

        if ($order->status !== 'assigned') {
            return back()->with('error', 'Order cannot be accepted at this stage.');
        }

        $order->update([
            'status' => 'accepted',
            'checkpoints' => $this->appendCheckpoint($order, 'accepted_at')
        ]);

        // Notify Admin?

        return back()->with('success', 'Order accepted successfully.');
    }

    /**
     * Reject the assignment.
     */
    public function reject(ServiceOrder $order)
    {
        $this->authorizeDriver($order);

        if ($order->status !== 'assigned') {
            return back()->with('error', 'Order cannot be rejected at this stage.');
        }

        $order->update([
            'status' => 'rejected',
            'checkpoints' => $this->appendCheckpoint($order, 'rejected_at')
        ]);

        // Notify Admin (crucial for re-assignment)

        return to_route('driver.dashboard')->with('success', 'Order rejected.');
    }

    /**
     * Update Status / Checkpoints (Start Shift, Arrived, etc.)
     */
    public function updateStatus(Request $request, ServiceOrder $order)
    {
        $this->authorizeDriver($order);

        $request->validate([
            'status' => 'required|in:en_route_base,at_pickup,on_board,finished',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $status = $request->status;
        $checkpointKey = $status . '_at'; // e.g. en_route_base_at

        $updates = [
            'status' => $status,
            'checkpoints' => $this->appendCheckpoint($order, $checkpointKey),
        ];

        if ($request->lat && $request->lng) {
            $updates['current_lat'] = $request->lat;
            $updates['current_lng'] = $request->lng;

            // Broadcast Location Update
            // We use a temporary instance or update first then broadcast
        }

        $order->update($updates);

        if ($request->lat && $request->lng) {
            broadcast(new \App\Events\LocationUpdated($order, $request->lat, $request->lng));
        }

        // Broadcast Status Update
        broadcast(new \App\Events\ServiceOrderUpdated($order));

        // Notify Passenger
        if (in_array($status, ['en_route_base', 'at_pickup', 'on_board', 'finished'])) {
            // Ensure relationships are loaded
            $order->load('reservationItem.reservation');
            $email = $order->reservationItem->reservation->email ?? $order->reservationItem->reservation->contact_email ?? null;

            if ($email) {
                \Illuminate\Support\Facades\Notification::route('mail', $email)
                    ->notify(new \App\Notifications\ServiceStatusNotification($order, $status));
            }
        }

        return back()->with('success', 'Status updated to ' . $status);
    }

    private function authorizeDriver($order)
    {
        if (Auth::id() !== $order->driver_id) {
            abort(403, 'Not your order.');
        }
    }

    private function appendCheckpoint($order, $key)
    {
        $checkpoints = $order->checkpoints ?? [];
        $checkpoints[$key] = now()->toDateTimeString();
        return $checkpoints;
    }
}

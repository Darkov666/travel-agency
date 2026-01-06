<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Notifications\NewServiceAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    /**
     * Display a listing of unassigned or pending service orders.
     */
    public function index(Request $request)
    {
        $query = ServiceOrder::query()
            ->with(['reservationItem.reservation', 'reservationItem.providerService', 'driver', 'vehicle'])
            ->whereNotIn('status', ['finished', 'cancelled']);

        // Filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereHas('reservationItem', function ($q) use ($request) {
                $q->whereDate('date', $request->date);
            });
        }

        if ($request->has('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Get Drivers for Dropdown
        // If Root: All drivers. If Org Admin: Drivers of that Org.
        $driversQuery = User::where('role', 'driver');
        // if (!auth()->user()->isRoot()) { 
        //    $driversQuery->where('organization_id', auth()->user()->organization_id); 
        // }
        // Scope to providers? For now assuming central dispatch or simple org match.

        $drivers = $driversQuery->select('id', 'name', 'email')->get();

        return Inertia::render('Admin/Dispatch/Index', [
            'orders' => $orders,
            'drivers' => $drivers,
            'filters' => $request->only(['status', 'date', 'driver_id'])
        ]);
    }

    /**
     * Assign a driver to a service order.
     */
    public function assign(Request $request, ServiceOrder $order)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $previousDriverId = $order->driver_id;

        $order->update([
            'driver_id' => $request->driver_id,
            'vehicle_id' => $request->vehicle_id,
            'status' => 'assigned',
            // Reset checkpoints if needed? Or just log assignment time
        ]);

        // Send Notification if driver changed or new assignment
        if ($previousDriverId !== $request->driver_id) {
            $driver = User::find($request->driver_id);
            if ($driver) {
                $driver->notify(new NewServiceAssignment($order));
            }
        }

        return back()->with('success', 'Driver assigned successfully.');
    }

    /**
     * Unassign a driver.
     */
    public function unassign(ServiceOrder $order)
    {
        $order->update([
            'driver_id' => null,
            'vehicle_id' => null,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Driver unassigned.');
    }
}

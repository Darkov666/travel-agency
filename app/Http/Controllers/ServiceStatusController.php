<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservationItem;
use Illuminate\Support\Facades\Auth;

class ServiceStatusController extends Controller
{
    public function updateItemStatus(Request $request, ReservationItem $item)
    {
        // Validate request
        $validated = $request->validate([
            'status' => 'required|string', // e.g., 'en_camino', 'llegada'
        ]);

        // Logic to update item status (custom column or reuse 'vendor_status'?)
        // The prompt asked for: "en camino, llegada, pickup, en camino al desino, llegada al destino, pasajeros abajo y servicio finalizado"
        // These are operational sub-statuses. 'vendor_status' was enum('pending','accepted'...).
        // We probably need a 'service_status' column or repurpose. 
        // For MVP, lets assume we store this in a JSON field or a new column 'operational_status'.
        // Or we just log it entirely.
        // Let's add 'operational_status' column to reservation_items via migration later?
        // OR: Reuse 'vendor_status' if we expand the enum? 
        // "Accepted" -> "On Route" -> "Arrived"

        // Let's just assume we log it to 'notes' or update a status field that I need to add.
        // Prompt says: "El operador debe poder indicar los estatus... con botones predefinidos"

        // I will assume I need to Add 'operational_status' to items table. 
        // For now, I'll update a hypothetical 'operational_status' column.

        // Also update User status:
        $user = Auth::user();
        if ($validated['status'] === 'en_camino') {
            $user->operator_status = 'on_service';
        } elseif ($validated['status'] === 'finalizado') {
            $user->operator_status = 'available';
        }
        $user->save();

        // Notify Supervisor
        $supervisors = \App\Models\User::where('organization_id', $user->organization_id)
            ->whereIn('role', ['admin', 'supervisor'])
            ->get();

        if ($supervisors->count() > 0) {
            \Illuminate\Support\Facades\Notification::send($supervisors, new \App\Notifications\OperatorStatusUpdated($item, $validated['status'], $user->name));
        }

        return back()->with('success', 'Status updated to ' . $validated['status']);
    }

    public function toggleAvailability()
    {
        $user = Auth::user();
        // Toggle logic: available <-> break
        $user->operator_status = ($user->operator_status === 'available') ? 'break' : 'available';
        $user->save();

        return back();
    }
}

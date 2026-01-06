<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackingController extends Controller
{
    /**
     * Show the public tracking page for a service order.
     */
    public function show(ServiceOrder $serviceOrder)
    {
        // Optional: Security check (using hash or ensuring order is active)
        // For now, allowing public access via ID if status is not 'pending' or 'cancelled'
        if (in_array($serviceOrder->status, ['pending', 'cancelled'])) {
            // Maybe show a generic "Trip not active" page
        }

        $serviceOrder->load(['reservationItem', 'vehicle', 'driver']);

        return Inertia::render('Tracking/Show', [
            'order' => $serviceOrder,
            'pusher_key' => config('broadcasting.connections.reverb.key'),
            'pusher_cluster' => config('broadcasting.connections.reverb.options.cluster'),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ReservationItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function confirm($token)
    {
        $item = ReservationItem::where('vendor_confirmation_token', $token)->firstOrFail();

        if ($item->vendor_status === 'accepted') {
            return Inertia::render('Vendor/Status', [
                'status' => 'already_accepted',
                'item' => $item
            ]);
        }

        // Update status
        $item->update([
            'vendor_status' => 'accepted',
            'vendor_confirmed_at' => Carbon::now()
        ]);

        // Send Notification to Admin (Root)
        \Illuminate\Support\Facades\Mail::to('root@example.com')->send(new \App\Mail\VendorAcceptedNotification($item));

        return Inertia::render('Vendor/Status', [
            'status' => 'success',
            'item' => $item
        ]);
    }
}

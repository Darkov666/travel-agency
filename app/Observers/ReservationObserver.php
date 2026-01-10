<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Models\ServiceOrder;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        // Check if status changed to confirmed (or paid) and orders don't exist yet
        if (
            $reservation->isDirty('status') &&
            in_array($reservation->status, ['confirmed', 'completed'])
        ) {

            foreach ($reservation->items as $item) {
                // Only create for transfer items, skip if already exists
                // We should add a check for service type if we had strict types, 
                // but assuming all items need execution for now or filter by 'transfer' keyword in service name?
                // Better: check if item->serviceOrder exists.

                if (!$item->serviceOrder) {
                    ServiceOrder::create([
                        'reservation_item_id' => $item->id,
                        'folio' => $reservation->booking_ref,
                        'item_reference' => 'ITM-' . str_pad($item->id, 5, '0', STR_PAD_LEFT), // Format: ITM-00001
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Reservation "deleted" event.
     */
    public function deleted(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "restored" event.
     */
    public function restored(Reservation $reservation): void
    {
        //
    }

    /**
     * Handle the Reservation "force deleted" event.
     */
    public function forceDeleted(Reservation $reservation): void
    {
        //
    }
}

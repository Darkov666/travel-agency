<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $fillable = [
        'reservation_id',
        'provider_service_id',
        'service_name',
        'provider_name',
        'zone_name',
        'quantity',
        'units',
        'pax',
        'passengers_data', // Updated
        'airline', // New
        'arrival_flight_number',
        'arrival_time',
        'arrival_terminal',
        'departure_airline',
        'departure_flight_number',
        'departure_time',
        'departure_terminal',
        'flight_type',
        'pickup_time',
        'date',
        'time',
        'return_date',
        'return_time',
        'holder_name',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'passengers_data' => 'array',
        'date' => 'date',
        'return_date' => 'date',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }
}

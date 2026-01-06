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
        'zone_id',
        'zone_name',
        'quantity',
        'units',
        'pax',
        'adults',
        'children',
        'infants',
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
        'total_price', // This seems to be used as total price
        'total', // Adding this as requested by error
        'cost', // Adding this as requested by error
        'vendor_status',
        'assigned_provider_id',
        'operational_status',
        'organization_id',
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

    public function assignedProvider()
    {
        return $this->belongsTo(Provider::class, 'assigned_provider_id');
    }

    public function serviceOrder()
    {
        return $this->hasOne(ServiceOrder::class);
    }
}

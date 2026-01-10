<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_item_id',
        'driver_id',
        'vehicle_id',
        'folio',
        'item_reference', // Copied from ReservationItem (Service Control Ref)
        'status',
        'current_lat',
        'current_lng',
        'checkpoints',
        'comments'
    ];

    protected $casts = [
        'checkpoints' => 'array',
        'current_lat' => 'decimal:8',
        'current_lng' => 'decimal:8',
    ];

    public function reservationItem()
    {
        return $this->belongsTo(ReservationItem::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}

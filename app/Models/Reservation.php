<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'booking_ref',
        'user_id',
        'contact_name',
        'contact_surname',
        'contact_email',
        'contact_phone',
        'contact_nationality',
        'total_amount',
        'amount_paid',
        'balance_due',
        'payment_method',
        'payment_choice', // Added
        'payment_status',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }
}

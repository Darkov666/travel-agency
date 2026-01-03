<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory, BelongsToOrganization;

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
        'organization_id',
        'subtotal',
        'tax',
        'total',
        'currency'
    ];

    public function items()
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}

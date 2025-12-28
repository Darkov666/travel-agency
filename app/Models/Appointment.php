<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'folio',
        'package_id',
        'scheduled_at',
        'end_time',
        'status',
        'payment_method',
        'payment_status',
        'google_event_id',
        'notes',
        'stripe_payment_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'token',
        'expires_at',
        'participants_data',
        'session_type',
        'organization_type',
        'organization_other',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

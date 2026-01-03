<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'settings',
        'is_active',
        'razon_social',
        'commercial_name',
        'rfc',
        'regimen_fiscal',
        'fiscal_address',
        'company_creation_date',
        'representative_name',
        'representative_curp',
        'representative_phone',
        'representative_email',
        'legal_docs',
        'hosting_mode',
        'custom_domain',
        'stripe_connect_id',
        'subscription_status',
        'last_payment_date',
        'next_payment_date',
        'monthly_fee',
        'commission_rate'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'legal_docs' => 'array',
        'company_creation_date' => 'date',
        'last_payment_date' => 'date',
        'next_payment_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'commission_rate' => 'decimal:2',
    ];

    public function isSuspended(): bool
    {
        return $this->subscription_status === 'suspended' || $this->subscription_status === 'cancelled';
    }

    public function isInGracePeriod(): bool
    {
        return $this->subscription_status === 'grace_period';
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

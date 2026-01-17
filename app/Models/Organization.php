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
        'commission_rate',
        'exchange_rate',
        'stripe_key',
        'stripe_secret',
        'stripe_webhook_secret',
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
        'exchange_rate' => 'decimal:4',
        'stripe_secret' => 'encrypted',
        'stripe_webhook_secret' => 'encrypted',
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

    public function providers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Provider::class); // Owned Providers
    }

    public function assignedProviders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'organization_provider')
            ->withPivot('is_active', 'commission_rate')
            ->withTimestamps();
    }

    /**
     * Check if organization has a specific module enabled.
     * Default modules: transport, tours, shop
     */
    public function hasModule(string $module): bool
    {
        // Null settings implies all enabled? Or none?
        // Let's assume if 'modules' key is missing, it's a legacy or default root -> All enabled (or restricted, better default to strict).
        // User request: "Cancun Sunny" (Transport Only).
        // Strategy: If 'modules' array exists, check against it. If not, default to ALL (for existing Root) OR strict?
        // Let's default to ['transport', 'tours', 'shop'] if not set, to avoid breaking existing.
        // BUT for "Cancun Sunny" we will explicitly set it.
        $modules = $this->settings['modules'] ?? ['transport', 'tours', 'shop'];
        return in_array($module, $modules);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Provider extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'name',
        'is_inhouse',
        'is_default',
        'is_active',
        // New fields
        'partner_id',
        'contact_name',
        'full_address',
        'email',
        'phone',
        'taxpayer_type',
        'logo_path',
        'tax_compliance_path',
        'provider_type',
        'priority',
        'organization_id',
    ];

    protected $casts = [
        'is_inhouse' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function assignedOrganizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_provider')
            ->withPivot('is_active', 'commission_rate')
            ->withTimestamps();
    }
}

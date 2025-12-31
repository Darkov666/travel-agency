<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

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

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
}

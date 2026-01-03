<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'name',
        'kml_path',
        'coordinates',
        // Saas
        'organization_id',
        'transfer_time', // Added in previous task
        'transfer_time_minutes',
        'priority',
        'color',
    ];

    protected $casts = [
        'priority' => 'integer',
        'coordinates' => 'array',
    ];

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
}

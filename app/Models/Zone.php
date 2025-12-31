<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'coordinates',
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

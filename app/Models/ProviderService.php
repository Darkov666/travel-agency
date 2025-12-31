<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'zone_id',
        'service_id',
        'name',
        'description',
        'cost_net',
        'price_public',
        'max_pax',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cost_net' => 'decimal:2',
        'price_public' => 'decimal:2',
        'max_pax' => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}

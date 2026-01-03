<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Vehicle extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $fillable = [
        'provider_id',
        'model_name',
        'type',
        'max_pax',
        'category',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_pax' => 'integer',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}

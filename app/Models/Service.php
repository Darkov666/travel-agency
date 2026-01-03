<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'slug',
        'description',
        'price',
        'price_mxn',
        'price_usd',
        'duration_minutes',
        'type',
        'is_active',
        'image',
        'features',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'price_mxn' => 'decimal:2',
        'features' => 'array',
    ];

    protected $appends = ['requires_scheduling', 'downloadable'];

    public function getRequiresSchedulingAttribute()
    {
        // Types that require booking a time slot
        return in_array($this->type, ['individual', 'couple', 'family', 'special', 'group']);
    }

    public function getDownloadableAttribute()
    {
        // Types that are digital products
        return in_array($this->type, ['ebook', 'manual', 'video', 'audio']);
    }
}

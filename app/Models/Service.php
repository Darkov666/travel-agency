<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'price_mxn',
        'price_usd',
        'duration_minutes',
        'type',
        'is_active',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'price_mxn' => 'decimal:2',
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

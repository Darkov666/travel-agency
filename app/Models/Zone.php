<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'coordinates',
        'priority',
        'color',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];
}

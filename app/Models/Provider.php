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
    ];

    protected $casts = [
        'is_inhouse' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
}

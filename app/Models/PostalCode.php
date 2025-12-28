<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'municipality',
        'city',
        'state',
        'settlement',
        'settlement_type',
        'zone',
    ];
}

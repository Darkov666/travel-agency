<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'provider_service_id',
        'quantity',
        'pax',
        'date',
        'return_date',
        'units',
        'price'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        // Shop items are services with type 'merchandise' or 'package' maybe?
        // User said: "Shop Page ... same for shop section".
        // In Shop.vue causing mock data. We should use DB now.
        // Assuming 'merchandise' type exists or we use specific types.

        return Inertia::render('Shop', [
            'products' => Service::where('is_active', true)
                ->whereIn('type', ['merchandise', 'package'])
                ->get()
        ]);
    }

    public function show($id)
    {
        $product = Service::findOrFail($id);

        return Inertia::render('Shop/Show', [
            'product' => $product,
            'relatedProducts' => Service::where('type', $product->type)
                ->where('id', '!=', $product->id)
                ->take(3)
                ->get()
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('Services', [
            'services' => Service::where('is_active', true)
                ->whereIn('type', ['transfer', 'tour', 'private'])
                ->get()
        ]);
    }

    public function show($slug) // Assuming slug or ID
    {
        // For now using ID or Slug. Let's assume ID if slug not ready, but slug is better for SEO.
        // Migration check needed? Service model has slug?
        // Checking Service model...
        $service = Service::where('id', $slug)->orWhere('slug', $slug)->firstOrFail();

        return Inertia::render('Services/Show', [
            'service' => $service,
            'relatedServices' => Service::where('type', $service->type)
                ->where('id', '!=', $service->id)
                ->take(3)
                ->get()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\ProviderService;
use App\Models\ChangeRequest;

class ProviderPortalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->provider_id) {
            abort(403, 'User is not linked to a provider.');
        }

        $services = ProviderService::where('provider_id', $user->provider_id)
            ->with(['service', 'zone'])
            ->get();

        return Inertia::render('Provider/Services/Index', [
            'services' => $services
        ]);
    }

    public function edit(ProviderService $providerService)
    {
        $user = Auth::user();
        if ($providerService->provider_id !== $user->provider_id) {
            abort(403);
        }

        return Inertia::render('Provider/Services/Edit', [
            'service' => $providerService->load(['service', 'zone'])
        ]);
    }

    public function update(Request $request, ProviderService $providerService)
    {
        $user = Auth::user();
        if ($providerService->provider_id !== $user->provider_id) {
            abort(403);
        }

        $validated = $request->validate([
            'price_public' => 'required|numeric',
            'cost_net' => 'required|numeric',
            'max_pax' => 'required|integer',
            // Add other fields as needed
        ]);

        // Check if there is already a pending request?
        // Ideally yes, but simplify: Just create new request.

        ChangeRequest::create([
            'organization_id' => $user->organization_id,
            'user_id' => $user->id,
            'model_type' => ProviderService::class,
            'model_id' => $providerService->id,
            'request_type' => 'update',
            'payload' => $validated,
            'status' => 'pending'
        ]);

        return redirect()->route('provider.services.index')
            ->with('success', 'Changes submitted for approval.');
    }
}

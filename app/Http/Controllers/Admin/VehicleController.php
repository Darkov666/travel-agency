<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Vehicle Store Request:', $request->all());

        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'model_name' => 'required|string',
            'type' => 'required|in:van,suv,bus,boat,catamaran',
            'max_pax' => 'required|integer|min:1',
            'category' => 'required|in:standard,vip',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('vehicles', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['image_path'] = $path;
        }

        Vehicle::create($validated);

        return back()->with('success', 'Vehicle added.');
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'model_name' => 'required|string',
            'type' => 'required|in:van,suv,bus,boat,catamaran',
            'max_pax' => 'required|integer|min:1',
            'category' => 'required|in:standard,vip',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($vehicle->image_path) {
                \Illuminate\Support\Facades\Storage::disk(env('PUBLIC_FILESYSTEM_DISK', 'public'))->delete($vehicle->image_path);
            }
            $path = $request->file('image')->store('vehicles', env('PUBLIC_FILESYSTEM_DISK', 'public'));
            $validated['image_path'] = $path;
        }

        $vehicle->update($validated);

        return back()->with('success', 'Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'Vehicle removed.');
    }
}

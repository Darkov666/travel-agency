<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class StaffController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = User::where('organization_id', $user->organization_id)
            ->whereIn('role', ['driver', 'supervisor', 'admin'])
            ->where('id', '!=', $user->id); // Don't show self? Or show but disable delete?

        $staff = $query->latest()->get();

        return Inertia::render('Admin/Staff/Index', [
            'staff' => $staff
        ]);
    }

    public function create()
    {
        // Modal usually, but let's stick to Index with Modal or separate page if stuck.
        // We'll use Index with Modal in Vue.
        return redirect()->route('admin.staff.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:driver,supervisor,admin',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'organization_id' => auth()->user()->organization_id,
        ]);

        return redirect()->back()->with('success', 'Staff member created successfully.');
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $staff->id,
            'role' => 'required|in:driver,supervisor,admin',
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->role = $request->role;

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        return redirect()->back()->with('success', 'Staff member updated.');
    }

    public function destroy(User $staff)
    {
        if ($staff->organization_id !== auth()->user()->organization_id) {
            abort(403);
        }

        $staff->delete();

        return redirect()->back()->with('success', 'Staff member removed.');
    }
}

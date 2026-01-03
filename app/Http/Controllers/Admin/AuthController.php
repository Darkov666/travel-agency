<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Admin/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Check credentials manually first to send 2FA
        if (Auth::validate($credentials)) {
            $user = User::where('email', $request->email)->first();

            // Role check (Modify as needed for your roles)
            if (!in_array($user->role, ['root', 'admin', 'supervisor', 'agent'])) {
                // If using 'admin' enum from old migration, check that.
                if ($user->role !== 'admin') {
                    return back()->withErrors(['email' => 'Access denied.']);
                }
            }

            // Generate 2FA Code
            $code = rand(100000, 999999);
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            // Send Email (Using generic message for now, create Mailable later)
            // Just logging for dev if Mailpit not checked immediately, but sending properly
            Mail::raw("Your Admin Login Code: $code", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Two-Factor Authentication Code');
            });

            return response()->json(['two_factor' => true]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'The provided credentials do not match our records.'], 422);
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'code' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->two_factor_code === $request->code && $user->two_factor_expires_at->gt(now())) {

            Auth::login($user);

            // Reset 2FA
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();

            // \App\Models\ActionLog::log('login', 'User logged in via 2FA');

            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

    public function logout(Request $request)
    {
        // \App\Models\ActionLog::log('logout', 'User logged out');
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}

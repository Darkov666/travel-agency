<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cartCount = 0;
        $user = $request->user();

        if ($user) {
            $cartCount = \App\Models\Cart::where('user_id', $user->id)->first()?->items()->sum('quantity') ?? 0;
        } else {
            $sessionId = $request->session()->getId();
            $cartCount = \App\Models\Cart::where('session_id', $sessionId)->first()?->items()->sum('quantity') ?? 0;
        }

        return [
            ...parent::share($request),
            'cartCount' => $cartCount,
            'locale' => app()->getLocale(),
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ];
    }
}

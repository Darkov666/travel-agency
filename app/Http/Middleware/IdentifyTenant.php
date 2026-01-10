<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $mainHost = parse_url(config('app.url'), PHP_URL_HOST);

        // If we are on the main domain, just proceed
        // Allow Local IP access for testing (IPv4 private ranges)
        if ($host === $mainHost || filter_var($host, FILTER_VALIDATE_IP)) {
            return $next($request);
        }

        // Try to identify tenant
        $organization = null;

        // 1. Check if Subdomain
        if (str_ends_with($host, '.' . $mainHost)) {
            $slug = str_replace('.' . $mainHost, '', $host);
            $organization = \App\Models\Organization::where('slug', $slug)
                ->where('hosting_mode', 'subdomain')
                ->first();
        }
        // 2. Check Custom Domain
        else {
            $organization = \App\Models\Organization::where('custom_domain', $host)
                ->where('hosting_mode', 'domain')
                ->first();
        }

        if (!$organization) {
            abort(404); // Tenant not found
        }

        if ($organization->isSuspended() || !$organization->is_active) {
            // "y después de dos meses se cancela la cuenta definitivamente" -> Assuming handled by Cron deletion or soft delete
            // "deja de estar en línea dicha página" -> Yes, show error
            abort(403, 'This organization account is suspended.');
        }

        // Bind to App Container
        app()->instance('tenant', $organization);

        // Optional: Share with Inertia - Handled by HandleInertiaRequests
        // \Inertia\Inertia::share('tenant', $organization);

        return $next($request);
    }
}

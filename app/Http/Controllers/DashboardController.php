<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Models\ReservationItem;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'root') {
            return $this->rootDashboard();
        }

        if (in_array($user->role, ['admin', 'supervisor'])) {
            return $this->adminDashboard($user);
        }

        if ($user->role === 'operator') {
            return $this->operatorDashboard($user);
        }

        // Client Dashboard (Default)
        return Inertia::render('Dashboard', [
            'appointments' => [] // $user->appointments logic here if needed
        ]);
    }

    protected function rootDashboard()
    {
        return Inertia::render('Admin/Dashboards/Root', [
            'organizations' => Organization::withCount('users')->latest()->get(),
            'stats' => [
                'total_orgs' => Organization::count(),
                'active_orgs' => Organization::where('is_active', true)->count(),
                'suspended_orgs' => Organization::where('subscription_status', 'suspended')->count(),
                'total_users' => \App\Models\User::count(),
                'total_reservations' => \App\Models\Reservation::count(),
                'total_revenue' => \App\Models\Reservation::sum('total_amount'),
                'total_commission' => \App\Models\Reservation::sum('total_amount') * 0.10, // Approx 10% commission
                'pending_changes' => \App\Models\ChangeRequest::where('status', 'pending')->count(),
            ]
        ]);
    }

    protected function adminDashboard($user)
    {
        // Get reservation counts for next 14 days
        $startDate = now();
        $endDate = now()->addDays(13);

        $chartData = ReservationItem::where('organization_id', $user->organization_id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date->format('M d'),
                    'count' => $item->count,
                    'day' => $item->date->format('D')
                ];
            });

        // Fill in missing dates with 0
        $fullData = [];
        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i);
            $key = $date->format('M d');
            $existing = $chartData->firstWhere('date', $key);

            $fullData[] = [
                'date' => $key,
                'full_date' => $date->format('Y-m-d'),
                'count' => $existing ? $existing['count'] : 0,
                'day' => $date->format('D')
            ];
        }

        return Inertia::render('Admin/Dashboards/Admin', [
            'organization' => $user->organization,
            'stats' => [
                'pending' => ReservationItem::where('organization_id', $user->organization_id)
                    ->where('vendor_status', 'pending')
                    ->count(),
                'today' => ReservationItem::where('organization_id', $user->organization_id)
                    ->whereDate('date', now()->toDateString())
                    ->count(),
                'completed_month' => ReservationItem::where('organization_id', $user->organization_id)
                    ->where('operational_status', 'completed')
                    ->whereMonth('date', now()->month)
                    ->count(),
                'issues' => ReservationItem::where('organization_id', $user->organization_id)
                    ->whereIn('operational_status', ['no_show', 'cancelled'])
                    ->whereMonth('date', now()->month)
                    ->count(),
            ],
            'chartData' => $fullData
        ]);
    }

    protected function operatorDashboard($user)
    {
        return Inertia::render('Admin/Dashboards/Operator', [
            'today_tasks' => ReservationItem::where('organization_id', $user->organization_id)
                ->whereDate('date', now()->toDateString())
                ->get(),
            'status' => $user->operator_status
        ]);
    }
}

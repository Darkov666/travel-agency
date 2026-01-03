<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActionLog;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ActionLogController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ActionLog::with('user')->latest();

        // If Tenant/Admin, modify query
        if ($user->role !== 'root') {
            $query->where('organization_id', $user->organization_id);
        }

        return Inertia::render('Admin/ActionLogs/Index', [
            'logs' => $query->paginate(50)->through(function ($log) {
                return [
                    'id' => $log->id,
                    'user' => $log->user ? $log->user->name : 'System',
                    'action' => $log->action,
                    'description' => $log->description,
                    'ip' => $log->ip_address,
                    'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }
}

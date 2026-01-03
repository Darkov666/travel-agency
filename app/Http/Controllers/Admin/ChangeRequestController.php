<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ChangeRequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ChangeRequest::where('status', 'pending')->with(['user']);

        if ($user->role !== 'root') {
            if (!$user->organization) {
                return Inertia::render('Admin/ChangeRequests/Index', [
                    'requests' => []
                ]);
            }
            $query->where('organization_id', $user->organization->id);
        }

        $requests = $query->latest()
            ->get()
            ->map(function ($req) {
                return [
                    'id' => $req->id,
                    'user_name' => $req->user->name ?? 'Unknown',
                    'organization' => $req->organization->commercial_name ?? 'N/A', // Add this for context
                    'request_type' => $req->request_type,
                    'model_type' => class_basename($req->model_type),
                    'created_at' => $req->created_at->format('Y-m-d H:i'),
                ];
            });

        return Inertia::render('Admin/ChangeRequests/Index', [
            'requests' => $requests
        ]);
    }

    public function show(ChangeRequest $changeRequest)
    {
        // Security check: Same Org
        if ($changeRequest->organization_id !== Auth::user()->organization_id)
            abort(403);

        $changeRequest->load('user');

        // Fetch Current Model Data for Diff
        $currentModel = $changeRequest->subject();

        return Inertia::render('Admin/ChangeRequests/Show', [
            'changeRequest' => $changeRequest,
            'currentData' => $currentModel ? $currentModel->toArray() : null,
        ]);
    }

    public function approve(Request $request, ChangeRequest $changeRequest)
    {
        if ($changeRequest->organization_id !== Auth::user()->organization_id)
            abort(403);

        $changeRequest->update(['status' => 'approved']);

        // Apply Changes
        $model = $changeRequest->subject();
        if ($model) {
            $model->update($changeRequest->payload);
        }

        return redirect()->route('admin.change-requests.index')->with('success', 'Changes approved and applied.');
    }

    public function reject(Request $request, ChangeRequest $changeRequest)
    {
        if ($changeRequest->organization_id !== Auth::user()->organization_id)
            abort(403);

        $validated = $request->validate(['reason' => 'nullable|string']);

        $changeRequest->update([
            'status' => 'rejected',
            'admin_feedback' => $validated['reason'] ?? null
        ]);

        return redirect()->route('admin.change-requests.index')->with('success', 'Request rejected.');
    }
}

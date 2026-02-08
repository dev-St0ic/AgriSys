<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use App\Models\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs - Superadmin only
     */
    public function index(Request $request)
    {
        // Only superadmin can view audit logs
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Only superadmin can access audit logs');
        }

        try {
            $query = Activity::with(['causer'])->latest();

            // Search by description
            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            // Filter by event type (created, updated, deleted)
            if ($request->filled('event')) {
                $query->where('event', $request->event);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->paginate(50);

            return view('admin.activity-logs.index', compact('activities'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show activity details via AJAX
     */
    public function show($id)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $activity = Activity::with('causer')->findOrFail($id);

            // Get user who performed the action
            $user = $activity->causer;
            $userName = 'System';
            $userEmail = 'N/A';
            $userRole = 'System';

            if ($user) {
                $userName = $user->name;
                $userEmail = $user->email;
                $userRole = ucfirst($user->role ?? 'user');
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'date' => $activity->created_at->format('M d, Y h:i A'),
                    'user' => $userName,
                    'email' => $userEmail,
                    'role' => $userRole,
                    'action' => ucfirst($activity->event),
                    'description' => $activity->description,
                    'model' => class_basename($activity->subject_type),
                    'ip' => $activity->properties['ip_address'] ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Not found'], 404);
        }
    }

    /**
     * Export activity logs to CSV - Superadmin only
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Activity::with('causer')->latest();

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['Date', 'User', 'Email', 'Role', 'Action', 'What Changed']);

            foreach ($activities as $activity) {
                $user = $activity->causer;
                $userName = 'System';
                $userEmail = '-';
                $userRole = 'System';

                if ($user) {
                    $userName = $user->name;
                    $userEmail = $user->email;
                    $userRole = ucfirst($user->role ?? 'user');
                }

                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $userName,
                    $userEmail,
                    $userRole,
                    ucfirst($activity->event),
                    $activity->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
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
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        try {
            $query = Activity::with(['causer'])->latest();

            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            if ($request->filled('event')) {
                $query->where('event', $request->event);
            }

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
     * Show activity details
     */
    public function show($id)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $activity = Activity::with('causer')->findOrFail($id);

            // Determine who actually did the action
            $user = $activity->causer;
            $userName = 'System';
            $userEmail = 'N/A';

            // For self-service actions on UserRegistration, get the user from the subject
            if (!$user && $activity->subject_type === 'App\Models\UserRegistration' && $activity->subject_id) {
                $userRegistration = UserRegistration::find($activity->subject_id);
                if ($userRegistration) {
                    $userName = $userRegistration->full_name ?? $userRegistration->username;
                    $userEmail = $userRegistration->username; // Show username since email might not be verified
                }
            } elseif ($user) {
                $userName = $user->name;
                $userEmail = $user->email;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'event' => ucfirst($activity->event),
                    'date' => $activity->created_at->format('M d, Y h:i A'),
                    'user' => $userName,
                    'email' => $userEmail,
                    'description' => $activity->description,
                    'subject_type' => $activity->subject_type,
                    'subject_id' => $activity->subject_id,
                    'properties' => $activity->properties,
                    'ip' => $activity->properties['ip_address'] ?? 'N/A'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Not found'], 404);
        }
    }

    /**
     * Export to CSV
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Activity::with('causer')->latest();

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
            
            fputcsv($file, ['Date', 'User', 'Email', 'Action', 'Description']);

            foreach ($activities as $activity) {
                // Determine actual user
                $user = $activity->causer;
                $userName = 'System';
                $userEmail = '-';

                // For self-service actions on UserRegistration
                if (!$user && $activity->subject_type === 'App\Models\UserRegistration' && $activity->subject_id) {
                    $userRegistration = UserRegistration::find($activity->subject_id);
                    if ($userRegistration) {
                        $userName = $userRegistration->full_name ?? $userRegistration->username;
                        $userEmail = $userRegistration->username;
                    }
                } elseif ($user) {
                    $userName = $user->name;
                    $userEmail = $user->email;
                }

                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $userName,
                    $userEmail,
                    ucfirst($activity->event),
                    $activity->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
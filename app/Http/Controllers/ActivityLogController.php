<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
   public function index(Request $request)
{
    try {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activities = $query->paginate(50);

        // Get unique subject types for filter
        $subjectTypes = Activity::distinct()
            ->pluck('subject_type')
            ->filter()
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            });

        return view('admin.activity-logs.index', compact('activities', 'subjectTypes'));
        
    } catch (\Exception $e) {
        return back()->with('error', 'Error loading activity logs: ' . $e->getMessage());
    }
}

    /**
     * Show specific log details
     */
    public function show($id)
    {
        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
        
        return view('admin.activity-logs.show', compact('activity'));
    }

    /**
     * Get logs for a specific model
     */
    public function forModel($modelType, $modelId)
    {
        $activities = Activity::where('subject_type', $modelType)
            ->where('subject_id', $modelId)
            ->with(['causer'])
            ->latest()
            ->paginate(20);

        return view('admin.activity-logs.model-logs', compact('activities'));
    }

    /**
     * Get logs by specific user
     */
    public function byUser($userId)
    {
        $activities = Activity::where('causer_id', $userId)
            ->with(['subject'])
            ->latest()
            ->paginate(50);

        return view('admin.activity-logs.user-logs', compact('activities'));
    }

    /**
     * Clear old logs (optional - be careful!)
     */
    public function clearOld(Request $request)
    {
        $days = $request->input('days', 90);
        
        $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success', "Deleted {$deleted} old activity logs");
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = 'activity-logs-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['ID', 'Date/Time', 'User', 'Action', 'Model', 'Changes']);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->causer ? $activity->causer->name : 'System',
                    $activity->description,
                    class_basename($activity->subject_type),
                    json_encode($activity->properties)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
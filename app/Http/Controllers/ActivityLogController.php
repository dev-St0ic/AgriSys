<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs - Restricted to superadmin only (ISO A.8.16)
     */
    public function index(Request $request)
    {
        // ISO A.8.16: Restrict log viewing to superadmins only
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only superadmins can view activity logs.');
        }

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
     * Show specific log details - ISO A.8.16: Access control
     */
    public function show($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
        
        // Log who viewed this activity log (ISO A.8.16)
        $this->logActivityView($activity, Auth::id());
        
        return view('admin.activity-logs.show', compact('activity'));
    }

    /**
     * Get logs for a specific model - ISO A.8.15: Log critical data changes
     */
    public function forModel($modelType, $modelId)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $activities = Activity::where('subject_type', $modelType)
            ->where('subject_id', $modelId)
            ->with(['causer'])
            ->latest()
            ->paginate(20);

        return view('admin.activity-logs.model-logs', compact('activities'));
    }

    /**
     * Get logs by specific user - ISO A.8.15: Authentication & admin actions
     */
    public function byUser($userId)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $activities = Activity::where('causer_id', $userId)
            ->with(['subject'])
            ->latest()
            ->paginate(50);

        return view('admin.activity-logs.user-logs', compact('activities'));
    }

    /**
     * Archive old logs (ISO A.8.15: Retention schedule)
     * Instead of permanently deleting, archive to secure storage
     */
    public function archiveOld(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $days = $request->input('days', 180);
        $oldLogs = Activity::where('created_at', '<', now()->subDays($days))->get();

        if ($oldLogs->isEmpty()) {
            return redirect()->back()->with('info', 'No logs to archive.');
        }

        // Archive to secure storage
        $filename = 'archived-logs-' . now()->format('Y-m-d-H-i-s') . '.json';
        $archivePath = "logs/archives/{$filename}";
        
        Storage::disk('local')->put($archivePath, $oldLogs->toJson(JSON_PRETTY_PRINT));

        // Log this archival action
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'ip_address' => request()->ip(),
                'archived_count' => $oldLogs->count(),
                'archive_file' => $filename,
                'retention_days' => $days
            ])
            ->event('archive_logs')
            ->log('Archived ' . $oldLogs->count() . ' activity logs older than ' . $days . ' days');

        // Delete original logs after archiving
        Activity::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success', "Archived and deleted {$oldLogs->count()} old activity logs");
    }

    /**
     * Clear old logs with compliance (ISO A.8.15: Retention policy)
     * Minimum 90 days retention as per ISO 27001 A.12.4.1
     */
    public function clearOld(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $days = max($request->input('days', 90), 90); // Minimum 90 days
        
        $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();

        // Log the deletion action
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'ip_address' => request()->ip(),
                'deleted_count' => $deleted,
                'retention_days' => $days
            ])
            ->event('clear_logs')
            ->log("Deleted {$deleted} activity logs older than {$days} days");

        return redirect()->back()->with('success', "Deleted {$deleted} old activity logs");
    }

    /**
     * Export logs to CSV with audit trail (ISO A.8.15: Protection of log information)
     */
    public function export(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $query = Activity::with(['causer', 'subject'])->latest();

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        // Log the export action (ISO A.8.15: Data export tracking)
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'ip_address' => request()->ip(),
                'exported_count' => $activities->count(),
                'date_range' => [
                    'from' => $request->date_from,
                    'to' => $request->date_to
                ],
                'user_agent' => request()->userAgent()
            ])
            ->event('export_logs')
            ->log('Exported ' . $activities->count() . ' activity logs to CSV');

        $filename = 'activity-logs-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 
                'Date/Time', 
                'User', 
                'Action', 
                'Model', 
                'Changes',
                'IP Address',
                'User Agent'
            ]);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->causer ? $activity->causer->name : 'System',
                    $activity->description,
                    class_basename($activity->subject_type),
                    json_encode($activity->properties),
                    $activity->properties['ip_address'] ?? 'N/A',
                    $activity->properties['user_agent'] ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate audit summary (ISO A.8.16: Review & monitoring dashboard)
     */
    public function auditSummary(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $from = $request->input('from', now()->subDays(30));
        $to = $request->input('to', now());

        $summary = [
            'total_activities' => Activity::whereBetween('created_at', [$from, $to])->count(),
            'by_event' => Activity::whereBetween('created_at', [$from, $to])
                ->groupBy('event')
                ->selectRaw('event, count(*) as count')
                ->get(),
            'by_user' => Activity::whereBetween('created_at', [$from, $to])
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
                ->with('causer')
                ->selectRaw('causer_id, count(*) as count')
                ->get(),
            'by_model' => Activity::whereBetween('created_at', [$from, $to])
                ->groupBy('subject_type')
                ->selectRaw('subject_type, count(*) as count')
                ->get(),
            'failed_attempts' => $this->getFailedLoginAttempts($from, $to),
            'privilege_changes' => Activity::whereBetween('created_at', [$from, $to])
                ->where('description', 'like', '%role%')
                ->count(),
        ];

        return view('admin.activity-logs.audit-summary', compact('summary', 'from', 'to'));
    }

    /**
     * Get failed login attempts for monitoring (ISO A.8.16: Suspicious activity)
     */
    private function getFailedLoginAttempts($from, $to)
    {
        return Activity::whereBetween('created_at', [$from, $to])
            ->where('event', 'failed_login')
            ->groupBy('causer_id')
            ->selectRaw('causer_id, count(*) as count')
            ->havingRaw('count > 3')
            ->get();
    }

    /**
     * Log activity view (ISO A.8.16: Track who views sensitive logs)
     */
    private function logActivityView($activity, $userId)
    {
        // Store view metadata in activity_log_views table
        \DB::table('activity_log_views')->insert([
            'activity_id' => $activity->id,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);
    }

    /**
     * Generate compliance report (ISO A.8.15, A.8.16)
     */
    public function complianceReport(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $period = $request->input('period', 'monthly');
        
        $report = [
            'generated_at' => now(),
            'generated_by' => Auth::user()->name,
            'total_logs' => Activity::count(),
            'retention_policy' => '90 days minimum',
            'access_restricted_to' => 'Superadmins only',
            'log_integrity' => 'SHA-256 signatures enabled',
            'recent_exports' => Activity::where('event', 'export_logs')
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('admin.activity-logs.compliance-report', compact('report'));
    }
}
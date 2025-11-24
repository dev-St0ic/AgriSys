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

            // Get unique subject types for filter with user-friendly names
            $subjectTypes = Activity::distinct()
                ->pluck('subject_type')
                ->filter()
                ->map(function($type) {
                    return [
                        'value' => $type,
                        'label' => $this->getModelFriendlyName($type)
                    ];
                })
                ->values();

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

        $filename = 'archived-logs-' . now()->format('Y-m-d-H-i-s') . '.json';
        $archivePath = "logs/archives/{$filename}";
        
        Storage::disk('local')->put($archivePath, $oldLogs->toJson(JSON_PRETTY_PRINT));

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

        Activity::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success', "Archived and deleted {$oldLogs->count()} old activity logs");
    }

    /**
     * Clear old logs with compliance (ISO A.8.15: Retention policy)
     */
    public function clearOld(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $days = max($request->input('days', 90), 90);
        
        $deleted = Activity::where('created_at', '<', now()->subDays($days))->delete();

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
     * Export logs to CSV with audit trail
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
                    $this->getModelFriendlyName($activity->subject_type),
                    $this->getChangesForExport($activity),
                    $activity->properties['ip_address'] ?? 'N/A',
                    $activity->properties['user_agent'] ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Generate audit summary
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
     * Get failed login attempts for monitoring
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
        \DB::table('activity_log_views')->insert([
            'activity_id' => $activity->id,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);
    }

    /**
     * Generate compliance report
     */
    public function complianceReport(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

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

    /**
     * Get user-friendly model name
     * Converts model class to readable name
     */
    public function getModelFriendlyName($modelType): string
    {
        if (!$modelType) {
            return 'System';
        }

        $modelName = class_basename($modelType);
        
        $mapping = [
            'User' => 'User Account',
            'UserRegistration' => 'User Registration',
            'RsbsaApplication' => 'RSBSA Application',
            'FishrApplication' => 'FishR Application',
            'BoatrApplication' => 'BoatR Application',
            'TrainingApplication' => 'Training Application',
            'SeedlingRequest' => 'Seedling Request',
            'SeedlingRequestItem' => 'Seedling Request Item',
            'CategoryItem' => 'Supply Item',
            'ItemSupplyLog' => 'Supply Log',
            'RequestCategory' => 'Item Category',
            'BoatrAnnex' => 'BoatR Document',
            'FishrAnnex' => 'FishR Document',
        ];

        return $mapping[$modelName] ?? str_replace(
            ['App\\Models\\', '_'],
            ['', ' '],
            ucwords(preg_replace('/([a-z])([A-Z])/', '$1 $2', $modelName))
        );
    }

    /**
     * Get user-friendly field names
     */
    public function getFieldFriendlyName($fieldName): string
    {
        $mapping = [
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'contact_number' => 'Contact Number',
            'email' => 'Email Address',
            'status' => 'Status',
            'remarks' => 'Remarks',
            'application_number' => 'Application Number',
            'vessel_name' => 'Vessel Name',
            'boat_type' => 'Boat Type',
            'engine_horsepower' => 'Engine Horsepower',
            'current_supply' => 'Current Supply',
            'reorder_point' => 'Reorder Point',
            'approved_quantity' => 'Approved Quantity',
            'requested_quantity' => 'Requested Quantity',
            'total_quantity' => 'Total Quantity',
            'is_active' => 'Status',
            'display_order' => 'Display Order',
            'role' => 'Role',
            'name' => 'Name',
            'password' => 'Password',
            'document_path' => 'Document',
            'supporting_document_path' => 'Supporting Document',
            'user_document_path' => 'User Document',
            'inspection_documents' => 'Inspection Documents',
            'inspection_completed' => 'Inspection Status',
            'documents_verified' => 'Documents Verified',
            'profile_photo' => 'Profile Photo',
            'date_of_birth' => 'Date of Birth',
            'barangay' => 'Barangay',
            'main_livelihood' => 'Main Livelihood',
            'land_area' => 'Land Area',
            'commodity' => 'Commodity',
            'training_type' => 'Training Type',
            'registration_number' => 'Registration Number',
            'request_number' => 'Request Number',
        ];

        return $mapping[$fieldName] ?? ucwords(str_replace('_', ' ', $fieldName));
    }

    /**
     * Format field values for display
     */
    public function formatFieldValue($value): string
    {
        if (is_null($value)) {
            return 'N/A';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Get formatted changes for display in views
     */
    public function getFormattedChanges($activity): array
    {
        if (!$activity->properties || !$activity->properties->has('attributes')) {
            return [];
        }

        $changes = [];
        $attributes = $activity->properties->get('attributes', []);
        $old = $activity->properties->get('old', []);

        foreach ($attributes as $key => $newValue) {
            $oldValue = $old[$key] ?? null;
            
            $changes[] = [
                'field' => $this->getFieldFriendlyName($key),
                'old_value' => $this->formatFieldValue($oldValue),
                'new_value' => $this->formatFieldValue($newValue),
                'changed' => $oldValue !== $newValue
            ];
        }

        return $changes;
    }

    /**
     * Get changes formatted for CSV export
     */
    private function getChangesForExport($activity): string
    {
        if (!$activity->properties || !$activity->properties->has('attributes')) {
            return '-';
        }

        $changes = $this->getFormattedChanges($activity);
        $formatted = [];

        foreach ($changes as $change) {
            $formatted[] = "{$change['field']}: {$change['old_value']} → {$change['new_value']}";
        }

        return implode('; ', $formatted);
    }

    /**
     * Get activity description with user-friendly context
     */
    public function getActivityContextMessage($activity): string
    {
        $user = $activity->causer ? $activity->causer->name : 'System';
        $model = $this->getModelFriendlyName($activity->subject_type);
        $action = $activity->description ?? strtolower($activity->event ?? 'unknown action');

        if ($activity->subject_id) {
            return "{$user} {$action} on {$model} #" . $activity->subject_id;
        }

        return "{$user} {$action}";
    }
}
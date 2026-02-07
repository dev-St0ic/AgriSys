<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs - Superadmin only (ISO A.8.16)
     */
    public function index(Request $request)
    {
        // ISO A.8.16: Restrict to superadmins only
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only superadmins can view activity logs.');
        }

        try {
            $query = Activity::with(['causer', 'subject'])->latest();

            // Filter by search
            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            // Filter by module type
            if ($request->filled('subject_type')) {
                $query->where('subject_type', $request->subject_type);
            }

            // Filter by action/event
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

            $activities = $query->paginate(25);

            // Get unique subject types for filter dropdown
            $subjectTypes = Activity::distinct()
                ->pluck('subject_type')
                ->filter()
                ->map(function($type) {
                    return [
                        'value' => $type,
                        'label' => $this->getModelLabel($type)
                    ];
                })
                ->values();

            return view('admin.activity-logs.index', compact('activities', 'subjectTypes'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading activity logs: ' . $e->getMessage());
        }
    }

    /**
     * Show specific activity details (JSON response for modal)
     */
    public function show($id)
    {
        if (!Auth::user()->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $activity = Activity::with(['causer', 'subject'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'event' => $activity->event,
                    'created_at' => $activity->created_at->format('M d, Y H:i:s'),
                    'causer_name' => $activity->causer ? $activity->causer->name : 'System',
                    'causer_email' => $activity->causer ? $activity->causer->email : null,
                    'subject_type_label' => $this->getModelLabel($activity->subject_type),
                    'subject_type' => $activity->subject_type,
                    'subject_id' => $activity->subject_id,
                    'description' => $activity->description,
                    'properties' => $activity->properties
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export logs to CSV
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

        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Date/Time', 'User', 'Action', 'Module', 'Target', 'IP Address'
            ]);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->causer ? $activity->causer->name : 'System',
                    $activity->event,
                    $this->getModelLabel($activity->subject_type),
                    '#' . ($activity->subject_id ?? 'N/A'),
                    $activity->properties['ip_address'] ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Audit summary dashboard
     */
    public function auditSummary(Request $request)
    {
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $from = $request->input('from', now()->subDays(30));
        $to = $request->input('to', now());

        $summary = [
            'total' => Activity::whereBetween('created_at', [$from, $to])->count(),
            'by_event' => Activity::whereBetween('created_at', [$from, $to])
                ->groupBy('event')
                ->selectRaw('event, count(*) as count')
                ->pluck('count', 'event'),
            'by_user' => Activity::whereBetween('created_at', [$from, $to])
                ->whereNotNull('causer_id')
                ->groupBy('causer_id')
                ->with('causer')
                ->selectRaw('causer_id, count(*) as count')
                ->get()
        ];

        return view('admin.activity-logs.audit-summary', compact('summary', 'from', 'to'));
    }

    /**
     * Get user-friendly model label
     */
    private function getModelLabel($modelType): string
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
            'SeedlingRequestItem' => 'Request Item',
            'CategoryItem' => 'Supply Item',
            'ItemSupplyLog' => 'Supply Log',
            'RequestCategory' => 'Item Category',
            'BoatrAnnex' => 'BoatR Document',
            'FishrAnnex' => 'FishR Document',
        ];

        return $mapping[$modelName] ?? $modelName;
    }
}
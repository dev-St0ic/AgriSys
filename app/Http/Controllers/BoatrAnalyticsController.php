<?php

namespace App\Http\Controllers;

use App\Models\BoatrApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BoatrAnalyticsController extends Controller
{
    /**
     * Display BOATR analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            // Log analytics view
            if (auth()->check()) {
                activity()
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'analytics_type' => 'BoatrAnalytics',
                        'start_date' => $request->get('start_date', now()->subMonths(6)->format('Y-m-d')),
                        'end_date' => $request->get('end_date', now()->format('Y-m-d')),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ])
                    ->event('viewed')
                    ->log('viewed - BoatrAnalytics Dashboard');
            }

            // Date range filter with better defaults
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            // Validate dates
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');

            // Base query with date range
            $baseQuery = BoatrApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);

            // 2. Application Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);

            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);

            // 4. Boat Type Analysis
            $boatTypeAnalysis = $this->getBoatTypeAnalysis(clone $baseQuery);

            // 5. Fishing Gear Analysis
            $fishingGearAnalysis = $this->getFishingGearAnalysis(clone $baseQuery);

            // 6. Vessel Size Analysis
            $vesselSizeAnalysis = $this->getVesselSizeAnalysis(clone $baseQuery);

            // 7. Application Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);

            // 8. Registration Patterns
            $registrationPatterns = $this->getRegistrationPatterns(clone $baseQuery);

            // 9. Document Submission Analysis
            $documentAnalysis = $this->getDocumentAnalysis(clone $baseQuery);

            // 10. Monthly Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);

            // 11. Inspection Analysis
            $inspectionAnalysis = $this->getInspectionAnalysis(clone $baseQuery);

            // 12. Engine Analysis
            $engineAnalysis = $this->getEngineAnalysis(clone $baseQuery);

            // 13. Motorized / Non-Motorized Analysis
            $motorizedAnalysis = $this->getMotorizedAnalysis(clone $baseQuery);

            // 14. Fishers with Multiple BoatR Registrations
            $multipleRegistrationsAnalysis = $this->getMultipleRegistrationsAnalysis(clone $baseQuery);

            return view('admin.analytics.boatr', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'boatTypeAnalysis',
                'fishingGearAnalysis',
                'vesselSizeAnalysis',
                'processingTimeAnalysis',
                'registrationPatterns',
                'documentAnalysis',
                'performanceMetrics',
                'inspectionAnalysis',
                'engineAnalysis',
                'motorizedAnalysis',
                'multipleRegistrationsAnalysis',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('BOATR Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading BOATR analytics data. Please try again.');
        }
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStatistics($baseQuery)
    {
        try {
            $total = $baseQuery->count();
            $approved = (clone $baseQuery)->where('status', 'approved')->count();
            $rejected = (clone $baseQuery)->where('status', 'rejected')->count();
            $pending = (clone $baseQuery)->whereIn('status', ['pending', 'under_review', 'inspection_scheduled', 'inspection_required', 'documents_pending'])->count();

            // Count unique applicants safely
            $uniqueApplicants = (clone $baseQuery)
                ->select(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
                ->distinct()
                ->whereNotNull('first_name')
                ->count();

            // Count unique vessels
            $uniqueVessels = (clone $baseQuery)->whereNotNull('vessel_name')->distinct()->count('vessel_name');

            // Count applications with user documents
            $withUserDocuments = (clone $baseQuery)->whereNotNull('user_document_path')->where('user_document_path', '!=', '')->count();

            // Count inspections completed
            $inspectionsCompleted = (clone $baseQuery)->where('inspection_completed', true)->count();

            // Count unique boat types
            $uniqueBoatTypes = (clone $baseQuery)->whereNotNull('boat_type')->distinct()->count('boat_type');

            return [
                'total_applications' => $total,
                'approved_applications' => $approved,
                'rejected_applications' => $rejected,
                'pending_applications' => $pending,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'unique_applicants' => $uniqueApplicants,
                'unique_vessels' => $uniqueVessels,
                'with_user_documents' => $withUserDocuments,
                'user_document_submission_rate' => $total > 0 ? round(($withUserDocuments / $total) * 100, 2) : 0,
                'unique_boat_types' => $uniqueBoatTypes,
                'inspections_completed' => $inspectionsCompleted,
                'inspection_completion_rate' => $total > 0 ? round(($inspectionsCompleted / $total) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Overview Statistics Error: ' . $e->getMessage());
            return $this->getDefaultOverview();
        }
    }

    /**
     * Get status analysis with trends
     */
    private function getStatusAnalysis($baseQuery)
    {
        try {
            $statusCounts = (clone $baseQuery)->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Ensure all statuses are present with default values
            $defaultStatuses = [
                'pending' => 0,
                'under_review' => 0,
                'inspection_scheduled' => 0,
                'inspection_required' => 0,
                'documents_pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ];
            $statusCounts = array_merge($defaultStatuses, $statusCounts);

            // Add percentage calculations
            $total = array_sum($statusCounts);
            $statusPercentages = [];
            foreach ($statusCounts as $status => $count) {
                $statusPercentages[$status] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            }

            return [
                'counts' => $statusCounts,
                'percentages' => $statusPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Status Analysis Error: ' . $e->getMessage());
            return [
                'counts' => [
                    'pending' => 0, 'under_review' => 0, 'inspection_scheduled' => 0,
                    'inspection_required' => 0, 'documents_pending' => 0,
                    'approved' => 0, 'rejected' => 0
                ],
                'percentages' => [
                    'pending' => 0, 'under_review' => 0, 'inspection_scheduled' => 0,
                    'inspection_required' => 0, 'documents_pending' => 0,
                    'approved' => 0, 'rejected' => 0
                ],
                'total' => 0
            ];
        }
    }

    /**
     * Get monthly trends
     */
    private function getMonthlyTrends($startDate, $endDate)
    {
        try {
            $trends = BoatrApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "under_review", "inspection_scheduled", "inspection_required", "documents_pending") THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN user_document_path IS NOT NULL AND user_document_path != "" THEN 1 ELSE 0 END) as with_user_documents'),
                    DB::raw('SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed'),
                    DB::raw('COUNT(DISTINCT vessel_name) as unique_vessels')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return $trends;
        } catch (\Exception $e) {
            Log::error('BOATR Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get motorized vs non-motorized analysis by boat_classification
     */
    private function getMotorizedAnalysis($baseQuery)
    {
        try {
            $classificationStats = (clone $baseQuery)->select(
                    'boat_classification',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed'),
                    DB::raw('AVG(engine_horsepower) as avg_horsepower')
                )
                ->whereNotNull('boat_classification')
                ->where('boat_classification', '!=', '')
                ->groupBy('boat_classification')
                ->orderBy('total_applications', 'desc')
                ->get();

            $total = $classificationStats->sum('total_applications');

            $classificationStats = $classificationStats->map(function ($item) use ($total) {
                $item->percentage = $total > 0 ? round(($item->total_applications / $total) * 100, 1) : 0;
                $item->approval_rate = $item->total_applications > 0
                    ? round(($item->approved / $item->total_applications) * 100, 1)
                    : 0;
                return $item;
            });

            return [
                'stats' => $classificationStats,
                'total' => $total,
                'motorized_count' => $classificationStats->where('boat_classification', 'Motorized')->sum('total_applications'),
                'non_motorized_count' => $classificationStats->where('boat_classification', 'Non-motorized')->sum('total_applications'),
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Motorized Analysis Error: ' . $e->getMessage());
            return ['stats' => collect([]), 'total' => 0, 'motorized_count' => 0, 'non_motorized_count' => 0];
        }
    }

    /**
     * Get fishers with multiple BoatR registrations
     */
    private function getMultipleRegistrationsAnalysis($baseQuery)
    {
        try {
            // Group by fishr_number to find fishers with >1 boat registration
            $byFishrNumber = (clone $baseQuery)
                ->whereNotNull('fishr_number')
                ->where('fishr_number', '!=', '')
                ->select('fishr_number', DB::raw('COUNT(*) as boat_count'))
                ->groupBy('fishr_number')
                ->having('boat_count', '>', 1)
                ->orderBy('boat_count', 'desc')
                ->get();

            $fishersWithMultiple = $byFishrNumber->count();
            $totalExtraBoats = $byFishrNumber->sum(function ($row) {
                return $row->boat_count - 1;
            });

            return [
                'fishers_with_multiple' => $fishersWithMultiple,
                'total_extra_boats' => $totalExtraBoats,
                'details' => $byFishrNumber->take(10),
                'max_boats_per_fisher' => $byFishrNumber->max('boat_count') ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Multiple Registrations Analysis Error: ' . $e->getMessage());
            return ['fishers_with_multiple' => 0, 'total_extra_boats' => 0, 'details' => collect([]), 'max_boats_per_fisher' => 0];
        }
    }

    /**
     * Get boat type analysis
     */
    private function getBoatTypeAnalysis($baseQuery)
    {
        try {
            $boatTypeStats = (clone $baseQuery)->select(
                    'boat_type',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants'),
                    DB::raw('SUM(CASE WHEN user_document_path IS NOT NULL AND user_document_path != "" THEN 1 ELSE 0 END) as with_user_documents'),
                    DB::raw('SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed'),
                    DB::raw('AVG(CASE WHEN boat_length IS NOT NULL THEN boat_length END) as avg_length'),
                    DB::raw('AVG(CASE WHEN boat_width IS NOT NULL THEN boat_width END) as avg_width'),
                    DB::raw('AVG(CASE WHEN boat_depth IS NOT NULL THEN boat_depth END) as avg_depth')
                )
                ->whereNotNull('boat_type')
                ->where('boat_type', '!=', '')
                ->groupBy('boat_type')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $boatTypeStats;
        } catch (\Exception $e) {
            Log::error('BOATR Boat Type Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get fishing gear analysis - simplified version
     */
    public function getFishingGearAnalysis($baseQuery)
    {
        try {
            Log::info('BOATR Fishing Gear Analysis - Starting analysis');

            // Get the main fishing gear statistics
            $fishingGearStats = (clone $baseQuery)->select(
                    'primary_fishing_gear',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status IN ("pending", "under_review", "inspection_scheduled", "inspection_required", "documents_pending") THEN 1 ELSE 0 END) as pending')
                )
                ->whereNotNull('primary_fishing_gear')
                ->where('primary_fishing_gear', '!=', '')
                ->groupBy('primary_fishing_gear')
                ->orderBy('total_applications', 'desc')
                ->get();

            Log::info('BOATR Fishing Gear Analysis - Found ' . $fishingGearStats->count() . ' gear types');

            // Add percentage calculations for each gear type
            $totalApplications = $fishingGearStats->sum('total_applications');
            $fishingGearStats = $fishingGearStats->map(function($gear) use ($totalApplications) {
                $gear->percentage = $totalApplications > 0 ? round(($gear->total_applications / $totalApplications) * 100, 1) : 0;
                $gear->approval_rate = $gear->total_applications > 0 ? round(($gear->approved / $gear->total_applications) * 100, 1) : 0;
                return $gear;
            });

            return $fishingGearStats;

        } catch (\Exception $e) {
            Log::error('BOATR Fishing Gear Analysis Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            // Return empty collection on error
            return collect([]);
        }
    }

    /**
     * Get vessel size analysis
     */
    private function getVesselSizeAnalysis($baseQuery)
    {
        try {
            $vesselSizeStats = (clone $baseQuery)
                ->whereNotNull('boat_length')
                ->whereNotNull('boat_width')
                ->whereNotNull('boat_depth')
                ->select(
                    DB::raw('
                        CASE
                            WHEN boat_length <= 10 THEN "Small (≤10ft)"
                            WHEN boat_length <= 20 THEN "Medium (11-20ft)"
                            WHEN boat_length <= 30 THEN "Large (21-30ft)"
                            ELSE "Extra Large (>30ft)"
                        END as size_category
                    '),
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('AVG(boat_length) as avg_length'),
                    DB::raw('AVG(boat_width) as avg_width'),
                    DB::raw('AVG(boat_depth) as avg_depth'),
                    DB::raw('AVG(CASE WHEN engine_horsepower IS NOT NULL THEN engine_horsepower END) as avg_horsepower'),
                    DB::raw('SUM(CASE WHEN inspection_completed = 1 THEN 1 ELSE 0 END) as inspections_completed')
                )
                ->groupBy('size_category')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $vesselSizeStats;
        } catch (\Exception $e) {
            Log::error('BOATR Vessel Size Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis($baseQuery)
    {
        try {
            $processedApplications = (clone $baseQuery)->whereNotNull('reviewed_at')->get();

            $processingTimes = [];
            foreach ($processedApplications as $application) {
                if ($application->reviewed_at && $application->created_at) {
                    $processingTime = Carbon::parse($application->created_at)
                        ->diffInDays(Carbon::parse($application->reviewed_at));
                    $processingTimes[] = $processingTime;
                }
            }

            if (empty($processingTimes)) {
                return [
                    'avg_processing_days' => 0,
                    'min_processing_days' => 0,
                    'max_processing_days' => 0,
                    'processed_count' => 0,
                    'median_processing_days' => 0
                ];
            }

            sort($processingTimes);
            $count = count($processingTimes);
            $median = $count % 2 === 0
                ? ($processingTimes[$count/2 - 1] + $processingTimes[$count/2]) / 2
                : $processingTimes[floor($count/2)];

            return [
                'avg_processing_days' => round(array_sum($processingTimes) / count($processingTimes), 2),
                'min_processing_days' => min($processingTimes),
                'max_processing_days' => max($processingTimes),
                'processed_count' => count($processingTimes),
                'median_processing_days' => round($median, 2)
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Processing Time Error: ' . $e->getMessage());
            return [
                'avg_processing_days' => 0,
                'min_processing_days' => 0,
                'max_processing_days' => 0,
                'processed_count' => 0,
                'median_processing_days' => 0
            ];
        }
    }

    /**
     * Get registration patterns (day of week, time patterns)
     */
    private function getRegistrationPatterns($baseQuery)
    {
        try {
            $dayOfWeekStats = (clone $baseQuery)->select(
                    DB::raw('DAYNAME(created_at) as day_name'),
                    DB::raw('DAYOFWEEK(created_at) as day_number'),
                    DB::raw('COUNT(*) as applications_count')
                )
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get();

            $hourlyStats = (clone $baseQuery)->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('COUNT(*) as applications_count')
                )
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();

            return [
                'day_of_week' => $dayOfWeekStats,
                'hourly' => $hourlyStats
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Registration Patterns Error: ' . $e->getMessage());
            return [
                'day_of_week' => collect([]),
                'hourly' => collect([])
            ];
        }
    }

    /**
     * Get document submission analysis
     */
    private function getDocumentAnalysis($baseQuery)
    {
        try {
            $withUserDocs = (clone $baseQuery)->whereNotNull('user_document_path')->where('user_document_path', '!=', '')->count();
            $withoutUserDocs = (clone $baseQuery)->where(function($q) {
                $q->whereNull('user_document_path')->orWhere('user_document_path', '');
            })->count();

            $withInspectionDocs = (clone $baseQuery)->whereNotNull('inspection_documents')->count();
            $withoutInspectionDocs = (clone $baseQuery)->whereNull('inspection_documents')->count();

            $total = $withUserDocs + $withoutUserDocs;

            // Approval rates by document submission
            $approvalWithUserDocs = (clone $baseQuery)
                ->whereNotNull('user_document_path')
                ->where('user_document_path', '!=', '')
                ->where('status', 'approved')
                ->count();

            $approvalWithoutUserDocs = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('user_document_path')->orWhere('user_document_path', '');
                })
                ->where('status', 'approved')
                ->count();

            return [
                'with_user_documents' => $withUserDocs,
                'without_user_documents' => $withoutUserDocs,
                'with_inspection_documents' => $withInspectionDocs,
                'without_inspection_documents' => $withoutInspectionDocs,
                'total' => $total,
                'user_doc_submission_rate' => $total > 0 ? round(($withUserDocs / $total) * 100, 2) : 0,
                'inspection_doc_submission_rate' => $total > 0 ? round(($withInspectionDocs / $total) * 100, 2) : 0,
                'approval_rate_with_user_docs' => $withUserDocs > 0 ? round(($approvalWithUserDocs / $withUserDocs) * 100, 2) : 0,
                'approval_rate_without_user_docs' => $withoutUserDocs > 0 ? round(($approvalWithoutUserDocs / $withoutUserDocs) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Document Analysis Error: ' . $e->getMessage());
            return [
                'with_user_documents' => 0,
                'without_user_documents' => 0,
                'with_inspection_documents' => 0,
                'without_inspection_documents' => 0,
                'total' => 0,
                'user_doc_submission_rate' => 0,
                'inspection_doc_submission_rate' => 0,
                'approval_rate_with_user_docs' => 0,
                'approval_rate_without_user_docs' => 0
            ];
        }
    }

    /**
     * Get inspection analysis
     */
    private function getInspectionAnalysis($baseQuery)
    {
        try {
            $totalInspectable = (clone $baseQuery)->whereIn('status', ['inspection_scheduled', 'inspection_required', 'approved', 'rejected'])->count();
            $inspectionsCompleted = (clone $baseQuery)->where('inspection_completed', true)->count();
            $inspectionsScheduled = (clone $baseQuery)->where('status', 'inspection_scheduled')->count();
            $inspectionsRequired = (clone $baseQuery)->where('status', 'inspection_required')->count();

            // Average inspection time
            $inspectionTimes = (clone $baseQuery)
                ->whereNotNull('inspection_date')
                ->whereNotNull('created_at')
                ->select(DB::raw('DATEDIFF(inspection_date, created_at) as days_to_inspection'))
                ->pluck('days_to_inspection')
                ->filter()
                ->toArray();

            $avgInspectionTime = !empty($inspectionTimes) ? round(array_sum($inspectionTimes) / count($inspectionTimes), 2) : 0;

            // Inspector workload
            $inspectorWorkload = (clone $baseQuery)
                ->whereNotNull('inspected_by')
                ->select('inspected_by', DB::raw('COUNT(*) as inspections_count'))
                ->groupBy('inspected_by')
                ->with('inspector:id,name')
                ->orderBy('inspections_count', 'desc')
                ->get();

            return [
                'total_inspectable' => $totalInspectable,
                'inspections_completed' => $inspectionsCompleted,
                'inspections_scheduled' => $inspectionsScheduled,
                'inspections_required' => $inspectionsRequired,
                'completion_rate' => $totalInspectable > 0 ? round(($inspectionsCompleted / $totalInspectable) * 100, 2) : 0,
                'avg_inspection_time' => $avgInspectionTime,
                'inspector_workload' => $inspectorWorkload
            ];
        } catch (\Exception $e) {
            Log::error('BOATR Inspection Analysis Error: ' . $e->getMessage());
            return [
                'total_inspectable' => 0,
                'inspections_completed' => 0,
                'inspections_scheduled' => 0,
                'inspections_required' => 0,
                'completion_rate' => 0,
                'avg_inspection_time' => 0,
                'inspector_workload' => collect([])
            ];
        }
    }

    /**
     * Get engine analysis
     */
    private function getEngineAnalysis($baseQuery)
    {
        try {
            $engineStats = (clone $baseQuery)
                ->select(
                    'engine_type',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('AVG(CASE WHEN engine_horsepower IS NOT NULL THEN engine_horsepower END) as avg_horsepower'),
                    DB::raw('MIN(CASE WHEN engine_horsepower IS NOT NULL THEN engine_horsepower END) as min_horsepower'),
                    DB::raw('MAX(CASE WHEN engine_horsepower IS NOT NULL THEN engine_horsepower END) as max_horsepower'),
                    DB::raw('COUNT(DISTINCT boat_type) as boat_types_used')
                )
                ->whereNotNull('engine_type')
                ->where('engine_type', '!=', '')
                ->groupBy('engine_type')
                ->orderBy('total_applications', 'desc')
                ->get();

            return $engineStats;
        } catch (\Exception $e) {
            Log::error('BOATR Engine Analysis Error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($baseQuery)
    {
        try {
            $metrics = [];

            // Overall completion rate
            $total = (clone $baseQuery)->count();
            $completed = (clone $baseQuery)->whereIn('status', ['approved', 'rejected'])->count();
            $metrics['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

            // Average applications per day
            $dateRange = (clone $baseQuery)->selectRaw('DATEDIFF(MAX(created_at), MIN(created_at)) + 1 as days')->first();
            $totalDays = $dateRange ? $dateRange->days : 1;
            $metrics['avg_applications_per_day'] = round($total / max(1, $totalDays), 2);

            // Quality score (based on document submission, inspection completion, and approval rates)
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            $docAnalysis = $this->getDocumentAnalysis(clone $baseQuery);
            $inspectionAnalysis = $this->getInspectionAnalysis(clone $baseQuery);

            $metrics['quality_score'] = round(
                ($overview['approval_rate'] * 0.4) +
                ($docAnalysis['user_doc_submission_rate'] * 0.3) +
                ($inspectionAnalysis['completion_rate'] * 0.3), 2
            );

            // Efficiency score
            $metrics['efficiency_score'] = round(
                ($metrics['completion_rate'] * 0.5) +
                (min(100, (14 - $this->getProcessingTimeAnalysis(clone $baseQuery)['avg_processing_days']) / 14 * 100) * 0.5), 2
            );

            return $metrics;
        } catch (\Exception $e) {
            Log::error('BOATR Performance Metrics Error: ' . $e->getMessage());
            return [
                'completion_rate' => 0,
                'avg_applications_per_day' => 0,
                'quality_score' => 0,
                'efficiency_score' => 0
            ];
        }
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate($total, $approved)
    {
        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    /**
     * Get default overview when errors occur
     */
    private function getDefaultOverview()
    {
        return [
            'total_applications' => 0,
            'approved_applications' => 0,
            'rejected_applications' => 0,
            'pending_applications' => 0,
            'approval_rate' => 0,
            'unique_applicants' => 0,
            'unique_vessels' => 0,
            'with_user_documents' => 0,
            'user_document_submission_rate' => 0,
            'unique_boat_types' => 0,
            'inspections_completed' => 0,
            'inspection_completion_rate' => 0
        ];
    }

    /**
     * Export analytics data
     */
  public function export(Request $request)
    {
        try {
            $startDate = $request->get('start_date', now()->subMonths(6)->format('Y-m-d'));
            $endDate   = $request->get('end_date', now()->format('Y-m-d'));

            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate   = Carbon::parse($endDate)->format('Y-m-d');

            $baseQuery = BoatrApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $filename = 'boatr-analytics-' . $startDate . '-to-' . $endDate . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $overview           = $this->getOverviewStatistics(clone $baseQuery);
            $statusAnalysis     = $this->getStatusAnalysis(clone $baseQuery);
            $monthlyTrends      = $this->getMonthlyTrends($startDate, $endDate);
            $boatTypeAnalysis   = $this->getBoatTypeAnalysis(clone $baseQuery);
            $fishingGearAnalysis= $this->getFishingGearAnalysis(clone $baseQuery);
            $vesselSizeAnalysis = $this->getVesselSizeAnalysis(clone $baseQuery);
            $processingTime     = $this->getProcessingTimeAnalysis(clone $baseQuery);
            $documentAnalysis   = $this->getDocumentAnalysis(clone $baseQuery);
            $inspectionAnalysis = $this->getInspectionAnalysis(clone $baseQuery);
            $engineAnalysis     = $this->getEngineAnalysis(clone $baseQuery);
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);
            $motorizedAnalysis  = $this->getMotorizedAnalysis(clone $baseQuery);
            $multipleRegistrationsAnalysis = $this->getMultipleRegistrationsAnalysis(clone $baseQuery);

            $callback = function () use (
                $startDate, $endDate, $overview, $statusAnalysis, $monthlyTrends,
                $boatTypeAnalysis, $fishingGearAnalysis, $vesselSizeAnalysis,
                $processingTime, $documentAnalysis, $inspectionAnalysis,
                $engineAnalysis, $performanceMetrics, $motorizedAnalysis,
                $multipleRegistrationsAnalysis
            ) {
                $file = fopen('php://output', 'w');

                // ── Export Info ───────────────────────────────────────
                fputcsv($file, ['BOATR ANALYTICS EXPORT']);
                fputcsv($file, ['Generated At', now()->format('Y-m-d H:i:s')]);
                fputcsv($file, ['Date Range', $startDate . ' to ' . $endDate]);
                fputcsv($file, ['Generated By', auth()->user()->name ?? 'System']);
                fputcsv($file, []);

                // ── Overview ──────────────────────────────────────────
                fputcsv($file, ['OVERVIEW']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Applications',         $overview['total_applications']]);
                fputcsv($file, ['Approved Applications',      $overview['approved_applications']]);
                fputcsv($file, ['Rejected Applications',      $overview['rejected_applications']]);
                fputcsv($file, ['Pending Applications',       $overview['pending_applications']]);
                fputcsv($file, ['Approval Rate',              $overview['approval_rate'] . '%']);
                fputcsv($file, ['Unique Applicants',          $overview['unique_applicants']]);
                fputcsv($file, ['Unique Vessels',             $overview['unique_vessels']]);
                fputcsv($file, ['With User Documents',        $overview['with_user_documents']]);
                fputcsv($file, ['Document Submission Rate',   $overview['user_document_submission_rate'] . '%']);
                fputcsv($file, ['Unique Boat Types',          $overview['unique_boat_types']]);
                fputcsv($file, ['Inspections Completed',      $overview['inspections_completed']]);
                fputcsv($file, ['Inspection Completion Rate', $overview['inspection_completion_rate'] . '%']);
                fputcsv($file, []);

                // ── Status Analysis ───────────────────────────────────
                fputcsv($file, ['STATUS ANALYSIS']);
                fputcsv($file, ['Status', 'Count', 'Percentage']);
                foreach ($statusAnalysis['counts'] as $status => $count) {
                    fputcsv($file, [
                        ucfirst(str_replace('_', ' ', $status)),
                        $count,
                        ($statusAnalysis['percentages'][$status] ?? 0) . '%',
                    ]);
                }
                fputcsv($file, []);

                // ── Monthly Trends ────────────────────────────────────
                fputcsv($file, ['MONTHLY TRENDS']);
                fputcsv($file, ['Month', 'Total Applications', 'Approved', 'Rejected', 'Pending', 'With User Docs', 'Inspections Completed', 'Unique Vessels']);
                foreach ($monthlyTrends as $trend) {
                    fputcsv($file, [
                        $trend->month,
                        $trend->total_applications,
                        $trend->approved,
                        $trend->rejected,
                        $trend->pending,
                        $trend->with_user_documents,
                        $trend->inspections_completed,
                        $trend->unique_vessels,
                    ]);
                }
                fputcsv($file, []);

                // ── Boat Type Analysis ────────────────────────────────
                fputcsv($file, ['BOAT TYPE ANALYSIS']);
                fputcsv($file, ['Boat Type', 'Total Applications', 'Approved', 'Rejected', 'Unique Applicants', 'Inspections Completed', 'Avg Length', 'Avg Width', 'Avg Depth']);
                foreach ($boatTypeAnalysis as $bt) {
                    fputcsv($file, [
                        $bt->boat_type,
                        $bt->total_applications,
                        $bt->approved,
                        $bt->rejected,
                        $bt->unique_applicants,
                        $bt->inspections_completed,
                        round($bt->avg_length ?? 0, 2),
                        round($bt->avg_width ?? 0, 2),
                        round($bt->avg_depth ?? 0, 2),
                    ]);
                }
                fputcsv($file, []);

                // ── Fishing Gear Analysis ─────────────────────────────
                fputcsv($file, ['FISHING GEAR ANALYSIS']);
                fputcsv($file, ['Primary Fishing Gear', 'Total Applications', 'Approved', 'Rejected', 'Pending', 'Approval Rate', 'Percentage']);
                foreach ($fishingGearAnalysis as $gear) {
                    fputcsv($file, [
                        $gear->primary_fishing_gear,
                        $gear->total_applications,
                        $gear->approved,
                        $gear->rejected,
                        $gear->pending,
                        $gear->approval_rate . '%',
                        $gear->percentage . '%',
                    ]);
                }
                fputcsv($file, []);

                // ── Vessel Size Analysis ──────────────────────────────
                fputcsv($file, ['VESSEL SIZE ANALYSIS']);
                fputcsv($file, ['Size Category', 'Total Applications', 'Approved', 'Avg Length', 'Avg Width', 'Avg Depth', 'Avg Horsepower', 'Inspections Completed']);
                foreach ($vesselSizeAnalysis as $vs) {
                    fputcsv($file, [
                        $vs->size_category,
                        $vs->total_applications,
                        $vs->approved,
                        round($vs->avg_length ?? 0, 2),
                        round($vs->avg_width ?? 0, 2),
                        round($vs->avg_depth ?? 0, 2),
                        round($vs->avg_horsepower ?? 0, 2),
                        $vs->inspections_completed,
                    ]);
                }
                fputcsv($file, []);

                // ── Processing Time ───────────────────────────────────
                fputcsv($file, ['PROCESSING TIME ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Avg Processing Days',    $processingTime['avg_processing_days']]);
                fputcsv($file, ['Min Processing Days',    $processingTime['min_processing_days']]);
                fputcsv($file, ['Max Processing Days',    $processingTime['max_processing_days']]);
                fputcsv($file, ['Median Processing Days', $processingTime['median_processing_days']]);
                fputcsv($file, ['Processed Count',        $processingTime['processed_count']]);
                fputcsv($file, []);

                // ── Document Analysis ─────────────────────────────────
                fputcsv($file, ['DOCUMENT ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['With User Documents',              $documentAnalysis['with_user_documents']]);
                fputcsv($file, ['Without User Documents',           $documentAnalysis['without_user_documents']]);
                fputcsv($file, ['With Inspection Documents',        $documentAnalysis['with_inspection_documents']]);
                fputcsv($file, ['Without Inspection Documents',     $documentAnalysis['without_inspection_documents']]);
                fputcsv($file, ['User Doc Submission Rate',         $documentAnalysis['user_doc_submission_rate'] . '%']);
                fputcsv($file, ['Inspection Doc Submission Rate',   $documentAnalysis['inspection_doc_submission_rate'] . '%']);
                fputcsv($file, ['Approval Rate With User Docs',     $documentAnalysis['approval_rate_with_user_docs'] . '%']);
                fputcsv($file, ['Approval Rate Without User Docs',  $documentAnalysis['approval_rate_without_user_docs'] . '%']);
                fputcsv($file, []);

                // ── Inspection Analysis ───────────────────────────────
                fputcsv($file, ['INSPECTION ANALYSIS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Inspectable',      $inspectionAnalysis['total_inspectable']]);
                fputcsv($file, ['Inspections Completed',  $inspectionAnalysis['inspections_completed']]);
                fputcsv($file, ['Inspections Scheduled',  $inspectionAnalysis['inspections_scheduled']]);
                fputcsv($file, ['Inspections Required',   $inspectionAnalysis['inspections_required']]);
                fputcsv($file, ['Completion Rate',        $inspectionAnalysis['completion_rate'] . '%']);
                fputcsv($file, ['Avg Inspection Time',    $inspectionAnalysis['avg_inspection_time'] . ' days']);
                fputcsv($file, []);

                // ── Engine Analysis ───────────────────────────────────
                fputcsv($file, ['ENGINE ANALYSIS']);
                fputcsv($file, ['Engine Type', 'Total Applications', 'Approved', 'Avg Horsepower', 'Min Horsepower', 'Max Horsepower', 'Boat Types Used']);
                foreach ($engineAnalysis as $engine) {
                    fputcsv($file, [
                        $engine->engine_type,
                        $engine->total_applications,
                        $engine->approved,
                        round($engine->avg_horsepower ?? 0, 2),
                        $engine->min_horsepower ?? 0,
                        $engine->max_horsepower ?? 0,
                        $engine->boat_types_used,
                    ]);
                }
                fputcsv($file, []);

                // ── Performance Metrics ───────────────────────────────
                fputcsv($file, ['PERFORMANCE METRICS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Completion Rate',          $performanceMetrics['completion_rate'] . '%']);
                fputcsv($file, ['Avg Applications Per Day', $performanceMetrics['avg_applications_per_day']]);
                fputcsv($file, ['Quality Score',            $performanceMetrics['quality_score'] . '%']);
                fputcsv($file, ['Efficiency Score',         $performanceMetrics['efficiency_score'] . '%']);
                fputcsv($file, []);

                // ── Motorized / Non-Motorized ─────────────────────────
                fputcsv($file, ['MOTORIZED / NON-MOTORIZED ANALYSIS']);
                fputcsv($file, ['Classification', 'Total Applications', 'Approved', 'Rejected', 'Inspections Completed', 'Avg Horsepower', 'Percentage', 'Approval Rate']);
                foreach ($motorizedAnalysis['stats'] as $cls) {
                    fputcsv($file, [
                        $cls->boat_classification,
                        $cls->total_applications,
                        $cls->approved,
                        $cls->rejected,
                        $cls->inspections_completed,
                        round($cls->avg_horsepower ?? 0, 2),
                        $cls->percentage . '%',
                        $cls->approval_rate . '%',
                    ]);
                }
                fputcsv($file, []);

                // ── Fishers with Multiple Registrations ───────────────
                fputcsv($file, ['FISHERS WITH MULTIPLE BOATR REGISTRATIONS']);
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Fishers with Multiple Registrations', $multipleRegistrationsAnalysis['fishers_with_multiple']]);
                fputcsv($file, ['Max Boats per Fisher',               $multipleRegistrationsAnalysis['max_boats_per_fisher']]);
                fputcsv($file, []);
                if ($multipleRegistrationsAnalysis['details']->count() > 0) {
                    fputcsv($file, ['FishR Number', 'Boat Count']);
                    foreach ($multipleRegistrationsAnalysis['details'] as $row) {
                        fputcsv($file, [$row->fishr_number, $row->boat_count]);
                    }
                }

                fclose($file);
            };

            // Log activity
            $this->logActivity('exported', 'BoatrApplication', null, [
                'format'     => 'CSV',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported BoatR analytics data from ' . $startDate . ' to ' . $endDate);

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('BOATR Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting BOATR data: ' . $e->getMessage());
        }
    }
}

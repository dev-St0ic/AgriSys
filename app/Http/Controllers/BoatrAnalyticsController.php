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
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            // Validate dates
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
            
            $baseQuery = BoatrApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            $data = [
                'export_info' => [
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'date_range' => [
                        'start' => $startDate,
                        'end' => $endDate
                    ],
                    'generated_by' => auth()->user()->name ?? 'System'
                ],
                'overview' => $this->getOverviewStatistics(clone $baseQuery),
                'status_analysis' => $this->getStatusAnalysis(clone $baseQuery),
                'monthly_trends' => $this->getMonthlyTrends($startDate, $endDate)->toArray(),
                'boat_type_analysis' => $this->getBoatTypeAnalysis(clone $baseQuery)->toArray(),
                'fishing_gear_analysis' => $this->getFishingGearAnalysis(clone $baseQuery)->toArray(),
                'vessel_size_analysis' => $this->getVesselSizeAnalysis(clone $baseQuery)->toArray(),
                'processing_time' => $this->getProcessingTimeAnalysis(clone $baseQuery),
                'registration_patterns' => $this->getRegistrationPatterns(clone $baseQuery),
                'document_analysis' => $this->getDocumentAnalysis(clone $baseQuery),
                'inspection_analysis' => $this->getInspectionAnalysis(clone $baseQuery),
                'engine_analysis' => $this->getEngineAnalysis(clone $baseQuery)->toArray(),
                'performance_metrics' => $this->getPerformanceMetrics(clone $baseQuery)
            ];
            
            $filename = 'boatr-analytics-' . $startDate . '-to-' . $endDate . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('BOATR Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting BOATR data: ' . $e->getMessage());
        }
    }
}
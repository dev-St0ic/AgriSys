<?php

namespace App\Http\Controllers;

use App\Models\TrainingApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TrainingAnalyticsController extends Controller
{
    /**
     * Display Training analytics dashboard
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
            $baseQuery = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
            // 1. Overview Statistics
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            
            // 2. Application Status Analysis
            $statusAnalysis = $this->getStatusAnalysis(clone $baseQuery);
            
            // 3. Monthly Trends
            $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);
            
            // 4. Training Type Analysis
            $trainingTypeAnalysis = $this->getTrainingTypeAnalysis(clone $baseQuery);
            
            // 5. Registration Patterns
            $registrationPatterns = $this->getRegistrationPatterns(clone $baseQuery);
            
            // 6. Application Processing Time Analysis
            $processingTimeAnalysis = $this->getProcessingTimeAnalysis(clone $baseQuery);
            
            // 7. Document Submission Analysis
            $documentAnalysis = $this->getDocumentAnalysis(clone $baseQuery);
            
            // 8. Performance Metrics
            $performanceMetrics = $this->getPerformanceMetrics(clone $baseQuery);
            
            // 9. Applicant Demographics
            $demographicAnalysis = $this->getDemographicAnalysis(clone $baseQuery);
            
            // 10. Contact Method Analysis
            $contactAnalysis = $this->getContactAnalysis(clone $baseQuery);
            
            return view('admin.analytics.training', compact(
                'overview',
                'statusAnalysis',
                'monthlyTrends',
                'trainingTypeAnalysis',
                'registrationPatterns',
                'processingTimeAnalysis',
                'documentAnalysis',
                'performanceMetrics',
                'demographicAnalysis',
                'contactAnalysis',
                'startDate',
                'endDate'
            ));
        } catch (\Exception $e) {
            Log::error('Training Analytics Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading Training analytics data. Please try again.');
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
            $pending = (clone $baseQuery)->where('status', 'under_review')->count();
            
            // Count unique applicants
            $uniqueApplicants = (clone $baseQuery)
                ->select(DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"))
                ->distinct()
                ->whereNotNull('first_name')
                ->count();
                
            // Count applications with documents
            $withDocuments = (clone $baseQuery)->whereNotNull('document_paths')->whereRaw('JSON_LENGTH(document_paths) > 0')->count();
            
            // Count unique training types requested
            $uniqueTrainingTypes = (clone $baseQuery)->whereNotNull('training_type')->distinct()->count('training_type');
            
            // Count applications with email vs mobile only
            $withEmail = (clone $baseQuery)->whereNotNull('email')->where('email', '!=', '')->count();
            
            return [
                'total_applications' => $total,
                'approved_applications' => $approved,
                'rejected_applications' => $rejected,
                'pending_applications' => $pending,
                'approval_rate' => $this->calculateApprovalRate($total, $approved),
                'unique_applicants' => $uniqueApplicants,
                'with_documents' => $withDocuments,
                'document_submission_rate' => $total > 0 ? round(($withDocuments / $total) * 100, 2) : 0,
                'unique_training_types' => $uniqueTrainingTypes,
                'with_email' => $withEmail,
                'email_provision_rate' => $total > 0 ? round(($withEmail / $total) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Training Overview Statistics Error: ' . $e->getMessage());
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
            $defaultStatuses = ['approved' => 0, 'rejected' => 0, 'under_review' => 0];
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
            Log::error('Training Status Analysis Error: ' . $e->getMessage());
            return [
                'counts' => ['approved' => 0, 'rejected' => 0, 'under_review' => 0],
                'percentages' => ['approved' => 0, 'rejected' => 0, 'under_review' => 0],
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
            $trends = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('SUM(CASE WHEN document_paths IS NOT NULL AND JSON_LENGTH(document_paths) > 0 THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('COUNT(DISTINCT training_type) as unique_training_types')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            return $trends;
        } catch (\Exception $e) {
            Log::error('Training Monthly Trends Error: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get training type analysis
     */
    private function getTrainingTypeAnalysis($baseQuery)
    {
        try {
            $trainingStats = (clone $baseQuery)->select(
                    'training_type',
                    DB::raw('COUNT(*) as total_applications'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected'),
                    DB::raw('SUM(CASE WHEN status = "under_review" THEN 1 ELSE 0 END) as pending'),
                    DB::raw('COUNT(DISTINCT CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, ""))) as unique_applicants'),
                    DB::raw('SUM(CASE WHEN document_paths IS NOT NULL AND JSON_LENGTH(document_paths) > 0 THEN 1 ELSE 0 END) as with_documents'),
                    DB::raw('AVG(CASE WHEN status_updated_at IS NOT NULL AND created_at IS NOT NULL 
                             THEN DATEDIFF(status_updated_at, created_at) END) as avg_processing_days')
                )
                ->whereNotNull('training_type')
                ->where('training_type', '!=', '')
                ->groupBy('training_type')
                ->orderBy('total_applications', 'desc')
                ->get();
                
            return $trainingStats;
        } catch (\Exception $e) {
            Log::error('Training Type Analysis Error: ' . $e->getMessage());
            return collect([]);
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
            Log::error('Training Registration Patterns Error: ' . $e->getMessage());
            return [
                'day_of_week' => collect([]),
                'hourly' => collect([])
            ];
        }
    }
    
    /**
     * Get processing time analysis
     */
    private function getProcessingTimeAnalysis($baseQuery)
    {
        try {
            $processedApplications = (clone $baseQuery)->whereNotNull('status_updated_at')->get();
            
            $processingTimes = [];
            foreach ($processedApplications as $application) {
                if ($application->status_updated_at && $application->created_at) {
                    $processingTime = Carbon::parse($application->created_at)
                        ->diffInDays(Carbon::parse($application->status_updated_at));
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
            Log::error('Training Processing Time Error: ' . $e->getMessage());
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
     * Get document submission analysis
     */
    private function getDocumentAnalysis($baseQuery)
    {
        try {
            $withDocs = (clone $baseQuery)->whereNotNull('document_paths')->whereRaw('JSON_LENGTH(document_paths) > 0')->count();
            $withoutDocs = (clone $baseQuery)->where(function($q) {
                $q->whereNull('document_paths')->orWhereRaw('JSON_LENGTH(document_paths) = 0');
            })->count();
            
            $total = $withDocs + $withoutDocs;
            
            // Approval rates by document submission
            $approvalWithDocs = (clone $baseQuery)
                ->whereNotNull('document_paths')
                ->whereRaw('JSON_LENGTH(document_paths) > 0')
                ->where('status', 'approved')
                ->count();
                
            $approvalWithoutDocs = (clone $baseQuery)
                ->where(function($q) {
                    $q->whereNull('document_paths')->orWhereRaw('JSON_LENGTH(document_paths) = 0');
                })
                ->where('status', 'approved')
                ->count();
            
            return [
                'with_documents' => $withDocs,
                'without_documents' => $withoutDocs,
                'total' => $total,
                'submission_rate' => $total > 0 ? round(($withDocs / $total) * 100, 2) : 0,
                'approval_rate_with_docs' => $withDocs > 0 ? round(($approvalWithDocs / $withDocs) * 100, 2) : 0,
                'approval_rate_without_docs' => $withoutDocs > 0 ? round(($approvalWithoutDocs / $withoutDocs) * 100, 2) : 0
            ];
        } catch (\Exception $e) {
            Log::error('Training Document Analysis Error: ' . $e->getMessage());
            return [
                'with_documents' => 0,
                'without_documents' => 0,
                'total' => 0,
                'submission_rate' => 0,
                'approval_rate_with_docs' => 0,
                'approval_rate_without_docs' => 0
            ];
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
            
            // Quality score (based on document submission and approval rates)
            $overview = $this->getOverviewStatistics(clone $baseQuery);
            $docAnalysis = $this->getDocumentAnalysis(clone $baseQuery);
            $metrics['quality_score'] = round(
                ($overview['approval_rate'] * 0.6) + 
                ($docAnalysis['submission_rate'] * 0.4), 2
            );
            
            return $metrics;
        } catch (\Exception $e) {
            Log::error('Training Performance Metrics Error: ' . $e->getMessage());
            return [
                'completion_rate' => 0,
                'avg_applications_per_day' => 0,
                'quality_score' => 0
            ];
        }
    }
    
    /**
     * Get demographic analysis
     */
    private function getDemographicAnalysis($baseQuery)
    {
        try {
            // Most common first names
            $commonNames = (clone $baseQuery)
                ->select('first_name', DB::raw('COUNT(*) as count'))
                ->whereNotNull('first_name')
                ->where('first_name', '!=', '')
                ->groupBy('first_name')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
                
            // Application length patterns
            $applicationsByNameLength = (clone $baseQuery)
                ->select(
                    DB::raw('CASE 
                        WHEN LENGTH(CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name)) <= 20 THEN "Short"
                        WHEN LENGTH(CONCAT(first_name, " ", COALESCE(middle_name, ""), " ", last_name)) <= 35 THEN "Medium"
                        ELSE "Long" END as name_length_category'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('AVG(CASE WHEN status = "approved" THEN 1.0 ELSE 0.0 END) * 100 as approval_rate')
                )
                ->groupBy('name_length_category')
                ->get();
                
            return [
                'common_names' => $commonNames,
                'name_length_patterns' => $applicationsByNameLength
            ];
        } catch (\Exception $e) {
            Log::error('Training Demographic Analysis Error: ' . $e->getMessage());
            return [
                'common_names' => collect([]),
                'name_length_patterns' => collect([])
            ];
        }
    }
    
    /**
     * Get contact method analysis
     */
    private function getContactAnalysis($baseQuery)
    {
        try {
            $contactStats = [
                'email_only' => (clone $baseQuery)->whereNotNull('email')->where('email', '!=', '')->whereNull('mobile_number')->count(),
                'mobile_only' => (clone $baseQuery)->whereNotNull('mobile_number')->where('mobile_number', '!=', '')->whereNull('email')->count(),
                'both_contacts' => (clone $baseQuery)->whereNotNull('email')->where('email', '!=', '')->whereNotNull('mobile_number')->where('mobile_number', '!=', '')->count(),
                'no_contact' => (clone $baseQuery)->where(function($q) {
                    $q->whereNull('email')->orWhere('email', '');
                })->where(function($q) {
                    $q->whereNull('mobile_number')->orWhere('mobile_number', '');
                })->count()
            ];
            
            $total = array_sum($contactStats);
            $contactPercentages = [];
            foreach ($contactStats as $type => $count) {
                $contactPercentages[$type] = $total > 0 ? round(($count / $total) * 100, 2) : 0;
            }
            
            return [
                'stats' => $contactStats,
                'percentages' => $contactPercentages,
                'total' => $total
            ];
        } catch (\Exception $e) {
            Log::error('Training Contact Analysis Error: ' . $e->getMessage());
            return [
                'stats' => ['email_only' => 0, 'mobile_only' => 0, 'both_contacts' => 0, 'no_contact' => 0],
                'percentages' => ['email_only' => 0, 'mobile_only' => 0, 'both_contacts' => 0, 'no_contact' => 0],
                'total' => 0
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
            'with_documents' => 0,
            'document_submission_rate' => 0,
            'unique_training_types' => 0,
            'with_email' => 0,
            'email_provision_rate' => 0
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
            
            $baseQuery = TrainingApplication::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            
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
                'training_type_analysis' => $this->getTrainingTypeAnalysis(clone $baseQuery)->toArray(),
                'registration_patterns' => $this->getRegistrationPatterns(clone $baseQuery),
                'processing_time' => $this->getProcessingTimeAnalysis(clone $baseQuery),
                'document_analysis' => $this->getDocumentAnalysis(clone $baseQuery),
                'performance_metrics' => $this->getPerformanceMetrics(clone $baseQuery),
                'demographic_analysis' => $this->getDemographicAnalysis(clone $baseQuery),
                'contact_analysis' => $this->getContactAnalysis(clone $baseQuery)
            ];
            
            $filename = 'training-analytics-' . $startDate . '-to-' . $endDate . '.json';
            
            // Log activity
            $this->logActivity('exported', 'TrainingApplication', null, [
                'format' => 'JSON',
                'date_range' => ['start' => $startDate, 'end' => $endDate]
            ], 'Exported Training analytics data from ' . $startDate . ' to ' . $endDate);
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Training Export Error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting Training data: ' . $e->getMessage());
        }
    }
}
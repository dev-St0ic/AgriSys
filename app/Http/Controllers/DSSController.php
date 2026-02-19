<?php

namespace App\Http\Controllers;

use App\Services\DSSDataService;
use App\Services\DSSReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DSSController extends Controller
{
    protected DSSDataService $dataService;
    protected DSSReportService $reportService;

    public function __construct(DSSDataService $dataService, DSSReportService $reportService)
    {
        $this->dataService = $dataService;
        $this->reportService = $reportService;
    }

    /**
     * Show DSS report preview page
     */
    public function preview(Request $request)
    {
        try {
            // Check if this is an AJAX request for data loading
            if ($request->ajax()) {
                $response = $this->loadDataAjax($request);

                // Log activity only on successful data load
                if ($response->getData()->success ?? false) {
                    $service = $request->get('service', 'comprehensive');
                    $month = $request->get('month', now()->format('m'));
                    $year = $request->get('year', now()->format('Y'));
                    $monthYear = Carbon::createFromDate($year, $month, 1)->format('F Y');

                    $serviceTypes = [
                        'comprehensive' => 'Supplies',
                        'training' => 'Training',
                        'rsbsa' => 'RSBSA',
                        'fishr' => 'FishR',
                        'boatr' => 'BoatR'
                    ];

                    $serviceName = $serviceTypes[$service] ?? 'Comprehensive';

                    activity()
                        ->causedBy(auth()->user())
                        ->event('dss_report_viewed')
                        ->withProperties([
                            'report_type' => $serviceName,
                            'month' => $month,
                            'year' => $year,
                            'period' => $monthYear,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent()
                        ])
                        ->log("Viewed {$serviceName} DSS Report for {$monthYear}");
                }

                return $response;
            }

            // Get service type from request (default: comprehensive)
            $service = $request->get('service', 'comprehensive');

            // Get month and year from request or default to current
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));

            // For initial page load, show page with loading state
            return view('admin.dss.preview', compact('month', 'year', 'service'));

        } catch (\Exception $e) {
            Log::error('DSS Preview Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate DSS preview: ' . $e->getMessage());
        }
    }

    /**
     * Load DSS data via AJAX to prevent timeouts
     */
    public function loadDataAjax(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Set longer timeout for this operation
            set_time_limit(300); // 5 minutes

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $viewPartial = 'admin.dss.partials.training-content';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $viewPartial = 'admin.dss.partials.rsbsa-content';
            } elseif ($service === 'fishr') {
                $data = $this->dataService->collectFishrData($month, $year);
                $report = $this->reportService->generateFishrReport($data);
                $viewPartial = 'admin.dss.partials.fishr-content';
            } elseif ($service === 'boatr') {
                $data = $this->dataService->collectBoatrData($month, $year);
                $report = $this->reportService->generateBoatrReport($data);
                $viewPartial = 'admin.dss.partials.boatr-content';
            } else {
                // Comprehensive report (default)
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $viewPartial = 'admin.dss.partials.report-content';
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'report' => $report,
                'html' => view($viewPartial, compact('data', 'report'))->render()
            ]);

        } catch (\Exception $e) {
            Log::error('DSS AJAX Data Loading Failed', [
                'error' => $e->getMessage(),
                'month' => $request->get('month'),
                'year' => $request->get('year'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load DSS data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF report
     */
    public function downloadPDF(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $view = 'admin.dss.pdf-training';
                $filename = 'Training_DSS_Report_';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $view = 'admin.dss.pdf-rsbsa';
                $filename = 'RSBSA_DSS_Report_';
            } elseif ($service === 'fishr') {
                $data = $this->dataService->collectFishrData($month, $year);
                $report = $this->reportService->generateFishrReport($data);
                $view = 'admin.dss.pdf-fishr';
                $filename = 'FISHR_DSS_Report_';
            } elseif ($service === 'boatr') {
                $data = $this->dataService->collectBoatrData($month, $year);
                $report = $this->reportService->generateBoatrReport($data);
                $view = 'admin.dss.pdf-boatr';
                $filename = 'BOATR_DSS_Report_';
            } else {
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $view = 'admin.dss.pdf-report';
                $filename = 'Supplies_DSS_Report_';
            }

            // Generate PDF
            $pdf = Pdf::loadView($view, compact('data', 'report'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                ]);

            $filename .= Carbon::createFromDate($year, $month, 1)->format('Y_m') . '.pdf';

            // Log activity
            $monthYear = Carbon::createFromDate($year, $month, 1)->format('F Y');
            $serviceTypes = [
                'comprehensive' => 'Supplies',
                'training' => 'Training',
                'rsbsa' => 'RSBSA',
                'fishr' => 'FishR',
                'boatr' => 'BoatR'
            ];
            $serviceName = $serviceTypes[$service] ?? 'Comprehensive';

            activity()
                ->causedBy(auth()->user())
                ->event('dss_report_downloaded')
                ->withProperties([
                    'report_type' => $serviceName,
                    'month' => $month,
                    'year' => $year,
                    'period' => $monthYear,
                    'filename' => $filename,
                    'format' => 'PDF',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ])
                ->log("Downloaded {$serviceName} DSS Report (PDF) for {$monthYear}");

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('DSS PDF Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate PDF report: ' . $e->getMessage());
        }
    }

    /**
     * Generate and download Word document
     */
    public function downloadWord(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('m'));
            $year = $request->get('year', now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Collect data based on service type
            if ($service === 'training') {
                $data = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
                $filename = 'Training_DSS_Report_';
            } elseif ($service === 'rsbsa') {
                $data = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
                $filename = 'RSBSA_DSS_Report_';
            } elseif ($service === 'fishr') {
                $data = $this->dataService->collectFishrData($month, $year);
                $report = $this->reportService->generateFishrReport($data);
                $filename = 'FISHR_DSS_Report_';
            } elseif ($service === 'boatr') {
                $data = $this->dataService->collectBoatrData($month, $year);
                $report = $this->reportService->generateBoatrReport($data);
                $filename = 'BOATR_DSS_Report_';
            } else {
                $data = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
                $filename = 'Supplies_DSS_Report_';
            }

            // Create Word document content
            $wordContent = $this->generateWordContent($data, $report);

            $filename .= Carbon::createFromDate($year, $month, 1)->format('Y_m') . '.doc';

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('DSS Word Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate Word document: ' . $e->getMessage());
        }
    }

    /**
     * Get fresh data via AJAX
     */
    public function refreshData(Request $request)
    {
        try {
            $month   = $request->get('month', now()->format('m'));
            $year    = $request->get('year',  now()->format('Y'));
            $service = $request->get('service', 'comprehensive');

            // Clear ALL caches once
            $this->dataService->clearCache($month, $year);
            \Cache::flush();

            if ($service === 'training') {
                $data   = $this->dataService->collectTrainingData($month, $year);
                $report = $this->reportService->generateTrainingReport($data);
            } elseif ($service === 'rsbsa') {
                $data   = $this->dataService->collectRsbsaData($month, $year);
                $report = $this->reportService->generateRsbsaReport($data);
            } elseif ($service === 'fishr') {
                $data   = $this->dataService->collectFishrData($month, $year);
                $report = $this->reportService->generateFishrReport($data);
            } elseif ($service === 'boatr') {
                $data   = $this->dataService->collectBoatrData($month, $year);
                $report = $this->reportService->generateBoatrReport($data);
            } else {
                $data   = $this->dataService->collectMonthlyData($month, $year);
                $report = $this->reportService->generateReport($data);
            }

            return response()->json([
                'success' => true,
                'data'    => $data,
                'report'  => $report,
                'message' => 'Data refreshed successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('DSS Data Refresh Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Word document content
     */
    private function generateWordContent(array $data, array $report): string
    {
        $reportData = $report['report_data'];
        $period = $data['period']['month'];

        $html = "
        <html>
        <head>
            <meta charset='utf-8'>
            <title>DSS Report - {$period}</title>
        </head>
        <body style='font-family: Arial, sans-serif; margin: 20px;'>
            <h1 style='color: #2E7D32; text-align: center;'>Decision Support System Report</h1>
            <h2 style='color: #4CAF50; text-align: center;'>{$period}</h2>
            <hr>

            <h3>Executive Summary</h3>
            <p>{$reportData['executive_summary']}</p>

            <h3>Performance Assessment</h3>
            <p><strong>Overall Rating:</strong> {$reportData['performance_assessment']['overall_rating']}</p>
            <p><strong>Approval Efficiency:</strong> {$reportData['performance_assessment']['approval_efficiency']}</p>
            <p><strong>Supply Adequacy:</strong> {$reportData['performance_assessment']['supply_adequacy']}</p>
            <p><strong>Geographic Coverage:</strong> {$reportData['performance_assessment']['geographic_coverage']}</p>

            <h3>Key Findings</h3>
            <ul>";

        foreach ($reportData['key_findings'] as $finding) {
            $html .= "<li>{$finding}</li>";
        }

        $html .= "</ul>

            <h3>Critical Issues</h3>
            <ul>";

        foreach ($reportData['critical_issues'] as $issue) {
            $html .= "<li>{$issue}</li>";
        }

        $html .= "</ul>

            <h3>Recommendations</h3>

            <h4>Immediate Actions</h4>
            <ul>";

        if (isset($reportData['recommendations']['immediate_actions'])) {
            foreach ($reportData['recommendations']['immediate_actions'] as $action) {
                $html .= "<li>{$action}</li>";
            }
        }

        $html .= "</ul>

            <h4>Short-term Strategies</h4>
            <ul>";

        if (isset($reportData['recommendations']['short_term_strategies'])) {
            foreach ($reportData['recommendations']['short_term_strategies'] as $strategy) {
                $html .= "<li>{$strategy}</li>";
            }
        }

        $html .= "</ul>

            <hr>
            <p style='font-size: 12px; color: #666;'>
                Report generated on " . now()->format('F j, Y \a\t g:i A') . "<br>
                Source: {$report['source']}<br>
                Confidence Level: {$reportData['confidence_level']}" .
                (isset($reportData['confidence_score']) ? " ({$reportData['confidence_score']}%)" : "") . "
            </p>
        </body>
        </html>";

        return $html;
    }

    /**
     * Get available months and years for dropdown
     */
    public function getAvailablePeriods()
    {
        $periods = [];
        $currentDate = now();

        // Generate last 12 months
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $periods[] = [
                'month' => $date->format('m'),
                'year' => $date->format('Y'),
                'label' => $date->format('F Y'),
                'value' => $date->format('Y-m')
            ];
        }

        return response()->json($periods);
    }

    /**
     * Get latest report metadata for a specific service
     */
    public static function getLatestReportMetadata(string $service)
    {
        try {
            $cacheKey = "dss_report_{$service}_" . now()->format('Y_m');

            if (\Cache::has($cacheKey)) {
                $cachedData = \Cache::get($cacheKey);

                return [
                    'exists' => true,
                    'generated_at' => $cachedData['generated_at'] ?? now()->format('Y-m-d H:i:s'),
                    'source' => $cachedData['report']['source'] ?? 'Not Available',
                    'period_start' => now()->startOfMonth()->format('Y-m-d'),
                    'period_end' => now()->endOfMonth()->format('Y-m-d'),
                    'period_label' => now()->format('F Y')
                ];
            }

            return [
                'exists' => false,
                'message' => 'No report generated yet for this period'
            ];

        } catch (\Exception $e) {
            return [
                'exists' => false,
                'message' => 'Unable to fetch report metadata'
            ];
        }
    }
}

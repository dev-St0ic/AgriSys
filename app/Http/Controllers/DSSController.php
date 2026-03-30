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
        $this->dataService   = $dataService;
        $this->reportService = $reportService;
    }

    /**
     * Show DSS report preview page
     */
    public function preview(Request $request)
    {
        try {
            if ($request->ajax()) {
                $response = $this->loadDataAjax($request);

                if ($response->getData()->success ?? false) {
                    $service    = $request->get('service', 'comprehensive');
                    $periodMode = $request->get('period_mode', 'monthly');
                    $year       = $request->get('year', now()->format('Y'));

                    if ($periodMode === 'quarterly') {
                        $quarter     = $request->get('quarter', (string) ceil(now()->month / 3));
                        $periodLabel = "Q{$quarter} {$year}";
                    } else {
                        $month       = $request->get('month', now()->format('m'));
                        $periodLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');
                    }

                    $serviceTypes = [
                        'comprehensive' => 'Supplies',
                        'training'      => 'Training',
                        'rsbsa'         => 'RSBSA',
                        'fishr'         => 'FishR',
                        'boatr'         => 'BoatR',
                    ];
                    $serviceName = $serviceTypes[$service] ?? 'Comprehensive';

                    activity()
                        ->causedBy(auth()->user())
                        ->event('dss_report_viewed')
                        ->withProperties([
                            'report_type' => $serviceName,
                            'period_mode' => $periodMode,
                            'period'      => $periodLabel,
                            'ip_address'  => $request->ip(),
                            'user_agent'  => $request->userAgent(),
                        ])
                        ->log("Viewed {$serviceName} DSS Report for {$periodLabel}");
                }

                return $response;
            }

            $service    = $request->get('service', 'comprehensive');
            $month      = $request->get('month', now()->format('m'));
            $year       = $request->get('year', now()->format('Y'));
            $periodMode = $request->get('period_mode', 'monthly');
            $quarter    = $request->get('quarter', (string) ceil(now()->month / 3));

            return view('admin.dss.preview', compact('month', 'year', 'service', 'periodMode', 'quarter'));

        } catch (\Exception $e) {
            Log::error('DSS Preview Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to generate DSS preview: ' . $e->getMessage());
        }
    }

    /**
     * Load DSS data via AJAX
     */
    public function loadDataAjax(Request $request)
    {
        try {
            $month      = $request->get('month', now()->format('m'));
            $year       = $request->get('year', now()->format('Y'));
            $service    = $request->get('service', 'comprehensive');
            $periodMode = $request->get('period_mode', 'monthly');
            $quarter    = $request->get('quarter', null);

            set_time_limit(300);

            [$data, $report, $viewPartial] = $this->resolveServiceData(
                $service, $month, $year, $periodMode, $quarter
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
                'report'  => $report,
                'html'    => view($viewPartial, compact('data', 'report'))->render(),
            ]);

        } catch (\Exception $e) {
            Log::error('DSS AJAX Data Loading Failed', [
                'error'       => $e->getMessage(),
                'month'       => $request->get('month'),
                'year'        => $request->get('year'),
                'period_mode' => $request->get('period_mode'),
                'quarter'     => $request->get('quarter'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load DSS data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate and download PDF report
     */
    public function downloadPDF(Request $request)
    {
        try {
            $month      = $request->get('month', now()->format('m'));
            $year       = $request->get('year', now()->format('Y'));
            $service    = $request->get('service', 'comprehensive');
            $periodMode = $request->get('period_mode', 'monthly');
            $quarter    = $request->get('quarter', null);

            [$data, $report] = $this->resolveServiceData(
                $service, $month, $year, $periodMode, $quarter
            );

            $viewMap = [
                'training'     => 'admin.dss.pdf-training',
                'rsbsa'        => 'admin.dss.pdf-rsbsa',
                'fishr'        => 'admin.dss.pdf-fishr',
                'boatr'        => 'admin.dss.pdf-boatr',
                'comprehensive'=> 'admin.dss.pdf-report',
            ];
            $view = $viewMap[$service] ?? 'admin.dss.pdf-report';

            $pdf = Pdf::loadView($view, compact('data', 'report'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont'         => 'Arial',
                    'isRemoteEnabled'     => true,
                    'isHtml5ParserEnabled'=> true,
                ]);

            $periodLabel = $data['period']['month'];
            $filename    = $this->buildFilename($service, $periodMode, $year, $month, $quarter, 'pdf');

            $serviceTypes = [
                'comprehensive' => 'Supplies',
                'training'      => 'Training',
                'rsbsa'         => 'RSBSA',
                'fishr'         => 'FishR',
                'boatr'         => 'BoatR',
            ];
            $serviceName = $serviceTypes[$service] ?? 'Comprehensive';

            activity()
                ->causedBy(auth()->user())
                ->event('dss_report_downloaded')
                ->withProperties([
                    'report_type' => $serviceName,
                    'period_mode' => $periodMode,
                    'period'      => $periodLabel,
                    'filename'    => $filename,
                    'format'      => 'PDF',
                    'ip_address'  => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                ])
                ->log("Downloaded {$serviceName} DSS Report (PDF) for {$periodLabel}");

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('DSS PDF Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
            $month      = $request->get('month', now()->format('m'));
            $year       = $request->get('year', now()->format('Y'));
            $service    = $request->get('service', 'comprehensive');
            $periodMode = $request->get('period_mode', 'monthly');
            $quarter    = $request->get('quarter', null);

            [$data, $report] = $this->resolveServiceData(
                $service, $month, $year, $periodMode, $quarter
            );

            $wordContent = $this->generateWordContent($data, $report);
            $filename    = $this->buildFilename($service, $periodMode, $year, $month, $quarter, 'doc');

            return response($wordContent)
                ->header('Content-Type', 'application/msword')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            Log::error('DSS Word Generation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to generate Word document: ' . $e->getMessage());
        }
    }

    /**
     * Refresh data — bypasses cache
     */
    public function refreshData(Request $request)
    {
        try {
            $month      = $request->get('month', now()->format('m'));
            $year       = $request->get('year', now()->format('Y'));
            $service    = $request->get('service', 'comprehensive');
            $periodMode = $request->get('period_mode', 'monthly');
            $quarter    = $request->get('quarter', null);

            // Clear the specific period cache then flush everything
            $this->dataService->clearCache($month, $year, $periodMode, $quarter);
            \Cache::flush();

            [$data, $report] = $this->resolveServiceData(
                $service, $month, $year, $periodMode, $quarter
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
                'report'  => $report,
                'message' => 'Data refreshed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('DSS Data Refresh Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Collect data + generate report for the given service and period.
     * Returns [$data, $report] — or [$data, $report, $viewPartial] when
     * called from loadDataAjax (the third element is always present but
     * callers that don't need it can simply ignore it via list assignment).
     */
    private function resolveServiceData(
        string $service,
        string $month,
        string $year,
        string $periodMode = 'monthly',
        ?string $quarter   = null
    ): array {
        $views = [
            'training'      => 'admin.dss.partials.training-content',
            'rsbsa'         => 'admin.dss.partials.rsbsa-content',
            'fishr'         => 'admin.dss.partials.fishr-content',
            'boatr'         => 'admin.dss.partials.boatr-content',
            'comprehensive' => 'admin.dss.partials.report-content',
        ];

        switch ($service) {
            case 'training':
                $data   = $this->dataService->collectTrainingData($month, $year, $periodMode, $quarter);
                $report = $this->reportService->generateTrainingReport($data);
                break;
            case 'rsbsa':
                $data   = $this->dataService->collectRsbsaData($month, $year, $periodMode, $quarter);
                $report = $this->reportService->generateRsbsaReport($data);
                break;
            case 'fishr':
                $data   = $this->dataService->collectFishrData($month, $year, $periodMode, $quarter);
                $report = $this->reportService->generateFishrReport($data);
                break;
            case 'boatr':
                $data   = $this->dataService->collectBoatrData($month, $year, $periodMode, $quarter);
                $report = $this->reportService->generateBoatrReport($data);
                break;
            default:
                $data   = $this->dataService->collectMonthlyData($month, $year, $periodMode, $quarter);
                $report = $this->reportService->generateReport($data);
                break;
        }

        $viewPartial = $views[$service] ?? $views['comprehensive'];

        return [$data, $report, $viewPartial];
    }

    /**
     * Build a consistent download filename for PDF and Word exports.
     */
    private function buildFilename(
        string  $service,
        string  $periodMode,
        string  $year,
        string  $month,
        ?string $quarter,
        string  $extension
    ): string {
        $prefixes = [
            'training'      => 'Training_DSS_Report_',
            'rsbsa'         => 'RSBSA_DSS_Report_',
            'fishr'         => 'FISHR_DSS_Report_',
            'boatr'         => 'BOATR_DSS_Report_',
            'comprehensive' => 'Supplies_DSS_Report_',
        ];
        $prefix = $prefixes[$service] ?? 'Supplies_DSS_Report_';

        $suffix = $periodMode === 'quarterly'
            ? "Q{$quarter}_{$year}"
            : Carbon::createFromDate($year, $month, 1)->format('Y_m');

        return "{$prefix}{$suffix}.{$extension}";
    }

    /**
     * Generate Word document HTML content
     */
    private function generateWordContent(array $data, array $report): string
    {
        $reportData  = $report['report_data'];
        $period      = $data['period']['month'];
        $periodMode  = $data['period']['period_mode'] ?? 'monthly';

        $pa                 = $reportData['performance_assessment'] ?? [];
        $overallRating      = $pa['overall_rating']              ?? 'N/A';
        $approvalEfficiency = $pa['approval_efficiency']         ?? 'N/A';
        $supplyAdequacy     = $pa['supply_adequacy']             ?? null;
        $geoCoverage        = $pa['geographic_coverage']         ?? null;
        $registrationEff    = $pa['registration_efficiency']     ?? null;
        $agriDiversity      = $pa['agricultural_diversity']      ?? null;
        $landUtilization    = $pa['land_utilization']            ?? null;
        $trainingDiversity  = $pa['training_diversity']          ?? null;
        $geographicReach    = $pa['geographic_reach']            ?? null;
        $coverageAdequacy   = $pa['coverage_adequacy']           ?? null;
        $inspectionEffect   = $pa['inspection_effectiveness']    ?? null;
        $trendAnalysis      = $pa['trend_analysis']              ?? null;

        $periodTypeLabel = $periodMode === 'quarterly' ? 'Quarterly Report' : 'Monthly Report';

        $html = "
        <html>
        <head>
            <meta charset='utf-8'>
            <title>DSS Report - {$period}</title>
        </head>
        <body style='font-family: Arial, sans-serif; margin: 20px;'>
            <h1 style='color: #2E7D32; text-align: center;'>Decision Support System Report</h1>
            <h2 style='color: #4CAF50; text-align: center;'>{$period}</h2>
            <p style='text-align: center; color: #666; font-size: 13px;'>{$periodTypeLabel}</p>
            <hr>

            <h3>Executive Summary</h3>
            <p>" . ($reportData['executive_summary'] ?? 'N/A') . "</p>

            <h3>Performance Assessment</h3>
            <p><strong>Overall Rating:</strong> {$overallRating}</p>
            <p><strong>Approval Efficiency:</strong> {$approvalEfficiency}</p>";

        if ($supplyAdequacy)    $html .= "<p><strong>Supply Adequacy:</strong> {$supplyAdequacy}</p>";
        if ($geoCoverage)       $html .= "<p><strong>Geographic Coverage:</strong> {$geoCoverage}</p>";
        if ($registrationEff)   $html .= "<p><strong>Registration Efficiency:</strong> {$registrationEff}</p>";
        if ($agriDiversity)     $html .= "<p><strong>Agricultural Diversity:</strong> {$agriDiversity}</p>";
        if ($landUtilization)   $html .= "<p><strong>Land Utilization:</strong> {$landUtilization}</p>";
        if ($trainingDiversity) $html .= "<p><strong>Training Diversity:</strong> {$trainingDiversity}</p>";
        if ($geographicReach)   $html .= "<p><strong>Geographic Reach:</strong> {$geographicReach}</p>";
        if ($coverageAdequacy)  $html .= "<p><strong>Coverage Adequacy:</strong> {$coverageAdequacy}</p>";
        if ($inspectionEffect)  $html .= "<p><strong>Inspection Effectiveness:</strong> {$inspectionEffect}</p>";
        if ($trendAnalysis)     $html .= "<p><strong>Trend Analysis:</strong> {$trendAnalysis}</p>";

        $html .= "<h3>Key Findings</h3><ul>";
        foreach (($reportData['key_findings'] ?? []) as $finding) {
            $html .= "<li>{$finding}</li>";
        }

        $html .= "</ul><h3>Critical Issues</h3><ul>";
        foreach (($reportData['critical_issues'] ?? []) as $issue) {
            $html .= "<li>{$issue}</li>";
        }

        $html .= "</ul><h3>Recommendations</h3><h4>Immediate Actions</h4><ul>";
        foreach (($reportData['recommendations']['immediate_actions'] ?? []) as $action) {
            $html .= "<li>{$action}</li>";
        }

        $html .= "</ul><h4>Short-term Strategies</h4><ul>";
        foreach (($reportData['recommendations']['short_term_strategies'] ?? []) as $strategy) {
            $html .= "<li>{$strategy}</li>";
        }

        $html .= "</ul>
            <hr>
            <p style='font-size: 12px; color: #666;'>
                Report generated on " . now()->format('F j, Y \a\t g:i A') . "<br>
                Source: " . ($report['source'] ?? 'N/A') . "<br>
                Confidence Level: " . (isset($reportData['confidence_score'])
                    ? $reportData['confidence_score'] . '%'
                    : ($reportData['confidence_level'] ?? 'N/A')) . "
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

        for ($i = 0; $i < 12; $i++) {
            $date      = $currentDate->copy()->subMonths($i);
            $periods[] = [
                'month' => $date->format('m'),
                'year'  => $date->format('Y'),
                'label' => $date->format('F Y'),
                'value' => $date->format('Y-m'),
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
                    'exists'       => true,
                    'generated_at' => $cachedData['generated_at'] ?? now()->format('Y-m-d H:i:s'),
                    'source'       => $cachedData['report']['source'] ?? 'Not Available',
                    'period_start' => now()->startOfMonth()->format('Y-m-d'),
                    'period_end'   => now()->endOfMonth()->format('Y-m-d'),
                    'period_label' => now()->format('F Y'),
                ];
            }

            return [
                'exists'  => false,
                'message' => 'No report generated yet for this period',
            ];

        } catch (\Exception $e) {
            return [
                'exists'  => false,
                'message' => 'Unable to fetch report metadata',
            ];
        }
    }
}
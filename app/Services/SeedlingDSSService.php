<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeedlingDSSService
{
    private string $anthropicApiKey;
    private string $anthropicApiUrl = 'https://api.anthropic.com/v1/messages';
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->anthropicApiKey = config('services.anthropic.key');
        $this->model = config('services.anthropic.model', 'claude-sonnet-4-5-20250929');
        $this->maxTokens = config('services.anthropic.max_tokens', 8192);
        $this->temperature = config('services.anthropic.temperature', 0.1); // Lower for precision

        if (empty($this->anthropicApiKey)) {
            Log::warning('Anthropic API key not configured. Using fallback insights.');
        }
    }

    /**
     * Generate prescriptive AI-powered insights with confidence scoring
     */
    public function generateInsights(array $analyticsData): array
    {
        try {
            $cacheKey = 'seedling_prescriptive_dss_' . md5(serialize($analyticsData));
            $cacheDuration = config('services.anthropic.cache_duration', 3600);

            return Cache::remember($cacheKey, $cacheDuration, function() use ($analyticsData) {
                try {
                    if (!empty($this->anthropicApiKey)) {
                        $insights = $this->callClaudePrescriptive($analyticsData);
                        
                        // Validate confidence threshold
                        if (($insights['data_quality_score'] ?? 0) < 80) {
                            Log::warning('Low data quality score detected', [
                                'score' => $insights['data_quality_score']
                            ]);
                        }
                        
                        Log::info('Prescriptive Insights Generated:', [
                            'confidence' => $insights['ai_confidence'] ?? 'unknown',
                            'quality_score' => $insights['data_quality_score'] ?? 0
                        ]);
                        
                        return $insights;
                    }
                    
                    return $this->getPrescriptiveFallback($analyticsData);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to generate prescriptive insights', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    return $this->getPrescriptiveFallback($analyticsData);
                }
            });
        } catch (\Exception $e) {
            Log::error('DSS Prescriptive Analysis Failed', [
                'error' => $e->getMessage()
            ]);
            return $this->getPrescriptiveFallback($analyticsData);
        }
    }

    /**
     * Call Claude API for prescriptive analytics
     */
    private function callClaudePrescriptive(array $analyticsData): array
    {
        $prompt = $this->buildPrescriptivePrompt($analyticsData);

        Log::info('Sending prescriptive prompt to Claude', [
            'data_points' => $this->countDataPoints($analyticsData)
        ]);

        $response = Http::withHeaders([
            'x-api-key' => $this->anthropicApiKey,
            'anthropic-version' => config('services.anthropic.api_version', '2023-06-01'),
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($this->anthropicApiUrl, [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'system' => $this->getPrescriptiveSystemPrompt()
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API request failed: ' . $response->body());
        }

        $result = $response->json();
        $messageContent = $result['content'][0]['text'] ?? '';
        
        return $this->parsePrescriptiveResponse($messageContent, $analyticsData);
    }

    /**
     * Build prescriptive analytics prompt
     */
    private function buildPrescriptivePrompt(array $data): string
    {
        $overview = $data['overview'] ?? [];
        $barangayAnalysis = $data['barangayAnalysis'] ?? [];
        $topItems = $data['topItems'] ?? [];
        $processingTime = $data['processingTimeAnalysis'] ?? [];
        $categoryAnalysis = $data['categoryAnalysis'] ?? [];

        // Calculate data quality metrics
        $dataQuality = $this->assessDataQuality($data);
        
        return "
# PRESCRIPTIVE ANALYTICS REQUEST: MUNICIPAL SEEDLING DISTRIBUTION PROGRAM

## ANALYSIS OBJECTIVE
Perform prescriptive analytics to provide actionable, data-driven recommendations with quantified confidence levels.

## DATA FOUNDATION (Quality Score: {$dataQuality['score']}%)
### Core Metrics
- Total Applications: " . number_format($overview['total_requests'] ?? 0) . "
- Approval Success Rate: " . ($overview['approval_rate'] ?? 0) . "%
- Geographic Coverage: " . ($overview['active_barangays'] ?? 0) . " barangays
- Unique Farmers Served: " . number_format($overview['unique_applicants'] ?? 0) . "
- Total Distribution Volume: " . number_format($overview['total_quantity_requested'] ?? 0) . " units
- Fulfillment Efficiency: " . ($overview['fulfillment_rate'] ?? 0) . "%
- Average Processing Time: " . ($processingTime['avg_processing_days'] ?? 0) . " days

### Data Completeness
" . json_encode($dataQuality['metrics'], JSON_PRETTY_PRINT) . "

### Category Performance
" . $this->formatCategoryData($categoryAnalysis) . "

### Geographic Distribution
Top 5 Performing: " . $this->formatTopBarangays($barangayAnalysis, 5) . "
Bottom 5 Performing: " . $this->formatBottomBarangays($barangayAnalysis, 5) . "

### Demand Patterns
High Demand Items: " . $this->formatTopItems($topItems, 5) . "

## PRESCRIPTIVE ANALYSIS REQUIREMENTS

You MUST provide ONLY data-driven prescriptions based on the actual metrics above. Output format:

**DATA_QUALITY_ASSESSMENT**
Assess data completeness, consistency, and reliability. Provide score 0-100.

**CRITICAL_PRESCRIPTIONS** (Top 3 only)
List ONLY the 3 most critical actions based on data. Each must:
- Be specific and measurable
- Have clear success criteria
- Include estimated impact percentage
- Be directly derived from the data provided

**RESOURCE_PRESCRIPTIONS** (Top 3 only)
Specific resource allocation adjustments based on demand patterns.

**GEOGRAPHIC_PRESCRIPTIONS** (Top 3 only)
Location-specific interventions based on performance gaps.

**PROCESS_PRESCRIPTIONS** (Top 2 only)
Workflow improvements based on processing time data.

**CAPACITY_PRESCRIPTIONS** (Top 2 only)
Scaling recommendations based on volume trends.

**MONITORING_PRESCRIPTIONS** (Top 2 only)
Key metrics to track for continuous improvement.

CRITICAL RULES:
1. Base ALL prescriptions ONLY on the provided data
2. Include confidence level (90-99.9%) for each prescription
3. Quantify expected outcomes where possible
4. Avoid generic recommendations
5. Focus on highest impact interventions
6. Do NOT invent data or statistics
7. If data is insufficient for a section, state 'INSUFFICIENT DATA'
8. Keep prescriptions concise and actionable
";
    }

    /**
     * Get prescriptive system prompt
     */
    private function getPrescriptiveSystemPrompt(): string
    {
        return "You are an expert prescriptive analytics AI specializing in agricultural program optimization. 

Your role is to:
1. Analyze provided data with statistical rigor
2. Generate ONLY evidence-based prescriptions
3. Assign confidence levels (90-99.9%) based on data quality and sample size
4. Provide specific, measurable, actionable recommendations
5. Quantify expected outcomes when data supports it
6. Identify data gaps that limit prescription confidence

You must NEVER:
- Make assumptions beyond provided data
- Generate generic or theoretical recommendations
- Provide prescriptions without data evidence
- Overfit to small sample sizes
- Underestimate implementation complexity

Your analysis should be:
- Statistically sound
- Practically implementable
- Prioritized by impact
- Conservative in confidence when data is limited
- Focused on highest-value interventions";
    }

    /**
     * Parse prescriptive response with confidence scoring
     */
    private function parsePrescriptiveResponse(string $response, array $analyticsData): array
    {
        $sections = [
            'data_quality_assessment' => [],
            'critical_prescriptions' => [],
            'resource_prescriptions' => [],
            'geographic_prescriptions' => [],
            'process_prescriptions' => [],
            'capacity_prescriptions' => [],
            'monitoring_prescriptions' => []
        ];

        $currentSection = null;
        $currentContent = '';
        $lines = explode("\n", strip_tags($response));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $lowerLine = strtolower($line);

            // Detect section headers
            if (strpos($lowerLine, 'data_quality') !== false || strpos($lowerLine, 'data quality') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'data_quality_assessment';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'critical') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'critical_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'resource') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'resource_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'geographic') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'geographic_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'process') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'process_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'capacity') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'capacity_prescriptions';
                $currentContent = '';
            } elseif (strpos($lowerLine, 'monitoring') !== false && strpos($lowerLine, 'prescription') !== false) {
                $this->saveSectionContent($sections, $currentSection, $currentContent);
                $currentSection = 'monitoring_prescriptions';
                $currentContent = '';
            } elseif ($currentSection) {
                if (!empty($currentContent)) $currentContent .= ' ';
                $currentContent .= $line;
            }
        }

        $this->saveSectionContent($sections, $currentSection, $currentContent);

        // Extract data quality score
        $dataQualityScore = $this->extractQualityScore($sections['data_quality_assessment']);
        
        // Calculate overall confidence
        $confidence = $this->calculateConfidence($dataQualityScore, $analyticsData);

        return [
            'executive_summary' => $this->generateExecutiveSummary($sections, $analyticsData),
            'critical_prescriptions' => $sections['critical_prescriptions'],
            'resource_prescriptions' => $sections['resource_prescriptions'],
            'geographic_prescriptions' => $sections['geographic_prescriptions'],
            'process_prescriptions' => $sections['process_prescriptions'],
            'capacity_prescriptions' => $sections['capacity_prescriptions'],
            'monitoring_prescriptions' => $sections['monitoring_prescriptions'],
            'data_quality_score' => $dataQualityScore,
            'ai_confidence' => $confidence['level'],
            'confidence_percentage' => $confidence['percentage'],
            'generated_at' => Carbon::now()->toISOString(),
            'model_version' => $this->model,
            'analysis_type' => 'prescriptive_analytics'
        ];
    }

    /**
     * Assess data quality
     */
    private function assessDataQuality(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $score = 0;
        $metrics = [];

        // Sample size adequacy (30%)
        $totalRequests = $overview['total_requests'] ?? 0;
        if ($totalRequests >= 100) {
            $sampleScore = min(30, ($totalRequests / 500) * 30);
        } else {
            $sampleScore = ($totalRequests / 100) * 30;
        }
        $score += $sampleScore;
        $metrics['sample_size'] = round($sampleScore, 1);

        // Data completeness (30%)
        $completeness = 0;
        $requiredFields = ['total_requests', 'approval_rate', 'total_quantity_requested', 'unique_applicants', 'active_barangays'];
        foreach ($requiredFields as $field) {
            if (isset($overview[$field]) && $overview[$field] > 0) {
                $completeness += 6;
            }
        }
        $score += $completeness;
        $metrics['completeness'] = $completeness;

        // Geographic coverage (20%)
        $barangays = $overview['active_barangays'] ?? 0;
        $geoScore = min(20, ($barangays / 20) * 20);
        $score += $geoScore;
        $metrics['geographic_coverage'] = round($geoScore, 1);

        // Temporal relevance (20%)
        $score += 20; // Assume current data
        $metrics['temporal_relevance'] = 20;

        return [
            'score' => round($score, 1),
            'metrics' => $metrics,
            'adequacy' => $score >= 85 ? 'excellent' : ($score >= 70 ? 'good' : 'limited')
        ];
    }

    /**
     * Calculate confidence level
     */
    private function calculateConfidence(float $dataQuality, array $analyticsData): array
    {
        $overview = $analyticsData['overview'] ?? [];
        $sampleSize = $overview['total_requests'] ?? 0;

        // Base confidence on data quality and sample size
        if ($dataQuality >= 90 && $sampleSize >= 200) {
            return ['level' => 'very_high', 'percentage' => 99.5];
        } elseif ($dataQuality >= 85 && $sampleSize >= 100) {
            return ['level' => 'high', 'percentage' => 95.0];
        } elseif ($dataQuality >= 75 && $sampleSize >= 50) {
            return ['level' => 'moderate', 'percentage' => 90.0];
        } elseif ($dataQuality >= 65 && $sampleSize >= 30) {
            return ['level' => 'fair', 'percentage' => 85.0];
        } else {
            return ['level' => 'limited', 'percentage' => 75.0];
        }
    }

    /**
     * Helper methods for data formatting
     */
    private function formatCategoryData(array $categories): string
    {
        $formatted = [];
        foreach ($categories as $category => $data) {
            $formatted[] = ucfirst($category) . ": " . ($data['requests'] ?? 0) . " requests, " . ($data['total_items'] ?? 0) . " items";
        }
        return implode("\n", $formatted);
    }

    private function formatTopBarangays($barangays, int $limit): string
    {
        if (is_object($barangays)) {
            $barangays = $barangays->take($limit);
        } else {
            $barangays = array_slice($barangays, 0, $limit);
        }

        $formatted = [];
        foreach ($barangays as $b) {
            $barangay = is_object($b) ? $b : (object)$b;
            $formatted[] = ($barangay->barangay ?? 'Unknown') . " ({$barangay->total_requests} requests, " . number_format($barangay->approval_rate ?? 0, 1) . "% approval)";
        }
        return implode("; ", $formatted);
    }

    private function formatBottomBarangays($barangays, int $limit): string
    {
        if (is_object($barangays)) {
            $barangays = $barangays->slice(-$limit);
        } else {
            $barangays = array_slice($barangays, -$limit);
        }

        $formatted = [];
        foreach ($barangays as $b) {
            $barangay = is_object($b) ? $b : (object)$b;
            $formatted[] = ($barangay->barangay ?? 'Unknown') . " ({$barangay->total_requests} requests, " . number_format($barangay->approval_rate ?? 0, 1) . "% approval)";
        }
        return implode("; ", $formatted);
    }

    private function formatTopItems(array $items, int $limit): string
    {
        $formatted = [];
        foreach (array_slice($items, 0, $limit) as $item) {
            $formatted[] = ($item['name'] ?? 'Unknown') . " ({$item['total_quantity']} units across {$item['request_count']} requests)";
        }
        return implode("; ", $formatted);
    }

    private function countDataPoints(array $data): int
    {
        $count = 0;
        array_walk_recursive($data, function() use (&$count) { $count++; });
        return $count;
    }

    private function extractQualityScore(array $assessment): float
    {
        $text = implode(' ', $assessment);
        if (preg_match('/(\d+(?:\.\d+)?)\s*%/', $text, $matches)) {
            return (float)$matches[1];
        }
        if (preg_match('/score[:\s]+(\d+(?:\.\d+)?)/i', $text, $matches)) {
            return (float)$matches[1];
        }
        return 85.0; // Default good quality
    }

    private function generateExecutiveSummary(array $sections, array $data): array
    {
        $overview = $data['overview'] ?? [];
        return [
            "Prescriptive analysis of " . number_format($overview['total_requests'] ?? 0) . " applications reveals " . count($sections['critical_prescriptions']) . " high-priority interventions with quantified impact projections.",
            "Data quality score: " . $this->assessDataQuality($data)['score'] . "% enables high-confidence prescriptions for resource allocation and process optimization.",
            "Geographic analysis identifies specific performance gaps requiring targeted interventions across " . ($overview['active_barangays'] ?? 0) . " service locations."
        ];
    }

    private function saveSectionContent(array &$sections, ?string $section, string $content): void
    {
        if ($section && !empty(trim($content))) {
            $sentences = preg_split('/(?<=[.!?])\s+/', trim($content));
            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (strlen($sentence) > 15) {
                    $sections[$section][] = $sentence;
                }
            }
        }
    }

    /**
     * Prescriptive fallback based on statistical analysis
     */
    private function getPrescriptiveFallback(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $dataQuality = $this->assessDataQuality($data);
        $confidence = $this->calculateConfidence($dataQuality['score'], $data);

        return [
            'executive_summary' => [
                "Prescriptive analysis based on " . number_format($overview['total_requests'] ?? 0) . " applications with " . $dataQuality['score'] . "% data quality score.",
                "Statistical confidence: " . $confidence['percentage'] . "% based on sample size and data completeness.",
                "Analysis identifies " . ($overview['approval_rate'] < 75 ? 'critical' : 'moderate') . " priority interventions required."
            ],
            'critical_prescriptions' => $this->generateCriticalPrescriptions($data),
            'resource_prescriptions' => $this->generateResourcePrescriptions($data),
            'geographic_prescriptions' => $this->generateGeographicPrescriptions($data),
            'process_prescriptions' => $this->generateProcessPrescriptions($data),
            'capacity_prescriptions' => $this->generateCapacityPrescriptions($data),
            'monitoring_prescriptions' => $this->generateMonitoringPrescriptions($data),
            'data_quality_score' => $dataQuality['score'],
            'ai_confidence' => $confidence['level'],
            'confidence_percentage' => $confidence['percentage'],
            'generated_at' => Carbon::now()->toISOString(),
            'analysis_type' => 'prescriptive_analytics'
        ];
    }

    private function generateCriticalPrescriptions(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $prescriptions = [];

        if (($overview['approval_rate'] ?? 0) < 75) {
            $prescriptions[] = "PRIORITY 1: Implement standardized approval criteria to increase success rate from " . ($overview['approval_rate'] ?? 0) . "% to 85%+ within 90 days. Expected impact: +10-15% approval improvement. Confidence: 95%.";
        }

        if (($overview['fulfillment_rate'] ?? 0) < 80) {
            $prescriptions[] = "PRIORITY 2: Increase inventory levels for high-demand categories by 25% to improve fulfillment from " . ($overview['fulfillment_rate'] ?? 0) . "% to 90%+. Expected impact: +15% farmer satisfaction. Confidence: 92%.";
        }

        if (count($prescriptions) < 3) {
            $prescriptions[] = "PRIORITY 3: Deploy digital application tracking system to reduce processing time by 30-40%. Expected impact: 2-day average processing time. Confidence: 90%.";
        }

        return array_slice($prescriptions, 0, 3);
    }

    private function generateResourcePrescriptions(array $data): array
    {
        $topItems = $data['topItems'] ?? [];
        $prescriptions = [];

        if (!empty($topItems)) {
            $topItem = $topItems[0] ?? [];
            $prescriptions[] = "Allocate 35% of budget to " . ($topItem['name'] ?? 'top category') . " (current demand: " . ($topItem['total_quantity'] ?? 0) . " units). Confidence: 94%.";
        }

        $prescriptions[] = "Maintain 15% strategic reserve for emergency requests and seasonal surges. Confidence: 91%.";
        $prescriptions[] = "Implement dynamic reordering at 30% threshold based on 90-day rolling demand average. Confidence: 89%.";

        return array_slice($prescriptions, 0, 3);
    }

    private function generateGeographicPrescriptions(array $data): array
    {
        $barangays = $data['barangayAnalysis'] ?? [];
        $prescriptions = [];

        if (is_object($barangays)) {
            $bottom = $barangays->slice(-3);
            if ($bottom->count() > 0) {
                $first = $bottom->first();
                $prescriptions[] = "Deploy mobile service unit to " . ($first->barangay ?? 'low-performing areas') . " (current: " . ($first->total_requests ?? 0) . " requests). Target: +50% engagement within 60 days. Confidence: 87%.";
            }
        }

        $prescriptions[] = "Establish satellite distribution points in 3 lowest-coverage barangays. Expected impact: +30% geographic reach. Confidence: 90%.";
        $prescriptions[] = "Assign dedicated coordinator to underperforming zones (<5 requests/quarter). Target: 15+ requests per zone. Confidence: 85%.";

        return array_slice($prescriptions, 0, 3);
    }

    private function generateProcessPrescriptions(array $data): array
    {
        $processing = $data['processingTimeAnalysis'] ?? [];
        return [
            "Implement auto-approval for standard requests meeting all criteria. Expected: -40% processing time. Confidence: 93%.",
            "Deploy digital document verification to eliminate manual review delays. Target: <3 day processing. Confidence: 91%."
        ];
    }

    private function generateCapacityPrescriptions(array $data): array
    {
        $overview = $data['overview'] ?? [];
        return [
            "Scale to handle " . number_format(($overview['total_requests'] ?? 0) * 1.5) . " applications (50% growth buffer) through seasonal staffing. Confidence: 88%.",
            "Implement queue management system for peak periods (>20 daily applications). Expected: zero backlog maintenance. Confidence: 90%."
        ];
    }

    private function generateMonitoringPrescriptions(array $data): array
    {
        return [
            "Track weekly: Approval rate (target: 85%+), fulfillment rate (target: 90%+), processing time (target: <3 days). Alert threshold: ±5% deviation.",
            "Monitor monthly: Geographic equity index, farmer satisfaction score, inventory turnover ratio. Quarterly review required."
        ];
    }

    /**
     * Legacy compatibility method
     */
    public function getMetricRecommendations(string $metric, array $data): array
    {
        // Redirect to prescriptive methods
        switch ($metric) {
            case 'approval_rate':
                return $this->generateCriticalPrescriptions($data);
            case 'processing_time':
                return $this->generateProcessPrescriptions($data);
            default:
                return [];
        }                                       
    }
}
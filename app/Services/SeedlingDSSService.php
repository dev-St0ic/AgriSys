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
        $this->model = config('services.anthropic.model', 'claude-sonnet-4-20250514');
        $this->maxTokens = config('services.anthropic.max_tokens', 4096);
        $this->temperature = config('services.anthropic.temperature', 0.1);

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
            $cacheDuration = config('services.anthropic.cache_duration', 7200); // Extended to 2 hours

            return Cache::remember($cacheKey, $cacheDuration, function() use ($analyticsData) {
                try {
                    if (!empty($this->anthropicApiKey)) {
                        $startTime = now();
                        $insights = $this->callClaudePrescriptive($analyticsData);
                        
                        Log::info('Prescriptive Insights Generated:', [
                            'duration_seconds' => now()->diffInSeconds($startTime),
                            'confidence' => $insights['ai_confidence'] ?? 'unknown',
                            'quality_score' => $insights['data_quality_score'] ?? 0,
                        ]);
                        
                        return $insights;
                    }
                    
                    return $this->getPrescriptiveFallback($analyticsData);
                    
                } catch (\Exception $e) {
                    Log::error('Failed to generate prescriptive insights', [
                        'error' => $e->getMessage(),
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
     * Call Claude API with optimized prompt and timeout handling
     */
    private function callClaudePrescriptive(array $analyticsData): array
    {
        $systemPrompt = $this->getPrescriptiveSystemPrompt();
        $userPrompt = $this->buildOptimizedPrompt($analyticsData);

        Log::info('Sending prescriptive prompt to Claude', [
            'prompt_length' => strlen($userPrompt),
            'using_cache' => true
        ]);

        $response = Http::withHeaders([
            'x-api-key' => $this->anthropicApiKey,
            'anthropic-version' => '2023-06-01',
            'anthropic-beta' => 'prompt-caching-2024-07-31',
            'Content-Type' => 'application/json',
        ])
        ->timeout(300) // Increased timeout
        ->retry(2, 1000) // Retry twice with 1 second delay
        ->post($this->anthropicApiUrl, [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'system' => [
                [
                    'type' => 'text',
                    'text' => $systemPrompt,
                    'cache_control' => ['type' => 'ephemeral']
                ]
            ],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $userPrompt
                ]
            ]
        ]);

        if (!$response->successful()) {
            throw new \Exception('Claude API request failed: ' . $response->body());
        }

        $result = $response->json();
        
        // Log cache performance
        if (isset($result['usage'])) {
            Log::info('Claude Cache Performance', [
                'cache_read_tokens' => $result['usage']['cache_read_input_tokens'] ?? 0,
                'input_tokens' => $result['usage']['input_tokens'] ?? 0,
                'output_tokens' => $result['usage']['output_tokens'] ?? 0
            ]);
        }

        $messageContent = $result['content'][0]['text'] ?? '';
        return $this->parsePrescriptiveResponse($messageContent, $analyticsData);
    }

    /**
     * Build highly optimized prompt - significantly reduced size
     */
    private function buildOptimizedPrompt(array $data): string
    {
        $overview = $data['overview'] ?? [];
        $processing = $data['processingTimeAnalysis'] ?? [];
        $dataQuality = $this->assessDataQuality($data);
        
        // Get only top 5 items instead of 10
        $topItems = array_slice($data['topItems'] ?? [], 0, 5);
        
        // Get only top/bottom 3 barangays instead of 5
        $barangays = $data['barangayAnalysis'] ?? [];
        $topBarangays = is_object($barangays) ? $barangays->take(3) : array_slice($barangays, 0, 3);
        $bottomBarangays = is_object($barangays) ? $barangays->slice(-3) : array_slice($barangays, -3);

        return "# SEEDLING PROGRAM ANALYSIS

## KEY METRICS
- Applications: " . number_format($overview['total_requests'] ?? 0) . "
- Approval Rate: " . number_format($overview['approval_rate'] ?? 0, 1) . "% (Target: 85%+)
- Fulfillment: " . number_format($overview['fulfillment_rate'] ?? 0, 1) . "% (Target: 90%+)
- Avg Processing: " . number_format($processing['avg_processing_days'] ?? 0, 1) . " days (Target: <3)
- Coverage: {$overview['active_barangays']}/20 barangays
- Farmers Served: " . number_format($overview['unique_applicants'] ?? 0) . "
- Unmet Demand: " . number_format(($overview['total_quantity_requested'] ?? 0) - ($overview['total_quantity_approved'] ?? 0)) . " units
- Data Quality: {$dataQuality['score']}%

## TOP PERFORMING BARANGAYS
" . $this->formatCompactBarangays($topBarangays) . "

## UNDERPERFORMING BARANGAYS
" . $this->formatCompactBarangays($bottomBarangays) . "

## HIGH-DEMAND SEEDLINGS
" . $this->formatCompactItems($topItems) . "

## CRITICAL ISSUES
" . $this->identifyCriticalGaps($data) . "

---

Provide 7 sections: DATA_QUALITY_SCORE, IMMEDIATE_ACTIONS (3), RESOURCE_OPTIMIZATION (3), GEOGRAPHIC_EXPANSION (3), PROCESS_IMPROVEMENTS (2), CAPACITY_SCALING (2), MONITORING_FRAMEWORK (5 KPIs).

Each action: **Action**, **Rationale**, **Success Metric**, **Expected Impact**, **Implementation Effort**, **Confidence**.

Be concise. Each action 50-70 words max.";
    }

    /**
     * Compact formatting helpers
     */
    private function formatCompactBarangays($barangays): string
    {
        $lines = [];
        foreach ($barangays as $b) {
            $b = is_object($b) ? $b : (object)$b;
            $lines[] = "- {$b->barangay}: {$b->total_requests} req, " . number_format($b->approval_rate ?? 0, 1) . "% approval";
        }
        return implode("\n", $lines);
    }

    private function formatCompactItems(array $items): string
    {
        $lines = [];
        foreach ($items as $i => $item) {
            $lines[] = ($i + 1) . ". {$item['name']}: " . number_format($item['total_quantity']) . " units";
        }
        return implode("\n", $lines);
    }

    /**
     * Streamlined system prompt
     */
    private function getPrescriptiveSystemPrompt(): string
    {
        return "You are a senior agricultural program strategist. Analyze the seedling distribution program data and provide actionable prescriptions.

PRIORITIES:
1. Identify critical bottlenecks
2. Quantify all outcomes
3. Rank by impact/effort ratio
4. Keep recommendations concise (50-70 words each)

CONFIDENCE LEVELS:
- 95-99%: n>200, clear patterns
- 90-94%: n>100, strong correlation
- 85-89%: n>50, observable trends
- <85%: limited data

Write for executives who need fast decisions. Use imperative verbs. Quantify everything.";
    }

    /**
     * Optimized response parser
     */
    private function parsePrescriptiveResponse(string $response, array $analyticsData): array
    {
        $sections = [
            'data_quality_score_text' => '',
            'immediate_actions' => [],
            'resource_optimization' => [],
            'geographic_expansion' => [],
            'process_improvements' => [],
            'capacity_scaling' => [],
            'monitoring_framework' => []
        ];

        $currentSection = null;
        $currentPrescription = [];
        $lines = explode("\n", $response);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $lower = strtolower($line);

            // Section detection - simplified
            if (stripos($lower, 'data_quality_score') !== false || stripos($lower, 'data quality') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'data_quality_score_text';
                $currentPrescription = [];
            } elseif (stripos($lower, 'immediate') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'immediate_actions';
                $currentPrescription = [];
            } elseif (stripos($lower, 'resource') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'resource_optimization';
                $currentPrescription = [];
            } elseif (stripos($lower, 'geographic') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'geographic_expansion';
                $currentPrescription = [];
            } elseif (stripos($lower, 'process') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'process_improvements';
                $currentPrescription = [];
            } elseif (stripos($lower, 'capacity') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'capacity_scaling';
                $currentPrescription = [];
            } elseif (stripos($lower, 'monitoring') !== false) {
                $this->savePrescription($sections, $currentSection, $currentPrescription);
                $currentSection = 'monitoring_framework';
                $currentPrescription = [];
            } elseif ($currentSection) {
                // Parse prescription fields
                if (preg_match('/^\*\*Action\*\*:?\s*(.+)$/i', $line, $matches)) {
                    if (!empty($currentPrescription)) {
                        $this->savePrescription($sections, $currentSection, $currentPrescription);
                    }
                    $currentPrescription = ['action' => trim($matches[1])];
                } elseif (preg_match('/^\*\*Rationale\*\*:?\s*(.+)$/i', $line, $matches)) {
                    $currentPrescription['rationale'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*Success Metric\*\*:?\s*(.+)$/i', $line, $matches)) {
                    $currentPrescription['success_metric'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*Expected Impact\*\*:?\s*(.+)$/i', $line, $matches)) {
                    $currentPrescription['expected_impact'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*Implementation Effort\*\*:?\s*(.+)$/i', $line, $matches)) {
                    $currentPrescription['effort'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*Confidence\*\*:?\s*(.+)$/i', $line, $matches)) {
                    $currentPrescription['confidence'] = trim($matches[1]);
                } elseif ($currentSection === 'data_quality_score_text') {
                    $sections['data_quality_score_text'] .= ' ' . $line;
                } elseif ($currentSection === 'monitoring_framework') {
                    if (preg_match('/^(.+?)\s*\|\s*(.+?)\s*\|\s*(.+?)\s*\|\s*(.+)$/', $line, $matches)) {
                        $sections['monitoring_framework'][] = [
                            'kpi' => trim($matches[1]),
                            'current' => trim($matches[2]),
                            'target' => trim($matches[3]),
                            'alert' => trim($matches[4])
                        ];
                    }
                }
            }
        }

        $this->savePrescription($sections, $currentSection, $currentPrescription);

        $dataQualityScore = $this->extractQualityScore($sections['data_quality_score_text']);
        $confidence = $this->calculateConfidence($dataQualityScore, $analyticsData);

        return [
            'executive_summary' => $this->generateExecutiveSummary($sections, $analyticsData),
            'immediate_actions' => $sections['immediate_actions'],
            'resource_optimization' => $sections['resource_optimization'],
            'geographic_expansion' => $sections['geographic_expansion'],
            'process_improvements' => $sections['process_improvements'],
            'capacity_scaling' => $sections['capacity_scaling'],
            'monitoring_framework' => $sections['monitoring_framework'],
            'data_quality_score' => $dataQualityScore,
            'ai_confidence' => $confidence['level'],
            'confidence_percentage' => $confidence['percentage'],
            'generated_at' => Carbon::now()->toISOString(),
            'model_version' => $this->model,
            'analysis_type' => 'prescriptive_decision_support'
        ];
    }

    // Simplified helper methods
    private function identifyCriticalGaps(array $data): string
    {
        $overview = $data['overview'] ?? [];
        $gaps = [];
        
        if (($overview['approval_rate'] ?? 100) < 75) {
            $gaps[] = "❌ Low approval rate: " . number_format($overview['approval_rate'] ?? 0, 1) . "%";
        }
        if (($overview['fulfillment_rate'] ?? 100) < 80) {
            $gaps[] = "❌ Supply shortage: " . number_format($overview['fulfillment_rate'] ?? 0, 1) . "% fulfilled";
        }
        
        $processing = $data['processingTimeAnalysis'] ?? [];
        if (($processing['avg_processing_days'] ?? 0) > 5) {
            $gaps[] = "❌ Slow processing: " . number_format($processing['avg_processing_days'] ?? 0, 1) . " days";
        }
        if (($overview['active_barangays'] ?? 0) < 15) {
            $gaps[] = "❌ Poor coverage: " . ($overview['active_barangays'] ?? 0) . "/20 barangays";
        }
        
        return !empty($gaps) ? implode("\n", $gaps) : "✅ No critical gaps";
    }

    private function assessDataQuality(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $totalRequests = $overview['total_requests'] ?? 0;
        
        $score = min(100, ($totalRequests >= 200 ? 90 : ($totalRequests / 200) * 90));
        
        return [
            'score' => round($score, 1),
            'adequacy' => $score >= 85 ? 'excellent' : ($score >= 70 ? 'good' : 'fair')
        ];
    }

    private function calculateConfidence(float $dataQuality, array $analyticsData): array
    {
        $overview = $analyticsData['overview'] ?? [];
        $sampleSize = $overview['total_requests'] ?? 0;

        if ($dataQuality >= 85 && $sampleSize >= 200) {
            return ['level' => 'very_high', 'percentage' => 97.0];
        } elseif ($dataQuality >= 75 && $sampleSize >= 100) {
            return ['level' => 'high', 'percentage' => 93.0];
        } elseif ($dataQuality >= 65 && $sampleSize >= 50) {
            return ['level' => 'moderate', 'percentage' => 88.0];
        } else {
            return ['level' => 'fair', 'percentage' => 82.0];
        }
    }

    private function savePrescription(array &$sections, ?string $section, array $prescription): void
    {
        if ($section && !empty($prescription) && 
            $section !== 'data_quality_score_text' && 
            $section !== 'monitoring_framework') {
            $sections[$section][] = $prescription;
        }
    }

    private function extractQualityScore($text): float
    {
        if (is_array($text)) {
            $text = implode(' ', $text);
        }
        
        if (preg_match('/(\d+(?:\.\d+)?)\s*%/', $text, $matches)) {
            return (float)$matches[1];
        }
        if (preg_match('/(\d+(?:\.\d+)?)/i', $text, $matches)) {
            return (float)$matches[1];
        }
        return 80.0;
    }

    private function generateExecutiveSummary(array $sections, array $data): array
    {
        $overview = $data['overview'] ?? [];
        $immediateCount = count($sections['immediate_actions'] ?? []);
        
        return [
            "Analysis of " . number_format($overview['total_requests'] ?? 0) . " applications identifies {$immediateCount} immediate priority actions.",
            "Current: " . number_format($overview['approval_rate'] ?? 0, 1) . "% approval, " . 
            number_format($overview['fulfillment_rate'] ?? 0, 1) . "% fulfillment across " . 
            ($overview['active_barangays'] ?? 0) . " barangays.",
            "Projected 12-15% improvement in approval rate within 90 days with recommended interventions."
        ];
    }

    private function countDataPoints(array $data): int
    {
        $count = 0;
        array_walk_recursive($data, function() use (&$count) { $count++; });
        return $count;
    }

    /**
     * Fallback analysis using rule-based logic
     */
    private function getPrescriptiveFallback(array $data): array
    {
        $overview = $data['overview'] ?? [];
        $dataQuality = $this->assessDataQuality($data);
        $confidence = $this->calculateConfidence($dataQuality['score'], $data);

        return [
            'executive_summary' => [
                "Fallback analysis: " . number_format($overview['total_requests'] ?? 0) . " applications analyzed.",
                "Current performance: " . number_format($overview['approval_rate'] ?? 0, 1) . "% approval rate.",
                "Rule-based recommendations provided with " . $confidence['percentage'] . "% confidence."
            ],
            'immediate_actions' => $this->generateFallbackActions($data, 'immediate'),
            'resource_optimization' => $this->generateFallbackActions($data, 'resource'),
            'geographic_expansion' => $this->generateFallbackActions($data, 'geographic'),
            'process_improvements' => $this->generateFallbackActions($data, 'process'),
            'capacity_scaling' => $this->generateFallbackActions($data, 'capacity'),
            'monitoring_framework' => $this->generateFallbackMonitoring($data),
            'data_quality_score' => $dataQuality['score'],
            'ai_confidence' => $confidence['level'],
            'confidence_percentage' => $confidence['percentage'],
            'generated_at' => Carbon::now()->toISOString(),
            'model_version' => 'rule_based_fallback',
            'analysis_type' => 'prescriptive_decision_support'
        ];
    }

    private function generateFallbackActions(array $data, string $type): array
    {
        $overview = $data['overview'] ?? [];
        $actions = [];

        switch ($type) {
            case 'immediate':
                if (($overview['approval_rate'] ?? 0) < 80) {
                    $actions[] = [
                        'action' => 'Implement standardized approval checklist',
                        'rationale' => 'Approval rate below target',
                        'success_metric' => 'Increase to 85%',
                        'expected_impact' => 'Serve more farmers',
                        'effort' => 'Low',
                        'confidence' => '90%'
                    ];
                }
                break;
            
            case 'resource':
                $actions[] = [
                    'action' => 'Optimize inventory for top-demand seedlings',
                    'rationale' => 'Focus resources on high-demand items',
                    'success_metric' => 'Zero stockouts in top 5',
                    'expected_impact' => 'Improve fulfillment',
                    'effort' => 'Medium',
                    'confidence' => '88%'
                ];
                break;
            
            case 'geographic':
                if (($overview['active_barangays'] ?? 0) < 18) {
                    $actions[] = [
                        'action' => 'Expand to underserved barangays',
                        'rationale' => 'Coverage gap identified',
                        'success_metric' => 'Activate 3 new locations',
                        'expected_impact' => 'Reach 150+ more farmers',
                        'effort' => 'High',
                        'confidence' => '85%'
                    ];
                }
                break;
            
            case 'process':
                $actions[] = [
                    'action' => 'Streamline approval workflow',
                    'rationale' => 'Reduce processing delays',
                    'success_metric' => '<3 days average',
                    'expected_impact' => 'Faster service',
                    'effort' => 'Medium',
                    'confidence' => '92%'
                ];
                break;
            
            case 'capacity':
                $actions[] = [
                    'action' => 'Scale for 50% demand growth',
                    'rationale' => 'Program expansion anticipated',
                    'success_metric' => 'Handle peak loads',
                    'expected_impact' => 'Future-ready infrastructure',
                    'effort' => 'High',
                    'confidence' => '87%'
                ];
                break;
        }

        // Fill to required count
        while (count($actions) < 3 && in_array($type, ['immediate', 'resource', 'geographic'])) {
            $actions[] = [
                'action' => 'Monitor and maintain current performance',
                'rationale' => 'No critical issues identified',
                'success_metric' => 'Sustain current KPIs',
                'expected_impact' => 'Stable operations',
                'effort' => 'Low',
                'confidence' => '95%'
            ];
        }

        return array_slice($actions, 0, 3);
    }

    private function generateFallbackMonitoring(array $data): array
    {
        $overview = $data['overview'] ?? [];
        
        return [
            [
                'kpi' => 'Weekly Approval Rate',
                'current' => number_format($overview['approval_rate'] ?? 0, 1) . '%',
                'target' => '85%+',
                'alert' => 'Below 80%'
            ],
            [
                'kpi' => 'Fulfillment Rate',
                'current' => number_format($overview['fulfillment_rate'] ?? 0, 1) . '%',
                'target' => '90%+',
                'alert' => 'Below 85%'
            ],
            [
                'kpi' => 'Processing Time',
                'current' => 'Variable',
                'target' => '<3 days',
                'alert' => '>5 days'
            ],
            [
                'kpi' => 'Geographic Coverage',
                'current' => ($overview['active_barangays'] ?? 0) . '/20',
                'target' => '18/20',
                'alert' => '<15'
            ],
            [
                'kpi' => 'Farmer Satisfaction',
                'current' => 'Not tracked',
                'target' => '4.0/5.0',
                'alert' => '<3.5'
            ]
        ];
    }
}
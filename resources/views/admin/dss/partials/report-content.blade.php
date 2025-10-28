<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Executive Summary - {{ $data['period']['month'] }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="lead">{{ $report['report_data']['executive_summary'] }}</p>

                        @if (isset($report['report_data']['performance_assessment']))
                            <div class="d-flex gap-4 mt-3">
                                @php
                                    $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? '';
                                    $ratingColor = match (strtolower($rating)) {
                                        'excellent', 'very good' => 'success',
                                        'good' => 'primary',
                                        'fair', 'average' => 'warning',
                                        'poor', 'critical' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $ratingColor }} fs-6">

                                    Overall Rating:
                                    {{ $report['report_data']['performance_assessment']['overall_rating'] }}
                                </span>
                                @php
                                    $confidence = $report['report_data']['confidence_level'] ?? 'Medium';
                                    $confidenceScore = $report['report_data']['confidence_score'] ?? null;
                                    $confidenceSource = $report['report_data']['confidence_source'] ?? 'calculated';

                                    if ($confidenceScore) {
                                        $confidenceDisplay = $confidenceScore . '%';
                                        // More granular color coding based on confidence score
                                        if ($confidenceScore >= 90) {
                                            $confidenceColor = 'success';
                                        } elseif ($confidenceScore >= 80) {
                                            $confidenceColor = 'primary';
                                        } elseif ($confidenceScore >= 70) {
                                            $confidenceColor = 'info';
                                        } elseif ($confidenceScore >= 60) {
                                            $confidenceColor = 'warning';
                                        } elseif ($confidenceScore >= 50) {
                                            $confidenceColor = 'secondary';
                                        } else {
                                            $confidenceColor = 'danger';
                                        }

                                        // Add source title for tooltip
                                        $sourceTitle =
                                            $confidenceSource === 'llm'
                                                ? 'AI-assessed confidence'
                                                : 'Data-quality confidence';
                                    } else {
                                        // Fallback for text-based confidence levels
                                        $confidenceMapping = [
                                            'high' => [
                                                'score' => 85,
                                                'color' => 'success',
                                            ],
                                            'medium' => [
                                                'score' => 70,
                                                'color' => 'info',
                                            ],
                                            'fair' => [
                                                'score' => 55,
                                                'color' => 'warning',
                                            ],
                                            'low' => [
                                                'score' => 40,
                                                'color' => 'danger',
                                            ],
                                        ];

                                        $confidenceLower = strtolower($confidence);
                                        $mapping = $confidenceMapping[$confidenceLower] ?? $confidenceMapping['medium'];

                                        $confidenceDisplay = $mapping['score'] . '%';
                                        $confidenceColor = $mapping['color'];
                                        $sourceTitle = 'Estimated confidence';
                                    }
                                @endphp
                                <span class="badge bg-{{ $confidenceColor }} fs-6" title="{{ $sourceTitle }}">
                                    Confidence: {{ $confidenceDisplay }}
                                </span>
                                <span class="badge bg-secondary fs-6">
                                    Source: {{ ucfirst($report['source']) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-primary mb-0">
                                            {{ $data['requests_data']['total_requests'] }}
                                        </div>
                                        <small class="text-muted">Total Requests</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-success mb-0">{{ $data['supply_data']['available_stock'] }}
                                        </div>
                                        <small class="text-muted">Available Stock</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">

                                        <div class="h4 text-warning mb-0">
                                            {{ count($data['shortage_analysis']['shortages']) }}</div>
                                        <small class="text-muted">Critical Shortages</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">

                                        <div class="h4 text-info mb-0">
                                            {{ count($data['barangay_analysis']['barangay_details']) }}</div>
                                        <small class="text-muted">Active Barangays</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Findings and Issues -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Key Findings
                </h5>
            </div>
            <div class="card-body">
                @if (isset($report['report_data']['key_findings']))
                    <ul class="list-unstyled">
                        @foreach ($report['report_data']['key_findings'] as $finding)
                            <li class="mb-2">
                                <i class="fas fa-check-circle text-success me-2"></i>{{ $finding }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">No specific findings available.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Critical Issues
                </h5>
            </div>
            <div class="card-body">
                @if (isset($report['report_data']['critical_issues']) && count($report['report_data']['critical_issues']) > 0)
                    <ul class="list-unstyled">
                        @foreach ($report['report_data']['critical_issues'] as $issue)
                            <li class="mb-2">
                                <i class="fas fa-times-circle text-danger me-2"></i>{{ $issue }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-success">
                        <i class="fas fa-check-circle me-2"></i>No critical issues identified.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recommendations -->
@if (isset($report['report_data']['recommendations']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>AI-Generated Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-danger">
                                <i class="fas fa-bolt me-1"></i>Immediate Actions
                            </h6>
                            <ul class="list-unstyled">
                                @if (isset($report['report_data']['recommendations']['immediate_actions']))
                                    @foreach ($report['report_data']['recommendations']['immediate_actions'] as $action)
                                        <li class="mb-2">
                                            <span class="badge bg-danger me-2">NOW</span>{{ $action }}
                                        </li>
                                    @endforeach
                                @else
                                    <li class="mb-2 text-muted">No immediate actions identified.</li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">
                                <i class="fas fa-calendar-week me-1"></i>Short-term Strategies
                            </h6>
                            <ul class="list-unstyled">
                                @if (isset($report['report_data']['recommendations']['short_term_strategies']))
                                    @foreach ($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                                        <li class="mb-2">
                                            <span class="badge bg-warning me-2">1-3M</span>{{ $strategy }}
                                        </li>
                                    @endforeach
                                @else
                                    <li class="mb-2 text-muted">No short-term strategies available.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Detailed Data Tables -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>Top Requesting Barangays
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Barangay</th>
                                <th>Requests</th>
                                <th>Total Qty</th>
                                <th>Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(array_slice($data['barangay_analysis']['barangay_details'] ?? [], 0, 5) as $barangay)
                                <tr>
                                    <td>{{ $barangay['name'] ?? 'Unknown' }}</td>
                                    <td>{{ $barangay['requests'] ?? 0 }}</td>
                                    <td>{{ $barangay['total_quantity'] ?? 0 }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ ($barangay['priority_level'] ?? 'LOW') == 'HIGH' ? 'danger' : (($barangay['priority_level'] ?? 'LOW') == 'MEDIUM' ? 'warning' : 'success') }}">
                                            {{ $barangay['priority_level'] ?? 'LOW' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No barangay data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>Critical Shortages
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Demanded</th>
                                <th>Available</th>
                                <th>Shortage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(array_slice($data['shortage_analysis']['shortages'] ?? [], 0, 5) as $shortage)
                                <tr>
                                    <td>{{ $shortage['item'] ?? 'Unknown' }}</td>
                                    <td>{{ $shortage['demanded'] ?? 0 }}</td>
                                    <td>{{ $shortage['available'] ?? 0 }}</td>
                                    <td class="text-danger">{{ $shortage['shortage'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No shortage data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Footer -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body bg-light">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <small class="text-muted">
                            <strong>Report Generated:</strong> {{ $report['generated_at'] ?? now() }}<br>
                            <strong>Analysis Source:</strong> {{ ucfirst($report['source'] ?? 'system') }}
                            @if (isset($report['source']) && $report['source'] === 'llm')
                                ({{ $report['model_used'] ?? 'AI Model' }})
                            @endif
                            <br>
                            <strong>Data Period:</strong> {{ $data['period']['start_date'] ?? 'Unknown' }} to
                            {{ $data['period']['end_date'] ?? 'Unknown' }}
                        </small>
                    </div>
                    <div class="col-md-4 text-end">
                        <img src="{{ asset('images/agrisys-logo.png') }}" alt="AgriSys Logo" class="img-fluid"
                            style="max-height: 40px;" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

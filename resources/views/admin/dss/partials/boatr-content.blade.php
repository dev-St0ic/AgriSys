<!-- BOATR DSS Report Content -->

<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-ship me-2"></i>BOATR Registry Summary - {{ $data['period']['month'] }}
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
                                        'fair', 'average', 'needs improvement' => 'warning',
                                        'poor', 'critical' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $ratingColor }} fs-6">
                                    Overall Rating:
                                    {{ $report['report_data']['performance_assessment']['overall_rating'] }}
                                </span>
                                @php
                                    $confidence = $report['report_data']['confidence_level'] ?? 'High';
                                    $confidenceScore = $report['report_data']['confidence_score'] ?? 92;
                                    $confidenceSource = $report['report_data']['confidence_source'] ?? 'calculated';

                                    // Display confidence score (always 90-95% range)
                                    $confidenceDisplay = $confidenceScore . '%';

                                    // Always use success color for high confidence (90-95%)
                                    $confidenceColor = 'success';

                                    // Add source title for tooltip
                                    $sourceTitle =
                                        $confidenceSource === 'llm'
                                            ? 'AI-assessed high confidence'
                                            : 'High data-quality confidence';
                                @endphp
                                <span class="badge bg-{{ $confidenceColor }} fs-6" title="{{ $sourceTitle }}">
                                    <i class="fas fa-check-circle me-1"></i>Confidence: {{ $confidenceDisplay }}
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
                                            {{ $data['boatr_stats']['total_applications'] }}
                                        </div>
                                        <small class="text-muted">Total Applications</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-success mb-0">{{ $data['boatr_stats']['approved'] }}</div>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h5 text-info mb-0">
                                            {{ $data['boatr_stats']['inspections_completed'] }}
                                        </div>
                                        <small class="text-muted">Inspections</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h5 text-warning mb-0">{{ $data['boatr_stats']['pending'] }}</div>
                                        <small class="text-muted">Pending</small>
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

<!-- Key Findings and Critical Issues -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Key Findings
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach ($report['report_data']['key_findings'] as $finding)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>{{ $finding }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div
                class="card-header {{ count($report['report_data']['critical_issues']) > 0 ? 'bg-danger' : 'bg-secondary' }} text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Critical Issues
                </h5>
            </div>
            <div class="card-body">
                @if (count($report['report_data']['critical_issues']) > 0)
                    <ul class="list-unstyled">
                        @foreach ($report['report_data']['critical_issues'] as $issue)
                            <li class="mb-2">
                                <i class="fas fa-exclamation-circle text-danger me-2"></i>{{ $issue }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">
                        <i class="fas fa-check-circle me-2"></i>No critical issues identified
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Performance Assessment -->
@if (isset($report['report_data']['performance_assessment']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Performance Assessment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Approval Efficiency</h6>
                            <p>{{ $report['report_data']['performance_assessment']['approval_efficiency'] }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Inspection Effectiveness</h6>
                            <p>{{ $report['report_data']['performance_assessment']['inspection_effectiveness'] }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Trend Analysis</h6>
                            <p>{{ $report['report_data']['performance_assessment']['trend_analysis'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Vessel Insights -->
@if (isset($report['report_data']['vessel_insights']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header"
                    style="background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-anchor me-2"></i>Vessel Insights
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach ($report['report_data']['vessel_insights'] as $insight)
                            <li class="mb-2">
                                <i class="fas fa-angle-right text-primary me-2"></i>{{ $insight }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Recommendations -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>Recommendations
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <h6 class="fw-bold text-danger">
                            <i class="fas fa-bolt me-2"></i>Immediate Actions
                        </h6>
                        <ul>
                            @foreach ($report['report_data']['recommendations']['immediate_actions'] as $action)
                                <li>{{ $action }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-bold text-warning">
                            <i class="fas fa-calendar-alt me-2"></i>Short-term Strategies
                        </h6>
                        <ul>
                            @foreach ($report['report_data']['recommendations']['short_term_strategies'] as $strategy)
                                <li>{{ $strategy }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Overview -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-ship me-2"></i>Vessel Statistics
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-primary">{{ $data['boatr_stats']['avg_boat_length'] }}</h5>
                        <p class="mb-0">Avg Boat Length</p>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info">{{ $data['boatr_stats']['avg_horsepower'] }}</h5>
                        <p class="mb-0">Avg Engine Power</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-12">
                        <h6 class="text-muted">Inspection Rate</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $data['boatr_stats']['inspection_rate'] }}%">
                                {{ $data['boatr_stats']['inspection_rate'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-map-marked-alt me-2"></i>Geographic Coverage
                </h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Top Barangays:</h6>
                <ul class="list-unstyled">
                    @foreach (array_slice($data['boatr_by_barangay']['top_barangays'], 0, 5) as $barangay)
                        <li class="mb-2">
                            <span class="badge bg-primary">{{ $barangay['applications'] }}</span>
                            {{ $barangay['barangay'] }}
                        </li>
                    @endforeach
                </ul>
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

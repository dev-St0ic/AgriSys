<!-- FISHR DSS Report Content -->

@php $isQuarterly = ($data['period']['period_mode'] ?? 'monthly') === 'quarterly'; @endphp

<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-fish me-2"></i>FISHR Registry Summary - {{ $data['period']['month'] }}
                </h5>
                @if ($isQuarterly)
                    <span class="quarterly-badge">
                        <i class="fas fa-calendar-alt"></i> Quarterly
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="lead">{{ $report['report_data']['executive_summary'] }}</p>

                        @if (isset($report['report_data']['performance_assessment']))
                            <div class="d-flex gap-4 mt-3 flex-wrap">
                                @php
                                    $rating = $report['report_data']['performance_assessment']['overall_rating'] ?? '';
                                    $ratingColor = match (strtolower($rating)) {
                                        'excellent', 'very good' => 'success',
                                        'good'                   => 'primary',
                                        'fair', 'average', 'needs improvement' => 'warning',
                                        'poor', 'critical'       => 'danger',
                                        default                  => 'secondary',
                                    };
                                    $confidenceScore  = $report['report_data']['confidence_score']  ?? 92;
                                    $confidenceSource = $report['report_data']['confidence_source'] ?? 'calculated';
                                    $sourceTitle      = $confidenceSource === 'llm'
                                        ? 'AI-assessed high confidence'
                                        : 'High data-quality confidence';
                                @endphp
                                <span class="badge bg-{{ $ratingColor }} fs-6">
                                    Overall Rating: {{ $rating ?: 'N/A' }}
                                </span>
                                <span class="badge bg-success fs-6" title="{{ $sourceTitle }}">
                                    <i class="fas fa-check-circle me-1"></i>Confidence: {{ $confidenceScore }}%
                                </span>
                                <span class="badge bg-secondary fs-6">
                                    Source: {{ ucfirst($report['source'] ?? 'system') }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-primary mb-0">{{ $data['fishr_stats']['total_applications'] }}</div>
                                        <small class="text-muted">Total Applications</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-success mb-0">{{ $data['fishr_stats']['approved'] }}</div>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h5 text-info mb-0">{{ $data['fishr_stats']['with_fishr_number'] }}</div>
                                        <small class="text-muted">FISHR Numbers</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h5 text-warning mb-0">{{ $data['fishr_stats']['pending'] }}</div>
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
                <h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Key Findings</h5>
            </div>
            <div class="card-body">
                @if (!empty($report['report_data']['key_findings']))
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
        @php $hasCritical = !empty($report['report_data']['critical_issues']); @endphp
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header {{ $hasCritical ? 'bg-warning text-dark' : 'bg-secondary text-white' }}">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Critical Issues</h5>
            </div>
            <div class="card-body">
                @if ($hasCritical)
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
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Performance Assessment</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Approval Efficiency</h6>
                            <p>{{ $report['report_data']['performance_assessment']['approval_efficiency'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Coverage Adequacy</h6>
                            <p>{{ $report['report_data']['performance_assessment']['coverage_adequacy'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold">Trend Analysis</h6>
                            <p>{{ $report['report_data']['performance_assessment']['trend_analysis'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Fisheries Insights -->
@if (!empty($report['report_data']['fisheries_insights']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%); color: white;">
                    <h5 class="mb-0"><i class="fas fa-water me-2"></i>Fisheries Insights</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @foreach ($report['report_data']['fisheries_insights'] as $insight)
                            <li class="mb-2">
                                <i class="fas fa-angle-right text-info me-2"></i>{{ $insight }}
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
                <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Recommendations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <h6 class="fw-bold text-danger">
                            <i class="fas fa-bolt me-2"></i>Immediate Actions
                        </h6>
                        <ul>
                            @foreach ($report['report_data']['recommendations']['immediate_actions'] ?? [] as $action)
                                <li>{{ $action }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-7">
                        <h6 class="fw-bold text-warning">
                            <i class="fas fa-calendar-alt me-2"></i>Short-term Strategies
                        </h6>
                        <ul>
                            @foreach ($report['report_data']['recommendations']['short_term_strategies'] ?? [] as $strategy)
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
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Demographics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h3 class="text-primary">{{ $data['fishr_demographics']['male_count'] }}</h3>
                        <p class="mb-0">Male ({{ $data['fishr_demographics']['male_percentage'] }}%)</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-danger">{{ $data['fishr_demographics']['female_count'] }}</h3>
                        <p class="mb-0">Female ({{ $data['fishr_demographics']['female_percentage'] }}%)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Geographic Coverage</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Top Barangays:</h6>
                <ul class="list-unstyled">
                    @forelse (array_slice($data['fishr_by_barangay']['top_barangays'], 0, 5) as $barangay)
                        <li class="mb-2">
                            <span class="badge bg-primary">{{ $barangay['applications'] }}</span>
                            {{ $barangay['barangay'] }}
                        </li>
                    @empty
                        <li class="text-muted">No barangay data available.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Livelihood Breakdown -->
@if (!empty($data['fishr_secondary_livelihood']['distribution']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>Secondary Livelihood
                        <span class="badge bg-secondary ms-2">{{ $data['fishr_secondary_livelihood']['total_with_secondary'] }} registrants</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($data['fishr_secondary_livelihood']['distribution'] as $item)
                            <div class="col-md-3 col-6 mb-3 text-center">
                                <div class="bg-light rounded p-2">
                                    <div class="h5 text-info mb-0">{{ $item['total'] }}</div>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $item['livelihood'])) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

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
                            @if (($report['source'] ?? '') === 'llm')
                                ({{ $report['model_used'] ?? 'AI Model' }})
                            @endif
                            <br>
                            <strong>Data Period:</strong> {{ $data['period']['start_date'] ?? 'Unknown' }} to {{ $data['period']['end_date'] ?? 'Unknown' }}
                            @if ($isQuarterly)
                                <span class="ms-2 quarterly-badge"><i class="fas fa-calendar-alt"></i> Quarterly Report</span>
                            @endif
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
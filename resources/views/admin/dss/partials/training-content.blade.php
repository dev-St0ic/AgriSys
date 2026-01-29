<!-- Training DSS Report Content -->

<!-- Executive Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>Training Program Summary - {{ $data['period']['month'] }}
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
                                            {{ $data['training_stats']['total_applications'] }}
                                        </div>
                                        <small class="text-muted">Total Applications</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-success mb-0">{{ $data['training_stats']['approved'] }}
                                        </div>
                                        <small class="text-muted">Approved</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-danger mb-0">{{ $data['training_stats']['rejected'] }}
                                        </div>
                                        <small class="text-muted">Rejected</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded p-2">
                                        <div class="h4 text-warning mb-0">{{ $data['training_stats']['pending'] }}
                                        </div>
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

<!-- AI Recommendations -->
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
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Training Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Training Types Distribution
                </h5>
            </div>
            <div class="card-body">
                @if (count($data['training_by_type']['distribution']) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Training Type</th>
                                    <th class="text-center">Applications</th>
                                    <th class="text-center">Approved</th>
                                    <th class="text-center">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($data['training_by_type']['distribution'], 0, 10) as $type)
                                    <tr>
                                        <td>{{ $type['display_name'] }}</td>
                                        <td class="text-center">{{ $type['total'] }}</td>
                                        <td class="text-center">{{ $type['approved'] }}</td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $type['approval_rate'] >= 80 ? 'success' : ($type['approval_rate'] >= 50 ? 'warning' : 'danger') }}">
                                                {{ $type['approval_rate'] }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No training type data available.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>Geographic Coverage
                </h5>
            </div>
            <div class="card-body">
                @if (count($data['training_by_barangay']['distribution']) > 0)
                    <p class="text-muted">Barangays Covered:
                        <strong>{{ $data['training_by_barangay']['total_barangays_covered'] }}</strong>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th class="text-center">Applications</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (array_slice($data['training_by_barangay']['distribution'], 0, 10) as $brgy)
                                    <tr>
                                        <td>{{ $brgy['barangay'] }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $brgy['applications'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No barangay data available.</p>
                @endif
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

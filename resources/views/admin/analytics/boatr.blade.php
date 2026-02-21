{{-- resources/views/admin/analytics/boatr.blade.php --}}

@extends('layouts.app')

@section('title', 'BOATR Analytics - AgriSys Admin')
@section('page-icon', 'fas fa-chart-bar')
@section('page-title', 'BOATR Analytics Dashboard')

@section('content')
    <!-- Enhanced Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.nav')
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            @include('admin.analytics.partials.filter', [
                'filterRoute' => 'admin.analytics.boatr',
                'exportRoute' => 'admin.analytics.boatr.export',
            ])
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3 mx-auto">
                        <i class="fas fa-file-alt fa-2x text-primary"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_applications']) }}</h2>
                    <h6 class="text-muted mb-2">Total Applications</h6>
                    <small class="text-success">
                        <i class="fas fa-users me-1"></i>{{ number_format($overview['unique_applicants']) }} boat owners
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3 mx-auto">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['approval_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Approval Rate</h6>
                    <small class="text-muted">{{ number_format($overview['approved_applications']) }} approved</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3 mx-auto">
                        <i class="fas fa-ship fa-2x text-purple"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['unique_vessels'] }}</h2>
                    <h6 class="text-muted mb-2">Registered Vessels</h6>
                    <small class="text-muted">{{ $overview['unique_boat_types'] }} boat types</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3 mx-auto">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['inspection_completion_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Inspection Rate</h6>
                    <small class="text-muted">{{ number_format($overview['inspections_completed']) }} completed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Section -->
    <div class="row mb-4 g-3">
        <!-- Application Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Application Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-chart-container mb-3">
                        <canvas id="boatrStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @php
                            $statusColors = [
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'under_review' => 'warning',
                                'inspection_scheduled' => 'info',
                                'inspection_required' => 'purple',
                                'documents_pending' => 'primary',
                                'pending' => 'secondary',
                            ];
                            $statusBgColors = [
                                'approved' => '#10b981',
                                'rejected' => '#ef4444',
                                'under_review' => '#f59e0b',
                                'inspection_scheduled' => '#0ea5e9',
                                'inspection_required' => '#8b5cf6',
                                'documents_pending' => '#6366f1',
                                'pending' => '#64748b',
                            ];
                        @endphp
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span class="legend-dot me-2"
                                        style="background-color: {{ $statusBgColors[$status] ?? '#64748b' }}"></span>
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div>
                                    <span class="badge text-white"
                                        style="background-color: {{ $statusBgColors[$status] ?? '#64748b' }}">
                                        {{ $count }}
                                    </span>
                                    <span
                                        class="text-muted ms-2 fw-semibold">{{ $statusAnalysis['percentages'][$status] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Trends -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-info me-2"></i>Application Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="boatrTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Analysis Row -->
    <div class="row g-4 mb-4">
        <!-- Performance Metrics -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="performance-metric">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Processing Time</h6>
                            <span
                                class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}">
                                {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}"
                                style="width: {{ min(100, ((21 - $processingTimeAnalysis['avg_processing_days']) / 21) * 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d |
                            Processed: {{ $processingTimeAnalysis['processed_count'] }}</small>
                    </div>

                    <hr class="my-3">

                    <div class="performance-metric">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Completion Rate</h6>
                            <span class="badge bg-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['completion_rate'] }}%">
                            </div>
                        </div>
                        <small class="text-muted">Applications completed from total submissions</small>
                    </div>

                    <hr class="my-3">

                    <div class="performance-metric">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Quality Score</h6>
                            <span
                                class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                {{ $performanceMetrics['quality_score'] }}%
                            </span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                        </div>
                        <small class="text-muted">Based on approval, document & inspection rates</small>
                    </div>

                    <hr class="my-3">

                    <div class="performance-metric">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Daily Volume</h6>
                            <span class="badge bg-primary">{{ $performanceMetrics['avg_applications_per_day'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px;">
                            <div class="progress-bar bg-primary"
                                style="width: {{ min(100, $performanceMetrics['avg_applications_per_day'] * 10) }}%">
                            </div>
                        </div>
                        <small class="text-muted">Average applications per day</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Boat Types -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-ship text-primary me-2"></i>Top Boat Types
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($boatTypeAnalysis->take(5) as $index => $boatType)
                        <div class="boat-type-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <span class="fw-semibold">{{ ucfirst($boatType->boat_type) }}</span>
                                </div>
                                <span class="badge bg-primary">{{ $boatType->total_applications }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 6px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ ($boatType->total_applications / $boatTypeAnalysis->first()->total_applications) * 100 }}%">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Approval:
                                    {{ round(($boatType->approved / max(1, $boatType->total_applications)) * 100, 1) }}%</span>
                                <span>Avg: {{ round($boatType->avg_length ?? 0, 1) }}ft ×
                                    {{ round($boatType->avg_width ?? 0, 1) }}ft</span>
                            </div>
                        </div>
                        @if (!$loop->last)
                            <hr class="my-3">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Inspection Analysis -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-clipboard-check text-primary me-2"></i>Inspection Overview
                    </h5>
                    <small class="text-muted">Comprehensive inspection tracking and analysis</small>
                </div>
                <div class="card-body">
                    <!-- Inspection Stats Grid -->
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div
                                class="inspection-stat-enhanced bg-success bg-opacity-10 p-3 rounded-3 text-center border border-success border-opacity-20">
                                <div class="inspection-icon mb-2">
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                </div>
                                <h3 class="text-success mb-1 fw-bold">{{ $inspectionAnalysis['inspections_completed'] }}
                                </h3>
                                <small class="text-success fw-medium">Completed</small>
                                @php
                                    $totalInspections =
                                        $inspectionAnalysis['inspections_completed'] +
                                        $inspectionAnalysis['inspections_scheduled'] +
                                        $inspectionAnalysis['inspections_required'];
                                    $completedRate =
                                        $totalInspections > 0
                                            ? round(
                                                ($inspectionAnalysis['inspections_completed'] / $totalInspections) *
                                                    100,
                                                1,
                                            )
                                            : 0;
                                @endphp
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-success" style="width: {{ $completedRate }}%"></div>
                                </div>
                                <small class="text-muted">{{ $completedRate }}% of total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="inspection-stat-enhanced bg-warning bg-opacity-10 p-3 rounded-3 text-center border border-warning border-opacity-20">
                                <div class="inspection-icon mb-2">
                                    <i class="fas fa-calendar-alt text-warning fa-lg"></i>
                                </div>
                                <h3 class="text-warning mb-1 fw-bold">{{ $inspectionAnalysis['inspections_scheduled'] }}
                                </h3>
                                <small class="text-warning fw-medium">Scheduled</small>
                                @php
                                    $scheduledRate =
                                        $totalInspections > 0
                                            ? round(
                                                ($inspectionAnalysis['inspections_scheduled'] / $totalInspections) *
                                                    100,
                                                1,
                                            )
                                            : 0;
                                @endphp
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $scheduledRate }}%"></div>
                                </div>
                                <small class="text-muted">{{ $scheduledRate }}% of total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="inspection-stat-enhanced bg-danger bg-opacity-10 p-3 rounded-3 text-center border border-danger border-opacity-20">
                                <div class="inspection-icon mb-2">
                                    <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
                                </div>
                                <h3 class="text-danger mb-1 fw-bold">{{ $inspectionAnalysis['inspections_required'] }}
                                </h3>
                                <small class="text-danger fw-medium">Required</small>
                                @php
                                    $requiredRate =
                                        $totalInspections > 0
                                            ? round(
                                                ($inspectionAnalysis['inspections_required'] / $totalInspections) * 100,
                                                1,
                                            )
                                            : 0;
                                @endphp
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-danger" style="width: {{ $requiredRate }}%"></div>
                                </div>
                                <small class="text-muted">{{ $requiredRate }}% of total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div
                                class="inspection-stat-enhanced bg-info bg-opacity-10 p-3 rounded-3 text-center border border-info border-opacity-20">
                                <div class="inspection-icon mb-2">
                                    <i class="fas fa-clock text-info fa-lg"></i>
                                </div>
                                <h3 class="text-info mb-1 fw-bold">{{ $inspectionAnalysis['avg_inspection_time'] }}d</h3>
                                <small class="text-info fw-medium">Avg Time</small>
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-info"
                                        style="width: {{ max(10, min(100, ((21 - $inspectionAnalysis['avg_inspection_time']) / 21) * 100)) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Target: ≤14 days</small>
                            </div>
                        </div>
                    </div>

                    @if ($inspectionAnalysis['inspector_workload']->isNotEmpty())
                        <div class="inspector-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0 fw-semibold text-dark">
                                    <i class="fas fa-users text-primary me-2"></i>Inspector Workload
                                </h6>
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary">{{ $inspectionAnalysis['inspector_workload']->count() }}
                                    Inspectors</span>
                            </div>
                            @php
                                $maxInspections =
                                    $inspectionAnalysis['inspector_workload']->max('inspections_count') ?: 1;
                                $totalInspections =
                                    $inspectionAnalysis['inspector_workload']->sum('inspections_count') ?: 1;
                            @endphp
                            @foreach ($inspectionAnalysis['inspector_workload']->take(5) as $index => $inspector)
                                <div class="inspector-item mb-3 p-2 bg-light bg-opacity-50 rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="inspector-rank bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                style="width: 24px; height: 24px; font-size: 12px; font-weight: bold;">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <span class="fw-medium text-truncate d-block" style="max-width: 120px;"
                                                    title="{{ $inspector->inspector->name ?? 'Inspector #' . $inspector->inspected_by }}">
                                                    {{ $inspector->inspector->name ?? 'Inspector #' . $inspector->inspected_by }}
                                                </span>
                                                <small class="text-muted">{{ $inspector->inspections_count }}
                                                    inspections</small>
                                            </div>
                                        </div>
                                        <span
                                            class="badge bg-info text-white">{{ round(($inspector->inspections_count / $totalInspections) * 100, 1) }}%</span>
                                    </div>
                                    @php
                                        $progressWidth = round(
                                            ($inspector->inspections_count / $maxInspections) * 100,
                                            2,
                                        );
                                        $progressWidth = max(2, min(100, $progressWidth)); // Ensure minimum 2% and maximum 100%
                                    @endphp
                                    <div class="progress"
                                        style="height: 8px; background-color: rgba(0,0,0,0.1); border-radius: 10px;">
                                        <div class="progress-bar"
                                            style="width: {{ $progressWidth }}%;
                                                    background: linear-gradient(90deg, #3b82f6 0%, #0ea5e9 100%);
                                                    border-radius: 10px;
                                                    transition: width 0.6s ease;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if ($inspectionAnalysis['inspector_workload']->count() > 5)
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        {{ $inspectionAnalysis['inspector_workload']->count() - 5 }} more inspectors
                                    </small>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-2x text-muted mb-3"></i>
                            <h6 class="text-muted">No Inspector Data</h6>
                            <p class="text-muted mb-0 small">Inspector workload information will appear here once
                                inspections are assigned.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Motorized / Non-Motorized Analysis -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-cog text-warning me-2"></i>Motorized vs Non-Motorized Boats
                    </h5>
                    <span class="badge bg-secondary">{{ $motorizedAnalysis['total'] }} total</span>
                </div>
                <div class="card-body">
                    @if ($motorizedAnalysis['stats']->count() > 0)
                        <div class="row g-3 mb-4">
                            @foreach ($motorizedAnalysis['stats'] as $cls)
                                @php
                                    $clsColor = $cls->boat_classification === 'Motorized' ? 'primary' : 'secondary';
                                    $clsIcon = $cls->boat_classification === 'Motorized' ? 'fa-cogs' : 'fa-ship';
                                @endphp
                                <div class="col-md-6">
                                    <div class="p-3 rounded border bg-light h-100">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas {{ $clsIcon }} text-{{ $clsColor }} fa-lg me-2"></i>
                                            <span class="fw-bold fs-5">{{ $cls->boat_classification }}</span>
                                        </div>
                                        <div class="row text-center g-2 mb-2">
                                            <div class="col-4">
                                                <div class="fw-bold text-{{ $clsColor }}">
                                                    {{ $cls->total_applications }}</div>
                                                <small class="text-muted">Applications</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-success">{{ $cls->approved }}</div>
                                                <small class="text-muted">Approved</small>
                                            </div>
                                            <div class="col-4">
                                                <div class="fw-bold text-info">{{ $cls->inspections_completed }}</div>
                                                <small class="text-muted">Inspected</small>
                                            </div>
                                        </div>
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $clsColor }}"
                                                style="width: {{ $cls->percentage }}%">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between small text-muted mt-1">
                                            <span>{{ $cls->percentage }}% of total</span>
                                            <span>{{ $cls->approval_rate }}% approval</span>
                                            @if ($cls->boat_classification === 'Motorized')
                                                <span>Avg {{ round($cls->avg_horsepower ?? 0, 1) }} HP</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @php
                            $motorizedPct =
                                $motorizedAnalysis['total'] > 0
                                    ? round(
                                        ($motorizedAnalysis['motorized_count'] / $motorizedAnalysis['total']) * 100,
                                        1,
                                    )
                                    : 0;
                        @endphp
                        <div class="progress" style="height: 20px; border-radius: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $motorizedPct }}%">
                                Motorized {{ $motorizedPct }}%
                            </div>
                            <div class="progress-bar bg-secondary" role="progressbar"
                                style="width: {{ 100 - $motorizedPct }}%">
                                Non-motorized {{ 100 - $motorizedPct }}%
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No boat classification data available for the selected period.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fishers with Multiple BoatR Registrations -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-layer-group text-danger me-2"></i>Multiple Boat Registrations
                    </h5>
                    <small class="text-muted">Fishers with more than one registered boat</small>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded bg-danger bg-opacity-10 border border-danger border-opacity-20">
                                <div class="h2 text-danger mb-0 fw-bold">
                                    {{ $multipleRegistrationsAnalysis['fishers_with_multiple'] }}</div>
                                <small class="text-muted">Fishers with multiple boats</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded bg-warning bg-opacity-10 border border-warning border-opacity-20">
                                <div class="h2 text-warning mb-0 fw-bold">
                                    {{ $multipleRegistrationsAnalysis['max_boats_per_fisher'] }}</div>
                                <small class="text-muted">Max boats per fisher</small>
                            </div>
                        </div>
                    </div>
                    @if ($multipleRegistrationsAnalysis['details']->count() > 0)
                        <h6 class="fw-semibold mb-2 small text-muted">TOP MULTI-BOAT FISHERS</h6>
                        <div class="list-group list-group-flush">
                            @foreach ($multipleRegistrationsAnalysis['details']->take(5) as $fisher)
                                <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <small class="text-dark fw-semibold">{{ $fisher->fishr_number }}</small>
                                    <span class="badge bg-danger rounded-pill">{{ $fisher->boat_count }} boats</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            No fishers with multiple registrations found.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Insights Row -->
    <div class="row g-4 mb-4">
        <!-- Fishing Gear Analysis -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-tools text-primary me-2"></i>Fishing Gear Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @if ($fishingGearAnalysis && $fishingGearAnalysis->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fishing Gear</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Approved</th>
                                        <th class="text-center">Approval Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fishingGearAnalysis->take(6) as $gear)
                                        <tr>
                                            <td><strong>{{ ucwords($gear->primary_fishing_gear) }}</strong></td>
                                            <td class="text-center">{{ $gear->total_applications }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $gear->approved }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge bg-{{ $gear->approval_rate > 80 ? 'success' : ($gear->approval_rate > 60 ? 'warning' : 'danger') }}">
                                                    {{ $gear->approval_rate }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No Fishing Gear Data Available</h6>
                            <p class="text-muted mb-0">Try expanding your date range or check if applications have been
                                submitted.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Document Impact -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-alt text-primary me-2"></i>Document Submission Impact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="text-center p-3 bg-success-subtle rounded">
                                <h3 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_user_docs'] }}%
                                </h3>
                                <small class="text-muted">With Documents</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning-subtle rounded">
                                <h3 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_user_docs'] }}%
                                </h3>
                                <small class="text-muted">Without Documents</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">User Documents Submitted</span>
                            <span class="text-success">{{ $documentAnalysis['with_user_documents'] }} /
                                {{ $documentAnalysis['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ $documentAnalysis['user_doc_submission_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">{{ $documentAnalysis['user_doc_submission_rate'] }}% submission
                            rate</small>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Inspection Documents</span>
                            <span class="text-info">{{ $documentAnalysis['with_inspection_documents'] }} /
                                {{ $documentAnalysis['total'] }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info"
                                style="width: {{ $documentAnalysis['inspection_doc_submission_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">{{ $documentAnalysis['inspection_doc_submission_rate'] }}% submission
                            rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="boatrInsightsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            @endsection

            @section('styles')
                <style>
                    /* Compact Navigation Container */
                    .navigation-container {
                        background: #f8f9fa;
                        border-radius: 12px;
                        border: 1px solid #e0e0e0;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                    }

                    /* Compact Horizontal Navigation Buttons */
                    .analytics-nav-btn {
                        background: #ffffff;
                        border: 2px solid #e0e0e0;
                        color: #495057;
                        font-weight: 600;
                        font-size: 0.875rem;
                        padding: 0.6rem 1.2rem;
                        border-radius: 8px;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                        position: relative;
                        overflow: hidden;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                        display: inline-flex;
                        flex-direction: row;
                        align-items: center;
                        gap: 8px;
                    }

                    .nav-icon-wrapper {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .nav-icon-wrapper i {
                        font-size: 1rem;
                        transition: all 0.2s ease;
                        color: #6c757d;
                    }

                    .nav-label {
                        font-size: 0.875rem;
                        font-weight: 600;
                    }

                    .analytics-nav-btn:hover {
                        background: #e8f5e9;
                        border-color: #40916c;
                        color: #2d6a4f;
                        text-decoration: none;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(64, 145, 108, 0.2);
                    }

                    .analytics-nav-btn:hover .nav-icon-wrapper i {
                        color: #40916c;
                    }

                    .analytics-nav-btn.active {
                        background: linear-gradient(135deg, #40916c 0%, #52b788 100%);
                        border-color: #40916c;
                        color: white;
                        box-shadow: 0 3px 10px rgba(64, 145, 108, 0.3);
                    }

                    .analytics-nav-btn.active .nav-icon-wrapper i {
                        color: #ffffff;
                    }

                    .analytics-nav-btn.active:hover {
                        background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
                        border-color: #2d6a4f;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 14px rgba(64, 145, 108, 0.35);
                    }

                    /* Responsive adjustments */
                    @media (max-width: 992px) {
                        .analytics-nav-btn {
                            padding: 0.5rem 1rem;
                            font-size: 0.8rem;
                        }

                        .nav-icon-wrapper i {
                            font-size: 0.9rem;
                        }

                        .nav-label {
                            font-size: 0.8rem;
                        }
                    }

                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            padding: 0.45rem 0.8rem;
                            font-size: 0.75rem;
                            gap: 6px;
                        }

                        .nav-icon-wrapper i {
                            font-size: 0.85rem;
                        }

                        .nav-label {
                            font-size: 0.75rem;
                        }
                    }

                    /* Custom Color Variables */
                    :root {
                        --primary-color: #3b82f6;
                        --success-color: #10b981;
                        --warning-color: #f59e0b;
                        --danger-color: #ef4444;
                        --info-color: #0ea5e9;
                        --purple-color: #8b5cf6;
                        --dark-color: #1f2937;
                    }

                    /* Metric Cards */
                    .metric-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border-radius: 10px;
                        cursor: pointer;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    }

                    /* Service Navigation */
                    .nav-pills .nav-link {
                        border-radius: 20px;
                        padding: 0.5rem 1.25rem;
                        margin: 0 0.25rem;
                        transition: all 0.3s ease;
                        font-weight: 500;
                        font-size: 0.9rem;
                        color: #495057;
                    }

                    .nav-pills .nav-link:hover {
                        background-color: rgba(255, 255, 255, 0.8);
                        color: #007bff;
                        transform: translateY(-2px);
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    }

                    .nav-pills .nav-link.active {
                        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
                        color: white !important;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.5) !important;
                    }
                    }

                    .service-nav .nav-link:hover {
                        color: var(--primary-color);
                        background: white;
                        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
                    }

                    .service-nav .nav-link.active {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                        color: white;
                        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                    }

                    /* Metric Cards */
                    .metric-card {
                        transition: all 0.3s ease;
                        border-radius: 12px;
                        overflow: hidden;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
                    }

                    .metric-label {
                        font-size: 0.875rem;
                        font-weight: 500;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }

                    .metric-value {
                        font-size: 2rem;
                        font-weight: 700;
                        color: var(--dark-color);
                    }

                    .metric-icon {
                        width: 56px;
                        height: 56px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        border-radius: 12px;
                        font-size: 1.5rem;
                    }

                    /* Soft Background Colors */
                    .bg-primary-soft {
                        background-color: rgba(59, 130, 246, 0.1);
                    }

                    .bg-success-soft {
                        background-color: rgba(16, 185, 129, 0.1);
                    }

                    .bg-warning-soft {
                        background-color: rgba(245, 158, 11, 0.1);
                    }

                    .bg-danger-soft {
                        background-color: rgba(239, 68, 68, 0.1);
                    }

                    .bg-info-soft {
                        background-color: rgba(14, 165, 233, 0.1);
                    }

                    .bg-purple-soft {
                        background-color: rgba(139, 92, 246, 0.1);
                    }

                    .bg-secondary-soft {
                        background-color: rgba(107, 114, 128, 0.1);
                    }

                    .text-purple {
                        color: var(--purple-color);
                    }

                    /* Badge Soft Colors */
                    .badge-success-soft {
                        background-color: rgba(16, 185, 129, 0.1);
                        color: var(--success-color);
                    }

                    .bg-purple {
                        background-color: var(--purple-color);
                    }

                    /* Chart Enhancements */
                    .status-chart-container {
                        position: relative;
                        height: 220px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    #boatrStatusChart {
                        max-height: 220px;
                        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
                    }

                    /* Card Styles */
                    .card {
                        border-radius: 12px;
                        transition: all 0.3s ease;
                    }

                    .card-header {
                        border-radius: 12px 12px 0 0 !important;
                        padding: 1.25rem;
                    }

                    /* Gradient Backgrounds */
                    .bg-gradient-primary {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                    }

                    /* Status Legend */
                    .status-legends .legend-item {
                        transition: all 0.2s ease;
                        background: #f8fafc;
                        border: 1px solid transparent;
                    }

                    .status-legends .legend-item:hover {
                        background: #f1f5f9;
                        transform: translateX(5px);
                        border-color: #e2e8f0;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                    }

                    .legend-dot {
                        width: 14px;
                        height: 14px;
                        border-radius: 50%;
                        display: inline-block;
                        border: 2px solid #ffffff;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    }

                    /* Progress Bars */
                    .progress {
                        border-radius: 10px;
                        background-color: #f1f5f9;
                    }

                    .progress-bar {
                        border-radius: 10px;
                        transition: width 0.6s ease;
                    }

                    /* Metric Items */
                    .metric-item {
                        padding: 1rem;
                        border-radius: 10px;
                        background: #f8fafc;
                        transition: all 0.3s ease;
                    }

                    .metric-item:hover {
                        background: #f1f5f9;
                        transform: scale(1.02);
                    }

                    /* Performance Metrics */
                    .performance-metric {
                        padding: 1rem;
                        border-radius: 10px;
                        background: #f8fafc;
                        transition: all 0.3s ease;
                    }

                    .performance-metric:hover {
                        background: #f1f5f9;
                        transform: scale(1.02);
                    }

                    /* Boat Type Items */
                    .boat-type-item {
                        padding: 12px 0;
                    }

                    .rank-badge {
                        width: 32px;
                        height: 32px;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                        color: white;
                        border-radius: 8px;
                        font-weight: 700;
                        margin-right: 12px;
                        font-size: 14px;
                    }

                    /* Inspection Stats */
                    .inspection-stat {
                        transition: transform 0.2s ease;
                    }

                    .inspection-stat:hover {
                        transform: scale(1.05);
                    }

                    .inspection-stat h3 {
                        font-size: 28px;
                        font-weight: 700;
                        margin: 0;
                    }

                    /* Enhanced Inspection Stats */
                    .inspection-stat-enhanced {
                        transition: all 0.3s ease;
                        cursor: pointer;
                        position: relative;
                        overflow: hidden;
                    }

                    .inspection-stat-enhanced:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                    }

                    .inspection-stat-enhanced::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -100%;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                        transition: left 0.6s ease;
                    }

                    .inspection-stat-enhanced:hover::before {
                        left: 100%;
                    }

                    .inspection-icon {
                        transition: transform 0.3s ease;
                    }

                    .inspection-stat-enhanced:hover .inspection-icon i {
                        transform: scale(1.2);
                    }

                    /* Inspector Section */
                    .inspector-item {
                        transition: all 0.2s ease;
                        border: 1px solid transparent;
                    }

                    .inspector-item:hover {
                        background-color: rgba(59, 130, 246, 0.05) !important;
                        border-color: rgba(59, 130, 246, 0.2);
                        transform: translateX(5px);
                    }

                    .inspector-rank {
                        transition: all 0.2s ease;
                    }

                    .inspector-item:hover .inspector-rank {
                        transform: scale(1.1);
                        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
                    }

                    /* Insight Icons */
                    .insight-icon-sm {
                        width: 36px;
                        height: 36px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s ease;
                    }

                    .alert:hover .insight-icon-sm {
                        transform: scale(1.1);
                    }

                    /* Progress Bar Enhancements */
                    .progress {
                        background-color: rgba(0, 0, 0, 0.05);
                        border-radius: 10px;
                        overflow: hidden;
                    }

                    .progress-bar {
                        border-radius: 10px;
                        transition: width 0.6s ease;
                        position: relative;
                    }

                    .bg-gradient {
                        background: linear-gradient(90deg, #3b82f6 0%, #0ea5e9 100%) !important;
                    }

                    /* Responsive Enhancements */
                    @media (max-width: 768px) {
                        .inspection-stat-enhanced h3 {
                            font-size: 24px;
                        }

                        .inspector-item {
                            margin-bottom: 12px !important;
                        }
                    }

                    /* Table Styles */
                    .table-hover tbody tr {
                        transition: all 0.2s ease;
                    }

                    .table-hover tbody tr:hover {
                        background-color: #f8fafc;
                        transform: scale(1.01);
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
                    }

                    /* Chart Containers - Enhanced */
                    .status-chart-container {
                        position: relative;
                        height: 220px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    #boatrStatusChart {
                        max-height: 220px;
                        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
                    }

                    /* Responsive Adjustments */
                    @media (max-width: 768px) {
                        .service-nav {
                            flex-wrap: wrap;
                            border-radius: 12px;
                            padding: 0.25rem;
                        }

                        .service-nav .nav-link {
                            font-size: 0.875rem;
                            padding: 0.5rem 0.75rem;
                            margin: 0.25rem;
                        }

                        .metric-value {
                            font-size: 1.5rem;
                        }

                        .metric-icon {
                            width: 48px;
                            height: 48px;
                            font-size: 1.25rem;
                        }

                        .rank-badge {
                            width: 28px;
                            height: 28px;
                        }
                    }

                    /* Animation */
                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    .card {
                        animation: fadeInUp 0.5s ease;
                    }

                    /* Scrollbar Styling */
                    .table-responsive::-webkit-scrollbar {
                        height: 8px;
                    }

                    .table-responsive::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 10px;
                    }

                    .table-responsive::-webkit-scrollbar-thumb {
                        background: #cbd5e1;
                        border-radius: 10px;
                    }

                    .table-responsive::-webkit-scrollbar-thumb:hover {
                        background: #94a3b8;
                    }

                    /* Focus States */
                    .btn:focus,
                    .form-control:focus {
                        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
                    }

                    /* Loading State */
                    .card.loading {
                        opacity: 0.6;
                        pointer-events: none;
                    }

                    /* Insight Boxes */
                    .insight-box {
                        padding: 8px;
                    }

                    .insight-icon {
                        width: 48px;
                        height: 48px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .insight-icon i {
                        font-size: 24px;
                    }

                    /* Modal Styles */
                    .modal-content {
                        border-radius: 16px;
                    }

                    .modal-header {
                        border-radius: 16px 16px 0 0;
                    }
                </style>
            @endsection

            @section('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Chart instances
                        let chartInstances = {};

                        // Chart.js default configuration
                        Chart.defaults.font.family = "'Inter', sans-serif";
                        Chart.defaults.color = '#64748b';

                        // Initialize Status Chart
                        initializeStatusChart();

                        // Initialize Trends Chart
                        initializeTrendsChart();

                        /**
                         * Status Distribution Doughnut Chart
                         */
                        function initializeStatusChart() {
                            const ctx = document.getElementById('boatrStatusChart');
                            if (!ctx) return;

                            // Define status color mapping
                            const statusColorMap = {
                                'approved': '#10b981', // emerald
                                'rejected': '#ef4444', // red
                                'under_review': '#f59e0b', // amber
                                'inspection_scheduled': '#0ea5e9', // sky blue
                                'inspection_required': '#8b5cf6', // violet
                                'documents_pending': '#6366f1', // indigo
                                'pending': '#64748b' // slate
                            };

                            // Get status data and create dynamic colors array
                            const statusData = [{{ implode(',', $statusAnalysis['counts']) }}];
                            const statusKeys = [
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    '{{ $status }}',
                                @endforeach
                            ];
                            const statusLabels = [
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                                @endforeach
                            ];

                            // Create colors array in the same order as data
                            const chartColors = statusKeys.map(status => statusColorMap[status] || '#64748b');
                            const totalApplications = statusData.reduce((a, b) => a + b, 0);

                            chartInstances.statusChart = new Chart(ctx.getContext('2d'), {
                                type: 'doughnut',
                                data: {
                                    labels: statusLabels,
                                    datasets: [{
                                        data: statusData,
                                        backgroundColor: chartColors,
                                        borderWidth: 3,
                                        borderColor: '#ffffff',
                                        cutout: '70%',
                                        spacing: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        },
                                        tooltip: {
                                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                            padding: 16,
                                            cornerRadius: 12,
                                            titleFont: {
                                                size: 15,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 14
                                            },
                                            borderColor: 'rgba(255, 255, 255, 0.2)',
                                            borderWidth: 1,
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.parsed;
                                                    const percentage = ((value / totalApplications) * 100).toFixed(
                                                        1);
                                                    return `${label}: ${value} applications (${percentage}%)`;
                                                }
                                            }
                                        }
                                    },
                                    onHover: (event, activeElements) => {
                                        event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' :
                                            'default';
                                    }
                                },
                                plugins: [{
                                    id: 'centerText',
                                    beforeDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const chartArea = chart.chartArea;
                                        const centerX = (chartArea.left + chartArea.right) / 2;
                                        const centerY = (chartArea.top + chartArea.bottom) / 2;

                                        ctx.save();
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';

                                        // Draw total applications count
                                        ctx.font = 'bold 28px Inter, sans-serif';
                                        ctx.fillStyle = '#1f2937';
                                        ctx.fillText(totalApplications.toLocaleString(), centerX, centerY -
                                            10);
                                    }
                                }, {
                                    id: 'segmentLabels',
                                    afterDatasetsDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const meta = chart.getDatasetMeta(0);

                                        ctx.save();
                                        ctx.font = 'bold 14px Inter, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillStyle = '#ffffff';

                                        meta.data.forEach((element, index) => {
                                            const value = statusData[index];
                                            const percentage = ((value / totalApplications) * 100)
                                                .toFixed(1);

                                            // Only show percentage if it's greater than 5% to avoid cluttering
                                            if (percentage > 5) {
                                                // Calculate the middle angle of the segment
                                                const startAngle = element.startAngle;
                                                const endAngle = element.endAngle;
                                                const midAngle = (startAngle + endAngle) / 2;

                                                // Calculate position based on the segment's center point
                                                const chartArea = chart.chartArea;
                                                const centerX = (chartArea.left + chartArea.right) /
                                                    2;
                                                const centerY = (chartArea.top + chartArea.bottom) /
                                                    2;

                                                // Position the text at 70% of the radius from center
                                                const radius = (element.outerRadius - element
                                                    .innerRadius) * 0.7 + element.innerRadius;
                                                const x = centerX + Math.cos(midAngle) * radius;
                                                const y = centerY + Math.sin(midAngle) * radius;

                                                const text = `${percentage}%`;

                                                ctx.fillStyle = '#ffffff';
                                                ctx.strokeStyle = '#000000';
                                                ctx.lineWidth = 3;
                                                ctx.strokeText(text, x, y);
                                                ctx.fillText(text, x, y);
                                            }
                                        });

                                        ctx.restore();
                                    }
                                }, {
                                    id: 'centerTextLabel',
                                    afterDatasetsDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const chartArea = chart.chartArea;
                                        const centerX = (chartArea.left + chartArea.right) / 2;
                                        const centerY = (chartArea.top + chartArea.bottom) / 2;

                                        ctx.save();
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';

                                        // Draw label
                                        ctx.font = '500 14px Inter, sans-serif';
                                        ctx.fillStyle = '#64748b';
                                        ctx.fillText('Total Applications', centerX, centerY + 15);

                                        ctx.restore();
                                    }
                                }]
                            });

                            // Add percentage labels on hover
                            const canvas = ctx;
                            canvas.addEventListener('mousemove', function(event) {
                                const points = chartInstances.statusChart.getElementsAtEventForMode(event, 'nearest', {
                                    intersect: true
                                }, true);
                                if (points.length) {
                                    const firstPoint = points[0];
                                    const value = statusData[firstPoint.index];
                                    const percentage = ((value / totalApplications) * 100).toFixed(1);
                                    canvas.title = `${statusLabels[firstPoint.index]}: ${percentage}%`;
                                }
                            });
                        }

                        /**
                         * Monthly Trends Line Chart
                         */
                        function initializeTrendsChart() {
                            const ctx = document.getElementById('boatrTrendsChart');
                            if (!ctx) return;

                            chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
                                type: 'line',
                                data: {
                                    labels: [
                                        @foreach ($monthlyTrends as $trend)
                                            '{{ \Carbon\Carbon::createFromFormat('Y-m', $trend->month)->format('M Y') }}',
                                        @endforeach
                                    ],
                                    datasets: [{
                                            label: 'Total Applications',
                                            data: [
                                                {{ $monthlyTrends->pluck('total_applications')->implode(',') }}
                                            ],
                                            borderColor: '#3b82f6',
                                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                            borderWidth: 3,
                                            tension: 0.4,
                                            fill: true,
                                            pointBackgroundColor: '#3b82f6',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 5,
                                            pointHoverRadius: 7,
                                            pointHoverBorderWidth: 3
                                        },
                                        {
                                            label: 'Approved',
                                            data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            borderWidth: 3,
                                            tension: 0.4,
                                            fill: true,
                                            pointBackgroundColor: '#10b981',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 5,
                                            pointHoverRadius: 7,
                                            pointHoverBorderWidth: 3
                                        },
                                        {
                                            label: 'Inspections',
                                            data: [
                                                {{ $monthlyTrends->pluck('inspections_completed')->implode(',') }}
                                            ],
                                            borderColor: '#f59e0b',
                                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                            borderWidth: 2,
                                            tension: 0.4,
                                            fill: false,
                                            pointBackgroundColor: '#f59e0b',
                                            pointBorderColor: '#ffffff',
                                            pointBorderWidth: 2,
                                            pointRadius: 4,
                                            pointHoverRadius: 6
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: {
                                        mode: 'index',
                                        intersect: false,
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                display: false,
                                                drawBorder: false
                                            },
                                            ticks: {
                                                font: {
                                                    size: 12,
                                                    weight: '500'
                                                },
                                                color: '#64748b'
                                            }
                                        },
                                        y: {
                                            beginAtZero: true,
                                            grid: {
                                                color: 'rgba(0, 0, 0, 0.05)',
                                                drawBorder: false
                                            },
                                            ticks: {
                                                font: {
                                                    size: 12,
                                                    weight: '500'
                                                },
                                                color: '#64748b',
                                                padding: 10
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                            align: 'end',
                                            labels: {
                                                usePointStyle: true,
                                                padding: 20,
                                                font: {
                                                    size: 13,
                                                    weight: '500'
                                                },
                                                color: '#64748b'
                                            }
                                        },
                                        tooltip: {
                                            mode: 'index',
                                            intersect: false,
                                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                            titleColor: 'white',
                                            bodyColor: 'white',
                                            borderColor: 'rgba(255, 255, 255, 0.1)',
                                            borderWidth: 1,
                                            cornerRadius: 8,
                                            padding: 12,
                                            displayColors: true,
                                            titleFont: {
                                                size: 14,
                                                weight: 'bold'
                                            },
                                            bodyFont: {
                                                size: 13
                                            }
                                        }
                                    }
                                }
                            });
                        }

                        /**
                         * Cleanup function
                         */
                        window.destroyCharts = function() {
                            Object.values(chartInstances).forEach(chart => {
                                if (chart) {
                                    chart.destroy();
                                }
                            });
                            chartInstances = {};
                        };

                        /**
                         * Add smooth animations on scroll
                         */
                        const observerOptions = {
                            threshold: 0.1,
                            rootMargin: '0px 0px -50px 0px'
                        };

                        const observer = new IntersectionObserver(function(entries) {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    entry.target.style.opacity = '1';
                                    entry.target.style.transform = 'translateY(0)';
                                }
                            });
                        }, observerOptions);

                        document.querySelectorAll('.card').forEach(card => {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(20px)';
                            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            observer.observe(card);
                        });

                        /**
                         * Add loading state to form submission
                         */
                        const filterForm = document.querySelector('form[action*="analytics.boatr"]');
                        if (filterForm) {
                            filterForm.addEventListener('submit', function() {
                                const submitBtn = this.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                                    submitBtn.disabled = true;
                                }
                            });
                        }

                        /**
                         * Add animation to metric cards
                         */
                        const metricCards = document.querySelectorAll('.metric-card');
                        metricCards.forEach((card, index) => {
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, index * 100);
                        });

                        /**
                         * Smooth scroll for navigation
                         */
                        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                            anchor.addEventListener('click', function(e) {
                                e.preventDefault();
                                const target = document.querySelector(this.getAttribute('href'));
                                if (target) {
                                    target.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start'
                                    });
                                }
                            });
                        });
                    });
                </script>
            @endsection

            @section('styles')
                <style>
                    /* Modern Analytics Navigation */
                    .analytics-nav-btn {
                        background: #f8f9fa;
                        border: 1px solid #e9ecef;
                        color: #6c757d;
                        font-weight: 500;
                        font-size: 0.875rem;
                        padding: 0.5rem 1rem;
                        border-radius: 2rem;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                    }

                    .analytics-nav-btn:hover {
                        background: #e9ecef;
                        border-color: #dee2e6;
                        color: #495057;
                        text-decoration: none;
                    }

                    .analytics-nav-btn.active {
                        background: #0d6efd;
                        border-color: #0d6efd;
                        color: white;
                        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
                    }

                    .analytics-nav-btn.active:hover {
                        background: #0b5ed7;
                        border-color: #0a58ca;
                        color: white;
                    }

                    .analytics-nav-btn i {
                        font-size: 0.875rem;
                    }

                    /* Responsive adjustments */
                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            font-size: 0.75rem;
                            padding: 0.375rem 0.75rem;
                        }

                        .analytics-nav-btn i {
                            font-size: 0.75rem;
                        }
                    }

                    .metric-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border-radius: 10px;
                        cursor: pointer;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    }

                    /* Navigation Container */
                    .navigation-container {
                        background: #f8f9fa;
                        border-radius: 15px;
                        border: 1px solid #dee2e6;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
                    }

                    /* Modern Analytics Navigation */
                    .analytics-nav-btn {
                        background: #e9ecef;
                        border: 1px solid #ced4da;
                        color: #495057;
                        font-weight: 500;
                        font-size: 0.875rem;
                        padding: 0.6rem 1.2rem;
                        border-radius: 2rem;
                        text-decoration: none;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        white-space: nowrap;
                        position: relative;
                        overflow: hidden;
                        transform: translateY(0);
                    }

                    .analytics-nav-btn::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -100%;
                        width: 100%;
                        height: 100%;
                        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
                        transition: left 0.5s;
                    }

                    .analytics-nav-btn:hover {
                        background: #6c757d;
                        border-color: #5a6268;
                        color: white;
                        text-decoration: none;
                        transform: translateY(-3px);
                        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
                    }

                    .analytics-nav-btn:hover::before {
                        left: 100%;
                    }

                    .analytics-nav-btn:hover i {
                        transform: scale(1.15) rotate(5deg);
                    }

                    .analytics-nav-btn.active {
                        background: linear-gradient(135deg, #495057 0%, #343a40 100%);
                        border-color: #495057;
                        color: white;
                        box-shadow: 0 4px 20px rgba(73, 80, 87, 0.4);
                        transform: translateY(-1px);
                    }

                    .analytics-nav-btn.active:hover {
                        background: linear-gradient(135deg, #343a40 0%, #212529 100%);
                        border-color: #343a40;
                        color: white;
                        transform: translateY(-4px);
                        box-shadow: 0 8px 30px rgba(73, 80, 87, 0.6);
                    }

                    .analytics-nav-btn i {
                        font-size: 0.875rem;
                        transition: transform 0.3s ease;
                    }

                    /* Responsive adjustments */
                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            font-size: 0.75rem;
                            padding: 0.375rem 0.75rem;
                        }

                        .analytics-nav-btn i {
                            font-size: 0.75rem;
                        }
                    }

                    .metric-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border-radius: 10px;
                        cursor: pointer;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    }

                    .card {
                        border-radius: 10px;
                        transition: all 0.3s ease;
                    }

                    .card:hover {
                        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                    }
                </style>
            @endsection

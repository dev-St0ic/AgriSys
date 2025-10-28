{{-- resources/views/admin/analytics/boatr.blade.php --}}

@extends('layouts.app')

@section('title', 'BOATR Analytics - AgriSys Admin')
@section('page-title', 'BOATR Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Title and Description -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">BOATR Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Boat Registration Services</p>
                    </div>

                    <!-- Service Navigation Tabs -->
                    <div class="d-flex justify-content-center">
                        <ul class="nav nav-pills service-nav" id="serviceTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.seedlings') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                                    <i class="fas fa-seedling me-2"></i>Seedlings
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.rsbsa') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                                    <i class="fas fa-user-check me-2"></i>RSBSA
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.fishr') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                                    <i class="fas fa-fish me-2"></i>FISHR
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.boatr') }}" class="nav-link active">
                                    <i class="fas fa-ship me-2"></i>BOATR
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.training') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                                    <i class="fas fa-graduation-cap me-2"></i>Training
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.supply-management') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                                    <i class="fas fa-boxes me-1"></i> Supply Management
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.analytics.user-registration') }}"
                                    class="nav-link {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                                    <i class="fas fa-user-plus me-2"></i>User Registration
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.analytics.boatr') }}" class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label for="start_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>Start Date
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label for="end_date" class="form-label fw-semibold">
                                <i class="fas fa-calendar-check text-primary me-1"></i>End Date
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-filter me-2"></i>Apply Filter
                                </button>
                                <a href="{{ route('admin.analytics.boatr.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                    class="btn btn-success px-4">
                                    <i class="fas fa-download me-2"></i>Export Data
                                </a>
                                <button type="button" class="btn btn-outline-info px-4" data-bs-toggle="modal"
                                    data-bs-target="#boatrInsightsModal">
                                    <i class="fas fa-lightbulb me-2"></i>AI Insights
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4 g-3">
        <!-- Total Applications -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Total Applications</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_applications']) }}</h2>
                            <span class="badge badge-success-soft">
                                <i class="fas fa-users me-1"></i>{{ number_format($overview['unique_applicants']) }} boat
                                owners
                            </span>
                        </div>
                        <div class="metric-icon bg-primary-soft">
                            <i class="fas fa-ship text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Rate -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Approval Rate</p>
                            <h2 class="metric-value mb-1">{{ $overview['approval_rate'] }}%</h2>
                            <small class="text-muted">{{ number_format($overview['approved_applications']) }}
                                approved</small>
                        </div>
                        <div class="metric-icon bg-success-soft">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registered Fleet -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Registered Fleet</p>
                            <h2 class="metric-value mb-1">{{ $overview['unique_vessels'] }}</h2>
                            <small class="text-muted">{{ $overview['unique_boat_types'] }} boat types</small>
                        </div>
                        <div class="metric-icon bg-purple-soft">
                            <i class="fas fa-anchor text-purple"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspection Rate -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Inspection Rate</p>
                            <h2 class="metric-value mb-1">{{ $overview['inspection_completion_rate'] }}%</h2>
                            <small class="text-muted">{{ number_format($overview['inspections_completed']) }}
                                completed</small>
                        </div>
                        <div class="metric-icon bg-warning-soft">
                            <i class="fas fa-clipboard-check text-warning"></i>
                        </div>
                    </div>
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
                                'inspection_required' => 'primary',
                                'documents_pending' => 'secondary',
                                'pending' => 'secondary',
                            ];
                        @endphp
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span class="legend-dot bg-{{ $statusColors[$status] ?? 'secondary' }} me-2"></span>
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div>
                                    <span
                                        class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}-soft text-{{ $statusColors[$status] ?? 'secondary' }}">
                                        {{ $count }}
                                    </span>
                                    <span class="text-muted ms-2">{{ $statusAnalysis['percentages'][$status] }}%</span>
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
                            <div class="progress-bar bg-info"
                                style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
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
                                    <span class="rank-badge">{{ $index + 1 }}</span>
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
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="inspection-stat bg-success-subtle p-3 rounded text-center">
                                <h3 class="text-success mb-1">{{ $inspectionAnalysis['inspections_completed'] }}</h3>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="inspection-stat bg-warning-subtle p-3 rounded text-center">
                                <h3 class="text-warning mb-1">{{ $inspectionAnalysis['inspections_scheduled'] }}</h3>
                                <small class="text-muted">Scheduled</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="inspection-stat bg-danger-subtle p-3 rounded text-center">
                                <h3 class="text-danger mb-1">{{ $inspectionAnalysis['inspections_required'] }}</h3>
                                <small class="text-muted">Required</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="inspection-stat bg-info-subtle p-3 rounded text-center">
                                <h3 class="text-info mb-1">{{ $inspectionAnalysis['avg_inspection_time'] }}d</h3>
                                <small class="text-muted">Avg Time</small>
                            </div>
                        </div>
                    </div>

                    @if ($inspectionAnalysis['inspector_workload']->isNotEmpty())
                        <h6 class="mb-3 fw-semibold">Inspector Workload</h6>
                        @foreach ($inspectionAnalysis['inspector_workload']->take(5) as $inspector)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span
                                    class="text-truncate">{{ $inspector->inspector->name ?? 'Inspector #' . $inspector->inspected_by }}</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 80px; height: 6px;">
                                        <div class="progress-bar bg-info"
                                            style="width: {{ ($inspector->inspections_count / $inspectionAnalysis['inspector_workload']->first()->inspections_count) * 100 }}%">
                                        </div>
                                    </div>
                                    <span class="badge bg-info">{{ $inspector->inspections_count }}</span>
                                </div>
                            </div>
                        @endforeach
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

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Key Insight:</strong> Applications with documents have
                        {{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}%
                        higher approval rate.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Insights Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">
                    <i class="fas fa-chart-pie me-2"></i>Key Performance Insights
                </h5>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="insight-box">
                            <div class="insight-icon bg-success bg-opacity-25 mb-2">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h6 class="fw-bold">{{ $overview['approval_rate'] }}% Approval Rate</h6>
                            <p class="mb-0 small opacity-75">{{ $overview['approved_applications'] }} approved out of
                                {{ $overview['total_applications'] }} total applications</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="insight-box">
                            <div class="insight-icon bg-info bg-opacity-25 mb-2">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h6 class="fw-bold">{{ $processingTimeAnalysis['avg_processing_days'] }}d Processing</h6>
                            <p class="mb-0 small opacity-75">Average time from submission to decision</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="insight-box">
                            <div class="insight-icon bg-warning bg-opacity-25 mb-2">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h6 class="fw-bold">{{ $documentAnalysis['user_doc_submission_rate'] }}% Doc Rate</h6>
                            <p class="mb-0 small opacity-75">
                                {{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}%
                                higher approval with documents</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="insight-box">
                            <div class="insight-icon bg-primary bg-opacity-25 mb-2">
                                <i class="fas fa-ship"></i>
                            </div>
                            <h6 class="fw-bold">{{ $overview['unique_boat_types'] }} Boat Types</h6>
                            <p class="mb-0 small opacity-75">Diverse fleet with {{ $overview['unique_vessels'] }}
                                registered vessels</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- AI Insights Modal -->
    <div class="modal fade" id="boatrInsightsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-robot me-2"></i>BOATR AI-Powered Insights
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-success me-2"></i>Growth
                                Opportunities</h6>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>Streamline Processing:</strong> Current
                                    {{ $processingTimeAnalysis['avg_processing_days'] }}d average can be reduced
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-file-alt text-info me-2"></i>
                                    <strong>Digital Documents:</strong>
                                    {{ 100 - $documentAnalysis['user_doc_submission_rate'] }}% applications lack documents
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-ship text-primary me-2"></i>
                                    <strong>Fleet Diversity:</strong> Focus on
                                    {{ $boatTypeAnalysis->first()->boat_type ?? 'primary' }} type optimization
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas
                                for Improvement</h6>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <strong>Processing Speed:</strong>
                                    {{ $processingTimeAnalysis['avg_processing_days'] > 10 ? 'Reduce processing time to under 10 days' : 'Maintain current processing efficiency' }}
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-clipboard-check text-info me-2"></i>
                                    <strong>Inspection Rate:</strong>
                                    {{ $inspectionAnalysis['completion_rate'] < 80 ? 'Improve completion to 80%+' : 'Maintain inspection quality standards' }}
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-balance-scale text-success me-2"></i>
                                    <strong>Gear Balance:</strong> Ensure fair distribution across fishing gear types
                                </li>
                            </ul>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="alert alert-primary mb-0">
                        <h6 class="fw-bold mb-2"><i class="fas fa-lightbulb me-2"></i>Strategic Recommendation</h6>
                        <p class="mb-2">Based on current data analysis:</p>
                        <ul class="mb-0">
                            <li>Implement mobile inspection units for remote coastal areas</li>
                            <li>Develop digital checklist system to standardize inspections</li>
                            <li>Create document submission awareness campaign
                                ({{ 100 - $documentAnalysis['user_doc_submission_rate'] }}% gap)</li>
                            <li>Establish fast-track processing for complete applications</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
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

        /* Service Navigation */
        .service-nav {
            background: #f8fafc;
            padding: 0.5rem;
            border-radius: 50px;
            display: inline-flex;
        }

        .service-nav .nav-link {
            border-radius: 30px;
            padding: 0.5rem 1.25rem;
            margin: 0 0.25rem;
            font-weight: 500;
            color: #64748b;
            transition: all 0.3s ease;
            border: none;
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
        }

        .status-legends .legend-item:hover {
            background: #f1f5f9;
            transform: translateX(5px);
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
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

        /* Table Styles */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Chart Containers */
        .status-chart-container {
            position: relative;
            height: 220px;
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

                chartInstances.statusChart = new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: [
                            @foreach ($statusAnalysis['counts'] as $status => $count)
                                '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                            @endforeach
                        ],
                        datasets: [{
                            data: [{{ implode(',', $statusAnalysis['counts']) }}],
                            backgroundColor: [
                                '#10b981', // approved - green
                                '#ef4444', // rejected - red
                                '#f59e0b', // under_review - amber
                                '#0ea5e9', // inspection_scheduled - blue
                                '#3b82f6', // inspection_required - indigo
                                '#6b7280', // documents_pending - gray
                                '#6b7280' // pending - gray
                            ],
                            borderWidth: 0,
                            cutout: '75%',
                            spacing: 2
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
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
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
                                    {{ $monthlyTrends->pluck('total_applications')->implode(',') }}],
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
                                    {{ $monthlyTrends->pluck('inspections_completed')->implode(',') }}],
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

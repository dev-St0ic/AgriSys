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
                <div class="text-center mb-4">
                    <h4 class="mb-2 fw-bold">BOATR Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive insights into Boat Registration Services</p>
                </div>
                
                <div class="d-flex justify-content-center">
                    <ul class="nav nav-pills" id="serviceTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.seedlings') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                                <i class="fas fa-seedling me-1"></i> Seedlings
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.rsbsa') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                                <i class="fas fa-user-check me-1"></i> RSBSA
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.fishr') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                                <i class="fas fa-fish me-1"></i> FISHR
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.boatr') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                                <i class="fas fa-ship me-1"></i> BOATR
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.training') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-1"></i> Training
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.supply-management') }}"
                                class="nav-link {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                                <i lass="fas fa-boxes me-1"></i> Supply Management
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.user-registration') }}"
                            class="nav-link {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                                <i class="fas fa-user-plus me-1"></i> User Registration
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
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="{{ $endDate }}">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-filter me-2"></i> Apply Filter
                        </button>
                        <a href="{{ route('admin.analytics.boatr.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                           class="btn btn-success px-4">
                            <i class="fas fa-download me-2"></i> Export Data
                        </a>
                        <button type="button" class="btn btn-outline-info px-4" data-bs-toggle="modal" data-bs-target="#boatrInsightsModal">
                            <i class="fas fa-lightbulb me-2"></i> AI Insights
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="metric-card card-blue">
            <div class="metric-icon">
                <i class="fas fa-ship"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Total Applications</h6>
                <h2 class="metric-value">{{ number_format($overview['total_applications']) }}</h2>
                <p class="metric-detail">{{ number_format($overview['unique_applicants']) }} boat owners</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="metric-card card-green">
            <div class="metric-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Approval Rate</h6>
                <h2 class="metric-value">{{ $overview['approval_rate'] }}%</h2>
                <p class="metric-detail">{{ number_format($overview['approved_applications']) }} approved</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="metric-card card-purple">
            <div class="metric-icon">
                <i class="fas fa-anchor"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Registered Fleet</h6>
                <h2 class="metric-value">{{ $overview['unique_vessels'] }}</h2>
                <p class="metric-detail">{{ $overview['unique_boat_types'] }} boat types</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="metric-card card-orange">
            <div class="metric-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Inspection Rate</h6>
                <h2 class="metric-value">{{ $overview['inspection_completion_rate'] }}%</h2>
                <p class="metric-detail">{{ number_format($overview['inspections_completed']) }} completed</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Row -->
<div class="row g-4 mb-4">
    <!-- Application Trends -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-line text-primary me-2"></i>Application Trends Over Time
                </h5>
            </div>
            <div class="card-body">
                <canvas id="boatrTrendsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Status Distribution -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-tasks text-primary me-2"></i>Application Status
                </h5>
            </div>
            <div class="card-body">
                <div class="status-list">
                    @php
                        $statusColors = [
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'under_review' => 'warning',
                            'inspection_scheduled' => 'info',
                            'inspection_required' => 'primary',
                            'documents_pending' => 'secondary',
                            'pending' => 'secondary'
                        ];
                    @endphp
                    @foreach($statusAnalysis['counts'] as $status => $count)
                    <div class="status-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="status-name">
                                <span class="status-dot bg-{{ $statusColors[$status] ?? 'secondary' }}"></span>
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                            <div>
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} me-1">{{ $count }}</span>
                                <small class="text-muted">{{ $statusAnalysis['percentages'][$status] }}%</small>
                            </div>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}" 
                                 style="width: {{ $statusAnalysis['percentages'][$status] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
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
                        <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}">
                            {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}" 
                             style="width: {{ min(100, (21 - $processingTimeAnalysis['avg_processing_days']) / 21 * 100) }}%"></div>
                    </div>
                    <small class="text-muted">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d | Processed: {{ $processingTimeAnalysis['processed_count'] }}</small>
                </div>
                
                <hr class="my-3">
                
                <div class="performance-metric">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Completion Rate</h6>
                        <span class="badge bg-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px;">
                        <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                    </div>
                    <small class="text-muted">Applications completed from total submissions</small>
                </div>
                
                <hr class="my-3">
                
                <div class="performance-metric">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Quality Score</h6>
                        <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
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
                        <div class="progress-bar bg-primary" style="width: {{ min(100, $performanceMetrics['avg_applications_per_day'] * 10) }}%"></div>
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
                @foreach($boatTypeAnalysis->take(5) as $index => $boatType)
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
                             style="width: {{ ($boatType->total_applications / $boatTypeAnalysis->first()->total_applications) * 100 }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Approval: {{ round(($boatType->approved / max(1, $boatType->total_applications)) * 100, 1) }}%</span>
                        <span>Avg: {{ round($boatType->avg_length ?? 0, 1) }}ft × {{ round($boatType->avg_width ?? 0, 1) }}ft</span>
                    </div>
                </div>
                @if(!$loop->last)<hr class="my-3">@endif
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
                
                @if($inspectionAnalysis['inspector_workload']->isNotEmpty())
                <h6 class="mb-3 fw-semibold">Inspector Workload</h6>
                @foreach($inspectionAnalysis['inspector_workload']->take(5) as $inspector)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-truncate">{{ $inspector->inspector->name ?? 'Inspector #' . $inspector->inspected_by }}</span>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 80px; height: 6px;">
                            <div class="progress-bar bg-info" 
                                 style="width: {{ ($inspector->inspections_count / $inspectionAnalysis['inspector_workload']->first()->inspections_count) * 100 }}%"></div>
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
                @if($fishingGearAnalysis && $fishingGearAnalysis->count() > 0)
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
                                @foreach($fishingGearAnalysis->take(6) as $gear)
                                <tr>
                                    <td><strong>{{ ucwords($gear->primary_fishing_gear) }}</strong></td>
                                    <td class="text-center">{{ $gear->total_applications }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $gear->approved }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $gear->approval_rate > 80 ? 'success' : ($gear->approval_rate > 60 ? 'warning' : 'danger') }}">
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
                        <p class="text-muted mb-0">Try expanding your date range or check if applications have been submitted.</p>
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
                            <h3 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_user_docs'] }}%</h3>
                            <small class="text-muted">With Documents</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-warning-subtle rounded">
                            <h3 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_user_docs'] }}%</h3>
                            <small class="text-muted">Without Documents</small>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">User Documents Submitted</span>
                        <span class="text-success">{{ $documentAnalysis['with_user_documents'] }} / {{ $documentAnalysis['total'] }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $documentAnalysis['user_doc_submission_rate'] }}%"></div>
                    </div>
                    <small class="text-muted">{{ $documentAnalysis['user_doc_submission_rate'] }}% submission rate</small>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Inspection Documents</span>
                        <span class="text-info">{{ $documentAnalysis['with_inspection_documents'] }} / {{ $documentAnalysis['total'] }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" 
                             style="width: {{ $documentAnalysis['inspection_doc_submission_rate'] }}%"></div>
                    </div>
                    <small class="text-muted">{{ $documentAnalysis['inspection_doc_submission_rate'] }}% submission rate</small>
                </div>
                
                <div class="alert alert-info mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Key Insight:</strong> Applications with documents have {{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}% higher approval rate.
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
                            <p class="mb-0 small opacity-75">{{ $overview['approved_applications'] }} approved out of {{ $overview['total_applications'] }} total applications</p>
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
                            <p class="mb-0 small opacity-75">{{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}% higher approval with documents</p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="insight-box">
                            <div class="insight-icon bg-primary bg-opacity-25 mb-2">
                                <i class="fas fa-ship"></i>
                            </div>
                            <h6 class="fw-bold">{{ $overview['unique_boat_types'] }} Boat Types</h6>
                            <p class="mb-0 small opacity-75">Diverse fleet with {{ $overview['unique_vessels'] }} registered vessels</p>
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
                        <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-success me-2"></i>Growth Opportunities</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <strong>Streamline Processing:</strong> Current {{ $processingTimeAnalysis['avg_processing_days'] }}d average can be reduced
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                <strong>Digital Documents:</strong> {{ 100 - $documentAnalysis['user_doc_submission_rate'] }}% applications lack documents
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-ship text-primary me-2"></i>
                                <strong>Fleet Diversity:</strong> Focus on {{ $boatTypeAnalysis->first()->boat_type ?? 'primary' }} type optimization
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <strong>Processing Speed:</strong> {{ $processingTimeAnalysis['avg_processing_days'] > 10 ? 'Reduce processing time to under 10 days' : 'Maintain current processing efficiency' }}
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-clipboard-check text-info me-2"></i>
                                <strong>Inspection Rate:</strong> {{ $inspectionAnalysis['completion_rate'] < 80 ? 'Improve completion to 80%+' : 'Maintain inspection quality standards' }}
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
                        <li>Create document submission awareness campaign ({{ 100 - $documentAnalysis['user_doc_submission_rate'] }}% gap)</li>
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
/* Custom Gradient Backgrounds */
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%); }

/* Metric Cards */
.metric-card {
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.card-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
.card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
.card-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; }
.card-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }

.metric-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    flex-shrink: 0;
}

.metric-icon i {
    font-size: 28px;
}

.metric-content {
    flex: 1;
}

.metric-label {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 8px;
    font-weight: 500;
}

.metric-value {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1;
}

.metric-detail {
    font-size: 13px;
    opacity: 0.85;
    margin: 0;
}

/* Card Styling */
.card {
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 12px 12px 0 0 !important;
    padding: 16px 20px;
}

/* Status List */
.status-list {
    max-height: 400px;
    overflow-y: auto;
}

.status-item {
    padding: 12px 0;
}

.status-item:not(:last-child) {
    border-bottom: 1px solid #f1f5f9;
}

.status-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

/* Performance Metrics */
.performance-metric {
    padding: 16px;
    border-radius: 8px;
    background: #f8fafc;
    transition: background 0.2s ease;
}

.performance-metric:hover {
    background: #f1f5f9;
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

/* Navigation Pills */
.nav-pills .nav-link {
    border-radius: 50px;
    padding: 10px 20px;
    margin: 0 4px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-pills .nav-link:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

/* Table Styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
}

.table tbody tr {
    transition: background 0.2s ease;
}

.table-hover tbody tr:hover {
    background: #f8fafc;
}

/* Progress Bars */
.progress {
    background: rgba(0,0,0,0.08);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border: none;
}

.btn-outline-info {
    border: 2px solid #06b6d4;
    color: #06b6d4;
}

.btn-outline-info:hover {
    background: #06b6d4;
    color: white;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 10px 16px;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-label {
    font-size: 14px;
    color: #475569;
    margin-bottom: 6px;
}

/* Badge Styling */
.badge {
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 500;
}

/* Alert Styling */
.alert {
    border-radius: 10px;
    border: none;
}

.alert-info {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    color: #0c4a6e;
}

.alert-primary {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e3a8a;
}

/* Responsive Design */
@media (max-width: 768px) {
    .metric-card {
        flex-direction: column;
        text-align: center;
    }
    
    .metric-value {
        font-size: 28px;
    }
    
    .nav-pills {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .nav-pills .nav-link {
        margin: 4px;
    }
}

/* Scrollbar Styling */
.status-list::-webkit-scrollbar {
    width: 6px;
}

.status-list::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.status-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.status-list::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation */
@keyframes fadeIn {
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
    animation: fadeIn 0.5s ease-out;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Trends Chart
    const trendsCtx = document.getElementById('boatrTrendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyTrends as $trend)
                        '{{ \Carbon\Carbon::createFromFormat("Y-m", $trend->month)->format("M Y") }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Total Applications',
                    data: [{{ $monthlyTrends->pluck('total_applications')->implode(',') }}],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }, {
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
                    pointHoverRadius: 7
                }, {
                    label: 'Inspections',
                    data: [{{ $monthlyTrends->pluck('inspections_completed')->implode(',') }}],
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
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y;
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // Smooth scroll for navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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

    // Add loading state to filter button
    const filterForm = document.querySelector('form[method="GET"]');
    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Loading...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>
@endsection
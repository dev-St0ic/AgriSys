{{-- resources/views/admin/analytics/fishr.blade.php --}}

@extends('layouts.app')

@section('title', 'FISHR Analytics - AgriSys Admin')
@section('page-title', 'FISHR Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Title and Description -->
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-2">FISHR Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive insights into Fishermen Registration Services</p>
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
                               class="nav-link active">
                                <i class="fas fa-fish me-2"></i>FISHR
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.boatr') }}" 
                               class="nav-link {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
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
                <form method="GET" action="{{ route('admin.analytics.fishr') }}" class="row g-3 align-items-end">
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
                            <a href="{{ route('admin.analytics.fishr.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                               class="btn btn-success px-4">
                                <i class="fas fa-download me-2"></i>Export Data
                            </a>
                            <button type="button" class="btn btn-outline-info px-4" data-bs-toggle="modal" data-bs-target="#fishrInsightsModal">
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
                            <i class="fas fa-arrow-up me-1"></i>Active
                        </span>
                    </div>
                    <div class="metric-icon bg-primary-soft">
                        <i class="fas fa-fish text-primary"></i>
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
                        <small class="text-muted">{{ number_format($overview['approved_applications']) }} approved</small>
                    </div>
                    <div class="metric-icon bg-success-soft">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Community Reach -->
    <div class="col-lg-3 col-md-6">
        <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label text-muted mb-2">Community Reach</p>
                        <h2 class="metric-value mb-1">{{ $overview['active_barangays'] }}</h2>
                        <small class="text-muted">{{ number_format($overview['unique_applicants']) }} fishermen</small>
                    </div>
                    <div class="metric-icon bg-purple-soft">
                        <i class="fas fa-map-marker-alt text-purple"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Document Submission -->
    <div class="col-lg-3 col-md-6">
        <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="metric-label text-muted mb-2">Document Submission</p>
                        <h2 class="metric-value mb-1">{{ $overview['document_submission_rate'] }}%</h2>
                        <small class="text-muted">{{ number_format($overview['with_documents']) }} with docs</small>
                    </div>
                    <div class="metric-icon bg-warning-soft">
                        <i class="fas fa-file-alt text-warning"></i>
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
                    <canvas id="fishrStatusChart" height="220"></canvas>
                </div>
                <div class="status-legends">
                    @foreach($statusAnalysis['counts'] as $status => $count)
                    <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                        <div class="d-flex align-items-center">
                            <span class="legend-dot bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }} me-2"></span>
                            <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                        </div>
                        <div>
                            <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}-soft text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
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
                <canvas id="fishrTrendsChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics & Document Analysis -->
<div class="row mb-4 g-3">
    <!-- Performance Metrics -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-tachometer-alt text-primary me-2"></i>Performance Metrics
                </h5>
            </div>
            <div class="card-body">
                <!-- Processing Time -->
                <div class="metric-item mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Processing Time</h6>
                        <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}-soft text-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}">
                            {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}" 
                             style="width: {{ min(100, (14 - $processingTimeAnalysis['avg_processing_days']) / 14 * 100) }}%"></div>
                    </div>
                    <small class="text-muted">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d | Processed: {{ $processingTimeAnalysis['processed_count'] }}</small>
                </div>
                
                <!-- Completion Rate -->
                <div class="metric-item mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Completion Rate</h6>
                        <span class="badge bg-info-soft text-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                    </div>
                    <small class="text-muted">Applications completed</small>
                </div>
                
                <!-- Quality Score -->
                <div class="metric-item mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Quality Score</h6>
                        <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}-soft text-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                            {{ $performanceMetrics['quality_score'] }}%
                        </span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}" 
                             style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                    </div>
                    <small class="text-muted">Based on approval & document rates</small>
                </div>
                
                <!-- Daily Average -->
                <div class="metric-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Daily Average</h6>
                        <span class="badge bg-primary-soft text-primary">{{ $performanceMetrics['avg_applications_per_day'] }}</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-primary" style="width: {{ min(100, $performanceMetrics['avg_applications_per_day'] * 10) }}%"></div>
                    </div>
                    <small class="text-muted">Applications per day</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Impact Analysis -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-file-alt text-success me-2"></i>Document Impact
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-6">
                        <div class="p-3 rounded bg-success-soft">
                            <h3 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_docs'] }}%</h3>
                            <p class="mb-0 small text-muted">With Documents</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-warning-soft">
                            <h3 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_docs'] }}%</h3>
                            <p class="mb-0 small text-muted">Without Documents</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">With Documents</span>
                        <span class="fw-semibold text-success">{{ $documentAnalysis['with_documents'] }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" 
                             style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['with_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Without Documents</span>
                        <span class="fw-semibold text-warning">{{ $documentAnalysis['without_documents'] }}</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" 
                             style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['without_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                
                <div class="alert alert-info-soft border-0">
                    <div class="d-flex">
                        <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                        <small class="text-muted">
                            Applications with supporting documents have a <strong>{{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</strong> higher approval rate.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Livelihood Distribution -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-briefcase text-warning me-2"></i>Top Livelihoods
                </h5>
            </div>
            <div class="card-body">
                @foreach($livelihoodAnalysis->take(5) as $index => $livelihood)
                <div class="livelihood-item mb-3 p-3 rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                            <span class="fw-semibold">{{ ucfirst($livelihood->main_livelihood) }}</span>
                        </div>
                        <span class="badge bg-success-soft text-success">
                            {{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}%
                        </span>
                    </div>
                    <div class="progress mb-1" style="height: 6px;">
                        <div class="progress-bar bg-primary" 
                             style="width: {{ ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $livelihood->total_applications }} applications</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Top Performing Barangays -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-trophy text-warning me-2"></i>Top Performing Barangays
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">Rank</th>
                                <th>Barangay</th>
                                <th class="text-center">Total Applications</th>
                                <th class="text-center">Approved</th>
                                <th class="text-center">Approval Rate</th>
                                <th class="text-center">With Documents</th>
                                <th class="text-center">Unique Applicants</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barangayAnalysis->take(10) as $index => $barangay)
                            <tr>
                                <td class="text-center">
                                    @if($index < 3)
                                        <div class="rank-badge rank-{{ $index + 1 }}">
                                            <i class="fas fa-medal"></i>
                                            <span>{{ $index + 1 }}</span>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-dark">#{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ $barangay->barangay }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-primary-soft text-primary">{{ $barangay->total_applications }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-soft text-success">{{ $barangay->approved }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%"></div>
                                        </div>
                                        <small class="fw-semibold">{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-soft text-warning">{{ $barangay->with_documents }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-soft text-info">{{ $barangay->unique_applicants }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Insights -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-gradient-primary text-white border-0">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-lightbulb me-2"></i>Key Performance Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="insight-card">
                            <div class="insight-icon bg-success-soft">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <h6 class="fw-semibold mb-2">Strong Approval Rate</h6>
                            <p class="text-muted small mb-0">
                                {{ $overview['approval_rate'] }}% approval rate shows effective application processing and quality submissions.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="insight-card">
                            <div class="insight-icon bg-info-soft">
                                <i class="fas fa-users text-info"></i>
                            </div>
                            <h6 class="fw-semibold mb-2">Wide Community Reach</h6>
                            <p class="text-muted small mb-0">
                                Services reaching {{ $overview['active_barangays'] }} barangays with {{ $overview['unique_applicants'] }} registered fishermen.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="insight-card">
                            <div class="insight-icon bg-warning-soft">
                                <i class="fas fa-file-alt text-warning"></i>
                            </div>
                            <h6 class="fw-semibold mb-2">Document Impact</h6>
                            <p class="text-muted small mb-0">
                                {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}% higher approval rate when supporting documents are provided.
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="insight-card">
                            <div class="insight-icon bg-primary-soft">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <h6 class="fw-semibold mb-2">Efficient Processing</h6>
                            <p class="text-muted small mb-0">
                                Average {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time with {{ $performanceMetrics['completion_rate'] }}% completion rate.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Insights Modal -->
<div class="modal fade" id="fishrInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title fw-semibold">
                    <i class="fas fa-robot me-2"></i>FISHR AI-Powered Insights
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="insight-section">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-chart-line text-success me-2"></i>Growth Opportunities
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-arrow-up text-success me-2 mt-1"></i>
                                    <span>Expand outreach to barangays with low registration rates to increase service coverage</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-file-alt text-info me-2 mt-1"></i>
                                    <span>Promote document submission through awareness campaigns to improve approval rates</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-clock text-warning me-2 mt-1"></i>
                                    <span>Implement online application system during peak hours for better service delivery</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="insight-section">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-balance-scale text-warning me-2 mt-1"></i>
                                    <span>Balance gender representation in applications through targeted outreach programs</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-tachometer-alt text-info me-2 mt-1"></i>
                                    <span>{{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'Reduce processing time to improve service efficiency' : 'Maintain current processing speed for quality service' }}</span>
                                </li>
                                <li class="mb-3 d-flex">
                                    <i class="fas fa-map-marker-alt text-success me-2 mt-1"></i>
                                    <span>Focus on underserved livelihood categories to ensure inclusive service delivery</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-primary border-0 bg-primary-soft">
                            <div class="d-flex">
                                <i class="fas fa-lightbulb text-primary me-3 mt-1 fs-5"></i>
                                <div>
                                    <h6 class="fw-semibold mb-2 text-primary">Strategic Recommendation</h6>
                                    <p class="mb-0 text-muted">
                                        Consider implementing a mobile registration unit for remote barangays and conducting information campaigns about the importance of supporting documents to further improve approval rates and service accessibility.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
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

/* Livelihood Items */
.livelihood-item {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.livelihood-item:hover {
    border-left-color: var(--primary-color);
    transform: translateX(5px);
    background: #f1f5f9 !important;
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

/* Rank Badges */
.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-weight: 700;
    position: relative;
}

.rank-badge.rank-1 {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.rank-badge.rank-2 {
    background: linear-gradient(135deg, #d1d5db 0%, #9ca3af 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(209, 213, 219, 0.3);
}

.rank-badge.rank-3 {
    background: linear-gradient(135deg, #fb923c 0%, #ea580c 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(251, 146, 60, 0.3);
}

.rank-badge i {
    position: absolute;
    font-size: 1.25rem;
}

.rank-badge span {
    position: relative;
    z-index: 1;
    font-size: 0.875rem;
}

/* Insight Cards */
.insight-card {
    padding: 1.5rem;
    border-radius: 12px;
    background: #f8fafc;
    height: 100%;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.insight-card:hover {
    background: white;
    border-color: #e2e8f0;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transform: translateY(-5px);
}

.insight-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.25rem;
    margin-bottom: 1rem;
}

/* Alert Soft */
.alert-info-soft {
    background-color: rgba(14, 165, 233, 0.1);
    color: #0e7490;
}

.alert-primary {
    background-color: rgba(59, 130, 246, 0.1);
}

/* Modal Styles */
.modal-content {
    border-radius: 16px;
}

.modal-header {
    border-radius: 16px 16px 0 0;
}

.insight-section ul li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.insight-section ul li:last-child {
    border-bottom: none;
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
        width: 32px;
        height: 32px;
    }
    
    .rank-badge i {
        font-size: 1rem;
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
        const ctx = document.getElementById('fishrStatusChart');
        if (!ctx) return;
        
        chartInstances.statusChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: [
                    @foreach($statusAnalysis['counts'] as $status => $count)
                        '{{ ucfirst(str_replace("_", " ", $status)) }}',
                    @endforeach
                ],
                datasets: [{
                    data: [{{ implode(',', $statusAnalysis['counts']) }}],
                    backgroundColor: [
                        '#10b981',  // Green for approved
                        '#ef4444',  // Red for rejected  
                        '#f59e0b'   // Amber for under_review
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
        const ctx = document.getElementById('fishrTrendsChart');
        if (!ctx) return;
        
        chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyTrends as $trend)
                        '{{ \Carbon\Carbon::createFromFormat("Y-m", $trend->month)->format("M Y") }}',
                    @endforeach
                ],
                datasets: [
                    {
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
    const filterForm = document.querySelector('form[action*="analytics.fishr"]');
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
});
</script>
@endsection
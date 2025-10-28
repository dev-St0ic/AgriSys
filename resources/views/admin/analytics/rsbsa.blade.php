{{-- resources/views/admin/analytics/rsbsa.blade.php --}}

@extends('layouts.app')

@section('title', 'RSBSA Analytics - AgriSys Admin')
@section('page-title', 'RSBSA Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Title and Description -->
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">RSBSA Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Registry System for Basic Sectors in
                            Agriculture</p>
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
                                <a href="{{ route('admin.analytics.rsbsa') }}" class="nav-link active">
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
                    <form method="GET" action="{{ route('admin.analytics.rsbsa') }}" class="row g-3 align-items-end">
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
                                <a href="{{ route('admin.analytics.rsbsa.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                    class="btn btn-success px-4">
                                    <i class="fas fa-download me-2"></i>Export Data
                                </a>
                                <button type="button" class="btn btn-outline-info px-4" data-bs-toggle="modal"
                                    data-bs-target="#rsbsaInsightsModal">
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
                                <i class="fas fa-users me-1"></i>{{ $overview['unique_applicants'] }} farmers
                            </span>
                        </div>
                        <div class="metric-icon bg-primary-soft">
                            <i class="fas fa-file-alt text-primary"></i>
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

        <!-- Agricultural Impact -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Total Land Coverage</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_land_area'], 1) }}ha</h2>
                            <small class="text-muted">{{ $overview['active_barangays'] }} barangays</small>
                        </div>
                        <div class="metric-icon bg-purple-soft">
                            <i class="fas fa-map-marked-alt text-purple"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Efficiency -->
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label text-muted mb-2">Processing Time</p>
                            <h2 class="metric-value mb-1">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h2>
                            <small class="text-muted">{{ $processingTimeAnalysis['median_processing_days'] }}d
                                median</small>
                        </div>
                        <div class="metric-icon bg-warning-soft">
                            <i class="fas fa-clock text-warning"></i>
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
                        <canvas id="rsbsaStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="legend-dot bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }} me-2"></span>
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div>
                                    <span
                                        class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}-soft text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
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
                    <canvas id="rsbsaTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Commodities & Performance Metrics -->
    <div class="row g-3 mb-4">
        <!-- Top Commodities -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-seedling me-2 text-success"></i>Top Commodities by Registration
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($commodityAnalysis->take(5) as $index => $commodity)
                        <div class="commodity-item mb-3 p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="commodity-rank me-3">
                                    <div class="badge bg-success rounded-circle p-2"
                                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                        <strong>{{ $index + 1 }}</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">{{ ucfirst($commodity->commodity) }}</h6>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ ($commodity->total_applications / $commodityAnalysis->first()->total_applications) * 100 }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">
                                            <i class="fas fa-users me-1"></i>{{ $commodity->total_applications }} farmers
                                        </span>
                                        <span class="text-success fw-semibold">
                                            <i class="fas fa-map me-1"></i>{{ round($commodity->total_land_area, 1) }} ha
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-tachometer-alt me-2 text-info"></i>Key Performance Indicators
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Completion Rate -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Completion Rate</h6>
                            <span class="badge bg-info fs-6">{{ $performanceMetrics['completion_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $processingTimeAnalysis['processed_count'] }} of {{ $overview['total_applications'] }}
                            processed
                        </small>
                    </div>

                    <!-- Quality Score -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Quality Score</h6>
                            <span
                                class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }} fs-6">
                                {{ $performanceMetrics['quality_score'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">Based on approval rate & documentation</small>
                    </div>

                    <!-- Document Submission -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Document Submission</h6>
                            <span class="badge bg-primary fs-6">{{ $documentAnalysis['submission_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $documentAnalysis['submission_rate'] }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $documentAnalysis['with_documents'] }} with supporting documents
                        </small>
                    </div>

                    <!-- Agricultural Impact -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Avg Land per Farmer</h6>
                            <span class="badge bg-success fs-6">{{ number_format($overview['avg_land_area'], 2) }}
                                ha</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ min(100, ($overview['avg_land_area'] / 10) * 100) }}%"></div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ $overview['unique_commodities'] }} different crops registered
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Barangays -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Performing Barangays
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">Rank</th>
                                    <th class="fw-semibold">Barangay</th>
                                    <th class="fw-semibold text-center">Applications</th>
                                    <th class="fw-semibold text-center">Approved</th>
                                    <th class="fw-semibold">Approval Rate</th>
                                    <th class="fw-semibold text-center">Land Area</th>
                                    <th class="fw-semibold text-center">Commodities</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                    <tr>
                                        <td>
                                            <div
                                                class="badge {{ $index < 3 ? ($index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-info')) : 'bg-light text-dark' }} rounded-pill px-3">
                                                #{{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td class="fw-semibold">{{ $barangay->barangay }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $barangay->total_applications }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $barangay->approved }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2"
                                                    style="height: 8px; max-width: 100px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small
                                                    class="fw-semibold">{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="text-success fw-semibold">{{ round($barangay->total_land_area, 1) }}
                                                ha</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $barangay->commodities_grown }}</span>
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

    <!-- Gender & Livelihood Analysis -->
    <div class="row g-3 mb-4">
        <!-- Gender Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-venus-mars me-2 text-primary"></i>Gender Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($genderAnalysis['stats'] as $gender)
                            <div class="col-6">
                                <div class="gender-card p-4 rounded text-center h-100"
                                    style="background: linear-gradient(135deg, {{ $gender->sex === 'Male' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(236, 72, 153, 0.1)' }} 0%, {{ $gender->sex === 'Male' ? 'rgba(59, 130, 246, 0.05)' : 'rgba(236, 72, 153, 0.05)' }} 100%);">
                                    <i
                                        class="fas fa-{{ $gender->sex === 'Male' ? 'mars' : 'venus' }} fa-3x mb-3 text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}"></i>
                                    <h3 class="mb-2 fw-bold text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">
                                        {{ $gender->total_applications }}
                                    </h3>
                                    <p class="mb-2 fw-semibold">{{ $gender->sex }} Farmers</p>
                                    <div class="small text-muted">
                                        <div class="mb-1">
                                            <i class="fas fa-percentage me-1"></i>
                                            <span
                                                class="fw-semibold">{{ $genderAnalysis['percentages'][$gender->sex] ?? 0 }}%</span>
                                            of total
                                        </div>
                                        <div>
                                            <i class="fas fa-map me-1"></i>
                                            <span class="fw-semibold">{{ round($gender->total_land_area, 1) }} ha</span>
                                            land area
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Livelihoods -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-briefcase me-2 text-success"></i>Main Livelihood Distribution
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($livelihoodAnalysis->take(5) as $index => $livelihood)
                        <div class="livelihood-item mb-3 p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="livelihood-rank me-3">
                                    <div class="badge bg-success rounded-circle p-2"
                                        style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        <strong>{{ $index + 1 }}</strong>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">{{ ucfirst($livelihood->main_livelihood) }}</h6>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-success"
                                            style="width: {{ ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 }}%">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">{{ $livelihood->total_applications }} applications</span>
                                        <span class="text-success fw-semibold">
                                            {{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}%
                                            approval
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Modal -->
    <div class="modal fade" id="rsbsaInsightsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <h5 class="modal-title fw-semibold">
                        <i class="fas fa-robot me-2"></i>RSBSA AI-Powered Insights
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
                                        <span>Expand agricultural coverage to reach
                                            {{ 26 - $overview['active_barangays'] }} remaining barangays for complete
                                            city-wide registration</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="fas fa-seedling text-info me-2 mt-1"></i>
                                        <span>Promote crop diversification among farmers currently growing only
                                            {{ $overview['unique_commodities'] }} commodity types</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="fas fa-chart-bar text-warning me-2 mt-1"></i>
                                        <span>Implement digital application system to improve
                                            {{ $documentAnalysis['submission_rate'] }}% document submission rate</span>
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
                                        <span>Address gender disparity in agricultural registration through targeted
                                            outreach programs</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="fas fa-tachometer-alt text-info me-2 mt-1"></i>
                                        <span>{{ $processingTimeAnalysis['avg_processing_days'] > 7 ? 'Reduce processing time to improve farmer experience' : 'Maintain current processing efficiency for quality service' }}</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="fas fa-map-marker-alt text-success me-2 mt-1"></i>
                                        <span>Focus on underperforming barangays to ensure equitable agricultural support
                                            distribution</span>
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
                                            Consider implementing a mobile registration unit for remote agricultural areas
                                            and conducting farmer education campaigns about RSBSA benefits to increase
                                            participation from {{ number_format($overview['total_land_area'], 1) }}ha to
                                            the full agricultural potential of San Pedro, Laguna.
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

        /* Commodity Items */
        .commodity-item {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
            border-left: 4px solid #10b981;
            transition: all 0.2s ease;
        }

        .commodity-item:hover {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.04) 100%);
            transform: translateX(5px);
        }

        /* Livelihood Items */
        .livelihood-item {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
            border-left: 4px solid #10b981;
            transition: all 0.2s ease;
        }

        .livelihood-item:hover {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.12) 0%, rgba(16, 185, 129, 0.04) 100%);
            transform: translateX(5px);
        }

        /* Gender Cards */
        .gender-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .gender-card:hover {
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .text-pink {
            color: #ec4899 !important;
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

        /* Insight Cards */
        .insight-card {
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .insight-card ul li {
            line-height: 1.6;
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
                const ctx = document.getElementById('rsbsaStatusChart');
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
                                '#10b981', // Green for approved
                                '#ef4444', // Red for rejected
                                '#f59e0b' // Amber for under_review
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
                const ctx = document.getElementById('rsbsaTrendsChart');
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
                                label: 'Land Area (ha)',
                                data: [{{ $monthlyTrends->pluck('total_land_area')->implode(',') }}],
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: false,
                                pointBackgroundColor: '#8b5cf6',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'y1'
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
                                },
                                title: {
                                    display: true,
                                    text: 'Applications',
                                    font: {
                                        weight: '600',
                                        size: 12
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: '#64748b'
                                },
                                title: {
                                    display: true,
                                    text: 'Land Area (ha)',
                                    font: {
                                        weight: '600',
                                        size: 12
                                    }
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
            const filterForm = document.querySelector('form[action*="analytics.rsbsa"]');
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

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
                    <div class="text-center mb-3">
                        <h4 class="mb-2 fw-bold">RSBSA Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Registry System for Basic Sectors in Agriculture</p>
                    </div>
                    <!-- Service Tabs -->
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
                                    <i class="fas fa-boxes me-1"></i> Supply Management
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
                    <form method="GET" action="{{ route('admin.analytics.rsbsa') }}" class="row g-3 align-items-end">
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
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i> Apply Filter
                            </button>
                            <a href="{{ route('admin.analytics.rsbsa.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                class="btn btn-success me-2">
                                <i class="fas fa-download me-1"></i> Export
                            </a>
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                data-bs-target="#rsbsaInsightsModal">
                                <i class="fas fa-lightbulb me-1"></i> AI Insights
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4 g-3">
        <!-- Total Applications Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Total Applications</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_applications']) }}</h2>
                            <small class="metric-subtitle">
                                <i class="fas fa-users me-1"></i>{{ $overview['unique_applicants'] }} unique farmers
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Rate Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Approval Rate</p>
                            <h2 class="metric-value mb-1">{{ $overview['approval_rate'] }}%</h2>
                            <small class="metric-subtitle">
                                {{ number_format($overview['approved_applications']) }} approved applications
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agricultural Impact Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Total Land Coverage</p>
                            <h2 class="metric-value mb-1">{{ number_format($overview['total_land_area'], 1) }} ha</h2>
                            <small class="metric-subtitle">
                                Across {{ $overview['active_barangays'] }} barangays
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-map-marked-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Processing Efficiency Card -->
        <div class="col-lg-3 col-md-6">
            <div class="metric-card card border-0 shadow-sm h-100">
                <div class="card-body text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="metric-label mb-2">Avg Processing Time</p>
                            <h2 class="metric-value mb-1">{{ $processingTimeAnalysis['avg_processing_days'] }} days</h2>
                            <small class="metric-subtitle">
                                Median: {{ $processingTimeAnalysis['median_processing_days'] }} days
                            </small>
                        </div>
                        <div class="metric-icon">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Analytics Section -->
    <div class="row g-3 mb-4">
        <!-- Application Trends Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line me-2 text-primary"></i>Application Trends & Performance
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="rsbsaTrendsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-tasks me-2 text-success"></i>Application Status
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="rsbsaStatusChart"></canvas>
                    <div class="mt-3">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 rounded status-item">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-circle me-2 {{ $status === 'approved' ? 'text-success' : ($status === 'rejected' ? 'text-danger' : 'text-warning') }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                                <div>
                                    <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $count }}
                                    </span>
                                    <small class="text-muted ms-1">({{ $statusAnalysis['percentages'][$status] }}%)</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
                                    <div class="badge bg-success rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
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
                            {{ $processingTimeAnalysis['processed_count'] }} of {{ $overview['total_applications'] }} processed
                        </small>
                    </div>

                    <!-- Quality Score -->
                    <div class="metric-item mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-semibold">Quality Score</h6>
                            <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }} fs-6">
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
                            <span class="badge bg-success fs-6">{{ number_format($overview['avg_land_area'], 2) }} ha</span>
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
                                            <div class="badge {{ $index < 3 ? ($index === 0 ? 'bg-warning' : ($index === 1 ? 'bg-secondary' : 'bg-info')) : 'bg-light text-dark' }} rounded-pill px-3">
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
                                                <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 100px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small class="fw-semibold">{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-success fw-semibold">{{ round($barangay->total_land_area, 1) }} ha</span>
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
                                    <i class="fas fa-{{ $gender->sex === 'Male' ? 'mars' : 'venus' }} fa-3x mb-3 text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}"></i>
                                    <h3 class="mb-2 fw-bold text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">
                                        {{ $gender->total_applications }}
                                    </h3>
                                    <p class="mb-2 fw-semibold">{{ $gender->sex }} Farmers</p>
                                    <div class="small text-muted">
                                        <div class="mb-1">
                                            <i class="fas fa-percentage me-1"></i>
                                            <span class="fw-semibold">{{ $genderAnalysis['percentages'][$gender->sex] ?? 0 }}%</span> of total
                                        </div>
                                        <div>
                                            <i class="fas fa-map me-1"></i>
                                            <span class="fw-semibold">{{ round($gender->total_land_area, 1) }} ha</span> land area
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
                                    <div class="badge bg-success rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
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
                                            {{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}% approval
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
    <div class="modal fade" id="rsbsaInsightsModal" tabindex="-1" aria-labelledby="rsbsaInsightsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                    <h5 class="modal-title text-white fw-bold" id="rsbsaInsightsModalLabel">
                        <i class="fas fa-lightbulb me-2"></i>AI-Powered Analytics Insights
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Key Performance Insights -->
                    <div class="insight-card mb-4 p-4 rounded" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(8, 145, 178, 0.05) 100%);">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-chart-line me-2 text-info"></i>Key Performance Insights
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">
                                <strong>Approval Efficiency:</strong> Current approval rate of {{ $overview['approval_rate'] }}%
                                {{ $overview['approval_rate'] > 80 ? 'indicates excellent processing efficiency' : ($overview['approval_rate'] > 60 ? 'shows good performance with room for improvement' : 'suggests need for process optimization') }}.
                            </li>
                            <li class="mb-2">
                                <strong>Agricultural Impact:</strong> {{ number_format($overview['total_land_area'], 1) }} hectares under RSBSA registration represents significant agricultural coverage across {{ $overview['active_barangays'] }} barangays.
                            </li>
                            <li class="mb-2">
                                <strong>Document Compliance:</strong> {{ $documentAnalysis['submission_rate'] }}% document submission rate
                                {{ $documentAnalysis['submission_rate'] > 70 ? 'shows good farmer compliance' : 'indicates need for better guidance on documentation' }}.
                            </li>
                        </ul>
                    </div>

                    <!-- Agricultural Trends -->
                    <div class="insight-card mb-4 p-4 rounded" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(4, 120, 87, 0.05) 100%);">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-seedling me-2 text-success"></i>Agricultural Trends
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">
                                <strong>Crop Diversity:</strong> {{ $overview['unique_commodities'] }} different commodities registered shows
                                {{ $overview['unique_commodities'] > 10 ? 'excellent agricultural diversification' : 'moderate crop variety' }}.
                            </li>
                            <li class="mb-2">
                                <strong>Land Utilization:</strong> Average {{ number_format($overview['avg_land_area'], 2) }} hectares per farmer indicates
                                {{ $overview['avg_land_area'] > 2 ? 'larger scale farming operations' : 'small-scale agricultural practices' }}.
                            </li>
                            <li class="mb-2">
                                <strong>Geographic Coverage:</strong> {{ $overview['active_barangays'] }} barangays participating demonstrates wide program reach.
                            </li>
                        </ul>
                    </div>

                    <!-- Recommendations -->
                    <div class="insight-card p-4 rounded" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.05) 100%);">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>Strategic Recommendations
                        </h6>
                        <ul class="mb-0 ps-3">
                            @if ($processingTimeAnalysis['avg_processing_days'] > 10)
                                <li class="mb-2">
                                    <strong>Processing Time:</strong> Average {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time could be improved through workflow optimization.
                                </li>
                            @endif
                            @if ($documentAnalysis['submission_rate'] < 70)
                                <li class="mb-2">
                                    <strong>Documentation:</strong> Consider implementing better guidance systems to improve {{ $documentAnalysis['submission_rate'] }}% document submission rate.
                                </li>
                            @endif
                            <li class="mb-2">
                                <strong>Outreach:</strong> Focus on underperforming barangays to improve overall program participation and coverage.
                            </li>
                            <li class="mb-2">
                                <strong>Support:</strong> Provide targeted assistance to farmers with smaller land areas to maximize agricultural productivity.
                            </li>
                            <li class="mb-2">
                                <strong>Quality Improvement:</strong> Current quality score of {{ $performanceMetrics['quality_score'] }}% suggests
                                {{ $performanceMetrics['quality_score'] > 80 ? 'maintaining excellent standards' : 'opportunities for process enhancement' }}.
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('admin.analytics.rsbsa.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                        class="btn btn-success">
                        <i class="fas fa-download me-1"></i>Export Full Report
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
    <style>
        /* Global Styles */
        body {
            background-color: #f8f9fa;
        }

        /* Card Styles */
        .card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        /* Metric Cards */
        .metric-card .card-body {
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .metric-label {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .metric-subtitle {
            opacity: 0.85;
            font-size: 0.875rem;
        }

        .metric-icon {
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Status Items */
        .status-item {
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }

        .status-item:hover {
            background-color: #e9ecef;
            transform: translateX(5px);
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

        /* Progress Bars */
        .progress {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Table Styles */
        .table {
            font-size: 0.9rem;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f9fafb;
            transform: scale(1.01);
        }

        /* Navigation Pills */
        .nav-pills .nav-link {
            border-radius: 25px;
            transition: all 0.3s ease;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #f3f4f6;
            transform: translateY(-2px);
        }

        .nav-pills .nav-link.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Card Headers */
        .card-header {
            padding: 1.25rem 1.5rem;
        }

        .card-header h5 {
            font-size: 1.1rem;
        }

        /* Badge Styles */
        .badge {
            font-weight: 600;
            padding: 0.4em 0.8em;
            font-size: 0.875em;
        }

        /* Chart Container */
        canvas {
            max-height: 350px;
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
            border-radius: 15px;
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
        }

        /* Metric Item Styles */
        .metric-item .progress {
            border-radius: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .metric-value {
                font-size: 2rem;
            }

            .metric-label {
                font-size: 0.75rem;
            }

            .nav-pills .nav-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.875rem;
            }

            .table {
                font-size: 0.8rem;
            }

            .commodity-item,
            .livelihood-item {
                margin-bottom: 0.75rem !important;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }

            .metric-icon {
                display: none;
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
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Global Configuration
            Chart.defaults.font.family = "'Inter', 'Segoe UI', 'Roboto', sans-serif";
            Chart.defaults.color = '#6b7280';
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.padding = 15;

            // Status Distribution Donut Chart
            const statusCtx = document.getElementById('rsbsaStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            '{{ ucfirst(str_replace('_', ' ', $status)) }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($statusAnalysis['counts'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#10b981', // approved - green
                            '#ef4444', // rejected - red
                            '#f59e0b'  // pending - yellow
                        ],
                        borderWidth: 4,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.2,
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
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });

            // Monthly Trends Chart
            const trendsCtx = document.getElementById('rsbsaTrendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach ($monthlyTrends as $trend)
                            '{{ date('M Y', strtotime($trend->month . '-01')) }}',
                        @endforeach
                    ],
                    datasets: [
                        {
                            label: 'Total Applications',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->total_applications }},
                                @endforeach
                            ],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Approved',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->approved }},
                                @endforeach
                            ],
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        },
                        {
                            label: 'Land Area (ha)',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->total_land_area }},
                                @endforeach
                            ],
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2.5,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
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
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Applications',
                                font: {
                                    weight: '600',
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Land Area (ha)',
                                font: {
                                    weight: '600',
                                    size: 12
                                }
                            },
                            grid: {
                                drawOnChartArea: false,
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
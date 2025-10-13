{{-- resources/views/admin/analytics/rsbsa.blade.php --}}

@extends('layouts.app')

@section('title', 'RSBSA Analytics - AgriSys Admin')
@section('page-title', 'RSBSA Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div>
                            <h4 class="mb-2">RSBSA Analytics Dashboard</h4>
                            <p class="text-muted mb-0">Comprehensive insights into Registry System for Basic Sectors in
                                Agriculture</p>
                        </div>
                    <!-- Service Tabs - Unified Structure -->
                    <div class="d-flex justify-content-center">
                        <ul class="nav nav-pills" id="serviceTab" role="tablist">
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
                                    <a href="{{ route('admin.analytics.inventory') }}" 
                                    class="nav-link {{ request()->routeIs('admin.analytics.inventory') ? 'active' : '' }}">
                                        <i class="fas fa-boxes me-1"></i> Inventory
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
    </div>

    <!-- Service Content -->
    <div class="tab-content" id="serviceTabContent">
        <!-- RSBSA Service Tab -->
        <div class="tab-pane fade show active" id="rsbsa-service" role="tabpanel">

            <!-- Date Range Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.analytics.rsbsa') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                        value="{{ $startDate }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                        value="{{ $endDate }}">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
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
            <div class="row mb-4">
                <!-- Total Applications Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Total Applications</h6>
                                    <h2 class="mb-1">{{ number_format($overview['total_applications']) }}</h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-arrow-up me-1"></i>{{ $overview['unique_applicants'] }} farmers
                                        registered
                                    </small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-user-check fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Rate Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Approval Rate</h6>
                                    <h2 class="mb-1">{{ $overview['approval_rate'] }}%</h2>
                                    <small class="opacity-75">{{ number_format($overview['approved_applications']) }}
                                        approved</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agricultural Impact Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Land Area Coverage</h6>
                                    <h2 class="mb-1">{{ number_format($overview['total_land_area'], 1) }}</h2>
                                    <small class="opacity-75">hectares across {{ $overview['active_barangays'] }}
                                        barangays</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-map fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commodity Diversity Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Commodity Diversity</h6>
                                    <h2 class="mb-1">{{ $overview['unique_commodities'] }}</h2>
                                    <small class="opacity-75">different crops registered</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-wheat-awn fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Analytics Row -->
            <div class="row">
                <!-- Status Distribution -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-pie-chart me-2"></i>Application Status Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="position-relative">
                                <canvas id="rsbsaStatusDonutChart" height="250"></canvas>
                            </div>
                            <div class="mt-3">
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="d-flex align-items-center">
                                            <i
                                                class="fas fa-circle me-2 text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}"></i>
                                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        </span>
                                        <div>
                                            <span
                                                class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ $count }}
                                            </span>
                                            <small
                                                class="text-muted ms-1">{{ $statusAnalysis['percentages'][$status] }}%</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trends Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Monthly Application Trends
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="rsbsaTrendsChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commodity & Land Area Analysis Row -->
            <div class="row">
                <!-- Top Commodities -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-seedling me-2"></i>Top Commodities by Registration
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($commodityAnalysis->take(6) as $index => $commodity)
                                    <div class="col-12 mb-3">
                                        <div class="d-flex align-items-center p-3 rounded"
                                            style="background: linear-gradient(90deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);">
                                            <div class="me-3">
                                                <div class="badge bg-success rounded-pill p-2">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ ucfirst($commodity->commodity) }}</h6>
                                                <div class="progress mb-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ ($commodity->total_applications / $commodityAnalysis->first()->total_applications) * 100 }}%">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">{{ $commodity->total_applications }}
                                                        farmers</small>
                                                    <small
                                                        class="text-success">{{ round($commodity->total_land_area, 1) }}ha
                                                        total</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Land Area Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-area me-2"></i>Land Area Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-3">
                                <canvas id="rsbsaLandAreaChart" height="200"></canvas>
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-primary mb-1">{{ number_format($overview['total_land_area'], 1) }}
                                        </h4>
                                        <p class="mb-0 text-muted">Total Hectares</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-warning mb-1">{{ number_format($overview['avg_land_area'], 2) }}
                                        </h4>
                                        <p class="mb-0 text-muted">Average per Farmer</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Livelihood & Gender Analysis Row -->
            <div class="row">
                <!-- Livelihood Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-briefcase me-2"></i>Main Livelihood Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($livelihoodAnalysis->take(5) as $index => $livelihood)
                                    <div class="col-12 mb-3">
                                        <div class="d-flex align-items-center p-3 rounded"
                                            style="background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);">
                                            <div class="me-3">
                                                <div class="badge bg-success rounded-pill p-2">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ ucfirst($livelihood->main_livelihood) }}</h6>
                                                <div class="progress mb-1" style="height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 }}%">
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">{{ $livelihood->total_applications }}
                                                        applications</small>
                                                    <small
                                                        class="text-success">{{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}%
                                                        approval</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gender Distribution -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>Gender Distribution
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-3">
                                <canvas id="rsbsaGenderChart" height="200"></canvas>
                            </div>
                            <div class="row text-center">
                                @foreach ($genderAnalysis['stats'] as $gender)
                                    <div class="col-6">
                                        <div class="p-3 rounded"
                                            style="background: rgba({{ $gender->sex === 'Male' ? '59, 130, 246' : '236, 72, 153' }}, 0.1);">
                                            <h4 class="mb-1 text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">
                                                {{ $gender->total_applications }}</h4>
                                            <p class="mb-0 text-muted">{{ $gender->sex }} Farmers</p>
                                            <small class="text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">
                                                {{ $genderAnalysis['percentages'][$gender->sex] ?? 0 }}% |
                                                {{ round($gender->total_land_area, 1) }}ha
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performing Barangays -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top Performing Barangays
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Barangay</th>
                                            <th>Total Applications</th>
                                            <th>Approved</th>
                                            <th>Approval Rate</th>
                                            <th>Total Land Area</th>
                                            <th>Commodities</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                            <tr>
                                                <td>
                                                    <div
                                                        class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }} rounded-pill">
                                                        #{{ $index + 1 }}
                                                    </div>
                                                </td>
                                                <td><strong>{{ $barangay->barangay }}</strong></td>
                                                <td>{{ $barangay->total_applications }}</td>
                                                <td>{{ $barangay->approved }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%">
                                                            </div>
                                                        </div>
                                                        <small>{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                                    </div>
                                                </td>
                                                <td>{{ round($barangay->total_land_area, 1) }}ha</td>
                                                <td>{{ $barangay->commodities_grown }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i>Performance Metrics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="metric-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Processing Time</h6>
                                    <span
                                        class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}">
                                        {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                                    </span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}"
                                        style="width: {{ min(100, ((21 - $processingTimeAnalysis['avg_processing_days']) / 21) * 100) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Median:
                                    {{ $processingTimeAnalysis['median_processing_days'] }}d</small>
                            </div>

                            <div class="metric-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Completion Rate</h6>
                                    <span class="badge bg-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-info"
                                        style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                                </div>
                                <small class="text-muted">{{ $processingTimeAnalysis['processed_count'] }}
                                    processed</small>
                            </div>

                            <div class="metric-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Agricultural Impact</h6>
                                    <span
                                        class="badge bg-success">{{ number_format($performanceMetrics['total_land_impact'], 1) }}ha</span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ min(100, ($performanceMetrics['total_land_impact'] / 1000) * 100) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">Total land area covered</small>
                            </div>

                            <div class="metric-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Quality Score</h6>
                                    <span
                                        class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                        {{ $performanceMetrics['quality_score'] }}%
                                    </span>
                                </div>
                                <div class="progress mb-1" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                        style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                                </div>
                                <small class="text-muted">Based on approval & doc rates</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Analysis & Top Farmers Row -->
            <div class="row">
                <!-- Document Analysis -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>Document Submission Analysis
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_docs'] }}%
                                        </h4>
                                        <p class="mb-0 text-muted">Approval with Docs</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-warning mb-1">
                                            {{ $documentAnalysis['approval_rate_without_docs'] }}%</h4>
                                        <p class="mb-0 text-muted">Approval without Docs</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>With Supporting Documents</span>
                                    <span class="text-success">{{ $documentAnalysis['with_documents'] }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['with_documents'] / $documentAnalysis['total']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Without Documents</span>
                                    <span class="text-warning">{{ $documentAnalysis['without_documents'] }}</span>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['without_documents'] / $documentAnalysis['total']) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Applications with supporting documents have a
                                    {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%
                                    higher approval rate.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Modal -->
    <div class="modal fade" id="rsbsaInsightsModal" tabindex="-1" aria-labelledby="rsbsaInsightsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title" id="rsbsaInsightsModalLabel">
                        <i class="fas fa-lightbulb me-2"></i>RSBSA Analytics AI Insights
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-chart-line me-2"></i>Key Performance Insights</h6>
                                <ul class="mb-0">
                                    <li><strong>Approval Efficiency:</strong> Current approval rate of
                                        {{ $overview['approval_rate'] }}%
                                        {{ $overview['approval_rate'] > 80 ? 'indicates excellent processing efficiency' : ($overview['approval_rate'] > 60 ? 'shows good performance with room for improvement' : 'suggests need for process optimization') }}
                                    </li>
                                    <li><strong>Agricultural Impact:</strong>
                                        {{ number_format($overview['total_land_area'], 1) }} hectares under RSBSA
                                        registration represents significant agricultural coverage</li>
                                    <li><strong>Document Compliance:</strong> {{ $documentAnalysis['submission_rate'] }}%
                                        document submission rate
                                        {{ $documentAnalysis['submission_rate'] > 70 ? 'shows good farmer compliance' : 'indicates need for better guidance on documentation' }}
                                    </li>
                                </ul>
                            </div>

                            <div class="alert alert-success">
                                <h6><i class="fas fa-seedling me-2"></i>Agricultural Trends</h6>
                                <ul class="mb-0">
                                    <li><strong>Crop Diversity:</strong> {{ $overview['unique_commodities'] }} different
                                        commodities registered shows
                                        {{ $overview['unique_commodities'] > 10 ? 'excellent agricultural diversification' : 'moderate crop variety' }}
                                    </li>
                                    <li><strong>Land Utilization:</strong> Average
                                        {{ number_format($overview['avg_land_area'], 2) }} hectares per farmer indicates
                                        {{ $overview['avg_land_area'] > 2 ? 'larger scale farming operations' : 'small-scale agricultural practices' }}
                                    </li>
                                    <li><strong>Geographic Coverage:</strong> {{ $overview['active_barangays'] }} barangays
                                        participating demonstrates wide program reach</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Recommendations</h6>
                                <ul class="mb-0">
                                    @if ($processingTimeAnalysis['avg_processing_days'] > 10)
                                        <li><strong>Processing Time:</strong> Average
                                            {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time could
                                            be improved through workflow optimization</li>
                                    @endif
                                    @if ($documentAnalysis['submission_rate'] < 70)
                                        <li><strong>Documentation:</strong> Consider implementing better guidance systems to
                                            improve {{ $documentAnalysis['submission_rate'] }}% document submission rate
                                        </li>
                                    @endif
                                    <li><strong>Outreach:</strong> Focus on underperforming barangays to improve overall
                                        program participation</li>
                                    <li><strong>Support:</strong> Provide targeted assistance to farmers with smaller land
                                        areas to maximize agricultural productivity</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
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
        .bg-gradient-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        }

        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        .text-pink {
            color: #ec4899 !important;
        }

        .metric-item .progress {
            border-radius: 10px;
        }

        .card {
            border-radius: 15px;
        }

        .badge {
            font-size: 0.75em;
        }

        .nav-pills .nav-link {
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card-header h5 {
            font-weight: 600;
        }

        canvas {
            max-height: 400px;
        }

        .table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }

        @media (max-width: 768px) {

            .col-lg-3,
            .col-lg-4,
            .col-lg-6,
            .col-lg-8 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Status Distribution Donut Chart
            const statusCtx = document.getElementById('rsbsaStatusDonutChart').getContext('2d');
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
                            '#f59e0b' // pending - yellow
                        ],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
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
                    datasets: [{
                            label: 'Total Applications',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->total_applications }},
                                @endforeach
                            ],
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
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
                            tension: 0.4
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
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Applications'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Land Area (ha)'
                            },
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // Land Area Distribution Chart
            const landAreaCtx = document.getElementById('rsbsaLandAreaChart').getContext('2d');
            new Chart(landAreaCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($landAreaAnalysis['land_ranges'] as $range)
                            '{{ $range->land_range }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Number of Farmers',
                        data: [
                            @foreach ($landAreaAnalysis['land_ranges'] as $range)
                                {{ $range->farmer_count }},
                            @endforeach
                        ],
                        backgroundColor: [
                            '#f59e0b', '#f97316', '#ea580c', '#dc2626', '#b91c1c'
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Farmers'
                            }
                        }
                    }
                }
            });

            // Gender Distribution Chart
            const genderCtx = document.getElementById('rsbsaGenderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach ($genderAnalysis['stats'] as $gender)
                            '{{ $gender->sex }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($genderAnalysis['stats'] as $gender)
                                {{ $gender->total_applications }},
                            @endforeach
                        ],
                        backgroundColor: ['#3b82f6', '#ec4899'],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Day of Week Chart
            const dayOfWeekCtx = document.getElementById('rsbsaDayOfWeekChart').getContext('2d');
            new Chart(dayOfWeekCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($registrationPatterns['day_of_week'] as $day)
                            '{{ $day->day_name }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Applications',
                        data: [
                            @foreach ($registrationPatterns['day_of_week'] as $day)
                                {{ $day->applications_count }},
                            @endforeach
                        ],
                        backgroundColor: '#4f46e5',
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Hourly Registration Chart
            const hourlyCtx = document.getElementById('rsbsaHourlyChart').getContext('2d');
            new Chart(hourlyCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach ($registrationPatterns['hourly'] as $hour)
                            '{{ $hour->hour }}:00',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Applications',
                        data: [
                            @foreach ($registrationPatterns['hourly'] as $hour)
                                {{ $hour->applications_count }},
                            @endforeach
                        ],
                        borderColor: '#06b6d4',
                        backgroundColor: 'rgba(6, 182, 212, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Hour of Day'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection

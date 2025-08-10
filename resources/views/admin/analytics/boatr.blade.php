{{-- resources/views/admin/analytics/boatr.blade.php --}}

@extends('layouts.app')

@section('title', 'BOATR Analytics - AgriSys Admin')
@section('page-title', 'BOATR Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">BOATR Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Boat Registration Services</p>
                    </div>
                    <!-- Service Tabs - Unified Structure -->
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
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Content -->
<div class="tab-content" id="serviceTabContent">
    <!-- BOATR Service Tab -->
    <div class="tab-pane fade show active" id="boatr-service" role="tabpanel">
        
        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.analytics.boatr') }}" class="row g-3">
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
                                <a href="{{ route('admin.analytics.boatr.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#boatrInsightsModal">
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
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Total Applications</h6>
                                <h2 class="mb-1">{{ number_format($overview['total_applications']) }}</h2>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>12% from last period
                                </small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-ship fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Approval Rate Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Approval Rate</h6>
                                <h2 class="mb-1">{{ $overview['approval_rate'] }}%</h2>
                                <small class="opacity-75">{{ number_format($overview['approved_applications']) }} approved</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Vessel Fleet Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Registered Fleet</h6>
                                <h2 class="mb-1">{{ $overview['unique_vessels'] }}</h2>
                                <small class="opacity-75">{{ number_format($overview['unique_applicants']) }} boat owners</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-anchor fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Inspection Status Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Inspection Rate</h6>
                                <h2 class="mb-1">{{ $overview['inspection_completion_rate'] }}%</h2>
                                <small class="opacity-75">{{ number_format($overview['inspections_completed']) }} inspections done</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-clipboard-check fa-lg"></i>
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
                            <canvas id="boatrStatusDonutChart" height="250"></canvas>
                        </div>
                        <div class="mt-3">
                            @foreach($statusAnalysis['counts'] as $status => $count)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-circle me-2 text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}"></i>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                                <div>
                                    <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $count }}
                                    </span>
                                    <small class="text-muted ms-1">{{ $statusAnalysis['percentages'][$status] }}%</small>
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
                        <canvas id="boatrTrendsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Analytics Row -->
        <div class="row">
            <!-- Boat Type Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-ship me-2"></i>Boat Type Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($boatTypeAnalysis->take(5) as $index => $boatType)
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);">
                                    <div class="me-3">
                                        <div class="badge bg-primary rounded-pill p-2">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ ucfirst($boatType->boat_type) }}</h6>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($boatType->total_applications / $boatTypeAnalysis->first()->total_applications) * 100 }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $boatType->total_applications }} boats</small>
                                            <small class="text-primary">{{ round(($boatType->approved / max(1, $boatType->total_applications)) * 100, 1) }}% approval</small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">Avg L: {{ round($boatType->avg_length ?? 0, 1) }}ft</small>
                                            <small class="text-muted">W: {{ round($boatType->avg_width ?? 0, 1) }}ft</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fishing Gear Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tools me-2"></i>Fishing Gear Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <canvas id="boatrFishingGearChart" height="200"></canvas>
                        </div>
                        <div class="row">
                            @foreach($fishingGearAnalysis->take(4) as $gear)
                            <div class="col-6 mb-2">
                                <div class="p-2 rounded bg-light">
                                    <h6 class="mb-1 text-truncate">{{ ucwords($gear->primary_fishing_gear) }}</h6>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $gear->total_applications }} boats</small>
                                        <small class="text-success">{{ round(($gear->approved / max(1, $gear->total_applications)) * 100, 1) }}%</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vessel Size & Engine Analysis -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-ruler-combined me-2"></i>Vessel Size Categories
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Size Category</th>
                                        <th>Count</th>
                                        <th>Approval</th>
                                        <th>Avg Dimensions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vesselSizeAnalysis as $size)
                                    <tr>
                                        <td><strong>{{ $size->size_category }}</strong></td>
                                        <td>{{ $size->total_applications }}</td>
                                        <td>
                                            <span class="badge bg-success">{{ round(($size->approved / max(1, $size->total_applications)) * 100, 1) }}%</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                L: {{ round($size->avg_length ?? 0, 1) }}ft<br>
                                                W: {{ round($size->avg_width ?? 0, 1) }}ft
                                            </small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engine Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>Engine Type Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($engineAnalysis->take(5) as $engine)
                        <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded" style="background: rgba(0,0,0,0.05);">
                            <div>
                                <h6 class="mb-1">{{ ucwords($engine->engine_type) }}</h6>
                                <div class="progress" style="width: 120px; height: 6px;">
                                    <div class="progress-bar bg-dark" 
                                         style="width: {{ ($engine->total_applications / $engineAnalysis->first()->total_applications) * 100 }}%"></div>
                                </div>
                                <small class="text-muted">Avg HP: {{ round($engine->avg_horsepower ?? 0, 1) }}</small>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-primary">{{ $engine->total_applications }}</div>
                                <br>
                                <small class="text-success">{{ round(($engine->approved / max(1, $engine->total_applications)) * 100, 1) }}%</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Inspection & Document Analysis -->
        <div class="row">
            <!-- Inspection Analysis -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Inspection Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-success mb-1">{{ $inspectionAnalysis['inspections_completed'] }}</h4>
                                    <p class="mb-0 text-muted">Completed</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-warning mb-1">{{ $inspectionAnalysis['inspections_scheduled'] }}</h4>
                                    <p class="mb-0 text-muted">Scheduled</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-danger mb-1">{{ $inspectionAnalysis['inspections_required'] }}</h4>
                                    <p class="mb-0 text-muted">Required</p>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-info mb-1">{{ $inspectionAnalysis['completion_rate'] }}%</h4>
                                    <p class="mb-0 text-muted">Rate</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Inspector Workload -->
                        @if($inspectionAnalysis['inspector_workload']->isNotEmpty())
                        <h6 class="mb-3">Inspector Workload</h6>
                        @foreach($inspectionAnalysis['inspector_workload']->take(5) as $inspector)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $inspector->inspector->name ?? 'Inspector #' . $inspector->inspected_by }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px; height: 6px;">
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

            <!-- Performance Metrics -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tachometer-alt me-2"></i>Performance Metrics
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Processing Time</h6>
                                <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}">
                                    {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 5 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 10 ? 'warning' : 'danger') }}" 
                                     style="width: {{ min(100, (21 - $processingTimeAnalysis['avg_processing_days']) / 21 * 100) }}%"></div>
                            </div>
                            <small class="text-muted">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d</small>
                        </div>
                        
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Completion Rate</h6>
                                <span class="badge bg-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                            </div>
                            <small class="text-muted">{{ $processingTimeAnalysis['processed_count'] }} processed</small>
                        </div>
                        
                        <div class="metric-item mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Quality Score</h6>
                                <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                    {{ $performanceMetrics['quality_score'] }}%
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}" 
                                     style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                            </div>
                            <small class="text-muted">Based on approval & inspection rates</small>
                        </div>
                        
                        <div class="metric-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Daily Average</h6>
                                <span class="badge bg-primary">{{ $performanceMetrics['avg_applications_per_day'] }}</span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ min(100, $performanceMetrics['avg_applications_per_day'] * 10) }}%"></div>
                            </div>
                            <small class="text-muted">Applications per day</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Analysis & Registration Patterns -->
        <div class="row">
            <!-- Document Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Document Submission Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_user_docs'] }}%</h4>
                                    <p class="mb-0 text-muted">Approval with Docs</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_user_docs'] }}%</h4>
                                    <p class="mb-0 text-muted">Approval without Docs</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>User Documents</span>
                                <span class="text-success">{{ $documentAnalysis['with_user_documents'] }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['with_user_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Inspection Documents</span>
                                <span class="text-info">{{ $documentAnalysis['with_inspection_documents'] }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-info" 
                                     style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['with_inspection_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Applications with user documents have a {{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}% higher approval rate.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Patterns -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Registration Patterns
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Applications by Day of Week</h6>
                        @foreach($registrationPatterns['day_of_week'] as $day)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $day->day_name }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress me-2" style="width: 100px; height: 6px;">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ $registrationPatterns['day_of_week']->max('applications_count') > 0 ? ($day->applications_count / $registrationPatterns['day_of_week']->max('applications_count')) * 100 : 0 }}%"></div>
                                </div>
                                <span class="badge bg-primary">{{ $day->applications_count }}</span>
                            </div>
                        </div>
                        @endforeach
                        
                        <hr class="my-3">
                        
                        <h6 class="mb-3">Peak Registration Hours</h6>
                        @php
                            $peakHours = $registrationPatterns['hourly']->sortByDesc('applications_count')->take(3);
                        @endphp
                        @foreach($peakHours as $hour)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $hour->hour }}:00 - {{ $hour->hour + 1 }}:00</span>
                            <span class="badge bg-warning">{{ $hour->applications_count }} applications</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Insights Row -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-dark text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Key Performance Insights
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-success bg-opacity-10">
                                                <i class="fas fa-check-circle text-success"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Strong Registration Rate</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['approval_rate'] }}% approval rate with {{ $overview['unique_vessels'] }} vessels registered.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-info bg-opacity-10">
                                                <i class="fas fa-clipboard-check text-info"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Inspection Progress</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $inspectionAnalysis['completion_rate'] }}% inspection completion with {{ $inspectionAnalysis['avg_inspection_time'] }}d average time.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-warning bg-opacity-10">
                                                <i class="fas fa-file-alt text-warning"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Document Impact</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $documentAnalysis['approval_rate_with_user_docs'] - $documentAnalysis['approval_rate_without_user_docs'] }}% higher approval with supporting documents.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="insight-item mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="p-2 rounded-circle bg-primary bg-opacity-10">
                                                <i class="fas fa-ship text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Fleet Diversity</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['unique_boat_types'] }} different boat types with varied fishing gear applications.
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
    </div>

</div>

<!-- BOATR AI Insights Modal -->
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
                        <h6><i class="fas fa-chart-line text-success me-2"></i>Growth Opportunities</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-arrow-up text-success me-2"></i>
                                Streamline inspection process for faster approvals
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                Promote digital document submission system
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-ship text-warning me-2"></i>
                                Focus on underrepresented boat types
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                {{ $processingTimeAnalysis['avg_processing_days'] > 10 ? 'Reduce processing time' : 'Maintain current processing speed' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clipboard-check text-info me-2"></i>
                                {{ $inspectionAnalysis['completion_rate'] < 80 ? 'Improve inspection completion rate' : 'Maintain inspection quality' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-tools text-success me-2"></i>
                                Balance fishing gear type registrations
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Recommendation</h6>
                            <p class="mb-0">Consider implementing mobile inspection units for remote areas and developing a digital inspection checklist to improve efficiency and consistency across all boat registrations.</p>
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
.bg-gradient-primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #374151 0%, #111827 100%); }
.bg-gradient-secondary { background: linear-gradient(135deg, #6b7280 0%, #374151 100%); }

.hover-bg-light:hover {
    background-color: rgba(0,0,0,0.05);
    transition: background-color 0.2s;
}

.insight-item {
    transition: transform 0.2s;
}

.insight-item:hover {
    transform: translateX(5px);
}

.metric-item {
    padding: 1rem;
    border-radius: 8px;
    background: rgba(0,0,0,0.02);
}

.card {
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
}

.nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 5px;
    transition: all 0.3s;
}

.nav-pills .nav-link.active {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.05);
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let chartInstances = {};
    
    // Initialize charts immediately for BOATR tab
    initializeBoatrStatusDonutChart();
    initializeBoatrTrendsChart();
    initializeBoatrFishingGearChart();
    
    function initializeBoatrStatusDonutChart() {
        const ctx = document.getElementById('boatrStatusDonutChart');
        if (!ctx) return;
        
        chartInstances.boatrStatusDonutChart = new Chart(ctx.getContext('2d'), {
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
                        '#10b981',  // green for approved
                        '#ef4444',  // red for rejected  
                        '#f59e0b',  // amber for under_review
                        '#8b5cf6',  // purple for inspection_scheduled
                        '#06b6d4',  // cyan for inspection_required
                        '#f97316',  // orange for documents_pending
                        '#6b7280'   // gray for pending
                    ],
                    borderWidth: 0,
                    cutout: '70%'
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
    
    function initializeBoatrTrendsChart() {
        const ctx = document.getElementById('boatrTrendsChart');
        if (!ctx) return;
        
        chartInstances.boatrTrendsChart = new Chart(ctx.getContext('2d'), {
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
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'Approved Applications',
                    data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }, {
                    label: 'Inspections Completed',
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
                }, {
                    label: 'Unique Vessels',
                    data: [{{ $monthlyTrends->pluck('unique_vessels')->implode(',') }}],
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: '#8b5cf6',
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
                            color: 'rgba(0,0,0,0.1)'
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255,255,255,0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                elements: {
                    point: {
                        hoverBorderWidth: 3
                    }
                }
            }
        });
    }
    
    function initializeBoatrFishingGearChart() {
        const ctx = document.getElementById('boatrFishingGearChart');
        if (!ctx) return;
        
        const gearData = [
            @foreach($fishingGearAnalysis->take(5) as $gear)
                {{ $gear->total_applications }},
            @endforeach
        ];
        
        const gearLabels = [
            @foreach($fishingGearAnalysis->take(5) as $gear)
                '{{ ucwords($gear->primary_fishing_gear) }}',
            @endforeach
        ];
        
        chartInstances.boatrFishingGearChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: gearLabels,
                datasets: [{
                    data: gearData,
                    backgroundColor: [
                        '#3b82f6',  // blue
                        '#10b981',  // green
                        '#f59e0b',  // amber
                        '#8b5cf6',  // purple
                        '#ef4444'   // red
                    ],
                    borderWidth: 0,
                    cutout: '60%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
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
    
    // Service tab switching
    const serviceTabs = document.querySelectorAll('#serviceTab button[data-bs-toggle="tab"], #serviceTab a');
    serviceTabs.forEach(tab => {
        if (tab.hasAttribute('data-bs-toggle')) {
            tab.addEventListener('shown.bs.tab', function(event) {
                const targetTab = event.target.getAttribute('data-bs-target');
                
                // Handle other service analytics initialization here
                switch(targetTab) {
                    case '#seedlings-service':
                        console.log('Seedlings service selected');
                        break;
                    case '#rsbsa-service':
                        console.log('RSBSA service selected');
                        break;
                    case '#fishr-service':
                        console.log('FISHR service selected');
                        break;
                    default:
                        // BOATR is already initialized
                        break;
                }
            });
        }
    });
    
    // Cleanup function
    window.destroyCharts = function() {
        Object.values(chartInstances).forEach(chart => {
            if (chart) {
                chart.destroy();
            }
        });
        chartInstances = {};
    };
    
    // Add smooth scrolling for better UX
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading animation or effects here if needed
        });
    });
    
    // Add hover effects for metric cards
    document.querySelectorAll('.metric-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>
@endsection
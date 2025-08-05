{{-- resources/views/admin/analytics/fishr.blade.php --}}

@extends('layouts.app')

@section('title', 'FISHR Analytics - AgriSys Admin')
@section('page-title', 'FISHR Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">FISHR Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Fishermen Registration Services</p>
                    </div>
                    <!-- Service Tabs -->
                <!-- Service Tabs - Consistent Structure -->
                <ul class="nav nav-pills" id="serviceTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.analytics.seedlings') }}" 
                        class="nav-link {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                            <i class="fas fa-seedling me-1"></i> Seedlings
                        </a>
                    </li>
                   <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rsbsa-service-tab" data-bs-toggle="tab" 
                                    data-bs-target="#rsbsa-service" type="button" role="tab">
                                <i class="fas fa-user-check me-1"></i> RSBSA
                            </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.analytics.fishr') }}" 
                        class="nav-link {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                            <i class="fas fa-fish me-1"></i> FISHR
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                            <button class="nav-link" id="equipment-service-tab" data-bs-toggle="tab" 
                                    data-bs-target="#equipment-service" type="button" role="tab">
                                <i class="fas fa-tools me-1"></i> Equipment
                            </button>
                    </li>
                </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Content -->
<div class="tab-content" id="serviceTabContent">
    <!-- FISHR Service Tab -->
    <div class="tab-pane fade show active" id="fishr-service" role="tabpanel">
        
        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.analytics.fishr') }}" class="row g-3">
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
                                <a href="{{ route('admin.analytics.fishr.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#fishrInsightsModal">
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
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Total Applications</h6>
                                <h2 class="mb-1">{{ number_format($overview['total_applications']) }}</h2>
                                <small class="opacity-75">
                                    <i class="fas fa-arrow-up me-1"></i>15% from last period
                                </small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-fish fa-lg"></i>
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
            
            <!-- Community Reach Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Community Reach</h6>
                                <h2 class="mb-1">{{ $overview['active_barangays'] }}</h2>
                                <small class="opacity-75">{{ number_format($overview['unique_applicants']) }} fishermen registered</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-map-marker-alt fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Document Submission Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Document Submission</h6>
                                <h2 class="mb-1">{{ $overview['document_submission_rate'] }}%</h2>
                                <small class="opacity-75">{{ number_format($overview['with_documents']) }} with documents</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-file-alt fa-lg"></i>
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
                            <canvas id="fishrStatusDonutChart" height="250"></canvas>
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
                        <canvas id="fishrTrendsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Analytics Row -->
        <div class="row">
            <!-- Livelihood Distribution -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>Livelihood Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($livelihoodAnalysis->take(5) as $index => $livelihood)
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center p-3 rounded" style="background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);">
                                    <div class="me-3">
                                        <div class="badge bg-success rounded-pill p-2">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ ucfirst($livelihood->main_livelihood) }}</h6>
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ ($livelihood->total_applications / $livelihoodAnalysis->first()->total_applications) * 100 }}%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $livelihood->total_applications }} applications</small>
                                            <small class="text-success">{{ round(($livelihood->approved / max(1, $livelihood->total_applications)) * 100, 1) }}% approval</small>
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
                            <canvas id="fishrGenderChart" height="200"></canvas>
                        </div>
                        <div class="row text-center">
                            @foreach($genderAnalysis['stats'] as $gender)
                            <div class="col-6">
                                <div class="p-3 rounded" style="background: rgba({{ $gender->sex === 'Male' ? '59, 130, 246' : '236, 72, 153' }}, 0.1);">
                                    <h4 class="mb-1 text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">{{ $gender->total_applications }}</h4>
                                    <p class="mb-0 text-muted">{{ $gender->sex }} Applicants</p>
                                    <small class="text-{{ $gender->sex === 'Male' ? 'primary' : 'pink' }}">
                                        {{ $genderAnalysis['percentages'][$gender->sex] ?? 0 }}%
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
                                        <th>With Documents</th>
                                        <th>Unique Applicants</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($barangayAnalysis->take(10) as $index => $barangay)
                                    <tr>
                                        <td>
                                            <div class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }} rounded-pill">
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
                                                         style="width: {{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%"></div>
                                                </div>
                                                <small>{{ round(($barangay->approved / max(1, $barangay->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td>{{ $barangay->with_documents }}</td>
                                        <td>{{ $barangay->unique_applicants }}</td>
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
                                <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}">
                                    {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 8px;">
                                <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}" 
                                     style="width: {{ min(100, (14 - $processingTimeAnalysis['avg_processing_days']) / 14 * 100) }}%"></div>
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
                            <small class="text-muted">Based on approval & doc rates</small>
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
                    <div class="card-header bg-gradient-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Document Submission Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_docs'] }}%</h4>
                                    <p class="mb-0 text-muted">Approval with Docs</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded bg-light">
                                    <h4 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_docs'] }}%</h4>
                                    <p class="mb-0 text-muted">Approval without Docs</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>With Documents</span>
                                <span class="text-success">{{ $documentAnalysis['with_documents'] }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['with_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Without Documents</span>
                                <span class="text-warning">{{ $documentAnalysis['without_documents'] }}</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ $documentAnalysis['total'] > 0 ? ($documentAnalysis['without_documents'] / $documentAnalysis['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                Applications with supporting documents have a {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}% higher approval rate.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Patterns -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
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
                                            <h6 class="mb-1">Strong Approval Rate</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['approval_rate'] }}% approval rate shows effective application processing.
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
                                                <i class="fas fa-users text-info"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Wide Community Reach</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['active_barangays'] }} barangays with {{ $overview['unique_applicants'] }} registered fishermen.
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
                                                {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}% higher approval with supporting documents.
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
                                                <i class="fas fa-clock text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Efficient Processing</h6>
                                            <p class="text-muted mb-0 small">
                                                Average {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time with {{ $performanceMetrics['completion_rate'] }}% completion rate.
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

    <!-- Placeholder tabs for other services -->
    <div class="tab-pane fade" id="fertilizers-service" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                <h4>Fertilizers Analytics</h4>
                <p class="text-muted">Analytics for fertilizer distribution will be displayed here.</p>
                <button class="btn btn-primary">Configure Fertilizer Analytics</button>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="equipment-service" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                <h4>Equipment Analytics</h4>
                <p class="text-muted">Analytics for equipment loans and maintenance will be displayed here.</p>
                <button class="btn btn-primary">Configure Equipment Analytics</button>
            </div>
        </div>
    </div>
</div>

<!-- FISHR AI Insights Modal -->
<div class="modal fade" id="fishrInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot me-2"></i>FISHR AI-Powered Insights
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
                                Expand outreach to barangays with low registration rates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                Promote document submission to improve approval rates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                Implement online application system for peak hours
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-balance-scale text-warning me-2"></i>
                                Balance gender representation in applications
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-tachometer-alt text-info me-2"></i>
                                {{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'Reduce processing time' : 'Maintain current processing speed' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                Focus on underserved livelihood categories
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Recommendation</h6>
                            <p class="mb-0">Consider implementing a mobile registration unit for remote barangays and conducting information campaigns about the importance of supporting documents.</p>
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
.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.bg-gradient-dark { background: linear-gradient(135deg, #434343 0%, #000000 100%); }
.bg-gradient-secondary { background: linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%); }

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

.text-pink {
    color: #ec4899 !important;
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
    
    // Initialize charts immediately for FISHR tab
    initializeFishrStatusDonutChart();
    initializeFishrTrendsChart();
    initializeFishrGenderChart();
    
    function initializeFishrStatusDonutChart() {
        const ctx = document.getElementById('fishrStatusDonutChart');
        if (!ctx) return;
        
        chartInstances.fishrStatusDonutChart = new Chart(ctx.getContext('2d'), {
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
                        '#f59e0b'   // amber for under_review
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
    
    function initializeFishrTrendsChart() {
        const ctx = document.getElementById('fishrTrendsChart');
        if (!ctx) return;
        
        chartInstances.fishrTrendsChart = new Chart(ctx.getContext('2d'), {
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
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#0ea5e9',
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
                    label: 'With Documents',
                    data: [{{ $monthlyTrends->pluck('with_documents')->implode(',') }}],
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
    
    function initializeFishrGenderChart() {
        const ctx = document.getElementById('fishrGenderChart');
        if (!ctx) return;
        
        const genderData = [
            @foreach($genderAnalysis['stats'] as $gender)
                {{ $gender->total_applications }},
            @endforeach
        ];
        
        const genderLabels = [
            @foreach($genderAnalysis['stats'] as $gender)
                '{{ $gender->sex }}',
            @endforeach
        ];
        
        chartInstances.fishrGenderChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: genderLabels,
                datasets: [{
                    data: genderData,
                    backgroundColor: [
                        '#3b82f6',  // blue for male
                        '#ec4899'   // pink for female
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
                            padding: 20,
                            font: {
                                size: 12
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
                    case '#fertilizers-service':
                        console.log('Fertilizers service selected');
                        break;
                    case '#equipment-service':
                        console.log('Equipment service selected');
                        break;
                    default:
                        // FISHR is already initialized
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
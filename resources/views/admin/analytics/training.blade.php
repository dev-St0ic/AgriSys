{{-- resources/views/admin/analytics/training.blade.php --}}

@extends('layouts.app')

@section('title', 'Training Analytics - AgriSys Admin')
@section('page-title', 'Training Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Training Analytics Dashboard</h4>
                        <p class="text-muted mb-0">Comprehensive insights into Agricultural Training Services</p>
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
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('admin.analytics.training') }}" 
                            class="nav-link {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                                <i class="fas fa-graduation-cap me-1"></i> Training
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
    <!-- Training Service Tab -->
    <div class="tab-pane fade show active" id="training-service" role="tabpanel">
        
        <!-- Date Range Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.analytics.training') }}" class="row g-3">
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
                                <a href="{{ route('admin.analytics.training.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                                   class="btn btn-success me-2">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#trainingInsightsModal">
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
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
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
                                <i class="fas fa-graduation-cap fa-lg"></i>
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
            
            <!-- Training Types Card -->
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="card-title mb-2 opacity-75">Training Programs</h6>
                                <h2 class="mb-1">{{ $overview['unique_training_types'] }}</h2>
                                <small class="opacity-75">{{ number_format($overview['unique_applicants']) }} unique trainees</small>
                            </div>
                            <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                <i class="fas fa-book fa-lg"></i>
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
                            <canvas id="trainingStatusDonutChart" height="250"></canvas>
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
                        <canvas id="trainingTrendsChart" height="120"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Analytics Row -->
        <div class="row">
            <!-- Training Type Distribution -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Training Program Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Training Program</th>
                                        <th>Total Applications</th>
                                        <th>Approved</th>
                                        <th>Approval Rate</th>
                                        <th>Avg Processing Days</th>
                                        <th>With Documents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainingTypeAnalysis->take(10) as $index => $training)
                                    <tr>
                                        <td>
                                            <div class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }} rounded-pill">
                                                #{{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td>
                                            <strong>
                                                @switch($training->training_type)
                                                    @case('tilapia_hito')
                                                        Tilapia & Hito Training
                                                        @break
                                                    @case('hydroponics')
                                                        Hydroponics Training
                                                        @break
                                                    @case('aquaponics')
                                                        Aquaponics Training
                                                        @break
                                                    @case('mushrooms')
                                                        Mushrooms Production
                                                        @break
                                                    @case('livestock_poultry')
                                                        Livestock & Poultry
                                                        @break
                                                    @case('high_value_crops')
                                                        High Value Crops
                                                        @break
                                                    @case('sampaguita_propagation')
                                                        Sampaguita Propagation
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $training->training_type)) }}
                                                @endswitch
                                            </strong>
                                        </td>
                                        <td>{{ $training->total_applications }}</td>
                                        <td>{{ $training->approved }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress me-2" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%"></div>
                                                </div>
                                                <small>{{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $training->avg_processing_days < 3 ? 'success' : ($training->avg_processing_days < 7 ? 'warning' : 'danger') }}">
                                                {{ round($training->avg_processing_days ?? 0, 1) }}d
                                            </span>
                                        </td>
                                        <td>{{ $training->with_documents }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Method Analysis -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-address-book me-2"></i>Contact Method Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-3">
                            <canvas id="trainingContactChart" height="200"></canvas>
                        </div>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3 rounded" style="background: rgba(16, 185, 129, 0.1);">
                                    <h4 class="mb-1 text-success">{{ $contactAnalysis['stats']['both_contacts'] }}</h4>
                                    <p class="mb-0 text-muted small">Both Contacts</p>
                                    <small class="text-success">{{ $contactAnalysis['percentages']['both_contacts'] }}%</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 rounded" style="background: rgba(59, 130, 246, 0.1);">
                                    <h4 class="mb-1 text-primary">{{ $contactAnalysis['stats']['email_only'] }}</h4>
                                    <p class="mb-0 text-muted small">Email Only</p>
                                    <small class="text-primary">{{ $contactAnalysis['percentages']['email_only'] }}%</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 rounded" style="background: rgba(245, 158, 11, 0.1);">
                                    <h4 class="mb-1 text-warning">{{ $contactAnalysis['stats']['mobile_only'] }}</h4>
                                    <p class="mb-0 text-muted small">Mobile Only</p>
                                    <small class="text-warning">{{ $contactAnalysis['percentages']['mobile_only'] }}%</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 rounded" style="background: rgba(239, 68, 68, 0.1);">
                                    <h4 class="mb-1 text-danger">{{ $contactAnalysis['stats']['no_contact'] }}</h4>
                                    <p class="mb-0 text-muted small">No Contact</p>
                                    <small class="text-danger">{{ $contactAnalysis['percentages']['no_contact'] }}%</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics & Registration Patterns -->
        <div class="row">
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

            <!-- Document Analysis -->
            <div class="col-lg-4 mb-4">
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
            <div class="col-lg-4 mb-4">
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
                                                <i class="fas fa-graduation-cap text-success"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Training Diversity</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['unique_training_types'] }} different training programs with {{ $overview['approval_rate'] }}% approval rate.
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
                                            <h6 class="mb-1">Community Engagement</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $overview['unique_applicants'] }} unique applicants showing strong community interest in training.
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
                                                <i class="fas fa-phone text-primary"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">Communication Channels</h6>
                                            <p class="text-muted mb-0 small">
                                                {{ $contactAnalysis['percentages']['both_contacts'] }}% provide both email and mobile for better follow-up.
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

<!-- Training AI Insights Modal -->
<div class="modal fade" id="trainingInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-robot me-2"></i>Training AI-Powered Insights
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
                                Expand popular training programs like {{ $trainingTypeAnalysis->first()->training_type ?? 'aquaponics' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-file-alt text-info me-2"></i>
                                Promote document submission to improve approval rates
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-envelope text-warning me-2"></i>
                                Encourage email provision for better communication
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-exclamation-triangle text-warning me-2"></i>Areas for Improvement</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-clock text-warning me-2"></i>
                                {{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'Reduce processing time for faster enrollment' : 'Maintain current processing speed' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-graduation-cap text-info me-2"></i>
                                Consider hybrid online/offline training options
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-phone text-success me-2"></i>
                                Improve contact information collection rates
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-lightbulb me-2"></i>Recommendation</h6>
                            <p class="mb-0">Consider implementing online pre-registration for popular training programs and follow-up SMS notifications for approved applicants without email addresses.</p>
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
    transition: all 0.3s ease;
}

.metric-item:hover {
    background: rgba(0,0,0,0.05);
    transform: scale(1.02);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
    position: relative;
    overflow: hidden;
}

.nav-pills .nav-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.nav-pills .nav-link.active {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.05);
    transform: translateX(3px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.table-hover tbody tr {
    transition: all 0.3s ease;
}

.badge {
    transition: all 0.2s ease;
    cursor: pointer;
}

.badge:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.progress-bar {
    transition: width 1.5s ease-in-out;
    position: relative;
    overflow: hidden;
}

.progress-bar::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.btn {
    transition: all 0.3s ease;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.btn.disabled, .btn:disabled {
    transform: none;
    box-shadow: none;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    border-radius: 15px 15px 0 0;
    border-bottom: none;
}

.alert {
    border-radius: 10px;
    border: none;
    position: relative;
    overflow: hidden;
}

.alert::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: currentColor;
    opacity: 0.5;
}

/* Chart container styling */
canvas {
    border-radius: 8px;
}

.chart-container {
    position: relative;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    padding: 10px;
}

/* Custom scrollbar for tables */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Animation for loading states */
@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

.loading {
    animation: pulse 1.5s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .nav-pills .nav-link {
        margin: 2px;
        font-size: 0.875rem;
    }
    
    .card:hover {
        transform: none;
        box-shadow: none;
    }
    
    .metric-item:hover {
        transform: none;
        box-shadow: none;
    }
    
    .insight-item:hover {
        transform: none;
    }
}

@media (max-width: 768px) {
    .nav-pills {
        flex-wrap: wrap;
    }
    
    .nav-pills .nav-link {
        margin: 2px 1px;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .metric-item {
        padding: 0.75rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

@media (max-width: 576px) {
    .nav-pills .nav-link {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    .card-body h2 {
        font-size: 1.5rem;
    }
    
    .card-body h4 {
        font-size: 1.2rem;
    }
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .card {
        background-color: #1f2937;
        color: #f9fafb;
    }
    
    .table {
        color: #f9fafb;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .form-control {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .form-control:focus {
        background-color: #374151;
        border-color: #667eea;
        color: #f9fafb;
    }
    
    .modal-content {
        background-color: #1f2937;
        color: #f9fafb;
    }
    
    .alert {
        background-color: #374151;
        color: #f9fafb;
    }
}

/* Print styles */
@media print {
    .nav-pills,
    .btn,
    .modal {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        break-inside: avoid;
    }
    
    .card-header {
        background: #000 !important;
        color: #fff !important;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.4;
    }
    
    .table {
        font-size: 10pt;
    }
    
    .badge {
        border: 1px solid #000;
    }
    
    canvas {
        display: none !important;
    }
}

/* Accessibility improvements */
.btn:focus,
.nav-link:focus,
.form-control:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .card {
        border: 2px solid #000;
    }
    
    .badge {
        border: 1px solid #000;
    }
    
    .btn {
        border: 2px solid #000;
    }
    
    .progress-bar {
        border: 1px solid #000;
    }
}

/* Reduce motion for accessibility */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let chartInstances = {};
    
    // Initialize charts immediately for Training tab
    initializeTrainingStatusDonutChart();
    initializeTrainingTrendsChart();
    initializeTrainingContactChart();
    
    function initializeTrainingStatusDonutChart() {
        const ctx = document.getElementById('trainingStatusDonutChart');
        if (!ctx) return;
        
        chartInstances.trainingStatusDonutChart = new Chart(ctx.getContext('2d'), {
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
    
    function initializeTrainingTrendsChart() {
        const ctx = document.getElementById('trainingTrendsChart');
        if (!ctx) return;
        
        chartInstances.trainingTrendsChart = new Chart(ctx.getContext('2d'), {
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
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#8b5cf6',
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
                }, {
                    label: 'Unique Training Types',
                    data: [{{ $monthlyTrends->pluck('unique_training_types')->implode(',') }}],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: false,
                    pointBackgroundColor: '#0ea5e9',
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
    
    function initializeTrainingContactChart() {
        const ctx = document.getElementById('trainingContactChart');
        if (!ctx) return;
        
        const contactData = [
            {{ $contactAnalysis['stats']['both_contacts'] }},
            {{ $contactAnalysis['stats']['email_only'] }},
            {{ $contactAnalysis['stats']['mobile_only'] }},
            {{ $contactAnalysis['stats']['no_contact'] }}
        ];
        
        const contactLabels = ['Both Contacts', 'Email Only', 'Mobile Only', 'No Contact'];
        
        chartInstances.trainingContactChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: contactLabels,
                datasets: [{
                    data: contactData,
                    backgroundColor: [
                        '#10b981',  // green for both contacts
                        '#3b82f6',  // blue for email only
                        '#f59e0b',  // amber for mobile only
                        '#ef4444'   // red for no contact
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
    
    // Service tab switching functionality
    const serviceTabs = document.querySelectorAll('#serviceTab a, #serviceTab button[data-bs-toggle="tab"]');
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
                    case '#boatr-service':
                        console.log('BOATR service selected');
                        break;
                    default:
                        // Training is already initialized
                        break;
                }
            });
        }
    });
    
    // Add smooth scrolling animation for nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Add ripple effect
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add hover effects for metric cards
    document.querySelectorAll('.metric-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'all 0.3s ease';
            this.style.boxShadow = '0 4px 20px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Add animation for insight items
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
    
    document.querySelectorAll('.insight-item').forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
    });
    
    // Add progress bar animations
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            bar.style.transition = 'width 1.5s ease-in-out';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 200);
        });
    }
    
    // Trigger progress bar animations on page load
    setTimeout(animateProgressBars, 500);
    
    // Add counter animation for metric cards
    function animateCounters() {
        const counters = document.querySelectorAll('.card-body h2, .card-body h4');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent.replace(/[^0-9]/g, ''));
            if (isNaN(target)) return;
            
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = counter.textContent.replace(/[0-9,]+/, target.toLocaleString());
                    clearInterval(timer);
                } else {
                    counter.textContent = counter.textContent.replace(/[0-9,]+/, Math.floor(current).toLocaleString());
                }
            }, 30);
        });
    }
    
    // Trigger counter animations
    setTimeout(animateCounters, 300);
    
    // Add chart resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            Object.values(chartInstances).forEach(chart => {
                if (chart) {
                    chart.resize();
                }
            });
        }, 250);
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
    
    // Add loading states for export functionality
    document.querySelectorAll('a[href*="export"]').forEach(exportLink => {
        exportLink.addEventListener('click', function(e) {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exporting...';
            this.classList.add('disabled');
            
            setTimeout(() => {
                this.innerHTML = originalText;
                this.classList.remove('disabled');
            }, 3000);
        });
    });
    
    // Add smooth transitions for table rows
    document.querySelectorAll('.table-hover tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Add tooltip functionality for badges and metrics
    function initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[title], .badge, .progress-bar');
        tooltipElements.forEach(element => {
            if (!element.getAttribute('title')) {
                // Add contextual titles for badges and progress bars
                if (element.classList.contains('badge')) {
                    const text = element.textContent.trim();
                    if (text.includes('%')) {
                        element.setAttribute('title', 'Click for detailed breakdown');
                    } else if (text.includes('d')) {
                        element.setAttribute('title', 'Average processing time in days');
                    }
                }
            }
        });
    }
    
    initializeTooltips();
    
    // Add click handlers for interactive elements
    document.querySelectorAll('.badge, .metric-item').forEach(element => {
        element.style.cursor = 'pointer';
        element.addEventListener('click', function() {
            // Add click feedback
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });
    
    console.log('Training Analytics Dashboard initialized successfully');
});

// Add CSS animations via JavaScript
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
        width: 100px;
        height: 100px;
        left: 50%;
        top: 50%;
        margin-left: -50px;
        margin-top: -50px;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    .card {
        transition: all 0.3s ease !important;
    }
    
    .card:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 6px 25px rgba(0,0,0,0.15) !important;
    }
    
    .progress-bar {
        transition: width 1.5s ease-in-out !important;
    }
    
    .metric-item:hover {
        background: rgba(0,0,0,0.05) !important;
    }
    
    .table-hover tbody tr {
        transition: all 0.3s ease !important;
    }
    
    .nav-pills .nav-link {
        position: relative;
        overflow: hidden;
    }
    
    .insight-item {
        transition: all 0.6s ease !important;
    }
    
    @media (max-width: 768px) {
        .card:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection
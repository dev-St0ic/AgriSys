{{-- resources/views/admin/analytics/training.blade.php --}}

@extends('layouts.app')

@section('title', 'Training Analytics - AgriSys Admin')
@section('page-title', 'Training Analytics Dashboard')

@section('content')
<!-- Header with Service Navigation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-center mb-3">
                    <h4 class="mb-2 fw-bold">Training Analytics Dashboard</h4>
                    <p class="text-muted mb-0">Comprehensive insights into Agricultural Training Services</p>
                </div>
                <!-- Service Tabs -->
                <div class="d-flex justify-content-center flex-wrap">
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
                <form method="GET" action="{{ route('admin.analytics.training') }}" class="row g-3 align-items-end">
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
                        <a href="{{ route('admin.analytics.training.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}" 
                           class="btn btn-success me-2">
                            <i class="fas fa-download me-1"></i> Export Data
                        </a>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#trainingInsightsModal">
                            <i class="fas fa-lightbulb me-1"></i> AI Insights
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Key Metrics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="metric-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Total Applications</h6>
                <h2 class="metric-value">{{ number_format($overview['total_applications']) }}</h2>
                <p class="metric-subtitle">All time registrations</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="metric-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Approval Rate</h6>
                <h2 class="metric-value">{{ $overview['approval_rate'] }}%</h2>
                <p class="metric-subtitle">{{ number_format($overview['approved_applications']) }} approved</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
            <div class="metric-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Training Programs</h6>
                <h2 class="metric-value">{{ $overview['unique_training_types'] }}</h2>
                <p class="metric-subtitle">{{ number_format($overview['unique_applicants']) }} unique trainees</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="metric-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="metric-content">
                <h6 class="metric-label">Avg Processing Time</h6>
                <h2 class="metric-value">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h2>
                <p class="metric-subtitle">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Analytics -->
<div class="row mb-4">
    <!-- Monthly Trends Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-line me-2 text-primary"></i>Application Trends
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trainingTrendsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-pie-chart me-2 text-success"></i>Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trainingStatusChart"></canvas>
                <div class="mt-4">
                    @foreach($statusAnalysis['counts'] as $status => $count)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="d-flex align-items-center">
                            <span class="status-dot bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                        <div>
                            <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ $count }}
                            </span>
                            <small class="text-muted ms-2">{{ $statusAnalysis['percentages'][$status] }}%</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Training Programs Performance -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-graduation-cap me-2 text-info"></i>Training Program Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Rank</th>
                                <th class="fw-semibold">Training Program</th>
                                <th class="fw-semibold text-center">Applications</th>
                                <th class="fw-semibold text-center">Approved</th>
                                <th class="fw-semibold text-center">Approval Rate</th>
                                <th class="fw-semibold text-center">Processing Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainingTypeAnalysis->take(10) as $index => $training)
                            <tr>
                                <td>
                                    <div class="rank-badge rank-{{ $index < 3 ? ($index === 0 ? 'gold' : ($index === 1 ? 'silver' : 'bronze')) : 'default' }}">
                                        #{{ $index + 1 }}
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-dark">
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
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $training->total_applications }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $training->approved }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress-bar-container me-2">
                                            <div class="progress-bar-fill bg-success" 
                                                 style="width: {{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%"></div>
                                        </div>
                                        <small class="fw-semibold">{{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $training->avg_processing_days < 3 ? 'success' : ($training->avg_processing_days < 7 ? 'warning' : 'danger') }}">
                                        {{ round($training->avg_processing_days ?? 0, 1) }} days
                                    </span>
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

<!-- Performance Insights -->
<div class="row mb-4">
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-file-alt me-2 text-warning"></i>Document Impact
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-6">
                        <div class="stat-box bg-success-subtle">
                            <h3 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_docs'] }}%</h3>
                            <p class="mb-0 small text-muted">With Documents</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-warning-subtle">
                            <h3 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_docs'] }}%</h3>
                            <p class="mb-0 small text-muted">Without Documents</p>
                        </div>
                    </div>
                </div>
                <div class="alert alert-info border-0 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</strong> 
                    higher approval with documents
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-address-book me-2 text-info"></i>Contact Methods
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="stat-box bg-success-subtle">
                            <h4 class="text-success mb-1">{{ $contactAnalysis['stats']['both_contacts'] }}</h4>
                            <p class="mb-0 small text-muted">Both Contacts</p>
                            <small class="text-success fw-semibold">{{ $contactAnalysis['percentages']['both_contacts'] }}%</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-box bg-primary-subtle">
                            <h4 class="text-primary mb-1">{{ $contactAnalysis['stats']['email_only'] }}</h4>
                            <p class="mb-0 small text-muted">Email Only</p>
                            <small class="text-primary fw-semibold">{{ $contactAnalysis['percentages']['email_only'] }}%</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-box bg-warning-subtle">
                            <h4 class="text-warning mb-1">{{ $contactAnalysis['stats']['mobile_only'] }}</h4>
                            <p class="mb-0 small text-muted">Mobile Only</p>
                            <small class="text-warning fw-semibold">{{ $contactAnalysis['percentages']['mobile_only'] }}%</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-box bg-danger-subtle">
                            <h4 class="text-danger mb-1">{{ $contactAnalysis['stats']['no_contact'] }}</h4>
                            <p class="mb-0 small text-muted">No Contact</p>
                            <small class="text-danger fw-semibold">{{ $contactAnalysis['percentages']['no_contact'] }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-calendar-alt me-2 text-primary"></i>Peak Registration Days
                </h5>
            </div>
            <div class="card-body">
                @foreach($registrationPatterns['day_of_week'] as $day)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-semibold">{{ $day->day_name }}</span>
                    <div class="d-flex align-items-center">
                        <div class="progress-bar-container me-2" style="width: 100px;">
                            <div class="progress-bar-fill bg-primary" 
                                 style="width: {{ $registrationPatterns['day_of_week']->max('applications_count') > 0 ? ($day->applications_count / $registrationPatterns['day_of_week']->max('applications_count')) * 100 : 0 }}%"></div>
                        </div>
                        <span class="badge bg-primary">{{ $day->applications_count }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- AI Insights Modal -->
<div class="modal fade" id="trainingInsightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
                    <i class="fas fa-robot me-2"></i>AI-Powered Insights & Recommendations
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="insight-section">
                            <h6 class="fw-bold text-success mb-3">
                                <i class="fas fa-chart-line me-2"></i>Growth Opportunities
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-arrow-up text-success me-2 mt-1"></i>
                                    <span>Expand popular training programs like {{ $trainingTypeAnalysis->first()->training_type ?? 'aquaponics' }}</span>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-file-alt text-success me-2 mt-1"></i>
                                    <span>Promote document submission to improve approval rates by {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</span>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-envelope text-success me-2 mt-1"></i>
                                    <span>Encourage complete contact information for better communication</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="insight-section">
                            <h6 class="fw-bold text-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Areas for Improvement
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-clock text-warning me-2 mt-1"></i>
                                    <span>{{ $processingTimeAnalysis['avg_processing_days'] > 5 ? 'Reduce processing time for faster enrollment' : 'Maintain current processing speed' }}</span>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-graduation-cap text-warning me-2 mt-1"></i>
                                    <span>Consider hybrid online/offline training options</span>
                                </li>
                                <li class="mb-3 d-flex align-items-start">
                                    <i class="fas fa-phone text-warning me-2 mt-1"></i>
                                    <span>Improve contact information collection rates</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-primary border-0 mb-0">
                    <h6 class="fw-bold mb-2">
                        <i class="fas fa-lightbulb me-2"></i>Strategic Recommendation
                    </h6>
                    <p class="mb-0">Consider implementing online pre-registration for popular training programs and follow-up SMS notifications for approved applicants without email addresses.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Modern Card Styles */
.card {
    border-radius: 12px;
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}

/* Metric Cards */
.metric-card {
    padding: 1.5rem;
    border-radius: 12px;
    color: white;
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.metric-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.metric-content {
    flex: 1;
}

.metric-label {
    font-size: 0.875rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    line-height: 1;
}

.metric-subtitle {
    font-size: 0.875rem;
    opacity: 0.85;
    margin: 0;
}

/* Status Dot */
.status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

/* Rank Badges */
.rank-badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-weight: bold;
    font-size: 0.875rem;
}

.rank-gold {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #856404;
}

.rank-silver {
    background: linear-gradient(135deg, #c0c0c0, #d3d3d3);
    color: #495057;
}

.rank-bronze {
    background: linear-gradient(135deg, #cd7f32, #d4a574);
    color: #fff;
}

.rank-default {
    background: #f8f9fa;
    color: #6c757d;
}

/* Progress Bar Container */
.progress-bar-container {
    width: 80px;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 1s ease;
}

/* Stat Box */
.stat-box {
    padding: 1.25rem;
    border-radius: 10px;
    transition: transform 0.2s ease;
}

.stat-box:hover {
    transform: scale(1.05);
}

/* Nav Pills */
.nav-pills .nav-link {
    border-radius: 8px;
    padding: 0.6rem 1rem;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
    font-weight: 500;
}

.nav-pills .nav-link:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

/* Table Styles */
.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: scale(1.01);
}

/* Form Controls */
.form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 0.6rem 1.25rem;
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

.btn-info {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    border: none;
}

/* Badge */
.badge {
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    border-radius: 6px;
}

/* Modal */
.modal-content {
    border-radius: 16px;
    border: none;
}

.modal-header {
    border-radius: 16px 16px 0 0;
    border-bottom: none;
    padding: 1.5rem;
}

.modal-body {
    padding: 2rem;
}

/* Insight Section */
.insight-section ul li {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

/* Alert */
.alert {
    border-radius: 10px;
    padding: 1rem 1.25rem;
}

/* Background Utilities */
.bg-success-subtle {
    background-color: rgba(16, 185, 129, 0.1);
}

.bg-warning-subtle {
    background-color: rgba(245, 158, 11, 0.1);
}

.bg-primary-subtle {
    background-color: rgba(59, 130, 246, 0.1);
}

.bg-danger-subtle {
    background-color: rgba(239, 68, 68, 0.1);
}

.bg-info-subtle {
    background-color: rgba(14, 165, 233, 0.1);
}

/* Chart Container */
canvas {
    max-height: 400px;
}

/* Responsive */
@media (max-width: 992px) {
    .metric-card {
        flex-direction: column;
        text-align: center;
    }
    
    .metric-value {
        font-size: 1.75rem;
    }
    
    .nav-pills {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .nav-pills .nav-link {
        margin: 0.25rem;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
}

@media (max-width: 768px) {
    .metric-card {
        padding: 1.25rem;
    }
    
    .metric-icon {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .metric-value {
        font-size: 1.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
}

@media (max-width: 576px) {
    .nav-pills .nav-link {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}

/* Print Styles */
@media print {
    .nav-pills,
    .btn,
    .modal {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        border: 1px solid #dee2e6 !important;
    }
    
    .metric-card {
        background: #f8f9fa !important;
        color: #000 !important;
    }
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeIn 0.4s ease;
}

/* Accessibility */
.btn:focus,
.nav-link:focus,
.form-control:focus {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart instances
    let trendChart = null;
    let statusChart = null;
    
    // Initialize charts
    initializeTrendChart();
    initializeStatusChart();
    
    // Trend Chart
    function initializeTrendChart() {
        const ctx = document.getElementById('trainingTrendsChart');
        if (!ctx) return;
        
        trendChart = new Chart(ctx.getContext('2d'), {
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
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Approved',
                        data: [{{ $monthlyTrends->pluck('approved')->implode(',') }}],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 2.5,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
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
    
    // Status Chart
    function initializeStatusChart() {
        const ctx = document.getElementById('trainingStatusChart');
        if (!ctx) return;
        
        statusChart = new Chart(ctx.getContext('2d'), {
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
                        '#10b981',
                        '#ef4444',
                        '#f59e0b'
                    ],
                    borderWidth: 0,
                    cutout: '70%'
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
    
    // Animate progress bars
    function animateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }
    
    // Trigger animations
    setTimeout(animateProgressBars, 300);
    
    // Chart resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            if (trendChart) trendChart.resize();
            if (statusChart) statusChart.resize();
        }, 250);
    });
    
    // Export button loading state
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
    
    // Table row hover effect
    document.querySelectorAll('.table-hover tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Cleanup
    window.destroyCharts = function() {
        if (trendChart) trendChart.destroy();
        if (statusChart) statusChart.destroy();
    };
    
    console.log('Training Analytics Dashboard initialized');
});
</script>
@endsection
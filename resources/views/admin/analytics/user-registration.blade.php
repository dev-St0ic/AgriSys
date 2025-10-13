{{-- resources/views/admin/analytics/user-registration.blade.php --}}

@extends('layouts.app')

@section('title', 'User Registration Analytics - AgriSys Admin')
@section('page-title', 'User Registration Analytics Dashboard')

@section('content')
    <!-- Header with Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div>
                            <h4 class="mb-2">User Registration Analytics Dashboard</h4>
                            <p class="text-muted mb-0">Comprehensive insights into user registration and verification processes</p>
                        </div>
                        <!-- Service Tabs - Unified Structure -->
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
        <!-- User Registration Service Tab -->
        <div class="tab-pane fade show active" id="user-registration-service" role="tabpanel">

            <!-- Date Range Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.analytics.user-registration') }}" class="row g-3">
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
                                    <a href="{{ route('admin.analytics.user-registration.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                        class="btn btn-success me-2">
                                        <i class="fas fa-download me-1"></i> Export
                                    </a>
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#userRegInsightsModal">
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
                <!-- Total Registrations Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Total Registrations</h6>
                                    <h2 class="mb-1">{{ number_format($overview['total_registrations']) }}</h2>
                                    <small class="opacity-75">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $overview['active_barangays'] }} barangays
                                    </small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-users fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Rate Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Approval Rate</h6>
                                    <h2 class="mb-1">{{ $overview['approval_rate'] }}%</h2>
                                    <small class="opacity-75">{{ number_format($overview['approved_registrations']) }} approved</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Verification Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Email Verification</h6>
                                    <h2 class="mb-1">{{ $overview['email_verification_rate'] }}%</h2>
                                    <small class="opacity-75">{{ number_format($overview['email_verified']) }} verified</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-envelope-circle-check fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Completion Card -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-0 shadow-sm h-100"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <div class="card-body text-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-2 opacity-75">Document Completion</h6>
                                    <h2 class="mb-1">{{ $overview['document_completion_rate'] }}%</h2>
                                    <small class="opacity-75">{{ number_format($overview['with_all_documents']) }} complete</small>
                                </div>
                                <div class="p-3 bg-white bg-opacity-20 rounded-circle">
                                    <i class="fas fa-file-circle-check fa-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Analytics Row -->
            <div class="row mb-4">
                <!-- Monthly Trends Chart - Takes Priority -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>Registration Trends
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userRegTrendsChart" height="280"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Status Distribution - Compact -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Status Overview
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userRegStatusChart" height="180"></canvas>
                            <div class="mt-3">
                                @foreach ($statusAnalysis['counts'] as $status => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2 py-1">
                                        <span class="d-flex align-items-center small">
                                            <i class="fas fa-circle me-2 text-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : ($status === 'pending' ? 'warning' : 'secondary')) }}" style="font-size: 8px;"></i>
                                            {{ ucfirst($status) }}
                                        </span>
                                        <span class="badge bg-{{ $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : ($status === 'pending' ? 'warning' : 'secondary')) }}">
                                            {{ $count }} ({{ $statusAnalysis['percentages'][$status] }}%)
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Demographics Row - Side by Side -->
            <div class="row mb-4">
                <!-- User Type Distribution - Horizontal Bars -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tag me-2"></i>User Types
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userTypeChart" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Gender & Age Combined -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-warning text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>Demographics
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                @foreach ($genderAnalysis['stats'] as $gender)
                                    <div class="col-6">
                                        <div class="text-center p-3 rounded" style="background: rgba({{ $gender->gender === 'Male' ? '59, 130, 246' : '236, 72, 153' }}, 0.1);">
                                            <h3 class="mb-1 text-{{ $gender->gender === 'Male' ? 'primary' : 'pink' }}">
                                                {{ $gender->total_registrations }}
                                            </h3>
                                            <p class="mb-1 fw-bold">{{ $gender->gender }}</p>
                                            <small class="text-muted">{{ $genderAnalysis['percentages'][$gender->gender] ?? 0 }}% | Avg Age: {{ round($gender->avg_age ?? 0, 1) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <canvas id="userRegAgeChart" height="180"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance & Document Analysis Row -->
            <div class="row mb-4">
                <!-- Performance Metrics - Compact Cards -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tachometer-alt me-2"></i>Performance
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Processing Time -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 small">Processing Time</h6>
                                    <span class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}">
                                        {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}"
                                        style="width: {{ min(100, ((14 - $processingTimeAnalysis['avg_processing_days']) / 14) * 100) }}%">
                                    </div>
                                </div>
                            </div>

                            <!-- Completion Rate -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 small">Completion Rate</h6>
                                    <span class="badge bg-info">{{ $performanceMetrics['completion_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ $performanceMetrics['completion_rate'] }}%"></div>
                                </div>
                            </div>

                            <!-- User Engagement -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 small">User Engagement</h6>
                                    <span class="badge bg-success">{{ $performanceMetrics['engagement_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $performanceMetrics['engagement_rate'] }}%"></div>
                                </div>
                            </div>

                            <!-- Quality Score -->
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 small">Quality Score</h6>
                                    <span class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                        {{ $performanceMetrics['quality_score'] }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                        style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Verification -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>Documents
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light">
                                        <h5 class="text-primary mb-0">{{ $documentAnalysis['location_doc_rate'] }}%</h5>
                                        <small class="text-muted">Location</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light">
                                        <h5 class="text-success mb-0">{{ $documentAnalysis['id_front_rate'] }}%</h5>
                                        <small class="text-muted">ID Front</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light">
                                        <h5 class="text-warning mb-0">{{ $documentAnalysis['id_back_rate'] }}%</h5>
                                        <small class="text-muted">ID Back</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Complete Documentation</span>
                                    <span class="badge bg-success">{{ $documentAnalysis['with_all_documents'] }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $documentAnalysis['complete_docs_rate'] }}%"></div>
                                </div>
                            </div>
                            <div class="alert alert-info py-2 mb-0">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Complete docs: <strong>{{ $documentAnalysis['approval_rate_with_docs'] }}%</strong> approval vs <strong>{{ $documentAnalysis['approval_rate_without_docs'] }}%</strong> without
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Verification -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope me-2"></i>Email Status
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-success mb-1">{{ $emailVerificationAnalysis['verified'] }}</h4>
                                        <p class="mb-0 small text-muted">Verified</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded bg-light">
                                        <h4 class="text-warning mb-1">{{ $emailVerificationAnalysis['unverified'] }}</h4>
                                        <p class="mb-0 small text-muted">Unverified</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small">Verification Rate</span>
                                    <span class="badge bg-success">{{ $emailVerificationAnalysis['verification_rate'] }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ $emailVerificationAnalysis['verification_rate'] }}%"></div>
                                </div>
                            </div>
                            <div class="alert alert-success py-2 mb-0">
                                <small>
                                    <i class="fas fa-check-circle me-1"></i>
                                    Verified: <strong>{{ $emailVerificationAnalysis['approval_rate_verified'] }}%</strong> approval vs <strong>{{ $emailVerificationAnalysis['approval_rate_unverified'] }}%</strong> unverified
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Barangays & Registration Patterns -->
            <div class="row mb-4">
                <!-- Top Performing Barangays -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top Performing Barangays
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">Rank</th>
                                            <th>Barangay</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Approved</th>
                                            <th style="width: 120px;">Approval Rate</th>
                                            <th class="text-center">Verified</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-{{ $index < 3 ? ($index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info')) : 'light text-dark' }}">
                                                        #{{ $index + 1 }}
                                                    </span>
                                                </td>
                                                <td><strong>{{ $barangay->barangay }}</strong></td>
                                                <td class="text-center">{{ $barangay->total_registrations }}</td>
                                                <td class="text-center">{{ $barangay->approved }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2 flex-grow-1" style="height: 6px;">
                                                            <div class="progress-bar bg-success"
                                                                style="width: {{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%">
                                                            </div>
                                                        </div>
                                                        <small style="min-width: 40px;">{{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $barangay->email_verified }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Patterns - Compact -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Weekly Pattern
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="userRegDayOfWeekChart" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            @if($referralAnalysis->count() > 0)
            <!-- Referral Source Analysis -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-share-alt me-2"></i>Referral Sources
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($referralAnalysis->take(6) as $index => $referral)
                                    <div class="col-md-4 col-lg-2 mb-3">
                                        <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(6, 182, 212, 0.05) 100%);">
                                            <div class="badge bg-info rounded-pill mb-2">{{ $index + 1 }}</div>
                                            <h6 class="mb-1">{{ ucfirst($referral->referral_source) }}</h6>
                                            <small class="text-muted d-block">{{ $referral->total_registrations }} users</small>
                                            <small class="text-info">{{ $referral->approved }} approved</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- AI Insights Modal -->
    <div class="modal fade" id="userRegInsightsModal" tabindex="-1" aria-labelledby="userRegInsightsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info text-white">
                    <h5 class="modal-title" id="userRegInsightsModalLabel">
                        <i class="fas fa-lightbulb me-2"></i>User Registration Analytics AI Insights
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-chart-line me-2"></i>Key Performance Insights</h6>
                        <ul class="mb-0">
                            <li><strong>Approval Efficiency:</strong> Current approval rate of {{ $overview['approval_rate'] }}%
                                {{ $overview['approval_rate'] > 80 ? 'indicates excellent verification processing' : ($overview['approval_rate'] > 60 ? 'shows good performance with room for improvement' : 'suggests need for process optimization') }}
                            </li>
                            <li><strong>Email Verification:</strong> {{ $overview['email_verification_rate'] }}% verification rate
                                {{ $overview['email_verification_rate'] > 80 ? 'demonstrates strong user engagement' : 'indicates opportunity for improved email verification campaigns' }}
                            </li>
                            <li><strong>Document Compliance:</strong> {{ $overview['document_completion_rate'] }}% document completion rate
                                {{ $overview['document_completion_rate'] > 70 ? 'shows excellent user compliance' : 'suggests need for better onboarding guidance' }}
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-success">
                        <h6><i class="fas fa-users me-2"></i>User Engagement Trends</h6>
                        <ul class="mb-0">
                            <li><strong>Geographic Coverage:</strong> {{ $overview['active_barangays'] }} barangays represented shows
                                {{ $overview['active_barangays'] > 20 ? 'excellent geographic reach' : 'good coverage with expansion opportunities' }}
                            </li>
                            <li><strong>User Diversity:</strong> {{ $overview['user_types'] }} different user types registered demonstrates
                                {{ $overview['user_types'] > 3 ? 'diverse platform adoption' : 'focused user base' }}
                            </li>
                            <li><strong>Processing Speed:</strong> Average {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time
                                {{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'shows exceptional efficiency' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'indicates good workflow management' : 'suggests workflow optimization needed') }}
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Recommendations</h6>
                        <ul class="mb-0">
                            @if ($overview['email_verification_rate'] < 70)
                                <li><strong>Email Verification:</strong> Implement automated reminder emails to improve {{ $overview['email_verification_rate'] }}% verification rate</li>
                            @endif
                            @if ($overview['document_completion_rate'] < 60)
                                <li><strong>Documentation:</strong> Consider step-by-step guidance to improve {{ $overview['document_completion_rate'] }}% document completion rate</li>
                            @endif
                            @if ($processingTimeAnalysis['avg_processing_days'] > 7)
                                <li><strong>Processing Time:</strong> Average {{ $processingTimeAnalysis['avg_processing_days'] }} days processing time could be improved through workflow automation</li>
                            @endif
                            <li><strong>User Support:</strong> Focus on underperforming barangays to improve overall platform adoption</li>
                            <li><strong>Quality Assurance:</strong> Document submission improves approval rates by {{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}% - emphasize this to users</li>
                        </ul>
                    </div>

                    <div class="alert alert-primary">
                        <h6><i class="fas fa-target me-2"></i>Action Items</h6>
                        <ul class="mb-0">
                            <li>Send targeted email campaigns to {{ $emailVerificationAnalysis['unverified'] }} unverified users</li>
                            <li>Create tutorial content for document submission process</li>
                            <li>Review and optimize verification workflows for pending registrations</li>
                            <li>Analyze high-performing barangays for best practices replication</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('admin.analytics.user-registration.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
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
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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

        .text-pink {
            color: #ec4899 !important;
        }

        .card {
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .badge {
            font-size: 0.75em;
            font-weight: 600;
        }

        .nav-pills .nav-link {
            border-radius: 25px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .nav-pills .nav-link.active {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .card-header h5 {
            font-weight: 600;
            font-size: 1rem;
        }

        .progress {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            border-radius: 10px;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.5rem;
            vertical-align: middle;
        }

        .table thead th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            font-size: 0.875rem;
        }

        canvas {
            max-height: 350px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .modal-content {
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .col-lg-3,
            .col-lg-4,
            .col-lg-6,
            .col-lg-8 {
                margin-bottom: 1rem;
            }
            
            .nav-pills {
                flex-wrap: wrap;
            }
            
            .nav-pills .nav-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Common chart options
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            };

            // Monthly Trends Chart - Primary Focus
            const trendsCtx = document.getElementById('userRegTrendsChart').getContext('2d');
            new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach ($monthlyTrends as $trend)
                            '{{ date('M Y', strtotime($trend->month . '-01')) }}',
                        @endforeach
                    ],
                    datasets: [{
                            label: 'Total Registrations',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->total_registrations }},
                                @endforeach
                            ],
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2
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
                            borderWidth: 2
                        },
                        {
                            label: 'Email Verified',
                            data: [
                                @foreach ($monthlyTrends as $trend)
                                    {{ $trend->email_verified }},
                                @endforeach
                            ],
                            borderColor: '#06b6d4',
                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                            tension: 0.4,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Status Distribution - Compact Pie
            const statusCtx = document.getElementById('userRegStatusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            '{{ ucfirst($status) }}',
                        @endforeach
                    ],
                    datasets: [{
                        data: [
                            @foreach ($statusAnalysis['counts'] as $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: ['#6b7280', '#f59e0b', '#10b981', '#ef4444', '#1f2937'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // User Type Horizontal Bar Chart
            const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
            new Chart(userTypeCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($userTypeAnalysis->take(5) as $userType)
                            '{{ ucfirst($userType->user_type) }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Total Users',
                        data: [
                            @foreach ($userTypeAnalysis->take(5) as $userType)
                                {{ $userType->total_registrations }},
                            @endforeach
                        ],
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    }, {
                        label: 'Verified',
                        data: [
                            @foreach ($userTypeAnalysis->take(5) as $userType)
                                {{ $userType->email_verified }},
                            @endforeach
                        ],
                        backgroundColor: '#06b6d4',
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    ...commonOptions,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Age Distribution Chart - Compact
            const ageCtx = document.getElementById('userRegAgeChart').getContext('2d');
            new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($ageAnalysis as $age)
                            '{{ $age->age_range }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Users',
                        data: [
                            @foreach ($ageAnalysis as $age)
                                {{ $age->user_count }},
                            @endforeach
                        ],
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });

            // Day of Week Chart - Compact
            const dayOfWeekCtx = document.getElementById('userRegDayOfWeekChart').getContext('2d');
            new Chart(dayOfWeekCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach ($registrationPatterns['day_of_week'] as $day)
                            '{{ substr($day->day_name, 0, 3) }}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Registrations',
                        data: [
                            @foreach ($registrationPatterns['day_of_week'] as $day)
                                {{ $day->registrations_count }},
                            @endforeach
                        ],
                        backgroundColor: '#6366f1',
                        borderRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
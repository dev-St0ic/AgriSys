{{-- resources/views/admin/analytics/user-registration.blade.php --}}

@extends('layouts.app')

@section('title', 'User Registration Analytics - AgriSys Admin')
@section('page-title')
    <i class="fas fa-chart-bar me-2"></i>User Registration Analytics Dashboard
@endsection

@section('content')
    <!-- Enhanced Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 navigation-container">
                <div class="card-body py-4 px-4">
                    <div class="d-flex justify-content-center flex-wrap gap-3 align-items-center">
                        <a href="{{ route('admin.analytics.seedlings') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <span class="nav-label">Seedlings</span>
                        </a>
                        <a href="{{ route('admin.analytics.rsbsa') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <span class="nav-label">RSBSA</span>
                        </a>
                        <a href="{{ route('admin.analytics.fishr') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-fish"></i>
                            </div>
                            <span class="nav-label">FISHR</span>
                        </a>
                        <a href="{{ route('admin.analytics.boatr') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-ship"></i>
                            </div>
                            <span class="nav-label">BOATR</span>
                        </a>
                        <a href="{{ route('admin.analytics.training') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <span class="nav-label">Training</span>
                        </a>
                        <a href="{{ route('admin.analytics.supply-management') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <span class="nav-label">Supply Management</span>
                        </a>
                        <a href="{{ route('admin.analytics.user-registration') }}"
                            class="analytics-nav-btn {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                            <div class="nav-icon-wrapper">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span class="nav-label">User Registration</span>
                        </a>
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
                    <form method="GET" action="{{ route('admin.analytics.user-registration') }}"
                        class="row g-3 align-items-end">
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
                                <a href="{{ route('admin.analytics.user-registration.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
                                    class="btn btn-success px-4">
                                    <i class="fas fa-download me-2"></i>Export Data
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_registrations']) }}</h2>
                    <h6 class="text-muted mb-2">Total Registrations</h6>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $overview['active_barangays'] }} barangays
                    </small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['approval_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Approval Rate</h6>
                    <small class="text-muted">{{ number_format($overview['approved_registrations']) }} approved</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-envelope-circle-check fa-2x text-info"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['email_verification_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Email Verification</h6>
                    <small class="text-muted">{{ number_format($overview['email_verified']) }} verified</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-file-circle-check fa-2x text-warning"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['document_completion_rate'] }}%</h2>
                    <h6 class="text-muted mb-2">Document Completion</h6>
                    <small class="text-muted">{{ number_format($overview['with_all_documents']) }} complete</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="row mb-4 g-3">
        <!-- Status Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="status-chart-container mb-3">
                        <canvas id="userRegStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <span class="d-flex align-items-center">
                                    <span class="legend-dot me-2"
                                        style="background-color: {{ $status === 'approved' ? '#10b981' : ($status === 'rejected' ? '#ef4444' : ($status === 'pending' ? '#f59e0b' : ($status === 'unverified' ? '#6b7280' : '#1f2937'))) }};"></span>
                                    {{ ucfirst($status) }}
                                </span>
                                <div>
                                    <span class="badge"
                                        style="background-color: {{ $status === 'approved' ? '#10b981' : ($status === 'rejected' ? '#ef4444' : ($status === 'pending' ? '#f59e0b' : ($status === 'unverified' ? '#6b7280' : '#1f2937'))) }};">
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

        <!-- Registration Trends -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-info me-2"></i>Registration Trends Over Time
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userRegTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance & Critical Metrics -->
    <div class="row mb-4 g-3">
        <!-- Performance Indicators -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-tachometer-alt me-2 text-success"></i>Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Processing Time -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">Processing Time</span>
                            <span
                                class="badge bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}">
                                {{ $processingTimeAnalysis['avg_processing_days'] }}d avg
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $processingTimeAnalysis['avg_processing_days'] < 3 ? 'success' : ($processingTimeAnalysis['avg_processing_days'] < 7 ? 'warning' : 'danger') }}"
                                style="width: {{ min(100, ((14 - $processingTimeAnalysis['avg_processing_days']) / 14) * 100) }}%">
                            </div>
                        </div>
                        <small class="text-muted">Target: < 7 days</small>
                    </div>

                    <!-- Quality Score -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">Quality Score</span>
                            <span
                                class="badge bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}">
                                {{ $performanceMetrics['quality_score'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $performanceMetrics['quality_score'] > 80 ? 'success' : ($performanceMetrics['quality_score'] > 60 ? 'warning' : 'danger') }}"
                                style="width: {{ $performanceMetrics['quality_score'] }}%"></div>
                        </div>
                        <small class="text-muted">Based on docs, email, approval rates</small>
                    </div>

                    <!-- User Engagement -->
                    <div class="metric-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold">User Engagement</span>
                            <span class="badge bg-info">{{ $performanceMetrics['engagement_rate'] }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info"
                                style="width: {{ $performanceMetrics['engagement_rate'] }}%"></div>
                        </div>
                        <small class="text-muted">Document completion rate</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document & Email Analysis -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-alt me-2 text-warning"></i>Verification Status
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Document Stats -->
                    <div class="verification-grid mb-3">
                        <div class="verification-item">
                            <div class="verification-value text-primary">{{ $documentAnalysis['location_doc_rate'] }}%
                            </div>
                            <div class="verification-label">Location Doc</div>
                        </div>
                        <div class="verification-item">
                            <div class="verification-value text-success">{{ $documentAnalysis['id_front_rate'] }}%</div>
                            <div class="verification-label">ID Front</div>
                        </div>
                        <div class="verification-item">
                            <div class="verification-value text-warning">{{ $documentAnalysis['id_back_rate'] }}%</div>
                            <div class="verification-label">ID Back</div>
                        </div>
                    </div>

                    <!-- Complete Documentation -->
                    <div class="alert alert-info mb-3 p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="fas fa-check-double me-1"></i>Complete Documents</small>
                            <span class="badge bg-info">{{ $documentAnalysis['with_all_documents'] }} users</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-info"
                                style="width: {{ $documentAnalysis['complete_docs_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Email Verification -->
                    <div class="alert alert-success mb-0 p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small><i class="fas fa-envelope-check me-1"></i>Email Verified</small>
                            <span class="badge bg-success">{{ $emailVerificationAnalysis['verified'] }} users</span>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success"
                                style="width: {{ $emailVerificationAnalysis['verification_rate'] }}%"></div>
                        </div>
                    </div>

                    <!-- Impact Info -->
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <strong>Impact:</strong> Complete docs improve approval by
                            <strong
                                class="text-success">+{{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Type Distribution -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-users me-2 text-info"></i>Top User Types
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($userTypeAnalysis->take(6) as $index => $userType)
                        @php
                            $totalUsers = $userTypeAnalysis->sum('total_registrations');
                            $percentage =
                                $totalUsers > 0 ? round(($userType->total_registrations / $totalUsers) * 100, 1) : 0;
                            $approvalRate =
                                $userType->total_registrations > 0
                                    ? round((($userType->approved_count ?? 0) / $userType->total_registrations) * 100)
                                    : 0;
                        @endphp
                        <div class="user-type-item mb-3 p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1 fw-bold">{{ ucfirst(str_replace('_', ' ', $userType->user_type)) }}
                                    </h6>
                                    <small class="text-muted">Approval: {{ $approvalRate }}%</small>
                                </div>
                                <span
                                    class="badge bg-info text-white px-2 py-1">{{ $userType->total_registrations }}</span>
                            </div>

                            <!-- Progress Bar for User Type Distribution -->
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"
                                    title="{{ $percentage }}% of total users"></div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $percentage }}% of total registrations</small>
                                @if (isset($userType->avg_age))
                                    <small class="text-muted">Avg: {{ round($userType->avg_age, 1) }} years</small>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Top Barangays -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-trophy me-2 text-dark"></i>Top Performing Barangays
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 80px;">Rank</th>
                                    <th>Barangay</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Approved</th>
                                    <th class="text-center">Approval Rate</th>
                                    <th class="text-center">Verified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangayAnalysis->take(10) as $index => $barangay)
                                    <tr>
                                        <td class="text-center">
                                            @if ($index < 3)
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
                                            <span
                                                class="badge bg-primary text-white px-2 py-1">{{ $barangay->total_registrations }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-success text-white px-2 py-1">{{ $barangay->approved }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="progress me-2" style="width: 60px; height: 6px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small
                                                    class="fw-semibold">{{ round(($barangay->approved / max(1, $barangay->total_registrations)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-info text-white px-2 py-1">{{ $barangay->email_verified }}</span>
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


    <div class="modal fade" id="userRegInsightsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
            @endsection

            @section('styles')
                <style>
                    /* Compact Navigation Container */
                    .navigation-container {
                        background: #f8f9fa;
                        border-radius: 12px;
                        border: 1px solid #e0e0e0;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                    }

                    /* Compact Horizontal Navigation Buttons */
                    .analytics-nav-btn {
                        background: #ffffff;
                        border: 2px solid #e0e0e0;
                        color: #495057;
                        font-weight: 600;
                        font-size: 0.875rem;
                        padding: 0.6rem 1.2rem;
                        border-radius: 8px;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        white-space: nowrap;
                        position: relative;
                        overflow: hidden;
                        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                        display: inline-flex;
                        flex-direction: row;
                        align-items: center;
                        gap: 8px;
                    }

                    .nav-icon-wrapper {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .nav-icon-wrapper i {
                        font-size: 1rem;
                        transition: all 0.2s ease;
                        color: #6c757d;
                    }

                    .nav-label {
                        font-size: 0.875rem;
                        font-weight: 600;
                    }

                    .analytics-nav-btn:hover {
                        background: #e8f5e9;
                        border-color: #40916c;
                        color: #2d6a4f;
                        text-decoration: none;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(64, 145, 108, 0.2);
                    }

                    .analytics-nav-btn:hover .nav-icon-wrapper i {
                        color: #40916c;
                    }

                    .analytics-nav-btn.active {
                        background: linear-gradient(135deg, #40916c 0%, #52b788 100%);
                        border-color: #40916c;
                        color: white;
                        box-shadow: 0 3px 10px rgba(64, 145, 108, 0.3);
                    }

                    .analytics-nav-btn.active .nav-icon-wrapper i {
                        color: #ffffff;
                    }

                    .analytics-nav-btn.active:hover {
                        background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);
                        border-color: #2d6a4f;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 14px rgba(64, 145, 108, 0.35);
                    }

                    /* Responsive adjustments */
                    @media (max-width: 992px) {
                        .analytics-nav-btn {
                            padding: 0.5rem 1rem;
                            font-size: 0.8rem;
                        }

                        .nav-icon-wrapper i {
                            font-size: 0.9rem;
                        }

                        .nav-label {
                            font-size: 0.8rem;
                        }
                    }

                    @media (max-width: 768px) {
                        .analytics-nav-btn {
                            padding: 0.45rem 0.8rem;
                            font-size: 0.75rem;
                            gap: 6px;
                        }

                        .nav-icon-wrapper i {
                            font-size: 0.85rem;
                        }

                        .nav-label {
                            font-size: 0.75rem;
                        }
                    }

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

                    /* Metric Cards */
                    .metric-card {
                        transition: transform 0.3s ease, box-shadow 0.3s ease;
                        border-radius: 10px;
                        cursor: pointer;
                    }

                    .metric-card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                    }

                    /* Service Navigation */
                    .nav-pills .nav-link {
                        border-radius: 20px;
                        padding: 0.5rem 1.25rem;
                        margin: 0 0.25rem;
                        transition: all 0.3s ease;
                        font-weight: 500;
                        font-size: 0.9rem;
                        color: #495057;
                    }

                    .nav-pills .nav-link:hover {
                        background-color: rgba(255, 255, 255, 0.8);
                        color: #007bff;
                        transform: translateY(-2px);
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    }

                    .nav-pills .nav-link.active {
                        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
                        color: white !important;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.5) !important;
                    }
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
                        margin-bottom: 1rem;
                    }

                    .metric-item:hover {
                        background: #f1f5f9;
                        transform: scale(1.02);
                    }

                    .metric-item:last-child {
                        margin-bottom: 0;
                    }

                    /* User Type Items */
                    .user-type-item {
                        transition: all 0.3s ease;
                        border-left: 3px solid transparent;
                        border-radius: 8px !important;
                    }

                    .user-type-item:hover {
                        border-left-color: var(--info-color);
                        transform: translateX(5px);
                        background: #f1f5f9 !important;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    }

                    .user-type-item:last-child {
                        margin-bottom: 0 !important;
                    }

                    .user-type-item .progress {
                        height: 8px !important;
                        border-radius: 6px;
                        background-color: #e2e8f0;
                    }

                    .user-type-item .progress-bar {
                        border-radius: 6px;
                        background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
                    }

                    /* Verification Grid */
                    .verification-grid {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 0.75rem;
                    }

                    .verification-item {
                        text-align: center;
                        padding: 0.75rem;
                        background: #f8fafc;
                        border-radius: 8px;
                        transition: all 0.3s ease;
                    }

                    .verification-item:hover {
                        background: #f1f5f9;
                        transform: scale(1.05);
                    }

                    .verification-value {
                        font-size: 1.5rem;
                        font-weight: 700;
                        margin-bottom: 0.25rem;
                    }

                    .verification-label {
                        font-size: 0.75rem;
                        color: #6b7280;
                    }

                    /* Demographics Cards */
                    .demographic-card {
                        transition: transform 0.2s;
                    }

                    .demographic-card:hover {
                        transform: scale(1.05);
                    }

                    .demographic-male {
                        background: rgba(59, 130, 246, 0.1);
                        border: 2px solid rgba(59, 130, 246, 0.3);
                    }

                    .demographic-female {
                        background: rgba(236, 72, 153, 0.1);
                        border: 2px solid rgba(236, 72, 153, 0.3);
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

                    .table thead th {
                        font-weight: 600;
                        font-size: 0.875rem;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                        color: #374151;
                        border-bottom: 2px solid #e5e7eb;
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

                    .rank-badge:not(.rank-1):not(.rank-2):not(.rank-3) {
                        background: #e5e7eb;
                        color: #374151;
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

                    /* Modal Styles */
                    .modal-content {
                        border-radius: 16px;
                    }

                    .modal-header {
                        border-radius: 16px 16px 0 0;
                        border-bottom: none;
                    }

                    /* Alert */
                    .alert {
                        border-radius: 10px;
                        padding: 1rem 1.25rem;
                    }

                    /* Chart Containers */
                    .status-chart-container {
                        position: relative;
                        height: 220px;
                    }

                    /* Form Controls */
                    .form-control {
                        border-radius: 8px;
                        border: 1px solid #dee2e6;
                        padding: 0.6rem 1rem;
                        transition: all 0.3s ease;
                    }

                    .form-control:focus {
                        border-color: #3b82f6;
                        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
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
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    }

                    .btn-primary {
                        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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

                        .verification-grid {
                            grid-template-columns: 1fr;
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
                            const ctx = document.getElementById('userRegStatusChart');
                            if (!ctx) return;

                            chartInstances.statusChart = new Chart(ctx.getContext('2d'), {
                                type: 'doughnut',
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
                                        backgroundColor: [
                                            @foreach ($statusAnalysis['counts'] as $status => $count)
                                                '{{ $status === 'approved' ? '#10b981' : ($status === 'rejected' ? '#ef4444' : ($status === 'pending' ? '#f59e0b' : ($status === 'unverified' ? '#6b7280' : '#1f2937'))) }}',
                                            @endforeach
                                        ],
                                        borderColor: '#ffffff',
                                        borderWidth: 3,
                                        cutout: '60%',
                                        spacing: 4
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
                                            backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                            titleColor: 'white',
                                            bodyColor: 'white',
                                            borderColor: 'rgba(255, 255, 255, 0.1)',
                                            borderWidth: 1,
                                            cornerRadius: 8,
                                            padding: 12,
                                            callbacks: {
                                                label: function(context) {
                                                    const label = context.label || '';
                                                    const value = context.parsed;
                                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                    const percentage = ((value / total) * 100).toFixed(1);
                                                    return `${label}: ${value.toLocaleString()} (${percentage}%)`;
                                                }
                                            }
                                        }
                                    },
                                    animation: {
                                        duration: 1000,
                                        easing: 'easeInOutQuart'
                                    }
                                },
                                plugins: [{
                                    id: 'centerText',
                                    beforeDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        ctx.save();

                                        const centerX = chart.width / 2;
                                        const centerY = chart.height / 2;
                                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b,
                                            0);

                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';
                                        ctx.fillStyle = '#1f2937';
                                        ctx.font = 'bold 28px Inter, sans-serif';
                                        ctx.fillText(total.toLocaleString(), centerX, centerY - 10);

                                        ctx.fillStyle = '#64748b';
                                        ctx.font = '14px Inter, sans-serif';
                                        ctx.fillText('Total Registrations', centerX, centerY + 15);
                                        ctx.restore();
                                    },
                                    afterDraw: function(chart) {
                                        const ctx = chart.ctx;
                                        const meta = chart.getDatasetMeta(0);
                                        const total = chart.data.datasets[0].data.reduce((a, b) => a + b,
                                            0);

                                        ctx.save();
                                        ctx.font = 'bold 14px Inter, sans-serif';
                                        ctx.textAlign = 'center';
                                        ctx.textBaseline = 'middle';

                                        chart.data.datasets[0].data.forEach((value, index) => {
                                            if (value > 0) {
                                                const percentage = ((value / total) * 100).toFixed(
                                                    1);

                                                // Show percentage for all segments with data
                                                const element = meta.data[index];

                                                // Calculate the middle angle of the segment
                                                const startAngle = element.startAngle;
                                                const endAngle = element.endAngle;
                                                const midAngle = (startAngle + endAngle) / 2;

                                                // Calculate position based on the segment's center point
                                                const chartArea = chart.chartArea;
                                                const centerX = (chartArea.left + chartArea.right) /
                                                    2;
                                                const centerY = (chartArea.top + chartArea.bottom) /
                                                    2;

                                                // Position the text at 70% of the radius from center
                                                const radius = (element.outerRadius - element
                                                    .innerRadius) * 0.7 + element.innerRadius;
                                                const x = centerX + Math.cos(midAngle) * radius;
                                                const y = centerY + Math.sin(midAngle) * radius;

                                                const text = `${percentage}%`;

                                                ctx.fillStyle = '#ffffff';
                                                ctx.strokeStyle = '#000000';
                                                ctx.lineWidth = 3;
                                                ctx.strokeText(text, x, y);
                                                ctx.fillText(text, x, y);
                                            }
                                        });

                                        ctx.restore();
                                    }
                                }]
                            });
                        }

                        /**
                         * Registration Trends Line Chart
                         */
                        function initializeTrendsChart() {
                            const ctx = document.getElementById('userRegTrendsChart');
                            if (!ctx) return;

                            chartInstances.trendsChart = new Chart(ctx.getContext('2d'), {
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
                                            borderColor: '#3b82f6',
                                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                            borderWidth: 3,
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
                        const filterForm = document.querySelector('form[action*="analytics.user-registration"]');
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

                        /**
                         * Export button loading state
                         */
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
                    });
                </script>
            @endsection

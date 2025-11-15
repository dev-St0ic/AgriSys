{{-- resources/views/admin/analytics/training.blade.php --}}

@extends('layouts.app')

@section('title', 'Training Analytics - AgriSys Admin')
@section('page-title')
    <i class="fas fa-chart-bar me-2"></i>Training Analytics Dashboard
@endsection

@section('content')
    <!-- Service Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 navigation-container">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-center flex-wrap gap-2">
                        <a href="{{ route('admin.analytics.seedlings') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.seedlings') ? 'active' : '' }}">
                            <i class="fas fa-seedling me-2"></i>Seedlings
                        </a>
                        <a href="{{ route('admin.analytics.rsbsa') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.rsbsa') ? 'active' : '' }}">
                            <i class="fas fa-user-check me-2"></i>RSBSA
                        </a>
                        <a href="{{ route('admin.analytics.fishr') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.fishr') ? 'active' : '' }}">
                            <i class="fas fa-fish me-2"></i>FISHR
                        </a>
                        <a href="{{ route('admin.analytics.boatr') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.boatr') ? 'active' : '' }}">
                            <i class="fas fa-ship me-2"></i>BOATR
                        </a>
                        <a href="{{ route('admin.analytics.training') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.training') ? 'active' : '' }}">
                            <i class="fas fa-graduation-cap me-2"></i>Training
                        </a>
                        <a href="{{ route('admin.analytics.supply-management') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.supply-management') ? 'active' : '' }}">
                            <i class="fas fa-boxes me-2"></i>Supply Management
                        </a>
                        <a href="{{ route('admin.analytics.user-registration') }}"
                            class="btn analytics-nav-btn {{ request()->routeIs('admin.analytics.user-registration') ? 'active' : '' }}">
                            <i class="fas fa-user-plus me-2"></i>User Registration
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
                    <form method="GET" action="{{ route('admin.analytics.training') }}" class="row g-3 align-items-end">
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
                                <a href="{{ route('admin.analytics.training.export') }}?start_date={{ $startDate }}&end_date={{ $endDate }}"
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
                        <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ number_format($overview['total_applications']) }}</h2>
                    <h6 class="text-muted mb-2">Total Applications</h6>
                    <small class="text-muted">All time registrations</small>
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
                    <small class="text-muted">{{ number_format($overview['approved_applications']) }} approved</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-book fa-2x text-info"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $overview['unique_training_types'] }}</h2>
                    <h6 class="text-muted mb-2">Training Programs</h6>
                    <small class="text-muted">{{ number_format($overview['unique_applicants']) }} unique trainees</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100 metric-card">
                <div class="card-body text-center p-4">
                    <div class="metric-icon mb-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h2 class="text-dark mb-1">{{ $processingTimeAnalysis['avg_processing_days'] }}d</h2>
                    <h6 class="text-muted mb-2">Avg. Processing Time</h6>
                    <small class="text-muted">Median: {{ $processingTimeAnalysis['median_processing_days'] }}d</small>
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
                        <canvas id="trainingStatusChart" height="220"></canvas>
                    </div>
                    <div class="status-legends">
                        @foreach ($statusAnalysis['counts'] as $status => $count)
                            @php
                                $dotColor = match ($status) {
                                    'approved' => '#10b981',
                                    'rejected' => '#ef4444',
                                    'under_review', 'pending' => '#f59e0b',
                                    default => '#64748b',
                                };
                            @endphp
                            <div class="legend-item d-flex justify-content-between align-items-center mb-2 p-2 rounded">
                                <div class="d-flex align-items-center">
                                    <span class="fw-medium">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge text-white me-2" style="background-color: {{ $dotColor }};">
                                        {{ $count }}
                                    </span>
                                    <span
                                        class="text-muted fw-semibold">{{ $statusAnalysis['percentages'][$status] }}%</span>
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
                    <canvas id="trainingTrendsChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Training Programs Performance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
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
                                @foreach ($trainingTypeAnalysis->take(10) as $index => $training)
                                    <tr>
                                        <td>
                                            <div class="rank-badge rank-{{ $index + 1 }}">
                                                @if ($index === 0)
                                                    <i class="fas fa-crown"></i>
                                                @elseif($index === 1)
                                                    <i class="fas fa-medal"></i>
                                                @elseif($index === 2)
                                                    <i class="fas fa-award"></i>
                                                @endif
                                                <span>{{ $index + 1 }}</span>
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
                                                        style="width: {{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%">
                                                    </div>
                                                </div>
                                                <small
                                                    class="fw-semibold">{{ round(($training->approved / max(1, $training->total_applications)) * 100, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-{{ $training->avg_processing_days < 3 ? 'success' : ($training->avg_processing_days < 7 ? 'warning' : 'danger') }}">
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
    <div class="row mb-4 g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-file-alt me-2 text-warning"></i>Document Impact
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="metric-item mb-3">
                                <h3 class="text-success mb-1">{{ $documentAnalysis['approval_rate_with_docs'] }}%</h3>
                                <p class="mb-0 small text-muted">With Documents</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-item mb-3">
                                <h3 class="text-warning mb-1">{{ $documentAnalysis['approval_rate_without_docs'] }}%</h3>
                                <p class="mb-0 small text-muted">Without Documents</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info-soft border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>{{ $documentAnalysis['approval_rate_with_docs'] - $documentAnalysis['approval_rate_without_docs'] }}%</strong>
                        higher approval with documents
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-address-book me-2 text-info"></i>Contact Methods
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="metric-item">
                                <h4 class="text-success mb-1">{{ $contactAnalysis['stats']['both_contacts'] }}</h4>
                                <p class="mb-0 small text-muted">Both Contacts</p>
                                <small
                                    class="text-success fw-semibold">{{ $contactAnalysis['percentages']['both_contacts'] }}%</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-item">
                                <h4 class="text-primary mb-1">{{ $contactAnalysis['stats']['email_only'] }}</h4>
                                <p class="mb-0 small text-muted">Email Only</p>
                                <small
                                    class="text-primary fw-semibold">{{ $contactAnalysis['percentages']['email_only'] }}%</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-item">
                                <h4 class="text-warning mb-1">{{ $contactAnalysis['stats']['mobile_only'] }}</h4>
                                <p class="mb-0 small text-muted">Mobile Only</p>
                                <small
                                    class="text-warning fw-semibold">{{ $contactAnalysis['percentages']['mobile_only'] }}%</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="metric-item">
                                <h4 class="text-danger mb-1">{{ $contactAnalysis['stats']['no_contact'] }}</h4>
                                <p class="mb-0 small text-muted">No Contact</p>
                                <small
                                    class="text-danger fw-semibold">{{ $contactAnalysis['percentages']['no_contact'] }}%</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>Peak Registration Days
                    </h5>
                </div>
                <div class="card-body">
                    @foreach ($registrationPatterns['day_of_week'] as $day)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-semibold">{{ $day->day_name }}</span>
                            <div class="d-flex align-items-center">
                                <div class="progress-bar-container me-2" style="width: 80px;">
                                    <div class="progress-bar-fill bg-primary"
                                        style="width: {{ $registrationPatterns['day_of_week']->max('applications_count') > 0 ? ($day->applications_count / $registrationPatterns['day_of_week']->max('applications_count')) * 100 : 0 }}%">
                                    </div>
                                </div>
                                <span class="badge bg-primary">{{ $day->applications_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Navigation Card */
        .navigation-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            overflow-x: auto;
        }

        /* Enhanced Analytics Navigation */
        .analytics-nav {
            gap: 0.5rem;
            min-height: 50px;
        }

        .analytics-nav .nav-item {
            flex-shrink: 0;
        }

        .analytics-nav .nav-link {
            border-radius: 12px;
            padding: 0.65rem 1.5rem;
            margin: 0.25rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            font-size: 0.9rem;
            color: #64748b;
            background: transparent;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .analytics-nav .nav-link i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .analytics-nav .nav-link span {
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .analytics-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .analytics-nav .nav-link:hover {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.08);
            border-color: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .analytics-nav .nav-link:hover::before {
            opacity: 1;
        }

        .analytics-nav .nav-link:hover i {
            transform: scale(1.15) rotate(5deg);
        }

        .analytics-nav .nav-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4), 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .analytics-nav .nav-link.active i {
            transform: scale(1.1);
        }

        .analytics-nav .nav-link.active:hover {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5), 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .analytics-nav .nav-link {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 768px) {
            .analytics-nav {
                justify-content: flex-start !important;
            }

            .analytics-nav .nav-link {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }

            .analytics-nav .nav-link i {
                font-size: 1rem;
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

        /* Legend Badge Styling */
        .status-legends .badge {
            min-width: 40px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .status-legends .badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Progress Bars */
        .progress-bar-container {
            width: 80px;
            height: 8px;
            background: #f1f5f9;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
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
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
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
                const ctx = document.getElementById('trainingStatusChart');
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
                                '#10b981',
                                '#ef4444',
                                '#f59e0b'
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            cutout: '65%',
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
                            },
                            // Custom plugin to display percentages inside the doughnut
                            datalabels: false
                        },
                        animation: {
                            animateRotate: true,
                            duration: 1000
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw: function(chart) {
                            const ctx = chart.ctx;
                            const chartArea = chart.chartArea;
                            const centerX = (chartArea.left + chartArea.right) / 2;
                            const centerY = (chartArea.top + chartArea.bottom) / 2;

                            // Get the total
                            const total = chart.data.datasets[0].data.reduce((a, b) => a + b,
                                0);

                            // Draw center text
                            ctx.save();
                            ctx.font = 'bold 24px Inter';
                            ctx.fillStyle = '#1f2937';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(total.toLocaleString(), centerX, centerY - 10);

                            ctx.font = '14px Inter';
                            ctx.fillStyle = '#64748b';
                            ctx.fillText('Total Applications', centerX, centerY + 15);
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

                                    // Only show percentage if slice is large enough
                                    if (percentage > 5) {
                                        const element = meta.data[index];

                                        // Calculate the middle angle of the segment
                                        const startAngle = element.startAngle;
                                        const endAngle = element.endAngle;
                                        const midAngle = (startAngle + endAngle) / 2;

                                        // Calculate position based on the segment's center point
                                        const chartArea = chart.chartArea;
                                        const centerX = (chartArea.left + chartArea
                                            .right) / 2;
                                        const centerY = (chartArea.top + chartArea
                                            .bottom) / 2;

                                        // Position the text at 70% of the radius from center
                                        const radius = (element.outerRadius - element
                                                .innerRadius) * 0.7 + element
                                            .innerRadius;
                                        const x = centerX + Math.cos(midAngle) * radius;
                                        const y = centerY + Math.sin(midAngle) * radius;

                                        const text = `${percentage}%`;

                                        ctx.fillStyle = '#ffffff';
                                        ctx.strokeStyle = '#000000';
                                        ctx.lineWidth = 3;
                                        ctx.strokeText(text, x, y);
                                        ctx.fillText(text, x, y);
                                    }
                                }
                            });

                            ctx.restore();
                        }
                    }]
                });
            }

            /**
             * Monthly Trends Line Chart
             */
            function initializeTrendsChart() {
                const ctx = document.getElementById('trainingTrendsChart');
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
                                    {{ $monthlyTrends->pluck('total_applications')->implode(',') }}
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
            const filterForm = document.querySelector('form[action*="analytics.training"]');
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

@section('styles')
    <style>
        /* Navigation Container */
        .navigation-container {
            background: #f8f9fa;
            border-radius: 15px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        }

        /* Modern Analytics Navigation */
        .analytics-nav-btn {
            background: #e9ecef;
            border: 1px solid #ced4da;
            color: #495057;
            font-weight: 500;
            font-size: 0.875rem;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            transform: translateY(0);
        }

        .analytics-nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent);
            transition: left 0.5s;
        }

        .analytics-nav-btn:hover {
            background: #6c757d;
            border-color: #5a6268;
            color: white;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
        }

        .analytics-nav-btn:hover::before {
            left: 100%;
        }

        .analytics-nav-btn:hover i {
            transform: scale(1.15) rotate(5deg);
        }

        .analytics-nav-btn.active {
            background: linear-gradient(135deg, #495057 0%, #343a40 100%);
            border-color: #495057;
            color: white;
            box-shadow: 0 4px 20px rgba(73, 80, 87, 0.4);
            transform: translateY(-1px);
        }

        .analytics-nav-btn.active:hover {
            background: linear-gradient(135deg, #343a40 0%, #212529 100%);
            border-color: #343a40;
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(73, 80, 87, 0.6);
        }

        .analytics-nav-btn i {
            font-size: 0.875rem;
            transition: transform 0.3s ease;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .analytics-nav-btn {
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            .analytics-nav-btn i {
                font-size: 0.75rem;
            }
        }

        .metric-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            cursor: pointer;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .card {
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

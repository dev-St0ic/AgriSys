@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@section('content')
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner card shadow-lg border-0 bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="mb-1 font-weight-bold">Welcome back, {{ $user->name }}! 👋</h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-clock me-2"></i>{{ now()->format('l, F j, Y • g:i A') }}
                            </p>
                        </div>
                        <div class="d-none d-md-block">
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-primary">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Super Admins</div>
                            <div class="metric-value text-primary">{{ $totalSuperAdmins }}</div>
                            <div class="metric-trend">
                                <i class="fas fa-arrow-up text-success me-1"></i>
                                <small class="text-success">System Admins</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-success">
                            <i class="fas fa-users-cog text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Admins</div>
                            <div class="metric-value text-success">{{ $totalAdmins }}</div>
                            <div class="metric-trend">
                                <i class="fas fa-users text-info me-1"></i>
                                <small class="text-info">Active Staff</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-info">
                            <i class="fas fa-warehouse text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Supply Management</div>
                            @if($supplyData['total_categories'] > 0)
                                <div class="metric-value text-info">
                                    {{ $supplyData['active_categories'] }}/{{ $supplyData['total_categories'] }}
                                </div>
                                <div class="metric-trend">
                                    @if($supplyData['low_supply_items'] > 0 || $supplyData['out_of_supply_items'] > 0)
                                        <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                                        <small class="text-warning">
                                            {{ $supplyData['low_supply_items'] + $supplyData['out_of_supply_items'] }} alerts
                                        </small>
                                    @else
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        <small class="text-success">
                                            {{ number_format($supplyData['total_supply']) }} items in stock
                                        </small>
                                    @endif
                                </div>
                            @else
                                <div class="metric-value text-info">0 Categories</div>
                                <div class="metric-trend">
                                    <i class="fas fa-info-circle text-muted me-1"></i>
                                    <small class="text-muted">Setup your supply categories</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-warning">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Total Applications</div>
                            <div class="metric-value text-warning">{{ number_format($analyticsData['totals']['total']) }}
                            </div>
                            <div class="metric-trend">
                                <i class="fas fa-percentage text-success me-1"></i>
                                <small class="text-success">
                                    @if ($analyticsData['totals']['total'] > 0)
                                        {{ round(($analyticsData['totals']['approved'] / $analyticsData['totals']['total']) * 100, 1) }}%
                                        approved
                                    @else
                                        No applications yet
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Overview Cards -->
    <div class="row mb-4 justify-content-center">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-success">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Total Approved</div>
                            <div class="metric-value text-success counter-number"
                                data-target="{{ $analyticsData['totals']['approved'] }}">0</div>
                            <div class="metric-trend">
                                @if ($analyticsData['totals']['total'] > 0)
                                    <i class="fas fa-percentage text-success me-1"></i>
                                    <small
                                        class="text-success">{{ round(($analyticsData['totals']['approved'] / $analyticsData['totals']['total']) * 100, 1) }}%
                                        approval rate</small>
                                @else
                                    <i class="fas fa-info-circle text-muted me-1"></i>
                                    <small class="text-muted">No data yet</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-warning">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Total Pending</div>
                            <div class="metric-value text-warning counter-number"
                                data-target="{{ $analyticsData['totals']['pending'] }}">0</div>
                            <div class="metric-trend">
                                <i class="fas fa-hourglass-half text-warning me-1"></i>
                                <small class="text-warning">Awaiting Review</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card metric-card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="metric-icon bg-danger">
                            <i class="fas fa-times-circle text-white"></i>
                        </div>
                        <div class="ms-3 flex-grow-1">
                            <div class="metric-label">Total Rejected</div>
                            <div class="metric-value text-danger counter-number"
                                data-target="{{ $analyticsData['totals']['rejected'] }}">0</div>
                            <div class="metric-trend">
                                @if ($analyticsData['totals']['total'] > 0)
                                    <i class="fas fa-percentage text-danger me-1"></i>
                                    <small
                                        class="text-danger">{{ round(($analyticsData['totals']['rejected'] / $analyticsData['totals']['total']) * 100, 1) }}%
                                        rejection rate</small>
                                @else
                                    <i class="fas fa-info-circle text-muted me-1"></i>
                                    <small class="text-muted">No data yet</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Alerts - Disabled for new supply management --}}
    {{-- @if ($lowStockItems > 0 || $outOfStockItems > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-modern alert-warning d-flex align-items-center" role="alert">
                    <div class="alert-icon me-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">
                            <i class="fas fa-warehouse me-2"></i>Inventory Alerts
                        </h5>
                        @if ($outOfStockItems > 0)
                            <div class="alert-item mb-1">
                                <i class="fas fa-times-circle text-danger me-2"></i>
                                <strong>{{ $outOfStockItems }} item(s) are out of stock</strong>
                            </div>
                        @endif
                        @if ($lowStockItems > 0)
                            <div class="alert-item mb-1">
                                <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                <strong>{{ $lowStockItems }} item(s) have low stock</strong>
                            </div>
                        @endif
                        <p class="mb-0">
                            <small class="opacity-75">
                                Review your inventory to ensure seedling requests can be fulfilled.
                            </small>
                        </p>
                    </div>
                    <div class="alert-action">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-warning btn-modern">
                            <i class="fas fa-eye me-2"></i>View Inventory
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif --}}

    <!-- Quick Actions Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-dark text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-bolt me-3"></i>Quick Actions
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- First Row -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('landing.page') }}" target="_blank" class="action-card-small btn-info">
                                <div class="action-icon-small">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">Landing</h6>
                                    <small class="action-desc-small">Public site</small>
                                </div>
                            </a>
                        </div>
                        {{-- Inventory quick action disabled for new supply management --}}
                        {{-- <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.inventory.index') }}" class="action-card-small btn-secondary">
                                <div class="action-icon-small">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">Inventory</h6>
                                    <small class="action-desc-small">Stock</small>
                                </div>
                            </a>
                        </div> --}}
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.seedlings.requests') }}" class="action-card-small btn-success">
                                <div class="action-icon-small">
                                    <i class="fas fa-seedling"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">Seedlings</h6>
                                    <small class="action-desc-small">Requests</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.rsbsa.applications') }}" class="action-card-small btn-primary">
                                <div class="action-icon-small">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">RSBSA</h6>
                                    <small class="action-desc-small">Apps</small>
                                </div>
                            </a>
                        </div>

                        <!-- Second Row -->
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.fishr.requests') }}" class="action-card-small btn-info">
                                <div class="action-icon-small">
                                    <i class="fas fa-fish"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">FishR</h6>
                                    <small class="action-desc-small">Register</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.boatr.requests') }}" class="action-card-small btn-warning">
                                <div class="action-icon-small">
                                    <i class="fas fa-ship"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">BoatR</h6>
                                    <small class="action-desc-small">Apps</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.training.requests') }}" class="action-card-small btn-purple">
                                <div class="action-icon-small">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">Training</h6>
                                    <small class="action-desc-small">Apps</small>
                                </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.analytics.seedlings') }}" class="action-card-small btn-dark">
                                <div class="action-icon-small">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="action-content-small">
                                    <h6 class="action-title-small">Analytics</h6>
                                    <small class="action-desc-small">Reports</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Statistics by Service -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 font-weight-bold">
                            <i class="fas fa-chart-bar me-3"></i>Application Statistics by Service
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row justify-content-center">
                        @foreach ($analyticsData['services'] as $serviceKey => $service)
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4 d-flex">
                                <div class="card service-card flex-fill border-0 shadow-sm position-relative">
                                    <div class="service-header bg-{{ $service['color'] }}"></div>
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="service-icon bg-{{ $service['color'] }}-light">
                                                <i class="{{ $service['icon'] }} text-{{ $service['color'] }}"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1 font-weight-bold text-{{ $service['color'] }}">
                                                    {{ $service['name'] }}
                                                </h6>
                                                <small class="text-muted">Service Management</small>
                                            </div>
                                        </div>

                                        <div class="text-center mb-3">
                                            <h2 class="mb-0 font-weight-bold text-dark counter-number"
                                                data-target="{{ $service['total'] }}">0</h2>
                                            <small class="text-muted">Total Applications</small>
                                        </div>

                                        <!-- Status Breakdown with enhanced design -->
                                        <div class="row text-center mb-3">
                                            <div class="col-4">
                                                <div class="status-item">
                                                    <div class="status-number text-success font-weight-bold">
                                                        {{ $service['approved'] }}</div>
                                                    <div class="status-label">
                                                        <i class="fas fa-check-circle text-success me-1"></i>
                                                        <small class="text-muted">Approved</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="status-item">
                                                    <div class="status-number text-warning font-weight-bold">
                                                        {{ $service['pending'] }}</div>
                                                    <div class="status-label">
                                                        <i class="fas fa-clock text-warning me-1"></i>
                                                        <small class="text-muted">Pending</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="status-item">
                                                    <div class="status-number text-danger font-weight-bold">
                                                        {{ $service['rejected'] }}</div>
                                                    <div class="status-label">
                                                        <i class="fas fa-times-circle text-danger me-1"></i>
                                                        <small class="text-muted">Rejected</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($service['total'] > 0)
                                            <!-- Enhanced Progress Bar -->
                                            <div class="progress-container mt-auto">
                                                <div class="progress modern-progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success progress-bar-animated"
                                                        role="progressbar"
                                                        style="width: {{ ($service['approved'] / $service['total']) * 100 }}%"
                                                        data-toggle="tooltip"
                                                        title="Approved: {{ $service['approved'] }}"></div>
                                                    <div class="progress-bar bg-warning progress-bar-animated"
                                                        role="progressbar"
                                                        style="width: {{ ($service['pending'] / $service['total']) * 100 }}%"
                                                        data-toggle="tooltip" title="Pending: {{ $service['pending'] }}">
                                                    </div>
                                                    <div class="progress-bar bg-danger progress-bar-animated"
                                                        role="progressbar"
                                                        style="width: {{ ($service['rejected'] / $service['total']) * 100 }}%"
                                                        data-toggle="tooltip"
                                                        title="Rejected: {{ $service['rejected'] }}"></div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-2">
                                                    <small class="text-success font-weight-bold">
                                                        {{ round(($service['approved'] / $service['total']) * 100, 1) }}%
                                                    </small>
                                                    <small class="text-muted">Approval Rate</small>
                                                </div>
                                            </div>
                                        @else
                                            <div class="progress-container mt-auto">
                                                <div class="text-center">
                                                    <small class="text-muted">No applications yet</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Enhanced Styles -->
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --purple-color: #6f42c1;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        /* Metric Cards */
        .metric-card {
            border-radius: 15px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        .metric-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        .metric-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .metric-trend {
            font-size: 0.875rem;
        }

        /* Alert Enhancements */
        .alert-modern {
            border-radius: 15px;
            border: none;
            padding: 1.5rem;
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .alert-icon {
            flex-shrink: 0;
        }

        .alert-action .btn-modern {
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        /* Service Cards */
        .service-card {
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .service-header {
            height: 4px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .service-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .status-item {
            padding: 0.5rem;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .status-item:hover {
            background: rgba(0, 0, 0, 0.02);
            transform: scale(1.05);
        }

        .status-number {
            font-size: 1.5rem;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .status-label {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Progress Enhancements */
        .progress-container {
            position: relative;
        }

        .modern-progress {
            border-radius: 10px;
            background: #f1f3f4;
            overflow: hidden;
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }

        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        /* Summary Items */
        .summary-item {
            padding: 1rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .summary-item:hover {
            background: rgba(0, 0, 0, 0.02);
            transform: translateY(-2px);
        }

        .summary-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 600;
        }

        /* Action Cards */
        .action-card {
            display: block;
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 15px;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            height: 120px;
            position: relative;
            overflow: hidden;
        }

        .action-card:hover {
            text-decoration: none;
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        /* Small Action Cards for Quick Actions */
        .action-card-small {
            display: block;
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 0.75rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            height: 70px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .action-card-small:hover {
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-color: transparent;
        }

        .action-card.btn-primary:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px rgba(78, 115, 223, 0.3);
        }

        .action-card.btn-success:hover {
            border-color: var(--success-color);
            box-shadow: 0 0 20px rgba(28, 200, 138, 0.3);
        }

        .action-card.btn-info:hover {
            border-color: var(--info-color);
            box-shadow: 0 0 20px rgba(54, 185, 204, 0.3);
        }

        .action-card.btn-warning:hover {
            border-color: var(--warning-color);
            box-shadow: 0 0 20px rgba(246, 194, 62, 0.3);
        }

        .action-card.btn-danger:hover {
            border-color: var(--danger-color);
            box-shadow: 0 0 20px rgba(231, 74, 59, 0.3);
        }

        .action-card.btn-purple:hover {
            border-color: var(--purple-color);
            box-shadow: 0 0 20px rgba(111, 66, 193, 0.3);
        }

        .action-card.btn-dark:hover {
            border-color: var(--dark-color);
            box-shadow: 0 0 20px rgba(90, 92, 105, 0.3);
        }

        .action-card.btn-secondary:hover {
            border-color: #6c757d;
            box-shadow: 0 0 20px rgba(108, 117, 125, 0.3);
        }

        /* Small action card hover effects */
        .action-card-small.btn-primary:hover {
            border-color: var(--primary-color);
            box-shadow: 0 0 15px rgba(78, 115, 223, 0.3);
        }

        .action-card-small.btn-success:hover {
            border-color: var(--success-color);
            box-shadow: 0 0 15px rgba(28, 200, 138, 0.3);
        }

        .action-card-small.btn-info:hover {
            border-color: var(--info-color);
            box-shadow: 0 0 15px rgba(54, 185, 204, 0.3);
        }

        .action-card-small.btn-warning:hover {
            border-color: var(--warning-color);
            box-shadow: 0 0 15px rgba(246, 194, 62, 0.3);
        }

        .action-card-small.btn-danger:hover {
            border-color: var(--danger-color);
            box-shadow: 0 0 15px rgba(231, 74, 59, 0.3);
        }

        .action-card-small.btn-purple:hover {
            border-color: var(--purple-color);
            box-shadow: 0 0 15px rgba(111, 66, 193, 0.3);
        }

        .action-card-small.btn-dark:hover {
            border-color: var(--dark-color);
            box-shadow: 0 0 15px rgba(90, 92, 105, 0.3);
        }

        .action-card-small.btn-secondary:hover {
            border-color: #6c757d;
            box-shadow: 0 0 15px rgba(108, 117, 125, 0.3);
        }

        .action-icon {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .action-title {
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: #5a5c69;
        }

        .action-desc {
            font-size: 0.75rem;
            color: #858796;
        }

        /* Small action card elements */
        .action-icon-small {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .action-title-small {
            font-size: 0.7rem;
            font-weight: 700;
            margin-bottom: 0;
            color: #5a5c69;
            line-height: 1;
        }

        .action-desc-small {
            font-size: 0.6rem;
            color: #858796;
            line-height: 1;
        }

        .action-content-small {
            margin-top: 0.25rem;
        }

        /* Color Utilities */
        .bg-primary-light {
            background-color: rgba(78, 115, 223, 0.1);
        }

        .bg-success-light {
            background-color: rgba(28, 200, 138, 0.1);
        }

        .bg-info-light {
            background-color: rgba(54, 185, 204, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(246, 194, 62, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(231, 74, 59, 0.1);
        }

        .bg-purple-light {
            background-color: rgba(111, 66, 193, 0.1);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, var(--dark-color) 0%, #3d3e46 100%);
        }

        .badge-light-primary {
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Counter Animation */
        .counter-number {
            transition: all 0.3s ease;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .metric-value {
                font-size: 1.5rem;
            }

            .summary-number {
                font-size: 1.5rem;
            }

            .action-card {
                height: 100px;
                padding: 1rem;
            }

            .action-icon {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
        }

        /* Animation Keyframes */
        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }

            100% {
                background-position: 0 0;
            }
        }
    </style>

    <!-- Enhanced JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Counter Animation
            function animateCounters() {
                const counters = document.querySelectorAll('.counter-number');

                counters.forEach(counter => {
                    const targetAttr = counter.getAttribute('data-target');
                    const target = parseInt(targetAttr) || 0;

                    // Skip animation if target is 0 or invalid
                    if (target === 0) {
                        counter.textContent = '0';
                        return;
                    }

                    const increment = target / 60; // Animation duration roughly 1 second
                    let current = 0;

                    const timer = setInterval(() => {
                        current += increment;

                        if (current >= target) {
                            counter.textContent = target.toLocaleString();
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current).toLocaleString();
                        }
                    }, 16); // ~60fps
                });
            }

            // Trigger counter animation when page loads
            setTimeout(animateCounters, 500);

            // Initialize tooltips if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Add hover effects to action cards
            document.querySelectorAll('.action-card, .action-card-small').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('.action-icon, .action-icon-small');
                    if (icon) {
                        icon.style.transform = 'scale(1.1) rotate(5deg)';
                    }
                });

                card.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('.action-icon, .action-icon-small');
                    if (icon) {
                        icon.style.transform = 'scale(1) rotate(0deg)';
                    }
                });
            });
        });
    </script>
@endsection

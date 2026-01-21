@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid p-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="greeting-title">
                            @php
                                $hour = now()->format('H');
                                $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
                            @endphp
                            {{ $greeting }}, {{ $user->name }}
                        </h1>
                        <p class="greeting-time">
                            <i class="fas fa-clock me-2"></i>{{ now()->format('l, F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CRITICAL ALERTS SECTION -->
    @if(count($criticalAlerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="section-title">
                <i class="fas fa-exclamation-circle text-danger me-2"></i>Critical Alerts
            </h5>
            @foreach($criticalAlerts as $alert)
            <div class="alert alert-{{ $alert['color'] }} alert-critical d-flex justify-content-between align-items-start mb-3" role="alert">
                <div class="d-flex align-items-start flex-grow-1">
                    <i class="{{ $alert['icon'] }} fa-lg me-3 mt-1"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-title mb-1">
                            {{ $alert['title'] }}
                            <span class="badge bg-dark ms-2">{{ $alert['count'] }} items</span>
                        </h6>
                        <p class="alert-subtitle mb-2">{{ $alert['subtitle'] }}</p>
                        @if(count($alert['actions']) > 0)
                        <div class="action-buttons">
                            @foreach($alert['actions'] as $action)
                            <a href="{{ route($action['route']) }}" class="btn btn-sm btn-outline-dark me-2">
                                {{ $action['label'] }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- KEY METRICS -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="metric-card metric-card-pending">
                <div class="metric-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Pending Approvals</div>
                    <div class="metric-number">{{ $keyMetrics['total_pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="metric-card metric-card-supply">
                <div class="metric-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Out of Stock</div>
                    <div class="metric-number">{{ $keyMetrics['out_of_stock_items'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="metric-card metric-card-users">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Total Users</div>
                    <div class="metric-number">{{ $keyMetrics['total_users'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="metric-card metric-card-time">
                <div class="metric-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-label">Last Updated</div>
                    <div class="metric-number">Now</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- RECENT ACTIVITY (LEFT) -->
        <div class="col-lg-6 mb-4">
            <div class="card card-clean">
                <div class="card-header card-header-action">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Activity - Pending Action
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if(count($recentActivity) > 0)
                    <div class="activity-list">
                        @foreach($recentActivity as $activity)
                        <a href="{{ $activity['action_url'] }}" class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-type">{{ $activity['type'] }}</div>
                                <div class="activity-name">{{ $activity['name'] }}</div>
                                <div class="activity-time">{{ $activity['created_at']->diffForHumans() }}</div>
                            </div>
                            <div class="activity-status">
                                <span class="badge badge-{{ $activity['status_color'] }}">{{ $activity['action'] }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending activities</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SUPPLY ALERTS (RIGHT) -->
        <div class="col-lg-6 mb-4">
            <div class="card card-clean">
                <div class="card-header card-header-action">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Supply Alerts ({{ $supplyAlerts['total_issues'] }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($supplyAlerts['total_issues'] > 0)
                    <div class="supply-list">
                        @if(count($supplyAlerts['out_of_stock']) > 0)
                        <div class="supply-section">
                            <div class="supply-section-title text-danger">
                                <i class="fas fa-times-circle me-1"></i>Out of Stock ({{ count($supplyAlerts['out_of_stock']) }})
                            </div>
                            @foreach($supplyAlerts['out_of_stock'] as $item)
                            <a href="{{ $item['action_url'] }}" class="supply-item supply-item-danger">
                                <div class="supply-name">{{ $item['item'] }}</div>
                                <div class="supply-category">{{ $item['category'] }}</div>
                                <div class="supply-status">Stock: {{ $item['current'] }}</div>
                            </a>
                            @endforeach
                        </div>
                        @endif

                        @if(count($supplyAlerts['low_stock']) > 0)
                        <div class="supply-section">
                            <div class="supply-section-title text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>Low Stock ({{ count($supplyAlerts['low_stock']) }})
                            </div>
                            @foreach($supplyAlerts['low_stock'] as $item)
                            <a href="{{ $item['action_url'] }}" class="supply-item supply-item-warning">
                                <div class="supply-name">{{ $item['item'] }}</div>
                                <div class="supply-category">{{ $item['category'] }}</div>
                                <div class="supply-status">Stock: {{ $item['current'] }} / Min: {{ $item['minimum'] }}</div>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle text-success"></i>
                        <p>All supplies are healthy</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- APPLICATION STATUS OVERVIEW -->
    <div class="row">
        <div class="col-12">
            <h5 class="section-title mb-3">
                <i class="fas fa-chart-bar me-2"></i>Application Status Overview
            </h5>
        </div>
        @foreach($applicationStatus as $service)
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="service-status-card">
                <div class="service-header">
                    <i class="{{ $service['icon'] }} fa-lg me-2"></i>
                    <span>{{ $service['name'] }}</span>
                </div>
                <div class="service-stats">
                    <a href="{{ route($service['route'], ['filter' => 'pending']) }}" class="stat-item stat-pending">
                        <span class="stat-number">{{ $service['pending'] }}</span>
                        <span class="stat-label">Pending</span>
                    </a>
                    <a href="{{ route($service['route'], ['filter' => 'approved']) }}" class="stat-item stat-approved">
                        <span class="stat-number">{{ $service['approved'] }}</span>
                        <span class="stat-label">Approved</span>
                    </a>
                    <a href="{{ route($service['route'], ['filter' => 'rejected']) }}" class="stat-item stat-rejected">
                        <span class="stat-number">{{ $service['rejected'] }}</span>
                        <span class="stat-label">Rejected</span>
                    </a>
                </div>
                <div class="service-action">
                    <a href="{{ route($service['route']) }}" class="btn btn-sm btn-outline-primary w-100">
                        Manage
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    :root {
        --primary: #4e73df;
        --success: #1cc88a;
        --danger: #e74a3b;
        --warning: #f6c23e;
        --info: #36b9cc;
        --light: #f8f9fc;
        --dark: #5a5c69;
    }

    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Welcome Card */
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, #224abe 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .greeting-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .greeting-time {
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Section Title */
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    /* Critical Alerts */
    .alert-critical {
        border: none;
        border-left: 4px solid;
        padding: 1.25rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .alert-critical.alert-danger {
        background-color: #fff5f5;
        border-left-color: var(--danger);
    }

    .alert-critical.alert-warning {
        background-color: #fffaf0;
        border-left-color: var(--warning);
    }

    .alert-title {
        font-weight: 600;
        color: inherit;
    }

    .alert-subtitle {
        font-size: 0.9rem;
        opacity: 0.8;
        margin-bottom: 0;
    }

    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .action-buttons .btn {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }

    /* Metric Cards */
    .metric-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 1rem;
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .metric-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .metric-card-pending {
        border-left-color: var(--warning);
    }

    .metric-card-supply {
        border-left-color: var(--danger);
    }

    .metric-card-users {
        border-left-color: var(--info);
    }

    .metric-card-time {
        border-left-color: var(--success);
    }

    .metric-icon {
        font-size: 1.5rem;
        opacity: 0.7;
        min-width: 40px;
    }

    .metric-label {
        font-size: 0.85rem;
        color: var(--dark);
        font-weight: 500;
        margin-bottom: 0.3rem;
    }

    .metric-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
    }

    /* Cards */
    .card-clean {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
    }

    .card-header-action {
        background: white;
        padding: 1.5rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .card-header-action h6 {
        color: var(--dark);
    }

    /* Activity List */
    .activity-list {
        max-height: 500px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e3e6f0;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background-color: #f8f9fc;
        padding-left: 1.75rem;
    }

    .activity-icon {
        font-size: 0.8rem;
        color: var(--primary);
        margin-right: 1rem;
        min-width: 20px;
    }

    .activity-content {
        flex-grow: 1;
    }

    .activity-type {
        font-size: 0.75rem;
        color: var(--dark);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .activity-name {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--dark);
        margin-bottom: 0.2rem;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #adb5bd;
    }

    .activity-status {
        margin-left: 1rem;
    }

    .badge-warning {
        background-color: var(--warning) !important;
        color: #333;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #adb5bd;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        display: block;
        color: var(--success);
    }

    /* Supply List */
    .supply-list {
        padding: 1rem;
    }

    .supply-section {
        margin-bottom: 1.5rem;
    }

    .supply-section:last-child {
        margin-bottom: 0;
    }

    .supply-section-title {
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.75rem 0;
        margin-bottom: 0.75rem;
        border-bottom: 2px solid;
        border-bottom-color: inherit;
        color: inherit;
    }

    .supply-item {
        display: block;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 6px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }

    .supply-item-danger {
        background-color: #fff5f5;
        border-left: 3px solid var(--danger);
    }

    .supply-item-danger:hover {
        background-color: #ffe0e0;
    }

    .supply-item-warning {
        background-color: #fffaf0;
        border-left: 3px solid var(--warning);
    }

    .supply-item-warning:hover {
        background-color: #fff0d6;
    }

    .supply-name {
        font-weight: 500;
        font-size: 0.95rem;
        margin-bottom: 0.2rem;
    }

    .supply-category {
        font-size: 0.8rem;
        opacity: 0.7;
        margin-bottom: 0.2rem;
    }

    .supply-status {
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* Service Status Cards */
    .service-status-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .service-status-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .service-header {
        font-size: 1rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .service-stats {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        flex: 1;
        padding: 0.75rem;
        border-radius: 6px;
        text-align: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        border: 1px solid;
    }

    .stat-pending {
        background-color: #fffaf0;
        border-color: var(--warning);
        color: #cc8800;
    }

    .stat-pending:hover {
        background-color: var(--warning);
        color: white;
    }

    .stat-approved {
        background-color: #f0fdf4;
        border-color: var(--success);
        color: #16a34a;
    }

    .stat-approved:hover {
        background-color: var(--success);
        color: white;
    }

    .stat-rejected {
        background-color: #fef2f2;
        border-color: var(--danger);
        color: #b91c1c;
    }

    .stat-rejected:hover {
        background-color: var(--danger);
        color: white;
    }

    .stat-number {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
    }

    .service-action .btn {
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .metric-card {
            flex-direction: column;
            text-align: center;
        }

        .activity-item:hover {
            padding-left: 1.5rem;
        }

        .service-stats {
            flex-direction: column;
        }
    }
</style>

@endsection
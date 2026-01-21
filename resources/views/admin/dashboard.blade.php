@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
@endpush

@section('content')
<div class="dashboard-wrapper p-4">
    <!-- Welcome Section with Quick Stats -->
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
                    <div class="quick-stats">
                        <div class="stat-badge">
                            <span class="badge-label">System Status</span>
                            <span class="badge badge-success"><i class="fas fa-circle-notch fa-spin me-1"></i>Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CRITICAL ALERTS SECTION - ENHANCED DESIGN -->
    @if(count($criticalAlerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="critical-alerts-container">
                <!-- Header with Icon -->
                <div class="alerts-header-enhanced">
                    <div class="alerts-header-content">
                        <div class="alerts-header-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alerts-header-text">
                            <h5 class="alerts-header-title">Critical Alerts</h5>
                            <p class="alerts-header-subtitle">{{ count($criticalAlerts) }} active alert{{ count($criticalAlerts) > 1 ? 's' : '' }} require immediate attention</p>
                        </div>
                    </div>
                </div>

                <!-- Alerts Grid -->
                <div class="critical-alerts-grid">
                    @foreach($criticalAlerts as $alert)
                    <div class="critical-alert-card alert-{{ $alert['color'] }}">
                        <!-- Alert Indicator Bar -->
                        <div class="alert-indicator"></div>

                        <!-- Main Content -->
                        <div class="alert-card-content">
                            <!-- Icon and Title Section -->
                            <div class="alert-header-section">
                                <div class="alert-icon-wrapper alert-icon-{{ $alert['color'] }}">
                                    <i class="{{ $alert['icon'] }}"></i>
                                </div>
                                <div class="alert-title-wrapper">
                                    <h6 class="alert-title-text">{{ $alert['title'] }}</h6>
                                    <p class="alert-subtitle-text">{{ $alert['subtitle'] }}</p>
                                </div>
                                <div class="alert-count-badge">
                                    <span class="count-number">{{ $alert['count'] }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            @if(count($alert['actions']) > 0)
                            <div class="alert-actions-wrapper">
                                @foreach($alert['actions'] as $action)
                                <a href="{{ route($action['route']) }}" class="btn-alert-enhanced">
                                    <span>{{ $action['label'] }}</span>
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- KEY METRICS - 2x2 GRID WITH BETTER ORGANIZATION -->
    <div class="row mb-4 g-3">
        <!-- First Row: Pending Approvals & Out of Stock -->
        <div class="col-lg-6 col-md-6">
            <div class="metric-card metric-card-pending">
                <div class="metric-content-wrapper">
                    <div class="metric-header">
                        <div class="metric-icon-box pending">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="metric-info">
                            <span class="metric-label">Pending Approvals</span>
                            <span class="metric-number">{{ $keyMetrics['total_pending'] }}</span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <a href="#" class="metric-link">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="metric-card metric-card-supply">
                <div class="metric-content-wrapper">
                    <div class="metric-header">
                        <div class="metric-icon-box supply">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="metric-info">
                            <span class="metric-label">Out of Stock</span>
                            <span class="metric-number">{{ $keyMetrics['out_of_stock_items'] }}</span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <a href="#" class="metric-link">Manage Stock <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Total Users & Low Stock Items -->
        <div class="col-lg-6 col-md-6">
            <div class="metric-card metric-card-users">
                <div class="metric-content-wrapper">
                    <div class="metric-header">
                        <div class="metric-icon-box users">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="metric-info">
                            <span class="metric-label">Total Users</span>
                            <span class="metric-number">{{ $keyMetrics['total_users'] }}</span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <a href="#" class="metric-link">All Users <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="metric-card metric-card-info">
                <div class="metric-content-wrapper">
                    <div class="metric-header">
                        <div class="metric-icon-box info">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="metric-info">
                            <span class="metric-label">Low Stock Items</span>
                            <span class="metric-number">{{ count($supplyAlerts['low_stock'] ?? []) }}</span>
                        </div>
                    </div>
                    <div class="metric-footer">
                        <a href="#" class="metric-link">Monitor <i class="fas fa-arrow-right ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUPPLY MANAGEMENT SECTION WITH HEADER AND CARDS -->
    <div class="supply-management-section">
        <!-- SUPPLY MANAGEMENT TITLE SECTION -->
        <div class="supply-section-header">
            <div class="supply-header-content">
                <div class="supply-header-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="supply-header-text">
                    <h5 class="supply-header-title">Supply Management</h5>
                    <p class="supply-header-subtitle">Monitor inventory levels and manage stock alerts</p>
                </div>
            </div>
        </div>

        <!-- SUPPLY MANAGEMENT - DUAL COLUMN LAYOUT -->
        <div class="row g-3 mt-0">
            <!-- OUT OF STOCK -->
            <div class="col-lg-6">
                <a href="{{ route('admin.seedlings.inventory.items', ['status' => 'out_of_stock']) }}" class="card card-enhanced supply-card-link">
                    <div class="card-header-enhanced">
                        <h6 class="card-title">
                            <i class="fas fa-times-circle text-danger me-2"></i>Out of Stock Items
                        </h6>
                        <span class="badge bg-danger text-white">{{ count($supplyAlerts['out_of_stock']) }}</span>
                    </div>
                    <div class="card-body card-body-enhanced">
                        @if(count($supplyAlerts['out_of_stock']) > 0)
                        <div class="supply-list">
                            @foreach($supplyAlerts['out_of_stock'] as $item)
                            <div class="supply-item critical" onclick="event.stopPropagation();">
                                <div class="supply-info">
                                    <div class="supply-name">{{ $item['item'] }}</div>
                                    <div class="supply-category">{{ $item['category'] }}</div>
                                </div>
                                <div class="supply-badge critical">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>All items in stock</p>
                        </div>
                        @endif
                    </div>
                </a>
            </div>

            <!-- LOW STOCK -->
            <div class="col-lg-6">
                <a href="{{ route('admin.seedlings.inventory.items', ['status' => 'low_stock']) }}" class="card card-enhanced supply-card-link">
                    <div class="card-header-enhanced">
                        <h6 class="card-title">
                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>Low Stock Items
                        </h6>
                        <span class="badge bg-warning text-dark">{{ count($supplyAlerts['low_stock']) }}</span>
                    </div>
                    <div class="card-body card-body-enhanced">
                        @if(count($supplyAlerts['low_stock']) > 0)
                        <div class="supply-list">
                            @foreach($supplyAlerts['low_stock'] as $item)
                            <div class="supply-item warning" onclick="event.stopPropagation();">
                                <div class="supply-info">
                                    <div class="supply-name">{{ $item['item'] }}</div>
                                    <div class="supply-detail">{{ $item['current'] }} / {{ $item['minimum'] }} (min)</div>
                                </div>
                                <div class="supply-progress">
                                    <div class="progress-bar" style="width: {{ ($item['current'] / $item['minimum'] * 100) }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-leaf"></i>
                            <p>All supplies are healthy</p>
                        </div>
                        @endif
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- APPLICATION STATUS OVERVIEW WITH CHARTS -->
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="section-header">
                <h5 class="section-title">
                    <i class="fas fa-chart-bar me-2"></i>Application Status Overview
                </h5>
                <p class="section-subtitle">Track application submissions and approvals across all services</p>
            </div>
        </div>
    </div>

    <div class="row mb-4 g-3">
        @foreach($applicationStatus as $service)
        <div class="col-lg-4 col-md-6">
            <div class="status-card-enhanced">
                <div class="status-card-header">
                    <div class="status-icon-wrapper">
                        <i class="{{ $service['icon'] }} fa-lg"></i>
                    </div>
                    <div class="status-title-group">
                        <h6 class="status-name">{{ $service['name'] }}</h6>
                        <span class="status-total">{{ $service['pending'] + $service['approved'] + $service['rejected'] }} total</span>
                    </div>
                </div>

                <div class="status-stats-container">
                    <div class="status-stat">
                        <a href="{{ route($service['route'], ['filter' => 'pending']) }}" class="stat-box pending">
                            <span class="stat-value">{{ $service['pending'] }}</span>
                            <span class="stat-name">Pending</span>
                        </a>
                    </div>
                    <div class="status-stat">
                        <a href="{{ route($service['route'], ['filter' => 'approved']) }}" class="stat-box approved">
                            <span class="stat-value">{{ $service['approved'] }}</span>
                            <span class="stat-name">Approved</span>
                        </a>
                    </div>
                    <div class="status-stat">
                        <a href="{{ route($service['route'], ['filter' => 'rejected']) }}" class="stat-box rejected">
                            <span class="stat-value">{{ $service['rejected'] }}</span>
                            <span class="stat-name">Rejected</span>
                        </a>
                    </div>
                </div>
                <div class="status-action">
                    <a href="{{ route($service['route']) }}" class="btn btn-status-action w-100">
                        <i class="fas fa-arrow-right me-2"></i>Manage Applications
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- RECENT ACTIVITY -->
    <div class="row mb-4 g-3">
        <div class="col-lg-12">
            <div class="card card-enhanced">
                <div class="card-header-enhanced">
                    <h6 class="card-title">
                        <i class="fas fa-history me-2"></i>Recent Activity
                    </h6>
                    <span class="badge bg-light text-dark">Latest</span>
                </div>
                <div class="card-body card-body-enhanced">
                    @if(count($recentActivity) > 0)
                    <div class="activity-list">
                        @foreach($recentActivity as $activity)
                        <a href="{{ $activity['action_url'] }}" class="activity-item">
                            <div class="activity-left">
                                <div class="activity-icon">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-type">{{ $activity['type'] }}</div>
                                    <div class="activity-name">{{ $activity['name'] }}</div>
                                </div>
                            </div>
                            <div class="activity-right">
                                <span class="activity-time">{{ $activity['created_at']->diffForHumans() }}</span>
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
    </div>

<script>
    // Supply Status Chart
    const supplyCtx = document.getElementById('supplyStatusChart')?.getContext('2d');
    if (supplyCtx) {
        @php
            $totalSupplyItems = count($supplyAlerts['out_of_stock'] ?? []) + count($supplyAlerts['low_stock'] ?? []) + ($keyMetrics['total_users'] ?? 0);
            $outOfStock = count($supplyAlerts['out_of_stock'] ?? []);
            $lowStock = count($supplyAlerts['low_stock'] ?? []);
            $healthy = max(0, $totalSupplyItems - $outOfStock - $lowStock);
            $totalForChart = $outOfStock + $lowStock + $healthy;
        @endphp
        
        new Chart(supplyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Healthy', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    data: [
                        {{ $healthy > 0 ? $healthy : 0 }},
                        {{ $lowStock }},
                        {{ $outOfStock }}
                    ],
                    backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }
</script>

<style>
    :root {
        --primary: #4e73df;
        --success: #1cc88a;
        --danger: #e74a3b;
        --warning: #f6c23e;
        --info: #36b9cc;
        --light: #f8f9fc;
        --dark: #2e3338;
        --gray: #858796;
    }

    .dashboard-wrapper {
        max-width: 1450px;
        margin: 0 auto;
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        min-height: 100vh;
        padding: 1rem !important;
    }

    /* ==================== WELCOME SECTION ==================== */
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, #224abe 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(78, 115, 223, 0.2);
        position: relative;
        overflow: hidden;
    }

    .welcome-card::before {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 150px;
        height: 150px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .greeting-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .greeting-time {
        opacity: 0.95;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .quick-stats {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .stat-badge {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.5rem;
    }

    .badge-label {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    /* ==================== CRITICAL ALERTS - ENHANCED DESIGN ==================== */
    .critical-alerts-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .alerts-header-enhanced {
        background: linear-gradient(135deg, #fff5f5 0%, #fffaf0 100%);
        border-bottom: 2px solid #ffe5e5;
        padding: 1.75rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .alerts-header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        width: 100%;
    }

    .alerts-header-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--danger), #d63a25);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.75rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(231, 74, 59, 0.25);
    }

    .alerts-header-text {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .alerts-header-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0;
    }

    .alerts-header-subtitle {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 0;
    }

    /* Critical Alerts Grid */
    .critical-alerts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.25rem;
        padding: 1.75rem;
    }

    .critical-alert-card {
        position: relative;
        background: white;
        border-radius: 10px;
        border: 1.5px solid;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .critical-alert-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .critical-alert-card.alert-danger {
        border-color: #ffe5e5;
        background: linear-gradient(135deg, #fff9f9 0%, #fffbfb 100%);
    }

    .critical-alert-card.alert-danger:hover {
        border-color: var(--danger);
    }

    .critical-alert-card.alert-warning {
        border-color: #fff3e0;
        background: linear-gradient(135deg, #fffef9 0%, #fffdfb 100%);
    }

    .critical-alert-card.alert-warning:hover {
        border-color: var(--warning);
    }

    .alert-indicator {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--danger), #f6c23e);
        animation: slideIn 0.6s ease;
    }

    @keyframes slideIn {
        from {
            width: 0;
        }
        to {
            width: 100%;
        }
    }

    .alert-card-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        flex: 1;
    }

    .alert-header-section {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }

    .alert-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .alert-icon-danger {
        background: linear-gradient(135deg, var(--danger), #d63a25);
        box-shadow: 0 4px 12px rgba(231, 74, 59, 0.2);
    }

    .alert-icon-warning {
        background: linear-gradient(135deg, var(--warning), #fcb92d);
        box-shadow: 0 4px 12px rgba(246, 194, 62, 0.2);
    }

    .alert-title-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .alert-title-text {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0;
        line-height: 1.4;
    }

    .alert-subtitle-text {
        font-size: 0.85rem;
        color: var(--gray);
        margin-bottom: 0;
        line-height: 1.4;
    }

    .alert-count-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--danger), #d63a25);
        border-radius: 10px;
        color: white;
        font-weight: 700;
        font-size: 1.3rem;
        box-shadow: 0 4px 12px rgba(231, 74, 59, 0.2);
        flex-shrink: 0;
    }

    .count-number {
        line-height: 1;
    }

    /* Alert Actions */
    .alert-actions-wrapper {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        border-top: 1px solid #e3e6f0;
        padding-top: 1.25rem;
        margin-top: auto;
    }

    .btn-alert-enhanced {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        background: linear-gradient(135deg, var(--danger), #d63a25);
        color: white;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-alert-enhanced:hover {
        box-shadow: 0 6px 16px rgba(231, 74, 59, 0.3);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .btn-alert-enhanced i {
        font-size: 0.85rem;
        transition: transform 0.2s ease;
    }

    .btn-alert-enhanced:hover i {
        transform: translateX(4px);
    }

    /* ==================== SECTION HEADERS ==================== */
    .section-header {
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
    }

    .section-subtitle {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 0;
    }

    /* ==================== SUPPLY MANAGEMENT SECTION ==================== */
    .supply-management-section {
        margin-bottom: 2rem;
    }

    .supply-section-header {
        background: white;
        border-radius: 12px 12px 0 0;
        padding: 1.75rem;
        border-bottom: 2px solid #e3e6f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .supply-header-content {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .supply-header-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary), #3d5fd5);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
    }

    .supply-header-text {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
    }

    .supply-header-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0;
    }

    .supply-header-subtitle {
        font-size: 0.9rem;
        color: var(--gray);
        margin-bottom: 0;
    }

    .supply-card-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .supply-card-link:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
        text-decoration: none;
        color: inherit;
    }

    /* ==================== METRIC CARDS - 2x2 LAYOUT ==================== */
    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-top: 4px solid;
        position: relative;
        height: 100%;
    }

    .metric-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .metric-card-pending {
        border-top-color: var(--warning);
    }

    .metric-card-supply {
        border-top-color: var(--danger);
    }

    .metric-card-users {
        border-top-color: var(--info);
    }

    .metric-card-info {
        border-top-color: var(--primary);
    }

    .metric-content-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
    }

    .metric-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .metric-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .metric-icon-box.pending {
        background: linear-gradient(135deg, var(--warning), #fcb92d);
    }

    .metric-icon-box.supply {
        background: linear-gradient(135deg, var(--danger), #d63a25);
    }

    .metric-icon-box.users {
        background: linear-gradient(135deg, var(--info), #2fa7b8);
    }

    .metric-icon-box.info {
        background: linear-gradient(135deg, var(--primary), #3d5fd5);
    }

    .metric-info {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        flex: 1;
    }

    .metric-label {
        font-size: 0.85rem;
        color: var(--gray);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
    }

    .metric-footer {
        border-top: 1px solid #e3e6f0;
        padding-top: 1rem;
        margin-top: auto;
    }

    .metric-link {
        color: var(--primary);
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .metric-link:hover {
        color: #224abe;
        gap: 0.75rem;
    }

    /* ==================== CARD STYLES ==================== */
    .card-enhanced {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-enhanced:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    }

    .card-header-enhanced {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        padding: 1.5rem;
        border-bottom: 2px solid #e3e6f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0;
    }

    .card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0;
    }

    .card-body-enhanced {
        padding: 0;
        flex: 1;
        overflow-y: auto;
        max-height: 450px;
    }

    .card-body-enhanced::-webkit-scrollbar {
        width: 6px;
    }

    .card-body-enhanced::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .card-body-enhanced::-webkit-scrollbar-thumb {
        background: #adb5bd;
        border-radius: 3px;
    }

    /* ==================== SUPPLY LIST ==================== */
    .supply-list {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .supply-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: white;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        border-left: 3px solid;
        border-bottom: 1px solid #e3e6f0;
    }

    .supply-item:last-child {
        border-bottom: none;
    }

    .supply-item.critical {
        border-left-color: var(--danger);
    }

    .supply-item.critical:hover {
        background: #fff5f5;
        padding-left: 1.5rem;
    }

    .supply-item.warning {
        border-left-color: var(--warning);
    }

    .supply-item.warning:hover {
        background: #fffaf0;
        padding-left: 1.5rem;
    }

    .supply-info {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
        flex: 1;
    }

    .supply-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--dark);
    }

    .supply-category {
        font-size: 0.8rem;
        color: var(--gray);
    }

    .supply-detail {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--gray);
    }

    .supply-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .supply-badge.critical {
        background: var(--danger);
    }

    .supply-progress {
        width: 100%;
        max-width: 80px;
        height: 4px;
        background: #e3e6f0;
        border-radius: 2px;
        overflow: hidden;
        margin-left: auto;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--warning), #fcb92d);
        transition: width 0.3s ease;
    }

    /* ==================== ACTIVITY LIST ==================== */
    .activity-list {
        max-height: 550px;
        overflow-y: auto;
    }

    .activity-list::-webkit-scrollbar {
        width: 6px;
    }

    .activity-list::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .activity-list::-webkit-scrollbar-thumb {
        background: #adb5bd;
        border-radius: 3px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
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
        padding-left: 1.25rem;
    }

    .activity-left {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex: 1;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, var(--primary), #3d5fd5);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .activity-content {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .activity-type {
        font-size: 0.75rem;
        color: var(--gray);
        font-weight: 600;
        text-transform: uppercase;
    }

    .activity-name {
        font-size: 0.95rem;
        font-weight: 500;
        color: var(--dark);
    }

    .activity-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-align: right;
    }

    .activity-time {
        font-size: 0.8rem;
        color: var(--gray);
        white-space: nowrap;
    }

    /* ==================== STATUS CARDS - ENHANCED ==================== */
    .status-card-enhanced {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        align-items: center;
        border-top: 5px solid var(--primary);
    }

    .status-card-enhanced:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .status-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: 100%;
    }

    .status-icon-wrapper {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary), #3d5fd5);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .status-title-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .status-name {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0;
    }

    .status-total {
        font-size: 0.8rem;
        color: var(--gray);
    }

    .status-stats-container {
        display: flex;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        width: 100%;
        justify-content: center;
    }

    .status-stat {
        flex: 1;
    }

    .stat-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        border-radius: 8px;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        border: 2px solid;
        text-align: center;
        gap: 0.5rem;
    }

    .stat-box.pending {
        background: #fffaf0;
        border-color: var(--warning);
        color: #cc8800;
    }

    .stat-box.pending:hover {
        background: var(--warning);
        color: white;
    }

    .stat-box.approved {
        background: #f0fdf4;
        border-color: var(--success);
        color: #16a34a;
    }

    .stat-box.approved:hover {
        background: var(--success);
        color: white;
    }

    .stat-box.rejected {
        background: #fef2f2;
        border-color: var(--danger);
        color: #b91c1c;
    }

    .stat-box.rejected:hover {
        background: var(--danger);
        color: white;
    }

    .stat-value {
        font-size: 1.4rem;
        font-weight: 700;
    }

    .stat-name {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-action {
        width: 100%;
        border-top: 1px solid #e3e6f0;
        padding-top: 1rem;
        margin-top: auto;
    }

    .btn-status-action {
        background: linear-gradient(135deg, var(--primary), #3d5fd5);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-block;
        text-decoration: none;
    }

    .btn-status-action:hover {
        box-shadow: 0 6px 16px rgba(78, 115, 223, 0.3);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    /* ==================== EMPTY STATE ==================== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--gray);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
        color: var(--success);
    }

    .empty-state p {
        font-size: 0.95rem;
        margin-bottom: 0;
    }

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 992px) {
        .greeting-title {
            font-size: 1.5rem;
        }

        .status-stats-container {
            grid-template-columns: repeat(3, 1fr);
        }

        .quick-stats {
            flex-direction: column;
            align-items: flex-start;
        }

        .critical-alerts-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dashboard-wrapper {
            padding: 0.75rem !important;
        }

        .metric-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .metric-number {
            font-size: 1.5rem;
        }

        .activity-right {
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .status-stats-container {
            grid-template-columns: 1fr;
        }

        .stat-box {
            padding: 0.75rem;
        }

        .card-body-enhanced {
            max-height: 400px;
        }

        .critical-alerts-grid {
            grid-template-columns: 1fr;
        }

        .alerts-header-enhanced {
            padding: 1.25rem;
        }

        .alert-card-content {
            padding: 1rem;
        }

        .supply-section-header {
            padding: 1.25rem;
        }

        .supply-header-content {
            gap: 0.75rem;
        }
    }

    @media (max-width: 576px) {
        .greeting-title {
            font-size: 1.3rem;
        }

        .metric-number {
            font-size: 1.3rem;
        }

        .section-title {
            font-size: 1.1rem;
        }

        .metric-label {
            font-size: 0.75rem;
        }

        .status-stats-container {
            grid-template-columns: 1fr;
        }

        .alerts-header-content {
            gap: 0.75rem;
        }

        .alerts-header-icon {
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
        }

        .alerts-header-title {
            font-size: 1.1rem;
        }

        .alerts-header-subtitle {
            font-size: 0.8rem;
        }

        .supply-header-icon {
            width: 42px;
            height: 42px;
            font-size: 1.25rem;
        }

        .supply-header-title {
            font-size: 1.1rem;
        }

        .supply-header-subtitle {
            font-size: 0.8rem;
        }
    }
</style>

@endsection
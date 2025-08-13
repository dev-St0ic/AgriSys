@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@section('content')


    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Super Admins
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSuperAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-crown fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Admins
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Inventory Items
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalInventoryItems }}</div>
                            @if ($lowStockItems > 0 || $outOfStockItems > 0)
                                <div class="text-xs text-danger">
                                    {{ $outOfStockItems }} out, {{ $lowStockItems }} low
                                </div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-warehouse fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Alerts -->
    @if ($lowStockItems > 0 || $outOfStockItems > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-left-warning shadow" role="alert">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-2">
                                <i class="fas fa-warehouse me-2"></i>Inventory Alerts
                            </h5>
                            @if ($outOfStockItems > 0)
                                <p class="mb-1">
                                    <strong class="text-danger">⚠ {{ $outOfStockItems }} item(s) are out of stock</strong>
                                </p>
                            @endif
                            @if ($lowStockItems > 0)
                                <p class="mb-1">
                                    <strong class="text-warning">⚠ {{ $lowStockItems }} item(s) have low stock</strong>
                                </p>
                            @endif
                            <p class="mb-0">
                                <small class="text-muted">
                                    Review your inventory to ensure seedling requests can be fulfilled.
                                </small>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-warning">
                                <i class="fas fa-eye me-2"></i>View Inventory
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- First Row -->
                        <div class="col-md-4">
                            <a href="{{ route('landing.page') }}" target="_blank" class="btn btn-info w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-home fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Access Landing Page</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-warehouse fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Manage Inventory</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.seedling.requests') }}" class="btn btn-success w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-seedling fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Seedling Requests</div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Second Row -->
                        <div class="col-md-4">
                            <a href="{{ route('admin.rsbsa.applications') }}" class="btn btn-primary w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-file-alt fa-3x mb-2"></i>
                                    <div class="h6 mb-0">RSBSA Applications</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.fishr.requests') }}" class="btn btn-info w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-fish fa-3x mb-2"></i>
                                    <div class="h6 mb-0">FishR Registrations</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.boatr.requests') }}" class="btn btn-warning w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-ship fa-3x mb-2"></i>
                                    <div class="h6 mb-0">BoatR Applications</div>
                                </div>
                            </a>
                        </div>
                        
                        <!-- Third Row -->
                        <div class="col-md-4">
                            <a href="{{ route('admin.analytics.seedlings') }}" class="btn btn-dark w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-chart-line fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Analytics</div>
                                </div>
                            </a>
                        </div>

                        @if ($user->isSuperAdmin())
                        <div class="col-md-4">
                            <a href="{{ route('admin.admins.index') }}" class="btn btn-purple w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px; background-color: #6f42c1; border-color: #6f42c1;">
                                <div>
                                    <i class="fas fa-users-cog fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Manage Admins</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin.admins.create') }}" class="btn btn-success w-100 py-4 text-center" style="min-height: 120px; border-radius: 15px;">
                                <div>
                                    <i class="fas fa-plus fa-3x mb-2"></i>
                                    <div class="h6 mb-0">Add New Admin</div>
                                </div>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }
    </style>
@endsection

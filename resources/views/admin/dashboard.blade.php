@extends('layouts.app')

@section('title', 'Admin Dashboard - AgriSys')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-2">
                                Welcome back, {{ $user->name }}!
                            </h4>
                            <p class="card-text mb-0">
                                You are logged in as {{ $user->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </p>
                        </div>
                        <div>
                            <i class="fas fa-user-shield fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Admin Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAdmins + $totalSuperAdmins }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <a href="{{ route('landing.page') }}" target="_blank" class="btn btn-info mb-2 me-2">
                        <i class="fas fa-home me-2"></i>Access Landing Page
                    </a>

                    @if ($user->isSuperAdmin())
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-primary mb-2 me-2">
                            <i class="fas fa-users-cog me-2"></i>Manage Admins
                        </a>
                        <a href="{{ route('admin.admins.create') }}" class="btn btn-success mb-2 me-2">
                            <i class="fas fa-plus me-2"></i>Add New Admin
                        </a>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Contact Super Admin for admin management access.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Name:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->name }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Email:</strong>
                        </div>
                        <div class="col-sm-8">
                            {{ $user->email }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Role:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-{{ $user->isSuperAdmin() ? 'danger' : 'primary' }}">
                                {{ $user->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Management Card -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>System Management
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Landing Page -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-left-info">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-home me-2"></i>Landing Page</h5>
                                    <p class="card-text">Access the public landing page and registration forms.</p>
                                    <a href="{{ route('landing.page') }}" target="_blank" class="btn btn-info">
                                        <i class="fas fa-external-link-alt me-2"></i>View Landing Page
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Applications -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-left-success">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-file-alt me-2"></i>Applications</h5>
                                    <p class="card-text">Manage incoming applications from citizens.</p>
                                    <button class="btn btn-success" disabled>
                                        <i class="fas fa-list me-2"></i>View Applications
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-left-warning">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-cog me-2"></i>System Settings</h5>
                                    <p class="card-text">Configure system settings and preferences.</p>
                                    <button class="btn btn-warning" disabled>
                                        <i class="fas fa-cogs me-2"></i>Settings
                                    </button>
                                </div>
                            </div>
                        </div>
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

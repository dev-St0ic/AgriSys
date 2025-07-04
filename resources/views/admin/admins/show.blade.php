@extends('layouts.app')

@section('title', 'View Admin - AgriSys')
@section('page-title', 'Admin Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Admin User Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <div class="avatar-title rounded-circle bg-primary">
                                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                                </div>
                            </div>
                            <span class="badge bg-{{ $admin->isSuperAdmin() ? 'danger' : 'primary' }} fs-6">
                                <i class="fas fa-{{ $admin->isSuperAdmin() ? 'crown' : 'user-shield' }} me-1"></i>
                                {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-3">{{ $admin->name }}</h4>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-envelope me-2"></i>Email:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->email }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-user-shield me-2"></i>Role:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-calendar-alt me-2"></i>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->created_at->format('F d, Y \a\t g:i A') }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4">
                                    <strong><i class="fas fa-clock me-2"></i>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $admin->updated_at->format('F d, Y \a\t g:i A') }}
                                </div>
                            </div>

                            @if ($admin->id === auth()->id())
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    This is your account
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Permissions Section -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-key me-2"></i>Permissions
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Access Admin Dashboard
                                        <i class="fas fa-check text-success"></i>
                                    </li>
                                    @if ($admin->isSuperAdmin())
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Manage Admin Users
                                            <i class="fas fa-check text-success"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Create New Admins
                                            <i class="fas fa-check text-success"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Delete Admin Users
                                            <i class="fas fa-check text-success"></i>
                                        </li>
                                    @else
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Manage Admin Users
                                            <i class="fas fa-times text-danger"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Create New Admins
                                            <i class="fas fa-times text-danger"></i>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Delete Admin Users
                                            <i class="fas fa-times text-danger"></i>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to List
                        </a>
                        <div>
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-2"></i>Edit Admin
                            </a>
                            @if ($admin->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this admin? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Delete Admin
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #6c757d;
            color: #fff;
            display: flex;
            font-size: 1.5rem;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
    </style>
@endsection

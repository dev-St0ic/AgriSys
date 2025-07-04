@extends('layouts.app')

@section('title', 'Manage Admins - AgriSys')
@section('page-title', 'Manage Admins')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Admin Users</h4>
        <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Admin
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            @if ($admins->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($admins as $admin)
                                <tr>
                                    <td>{{ $admin->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title rounded-circle">
                                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            {{ $admin->name }}
                                            @if ($admin->id === auth()->id())
                                                <span class="badge bg-info ms-2">You</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $admin->isSuperAdmin() ? 'danger' : 'primary' }}">
                                            <i
                                                class="fas fa-{{ $admin->isSuperAdmin() ? 'crown' : 'user-shield' }} me-1"></i>
                                            {{ $admin->isSuperAdmin() ? 'Super Admin' : 'Admin' }}
                                        </span>
                                    </td>
                                    <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.admins.show', $admin) }}"
                                                class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.admins.edit', $admin) }}"
                                                class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if ($admin->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this admin?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $admins->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No admin users found</h5>
                    <p class="text-muted">Create your first admin user to get started.</p>
                    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Admin
                    </a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .avatar-sm {
            height: 2.5rem;
            width: 2.5rem;
        }

        .avatar-title {
            align-items: center;
            background-color: #6c757d;
            color: #fff;
            display: flex;
            font-size: 0.875rem;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }
    </style>
@endsection

{{-- resources/views/admin/activity-logs/user-logs.blade.php --}}

@extends('layouts.app')

@section('title', 'User Activity - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-circle text-primary me-2"></i>
            <span class="text-primary fw-bold">User Activity Log</span>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                User Activity
            </li>
        </ol>
    </nav>

    <!-- User Profile Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3>
                        @if($activities->first()?->causer)
                            {{ $activities->first()->causer->name }}
                        @else
                            User Activity
                        @endif
                    </h3>
                    <p class="text-muted mb-1">
                        @if($activities->first()?->causer)
                            <i class="fas fa-envelope"></i> {{ $activities->first()->causer->email }}<br>
                            <i class="fas fa-shield-alt"></i> 
                            <span class="badge bg-info">{{ $activities->first()->causer->role }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats">
                        <h5 class="text-primary">{{ $activities->total() }}</h5>
                        <small class="text-muted">Total Activities</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h6 class="text-muted small">Total Activities</h6>
                    <h3 class="text-primary">{{ $activities->total() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6 class="text-muted small">Created</h6>
                    <h3 class="text-success">
                        @php
                            echo $activities->where('event', 'created')->count();
                        @endphp
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h6 class="text-muted small">Updated</h6>
                    <h3 class="text-info">
                        @php
                            echo $activities->where('event', 'updated')->count();
                        @endphp
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h6 class="text-muted small">Deleted</h6>
                    <h3 class="text-danger">
                        @php
                            echo $activities->where('event', 'deleted')->count();
                        @endphp
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Activity Records
            </h5>
            <small>{{ $activities->total() }} total</small>
        </div>

        <div class="card-body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 18%;">
                                    <i class="fas fa-calendar"></i> Date/Time
                                </th>
                                <th style="width: 12%;">
                                    <i class="fas fa-tag"></i> Event
                                </th>
                                <th style="width: 20%;">
                                    <i class="fas fa-edit"></i> Action
                                </th>
                                <th style="width: 15%;">
                                    <i class="fas fa-cube"></i> Model
                                </th>
                                <th style="width: 20%;">
                                    <i class="fas fa-info-circle"></i> Details
                                </th>
                                <th style="width: 15%;">
                                    <i class="fas fa-cog"></i> Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                            <tr>
                                <td>
                                    <small class="text-muted">
                                        {{ $activity->created_at->format('Y-m-d') }}<br>
                                        <code>{{ $activity->created_at->format('H:i:s') }}</code>
                                    </small>
                                </td>

                                <td>
                                    @php
                                        $eventColor = match($activity->event ?? 'default') {
                                            'created' => 'success',
                                            'updated' => 'info',
                                            'deleted' => 'danger',
                                            'restored' => 'warning',
                                            'login' => 'primary',
                                            'failed_login' => 'danger',
                                            'export' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $eventColor }}">
                                        {{ $activity->event ?? 'N/A' }}
                                    </span>
                                </td>

                                <td>
                                    <small>{{ $activity->description ?? 'N/A' }}</small>
                                </td>

                                <td>
                                    @if($activity->subject_type)
                                        <small>
                                            <strong>{{ class_basename($activity->subject_type) }}</strong><br>
                                            <code>#{{ $activity->subject_id }}</code>
                                        </small>
                                    @else
                                        <em class="text-muted">-</em>
                                    @endif
                                </td>

                                <td>
                                    @if($activity->properties && $activity->properties->has('attributes'))
                                        <small class="text-muted">
                                            {{ count($activity->properties->get('attributes')) }} field(s) changed
                                        </small>
                                    @elseif($activity->properties && $activity->properties->has('ip_address'))
                                        <small class="text-muted">
                                            <i class="fas fa-globe"></i>
                                            {{ $activity->properties['ip_address'] }}
                                        </small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.activity-logs.show', $activity->id) }}" 
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    @if($activity->subject_type && $activity->subject_id)
                                        <a href="{{ route('admin.activity-logs.for-model', [
                                            'modelType' => $activity->subject_type,
                                            'modelId' => $activity->subject_id
                                        ]) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($activities->hasPages())
                <nav aria-label="pagination" class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing <strong>{{ $activities->firstItem() }}</strong> to 
                            <strong>{{ $activities->lastItem() }}</strong> of 
                            <strong>{{ $activities->total() }}</strong> activities
                        </div>
                        <div>
                            {{ $activities->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </nav>
                @endif

            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No activities found for this user</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer Navigation -->
    <div class="mt-4 mb-4">
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to All Logs
        </a>
    </div>

</div>

@endsection
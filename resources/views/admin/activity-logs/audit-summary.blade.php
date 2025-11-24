{{-- resources/views/admin/activity-logs/audit-summary.blade.php --}}

@extends('layouts.app')

@section('title', 'Audit Summary - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-chart-bar text-primary me-2"></i>
            <span class="text-primary fw-bold">Audit Summary Report</span>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Audit Summary</li>
        </ol>
    </nav>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.audit-summary') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" name="from" id="from" class="form-control" 
                        value="{{ $from->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" name="to" id="to" class="form-control" 
                        value="{{ $to->format('Y-m-d') }}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-logs.audit-summary') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Total Activities</h6>
                            <h2 class="text-primary mb-0">{{ $summary['total_activities'] }}</h2>
                        </div>
                        <div class="text-primary" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Failed Logins</h6>
                            <h2 class="text-warning mb-0">{{ $summary['failed_attempts']->count() }}</h2>
                        </div>
                        <div class="text-warning" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Privilege Changes</h6>
                            <h2 class="text-danger mb-0">{{ $summary['privilege_changes'] }}</h2>
                        </div>
                        <div class="text-danger" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-0">Active Users</h6>
                            <h2 class="text-info mb-0">{{ $summary['by_user']->count() }}</h2>
                        </div>
                        <div class="text-info" style="font-size: 2rem; opacity: 0.3;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities by Event Type -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Activities by Event Type</h5>
                </div>
                <div class="card-body">
                    @if($summary['by_event']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Event Type</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = $summary['by_event']->sum('count');
                                    @endphp
                                    @foreach($summary['by_event'] as $event)
                                        <tr>
                                            <td>
                                                <span class="badge bg-info">{{ $event->event ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-end">{{ $event->count }}</td>
                                            <td class="text-end">
                                                {{ round(($event->count / $total) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center my-4">No activities found</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Activities by User -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Activities by User</h5>
                </div>
                <div class="card-body">
                    @if($summary['by_user']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalByUser = $summary['by_user']->sum('count');
                                    @endphp
                                    @foreach($summary['by_user'] as $user)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.activity-logs.by-user', $user->causer_id) }}">
                                                    {{ $user->causer->name ?? 'Unknown' }}
                                                </a>
                                            </td>
                                            <td class="text-end">{{ $user->count }}</td>
                                            <td class="text-end">
                                                {{ round(($user->count / $totalByUser) * 100, 1) }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center my-4">No user activities found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Activities by Model Type -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-cube"></i> Activities by Model Type</h5>
                </div>
                <div class="card-body">
                    @if($summary['by_model']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Model</th>
                                        <th class="text-end">Count</th>
                                        <th class="text-end">Percentage</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalByModel = $summary['by_model']->sum('count');
                                    @endphp
                                    @foreach($summary['by_model'] as $model)
                                        @php
                                            $percentage = round(($model->count / $totalByModel) * 100, 1);
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ class_basename($model->subject_type) }}</strong>
                                            </td>
                                            <td class="text-end">{{ $model->count }}</td>
                                            <td class="text-end">{{ $percentage }}%</td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="width: {{ $percentage }}%">
                                                        {{ $percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center my-4">No model activities found</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Login Attempts (Security Alert) -->
    @if($summary['failed_attempts']->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lock"></i> Security Alert: Multiple Failed Login Attempts
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" role="alert">
                        <strong>ISO 27001 A.8.16 Alert:</strong> Multiple failed login attempts detected for {{ $summary['failed_attempts']->count() }} user(s).
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Failed Attempts</th>
                                    <th>Last Attempt</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['failed_attempts'] as $attempt)
                                    <tr>
                                        <td>{{ $attempt->causer_id }}</td>
                                        <td><span class="badge bg-danger">{{ $attempt->count }} attempts</span></td>
                                        <td>Recent</td>
                                        <td>
                                            <a href="{{ route('admin.activity-logs.by-user', $attempt->causer_id) }}" 
                                                class="btn btn-sm btn-outline-danger">
                                                View Details
                                            </a>
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
    @endif

    <!-- Export & Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Logs
                    </a>
                    <a href="{{ route('admin.activity-logs.export', ['date_from' => $from->format('Y-m-d'), 'date_to' => $to->format('Y-m-d')]) }}" 
                        class="btn btn-success">
                        <i class="fas fa-download"></i> Export to CSV
                    </a>
                    <a href="{{ route('admin.activity-logs.compliance-report') }}" 
                        class="btn btn-info">
                        <i class="fas fa-file-pdf"></i> Compliance Report
                    </a>
                    <button onclick="window.print()" class="btn btn-warning">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="alert alert-info" role="alert">
        <strong>ISO 27001 Compliance Note:</strong> This audit summary is part of the organization's activity logging and monitoring 
        requirements under ISO 27001 standards (Controls A.8.15 and A.8.16). All data is restricted to authorized personnel only.
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff !important;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
</style>

@endsection
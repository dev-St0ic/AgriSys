{{-- resources/views/admin/activity-logs/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Activity Logs - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-history text-primary me-2"></i>
        <span class="text-primary fw-bold">Activity Logs</span>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header with Quick Actions -->
    <div class="row mb-4">
        <div class="col">
            <h2>Activity Logs</h2>
            <p class="text-muted small">ISO 27001 Compliant Activity Logging & Monitoring (Controls A.8.15, A.8.16)</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-success" title="Export filtered logs">
                    <i class="fas fa-download"></i> Export CSV
                </a>
                <a href="{{ route('admin.activity-logs.audit-summary') }}" class="btn btn-info" title="View audit summary dashboard">
                    <i class="fas fa-chart-bar"></i> Audit Summary
                </a>
                <a href="{{ route('admin.activity-logs.compliance-report') }}" class="btn btn-warning" title="View ISO 27001 compliance report">
                    <i class="fas fa-certificate"></i> Compliance Report
                </a>
            </div>
        </div>
    </div>

    <!-- Compliance Info Banner -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-shield-alt me-2"></i>
        <strong>Security Notice:</strong> Activity logs are restricted to superadmins only. All views, exports, and actions are tracked for audit purposes.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i> Filter Logs
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search Description</label>
                        <input type="text" name="search" id="search" class="form-control" 
                            placeholder="Search description..." value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="subject_type" class="form-label">Model Type</label>
                        <select name="subject_type" id="subject_type" class="form-control">
                            <option value="">All Models</option>
                            @foreach($subjectTypes as $type)
                                <option value="{{ $type['value'] }}" 
                                    {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" 
                            value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" 
                            value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Table Card -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Activity Records
            </h5>
            <small class="text-muted">
                Total: <strong>{{ $activities->total() }}</strong> records
            </small>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">
                                <i class="fas fa-calendar"></i> Date/Time
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-user"></i> User
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-tag"></i> Action
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-cube"></i> Model
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-edit"></i> Changes
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-cog"></i> Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    {{ $activity->created_at->format('Y-m-d') }}<br>
                                    <code>{{ $activity->created_at->format('H:i:s') }}</code>
                                </small>
                            </td>
                            
                            <td>
                                @if($activity->causer)
                                    <a href="{{ route('admin.activity-logs.by-user', $activity->causer_id) }}" 
                                        class="text-decoration-none" title="View user's activities">
                                        <i class="fas fa-user-circle me-1"></i>
                                        <strong>{{ $activity->causer->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $activity->causer->email }}</small>
                                @else
                                    <em class="text-secondary">
                                        <i class="fas fa-cogs"></i> System
                                    </em>
                                @endif
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
                                    {{ $activity->description ?? 'N/A' }}
                                </span>
                                @if($activity->event)
                                    <br>
                                    <small class="text-muted">Event: {{ $activity->event }}</small>
                                @endif
                            </td>
                            
                            <td>
                                @if($activity->subject_type)
                                    <strong>{{ class_basename($activity->subject_type) }}</strong>
                                    @if($activity->subject_id)
                                        <br>
                                        <code class="small">#{{ $activity->subject_id }}</code>
                                    @endif
                                @else
                                    <em class="text-muted">-</em>
                                @endif
                            </td>
                            
                            <td>
                                @if($activity->properties)
                                    @if($activity->properties->has('attributes'))
                                        <span class="badge bg-light text-dark">
                                            {{ count($activity->properties->get('attributes')) }} field(s) changed
                                        </span>
                                        <br>
                                        <small class="text-muted mt-2 d-block">
                                            @foreach(array_keys($activity->properties->get('attributes', [])) as $field)
                                                <i class="fas fa-arrow-right"></i> {{ $field }}<br>
                                            @endforeach
                                        </small>
                                    @elseif($activity->properties->has('ip_address'))
                                        <code class="small">
                                            <i class="fas fa-globe"></i> 
                                            {{ $activity->properties['ip_address'] }}
                                        </code>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            
                            <td>
                                <a href="{{ route('admin.activity-logs.show', $activity->id) }}" 
                                    class="btn btn-sm btn-outline-primary" title="View details">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($activity->subject_type && $activity->subject_id)
                                    <a href="{{ route('admin.activity-logs.for-model', [
                                        'modelType' => $activity->subject_type,
                                        'modelId' => $activity->subject_id
                                    ]) }}" class="btn btn-sm btn-outline-secondary" title="View model history">
                                        <i class="fas fa-history"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5;"></i>
                                <p class="mt-2">No activity logs found matching your filters</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
            <nav aria-label="Activity logs pagination" class="mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing <strong>{{ $activities->firstItem() }}</strong> to 
                        <strong>{{ $activities->lastItem() }}</strong> of 
                        <strong>{{ $activities->total() }}</strong> results
                    </div>
                    <div>
                        {{ $activities->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </nav>
            @endif
        </div>
    </div>

    <!-- Management Actions Card -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-tools"></i> Log Management
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6>Archive Old Logs</h6>
                    <p class="text-muted small">Archive logs older than specified days for long-term storage (ISO A.8.15)</p>
                    <form method="POST" action="{{ route('admin.activity-logs.archive') }}" class="d-flex gap-2">
                        @csrf
                        <input type="number" name="days" value="180" min="90" class="form-control" 
                            style="width: 100px;" placeholder="Days">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Archive logs older than this many days?')">
                            <i class="fas fa-archive"></i> Archive
                        </button>
                    </form>
                </div>

                <div class="col-md-6 mb-3">
                    <h6>Delete Archived Logs</h6>
                    <p class="text-muted small">Permanently delete logs exceeding retention period (minimum 90 days)</p>
                    <form method="POST" action="{{ route('admin.activity-logs.clear') }}" class="d-flex gap-2">
                        @csrf
                        @method('DELETE')
                        <input type="number" name="days" value="365" min="90" class="form-control" 
                            style="width: 100px;" placeholder="Days">
                        <button type="submit" class="btn btn-danger" 
                            onclick="return confirm('Permanently delete logs older than this many days? This cannot be undone!')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-12">
                    <h6>Quick Reports</h6>
                    <p class="text-muted small mb-3">Generate compliance and audit reports</p>
                    <a href="{{ route('admin.activity-logs.audit-summary', ['from' => now()->subDays(30)->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}" 
                        class="btn btn-info me-2">
                        <i class="fas fa-chart-line"></i> Last 30 Days Summary
                    </a>
                    <a href="{{ route('admin.activity-logs.audit-summary', ['from' => now()->subDays(7)->format('Y-m-d'), 'to' => now()->format('Y-m-d')]) }}" 
                        class="btn btn-info me-2">
                        <i class="fas fa-calendar-week"></i> Last 7 Days Summary
                    </a>
                    <a href="{{ route('admin.activity-logs.compliance-report') }}" 
                        class="btn btn-warning">
                        <i class="fas fa-file-certificate"></i> Full Compliance Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ISO 27001 Compliance Info -->
    <div class="alert alert-success mt-4" role="alert">
        <h6 class="alert-heading">
            <i class="fas fa-certificate"></i> ISO 27001 Compliance Status
        </h6>
        <div class="row">
            <div class="col-md-6">
                <small>
                    <i class="fas fa-check-circle text-success"></i> <strong>Control A.8.15:</strong> Activity logging enabled<br>
                    <i class="fas fa-check-circle text-success"></i> <strong>Control A.8.16:</strong> Superadmin-only access<br>
                    <i class="fas fa-check-circle text-success"></i> <strong>Control A.12.4.1:</strong> Event logging active<br>
                </small>
            </div>
            <div class="col-md-6">
                <small>
                    <i class="fas fa-check-circle text-success"></i> <strong>Retention:</strong> 90+ days minimum<br>
                    <i class="fas fa-check-circle text-success"></i> <strong>Archive:</strong> Secure storage enabled<br>
                    <i class="fas fa-check-circle text-success"></i> <strong>View Tracking:</strong> All views logged<br>
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary { border-left: 4px solid #007bff !important; }
    .border-left-success { border-left: 4px solid #28a745 !important; }
    .border-left-warning { border-left: 4px solid #ffc107 !important; }
    .border-left-danger { border-left: 4px solid #dc3545 !important; }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,.05);
    }
</style>

@endsection
{{-- resources/views/admin/archive/audit-log.blade.php --}}
@extends('layouts.app')

@section('title', 'Archive Audit Log - AgriSys Admin')
@section('page-icon', 'fas fa-history')
@section('page-title', 'Archive Audit Log')

@section('content')

<div class="alert alert-info border-0 mb-4" style="border-left:5px solid #0d6efd !important;">
    <strong><i class="fas fa-history me-2"></i>Immutable Audit Trail</strong>
    <p class="mb-0 small text-muted mt-1">
        This log records every action performed on archived records. It cannot be modified or deleted.
        All entries include the user, IP address, role, and timestamp.
    </p>
</div>

{{-- Filters --}}
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.archive.audit-log') }}">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        <option value="archived"          {{ request('action') == 'archived' ? 'selected' : '' }}>Archived</option>
                        <option value="viewed"            {{ request('action') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                        <option value="exported"          {{ request('action') == 'exported' ? 'selected' : '' }}>Exported</option>
                        <option value="disposal_approved" {{ request('action') == 'disposal_approved' ? 'selected' : '' }}>Disposal Approved</option>
                        <option value="disposal_revoked"  {{ request('action') == 'disposal_revoked' ? 'selected' : '' }}>Disposal Revoked</option>
                        <option value="disposed"          {{ request('action') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        <option value="integrity_check"   {{ request('action') == 'integrity_check' ? 'selected' : '' }}>Integrity Check</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="archive_id" class="form-control form-control-sm"
                           placeholder="Archive ID..."
                           value="{{ request('archive_id') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.archive.audit-log') }}" class="btn btn-secondary btn-sm">Clear</a>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('admin.archive.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to Archive
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Audit Log Table --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history me-2"></i>Audit Log Entries
            <span class="badge bg-primary ms-2">{{ $logs->total() }}</span>
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:80px;">ID</th>
                        <th class="text-center">Action</th>
                        <th>Archive Reference</th>
                        <th>Performed By</th>
                        <th class="text-center">Role</th>
                        <th>IP Address</th>
                        <th>Notes</th>
                        <th class="text-center">Performed At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-center">
                                <small class="text-muted">#{{ $log->id }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $log->action_badge_color }}">
                                    {{ $log->action_label }}
                                </span>
                            </td>
                            <td>
                                @if($log->archive)
                                    <code class="text-primary small">
                                        {{ $log->archive->archive_reference_number }}
                                    </code>
                                    <br>
                                    <small class="text-muted">{{ $log->archive->item_name }}</small>
                                @else
                                    <small class="text-muted">Archive #{{ $log->archive_id }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $log->performedBy?->name ?? 'System' }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary small">
                                    {{ ucfirst($log->performed_by_role ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted font-monospace">
                                    {{ $log->performed_by_ip ?? '—' }}
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $log->notes ?? '—' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">
                                    {{ $log->performed_at->format('M d, Y') }}<br>
                                    <span style="font-size:0.7rem;">
                                        {{ $log->performed_at->format('h:i A') }}
                                    </span>
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-history fa-3x mb-3" style="opacity:0.2;"></i>
                                <p>No audit log entries found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@endsection
@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-history"></i> Audit Logs</h2>
            <p class="text-muted small">Complete record of all system activity</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error:</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Search & Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search What Changed</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                        placeholder="e.g., 'application', 'status'" value="{{ request('search') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Action Type</label>
                    <select name="event" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">From Date</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">To Date</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row align-items-center g-0">
                <div class="col">
                    <h6 class="mb-0"><i class="fas fa-list"></i> Activity Records</h6>
                </div>
                <div class="col-auto">
                    <small class="text-muted">Total: <strong>{{ $activities->total() }}</strong></small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 18%;">Date & Time</th>
                        <th style="width: 18%;">User</th>
                        <th style="width: 12%;">Role</th>
                        <th style="width: 12%;">Action</th>
                        <th style="width: 28%;">What Changed</th>
                        <th style="width: 12%;" class="text-center">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr class="align-middle">
                        <td>
                            <div class="small fw-bold">{{ $activity->created_at->format('M d, Y') }}</div>
                            <code class="text-muted" style="font-size: 11px;">{{ $activity->created_at->format('H:i:s') }}</code>
                        </td>
                        <td>
                            @php
                                // Get the ACTUAL user who performed the action
                                $actionUser = $activity->causer;
                                $actionUserName = 'System';
                                $actionUserEmail = 'N/A';
                            @endphp
                            
                            @if($actionUser)
                                <div class="small fw-bold">{{ $actionUser->name }}</div>
                                <div class="text-muted small">{{ $actionUser->email }}</div>
                            @else
                                <em class="text-muted small">System</em>
                            @endif
                        </td>
                        <td>
                            @php
                                // Show the ACTUAL user's role, not the current superadmin
                                $roleBg = 'secondary';
                                $roleText = 'System';
                                
                                if ($actionUser) {
                                    $roleText = ucfirst($actionUser->role ?? 'user');
                                    
                                    if ($actionUser->role === 'superadmin') {
                                        $roleBg = 'danger';
                                    } elseif ($actionUser->role === 'admin') {
                                        $roleBg = 'warning';
                                    } elseif ($actionUser->role === 'user') {
                                        $roleBg = 'info';
                                    }
                                }
                            @endphp
                            <span class="badge bg-{{ $roleBg }}">{{ $roleText }}</span>
                        </td>
                        <td>
                            @php
                                $badgeColor = match($activity->event) {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    default => 'secondary'
                                };
                                $badgeIcon = match($activity->event) {
                                    'created' => 'fa-plus-circle',
                                    'updated' => 'fa-edit',
                                    'deleted' => 'fa-trash',
                                    default => 'fa-circle'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">
                                <i class="fas {{ $badgeIcon }}"></i> {{ ucfirst($activity->event) }}
                            </span>
                        </td>
                        <td>
                            <small class="text-dark">{{ $activity->description }}</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="showDetails({{ $activity->id }})"
                                    data-bs-toggle="tooltip" 
                                    title="View details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            <strong>No activity logs found</strong>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="card-footer bg-light border-top">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    {{-- Previous Page Link --}}
                    @if ($activities->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">« Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $activities->previousPageUrl() }}">« Previous</a></li>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $current = $activities->currentPage();
                        $last = $activities->lastPage();
                    @endphp
                    
                    @for ($i = max(1, $current - 2); $i <= min($last, $current + 2); $i++)
                        @if ($i == $current)
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $activities->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($activities->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $activities->nextPageUrl() }}">Next »</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next »</span></li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> Activity Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        color: #666;
    }
</style>

<script>
function showDetails(id) {
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    const content = document.getElementById('modalContent');
    
    fetch(`/admin/activity-logs/${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const d = data.data;
                
                let actionColor = 'success';
                if (d.action === 'Updated') actionColor = 'info';
                if (d.action === 'Deleted') actionColor = 'danger';
                
                content.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-5">
                                    <small class="text-muted d-block">DATE & TIME</small>
                                    <strong>${d.date}</strong>
                                </div>
                                <div class="col-7">
                                    <small class="text-muted d-block">ACTION</small>
                                    <span class="badge bg-${actionColor}"><i class="fas fa-circle"></i> ${d.action}</span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-2">
                        
                        <div class="col-12">
                            <small class="text-muted d-block">WHO DID IT</small>
                            <strong>${d.user}</strong>
                            <div class="small text-muted">${d.email}</div>
                            <span class="badge bg-secondary mt-1">${d.role}</span>
                        </div>
                        
                        <div class="col-12">
                            <small class="text-muted d-block">WHAT CHANGED</small>
                            <code class="d-block p-2 bg-light rounded">${d.description}</code>
                        </div>
                        
                        <div class="col-6">
                            <small class="text-muted d-block">RECORD TYPE</small>
                            <code>${d.model}</code>
                        </div>
                        
                        <div class="col-6">
                            <small class="text-muted d-block">IP ADDRESS</small>
                            <code>${d.ip}</code>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger mb-0">Failed to load details</div>';
            }
            modal.show();
        })
        .catch(err => {
            content.innerHTML = '<div class="alert alert-danger mb-0">Error loading details</div>';
            modal.show();
        });
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

@endsection
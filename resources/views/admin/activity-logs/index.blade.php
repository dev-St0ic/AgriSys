@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h2><i class="fas fa-history"></i> Audit Logs</h2>
            <p class="text-muted small">View system activity and user actions</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error:</strong> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filter & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="row g-3">
                
                <div class="col-md-4">
                    <label class="form-label">Search Description</label>
                    <input type="text" name="search" class="form-control" 
                        placeholder="Search..." value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select name="event" class="form-select">
                        <option value="">All Actions</option>
                        <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-success w-100">
                        <i class="fas fa-download"></i> CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list"></i> Activity Records</h6>
            <small class="text-muted">Total: <strong>{{ $activities->total() }}</strong></small>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 18%;">Date & Time</th>
                        <th style="width: 20%;">User</th>
                        <th style="width: 10%;">Action</th>
                        <th style="width: 25%;">Description</th>
                        <th style="width: 12%;">Type</th>
                        <th style="width: 5%;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td>
                            <small>{{ $activity->created_at->format('M d, Y') }}</small><br>
                            <code style="font-size: 11px;">{{ $activity->created_at->format('H:i:s') }}</code>
                        </td>
                        <td>
                            @php
                                // Determine actual user for self-service actions
                                $user = $activity->causer;
                                $userName = 'System';
                                $userEmail = 'N/A';
                                
                                if (!$user && $activity->subject_type === 'App\Models\UserRegistration' && $activity->subject_id) {
                                    $userReg = \App\Models\UserRegistration::find($activity->subject_id);
                                    if ($userReg) {
                                        $userName = $userReg->full_name ?? $userReg->username;
                                        $userEmail = $userReg->username;
                                    }
                                } elseif ($user) {
                                    $userName = $user->name;
                                    $userEmail = $user->email;
                                }
                            @endphp
                            
                            @if($userName !== 'System')
                                <strong>{{ $userName }}</strong><br>
                                <small class="text-muted">{{ $userEmail }}</small>
                            @else
                                <em class="text-muted">{{ $userName }}</em>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeColor = match($activity->event) {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    'login' => 'primary',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeColor }}">
                                {{ ucfirst($activity->event) }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $activity->description }}</small>
                        </td>
                        <td>
                            <small class="text-muted">{{ $activity->subject_type ? class_basename($activity->subject_type) : '-' }}</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary" onclick="showDetails({{ $activity->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                            No activity logs found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($activities->hasPages())
        <div class="card-footer bg-light">
            <nav>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    @if ($activities->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">« Prev</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $activities->previousPageUrl() }}">« Prev</a></li>
                    @endif

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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Activity Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.85rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
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
                content.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <strong>Date:</strong> ${data.data.date}
                        </div>
                        <div class="col-12">
                            <strong>User:</strong> ${data.data.user} (${data.data.email})
                        </div>
                        <div class="col-12">
                            <strong>Action:</strong> <span class="badge bg-info">${data.data.event}</span>
                        </div>
                        <div class="col-12">
                            <strong>Description:</strong><br>
                            <code>${data.data.description}</code>
                        </div>
                        <div class="col-12">
                            <strong>Type:</strong> ${data.data.subject_type || 'N/A'}
                        </div>
                        <div class="col-12">
                            <strong>IP Address:</strong> <code>${data.data.ip}</code>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="alert alert-danger">Failed to load details</div>';
            }
            modal.show();
        })
        .catch(err => {
            content.innerHTML = '<div class="alert alert-danger">Error loading details</div>';
            modal.show();
        });
}
</script>

@endsection
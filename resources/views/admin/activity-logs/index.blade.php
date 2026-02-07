{{-- resources/views/admin/activity-logs/index.blade.php --}}

@php
    use App\Traits\ActivityLogFormatter;
@endphp

@extends('layouts.app')

@section('title', 'Audit Logs - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-history text-primary me-2"></i>
        <span class="text-primary fw-bold">Audit Logs</span>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <p class="text-muted small">
                <i class="fas fa-shield-alt me-1"></i> Restricted to Superadmins Only
            </p>
        </div>
    </div>

    <!-- Security Banner -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-lock me-2"></i>
        <strong>Security:</strong> Audit logs are restricted to superadmins. All access is tracked and logged.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filter & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label fw-semibold">Search</label>
                        <input type="text" name="search" id="search" class="form-control form-control-sm" 
                            placeholder="Search description..." value="{{ request('search') }}" oninput="autoSearch()">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="subject_type" class="form-label fw-semibold">Module</label>
                        <select name="subject_type" id="subject_type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Modules</option>
                            @foreach($subjectTypes as $type)
                                <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="event" class="form-label fw-semibold">Action</label>
                        <select name="event" id="event" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            <option value="approved" {{ request('event') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('event') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-semibold">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" 
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_to" class="form-label fw-semibold">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" 
                            value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card shadow">
        <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Audit Records
            </h6>
            <small class="text-muted">
                <strong>{{ $activities->total() }}</strong> total logs
            </small>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 18%;">
                                <i class="fas fa-calendar-alt"></i> Date & Time
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-user"></i> User
                            </th>
                            <th style="width: 12%;">
                                <i class="fas fa-cog"></i> Action
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-cube"></i> Module
                            </th>
                            <th style="width: 20%;">
                                <i class="fas fa-exchange-alt"></i> Changes
                            </th>
                            <th style="width: 10%;">
                                <i class="fas fa-info-circle"></i> Details
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <!-- Date & Time -->
                            <td>
                                <small class="text-muted d-block">
                                    {{ $activity->created_at->format('M d, Y') }}
                                </small>
                                <code class="text-monospace" style="font-size: 11px;">
                                    {{ $activity->created_at->format('H:i:s') }}
                                </code>
                            </td>
                            
                            <!-- User -->
                            <td>
                                @if($activity->causer)
                                    <div>
                                        <strong class="text-primary d-block">{{ $activity->causer->name }}</strong>
                                        <small class="text-muted">{{ $activity->causer->email }}</small>
                                    </div>
                                @else
                                    <em class="text-secondary">
                                        <i class="fas fa-cogs me-1"></i>System
                                    </em>
                                @endif
                            </td>
                            
                            <!-- Action -->
                            <td>
                                @php
                                    $eventColor = ActivityLogFormatter::getEventColor($activity->event);
                                    $eventIcon = ActivityLogFormatter::getEventIcon($activity->event);
                                @endphp
                                <span class="badge bg-{{ $eventColor }}">
                                    <i class="fas {{ $eventIcon }}"></i>
                                    {{ ucfirst($activity->event) }}
                                </span>
                            </td>
                            
                            <!-- Module/Target -->
                            <td>
                                <strong>
                                    {{ ActivityLogFormatter::getModelLabel($activity->subject_type) }}
                                </strong>
                                @if($activity->subject_id)
                                    <br>
                                    <small class="text-muted">#{{ $activity->subject_id }}</small>
                                @endif
                            </td>
                            
                            <!-- Changes Summary -->
                            <td>
                                @if($activity->properties && isset($activity->properties['attributes']))
                                    @php
                                        $changes = ActivityLogFormatter::formatChanges($activity->properties);
                                    @endphp
                                    <small class="text-muted d-block">
                                        <strong>{{ count($changes) }}</strong> field(s) changed
                                    </small>
                                    @foreach(array_slice($changes, 0, 2) as $change)
                                        <small class="d-block" style="font-size: 11px;">
                                            {{ $change['field'] }}
                                        </small>
                                    @endforeach
                                    @if(count($changes) > 2)
                                        <small class="text-muted">+{{ count($changes) - 2 }} more</small>
                                    @endif
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            
                            <!-- Actions -->
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" 
                                    onclick="showActivityDetails({{ $activity->id }})" 
                                    title="View full details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-2x mb-3" style="opacity: 0.5;"></i>
                                <p>No activity logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
            <nav class="mt-4" aria-label="Activity logs pagination">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        Showing <strong>{{ $activities->firstItem() }}</strong> to 
                        <strong>{{ $activities->lastItem() }}</strong> of 
                        <strong>{{ $activities->total() }}</strong>
                    </small>
                </div>
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    {{-- Previous Link --}}
                    @if ($activities->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">« Previous</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $activities->previousPageUrl() }}">« Previous</a>
                        </li>
                    @endif

                    {{-- Page Numbers --}}
                    @php
                        $currentPage = $activities->currentPage();
                        $lastPage = $activities->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active">
                                <span class="page-link bg-primary border-primary">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $activities->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Next Link --}}
                    @if ($activities->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $activities->nextPageUrl() }}">Next »</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link">Next »</span>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title w-100 text-center">
                    <i class="fas fa-info-circle me-2"></i>Audit Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" id="activityDetailsContent">
                <!-- Content loaded here -->
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .form-control-sm, .form-select-sm {
        border-radius: 6px;
        border: 1px solid #e3e6f0;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.6rem;
    }
    
    code {
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 4px;
    }
</style>

<script>
    let searchTimeout;

    // Auto search functionality
    function autoSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    }

    // Submit filter form
    function submitFilterForm() {
        document.getElementById('filterForm').submit();
    }

    // Show activity details modal
    function showActivityDetails(activityId) {
        const modal = new bootstrap.Modal(document.getElementById('activityDetailsModal'));
        const content = document.getElementById('activityDetailsContent');
        
        content.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        modal.show();

        fetch(`/admin/activity-logs/${activityId}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const activity = data.data;
                    content.innerHTML = buildActivityDetailsHTML(activity);
                } else {
                    throw new Error(data.message || 'Failed to load activity');
                }
            })
            .catch(error => {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading audit details
                    </div>
                `;
            });
    }

    // Build activity details HTML
    function buildActivityDetailsHTML(activity) {
        const eventColor = getEventColor(activity.event);
        const statusBadge = `<span class="badge bg-${eventColor}">${activity.event.toUpperCase()}</span>`;
        
        let changesHTML = '';
        if (activity.properties && activity.properties.attributes) {
            const changes = activity.properties.attributes;
            const oldValues = activity.properties.old || {};
            
            changesHTML = '<div class="mt-3"><strong>Changes:</strong><table class="table table-sm mt-2">';
            
            for (const [field, newValue] of Object.entries(changes)) {
                const oldValue = oldValues[field] || '-';
                changesHTML += `
                    <tr>
                        <td class="text-muted">${formatFieldName(field)}</td>
                        <td><code>${oldValue}</code></td>
                        <td><i class="fas fa-arrow-right mx-2 text-muted"></i></td>
                        <td><code>${newValue}</code></td>
                    </tr>
                `;
            }
            
            changesHTML += '</table></div>';
        }

        return `
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Date & Time</small>
                                    <strong>${activity.created_at}</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">User</small>
                                    <strong>${activity.causer_name || 'System'}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Action</small>
                                    ${statusBadge}
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Module</small>
                                    <strong>${activity.subject_type_label}</strong>
                                    ${activity.subject_id ? `<br><small class="text-muted">#${activity.subject_id}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            ${activity.properties && activity.properties.ip_address ? `
                                <small class="text-muted d-block">IP Address</small>
                                <code>${activity.properties.ip_address}</code>
                                <br><small class="text-muted mt-2 d-block">User Agent</small>
                                <small><code style="font-size: 9px;">${activity.properties.user_agent || 'N/A'}</code></small>
                            ` : ''}
                        </div>
                    </div>
                </div>

                ${changesHTML}
            </div>
        `;
    }

    // Helper functions
    function getEventColor(event) {
        const colors = {
            'created': 'success',
            'updated': 'info',
            'deleted': 'danger',
            'approved': 'success',
            'rejected': 'danger',
            'login': 'primary'
        };
        return colors[event] || 'secondary';
    }

    function formatFieldName(field) {
        return field
            .replace(/_/g, ' ')
            .replace(/([A-Z])/g, ' $1')
            .replace(/^./, str => str.toUpperCase())
            .trim();
    }

    // CSRF token helper
    function getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // Ensure AJAX requests have CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': getCSRFToken()
        }
    });
</script>

@endsection
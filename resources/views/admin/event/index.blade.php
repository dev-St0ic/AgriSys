{{-- resources/views/admin/event/index.blade.php --}}
{{-- Event Management Admin Page - With Archive Functionality --}}

@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt me-2"></i>Manage Events</h2>
            <a href="{{ route('admin.event.archived') }}" class="btn btn-info">
                <i class="fas fa-archive me-2"></i>View Archive ({{ $stats['archived'] ?? 0 }})
            </a>
        </div>

        <!-- Session Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Events
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                Active
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Inactive
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
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
                                Archived
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['archived'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.event.index') }}" id="filterForm">
                <!-- Hidden date inputs -->
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                <div class="row">
                    <div class="col-md-2">
                        <select name="category" class="form-select form-select-sm" onchange="document.getElementById('filterForm').submit();">
                            <option value="">All Events</option>
                            <option value="announcement" {{ request('category') == 'announcement' ? 'selected' : '' }}>Announcements</option>
                            <option value="ongoing" {{ request('category') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="upcoming" {{ request('category') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="past" {{ request('category') == 'past' ? 'selected' : '' }}>Past</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search title, description, location..." 
                                value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                            <button class="btn btn-outline-secondary" type="submit" title="Search"
                                id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                            data-bs-target="#eventDateFilterModal">
                            <i class="fas fa-calendar-alt me-1"></i>Date Filter
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('admin.event.index') }}" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>

                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#createEventModal">
                            <i class="fas fa-plus me-1"></i>Add Event
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="eventDateFilterModal" tabindex="-1" aria-labelledby="eventDateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="eventDateFilterModalLabel">
                        <i class="fas fa-calendar-alt me-2"></i>Select Date Range
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <!-- Date Range Inputs -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-calendar-plus me-2"></i>Custom Date Range
                                    </h6>
                                    <div class="mb-3">
                                        <label for="event_modal_date_from" class="form-label">From Date</label>
                                        <input type="date" id="event_modal_date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="event_modal_date_to" class="form-label">To Date</label>
                                        <input type="date" id="event_modal_date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100"
                                        onclick="applyEventCustomDateRange()">
                                        <i class="fas fa-check me-2"></i>Apply Custom Range
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Date Presets -->
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-clock me-2"></i>Quick Presets
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="setEventDateRangeModal('today')">
                                            <i class="fas fa-calendar-day me-2"></i>Today
                                        </button>
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="setEventDateRangeModal('week')">
                                            <i class="fas fa-calendar-week me-2"></i>This Week
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="setEventDateRangeModal('month')">
                                            <i class="fas fa-calendar me-2"></i>This Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="setEventDateRangeModal('year')">
                                            <i class="fas fa-calendar-alt me-2"></i>This Year
                                        </button>
                                        <hr class="my-3">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="clearEventDateRangeModal()">
                                            <i class="fas fa-calendar-times me-2"></i>Clear Date Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Filter Display -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info mb-0" id="currentEventDateFilter">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="eventDateFilterStatus">
                                    @if (request('date_from') || request('date_to'))
                                        Current filter:
                                        @if (request('date_from'))
                                            From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                        @endif
                                        @if (request('date_to'))
                                            To {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @endif
                                    @else
                                        No date filter applied - showing all events
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Events List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Title</th>
                                <th style="width: 120px;">Category</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Order</th>
                                <th style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr class="event-row" data-category="{{ $event->category }}">
                                    <td>
                                        @if ($event->image_path)
                                            <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $event->title }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($event->description, 60) }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $colors = ['announcement' => 'info', 'ongoing' => 'warning', 'upcoming' => 'secondary', 'past' => 'danger'];
                                            $color = $colors[$event->category] ?? 'primary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($event->category) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $event->is_active ? 'success' : 'secondary' }}">
                                            {{ $event->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $event->display_order }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewEvent({{ $event->id }})" title="View" data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-primary" onclick="editEvent({{ $event->id }})" title="Edit" data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-{{ $event->is_active ? 'warning' : 'success' }}" onclick="toggleEvent({{ $event->id }})" title="Toggle Status" data-bs-toggle="tooltip">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button class="btn btn-info" onclick="archiveEvent({{ $event->id }})" title="Archive" data-bs-toggle="tooltip">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteEvent({{ $event->id }})" title="Delete" data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3" style="display: block;"></i>
                                        <p class="text-muted">No events found. Create your first event.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($events->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE EVENT MODAL -->
    <div class="modal fade" id="createEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="createEventForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="">Select category...</option>
                                    <option value="announcement">Announcement</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date/Time</label>
                            <input type="text" name="date" class="form-control" placeholder="e.g., November 15, 2025 | 6:00 AM">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" placeholder="Event location">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Max 5MB (JPEG, PNG, GIF, WebP)</small>
                        </div>

                        <hr>
                        <h6 class="mb-3">Event Details (Optional)</h6>

                        <div id="detailsContainer">
                            <div class="detail-row mb-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control form-control-sm detail-key" placeholder="Detail name">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control detail-value" placeholder="Detail value">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailRow(this)">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-secondary" onclick="addDetailRow()">
                            <i class="fas fa-plus me-1"></i>Add Detail
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">Create Event</span>
                            <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VIEW EVENT MODAL  -->
    <div class="modal fade" id="viewEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="eventDetailsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- EDIT EVENT MODAL -->
    <div class="modal fade" id="editEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editEventForm" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_event_id" name="event_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" id="edit_title" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea id="edit_description" name="description" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select id="edit_category" name="category" class="form-select" required>
                                    <option value="">Select category...</option>
                                    <option value="announcement">Announcement</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="past">Past</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select id="edit_is_active" name="is_active" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date/Time</label>
                            <input type="text" id="edit_date" name="date" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" id="edit_location" name="location" class="form-control">
                        </div>

                        <div class="mb-3">
                            <div id="current_event_image" class="mb-2"></div>
                            <label class="form-label">Change Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Max 5MB (JPEG, PNG, GIF, WebP)</small>
                        </div>

                        <hr>
                        <h6 class="mb-3">Event Details</h6>

                        <div id="editDetailsContainer"></div>

                        <button type="button" class="btn btn-sm btn-secondary" onclick="addDetailRow('edit')">
                            <i class="fas fa-plus me-1"></i>Add Detail
                        </button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="btn-text">Update Event</span>
                            <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ARCHIVE EVENT MODAL -->
    <div class="modal fade" id="archiveEventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Archive Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="archiveEventForm">
                    @csrf
                    <input type="hidden" id="archive_event_id" name="event_id">
                    <div class="modal-body">
                        <p class="mb-3">You are about to archive <strong id="archive_event_name"></strong>.</p>
                        <p class="text-muted mb-3">Archived events are hidden from the landing page but can be restored later.</p>
                        <div class="form-group">
                            <label class="form-label">Reason for archiving (optional)</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="e.g., Event completed, Rescheduled, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <span class="btn-text">Archive Event</span>
                            <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Archiving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <!-- DELETE MODAL -->
    <div class="modal fade" id="deleteEventModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Permanently Delete Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">This action cannot be undone. Permanently deleting <strong id="delete_event_name"></strong> will:</p>
                    </div>
                    <ul class="mb-0">
                        <li>Remove the event from the database</li>
                        <li>Delete all associated images</li>
                        <li>Delete all event logs and history</li>
                        <li>Cannot be recovered</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDelete()" id="confirm_delete_btn">
                        <span class="btn-text">Yes, Delete Permanently</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Success</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="successMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="location.reload()">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentFilter = 'all';

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function filterEvents(category) {
            currentFilter = category;
            document.querySelectorAll('.event-row').forEach(row => {
                if (category === 'all' || row.getAttribute('data-category') === category) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            document.querySelectorAll('.card-body .btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function addDetailRow(type = 'create') {
            const container = type === 'create' ? document.getElementById('detailsContainer') : document.getElementById('editDetailsContainer');
            const row = document.createElement('div');
            row.className = 'detail-row mb-2';
            row.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-sm detail-key" placeholder="Detail name">
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control detail-value" placeholder="Detail value">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailRow(this)">Remove</button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(row);
        }

        function removeDetailRow(btn) {
            btn.closest('.detail-row').remove();
        }

        function collectDetails(container) {
            const details = {};
            container.querySelectorAll('.detail-row').forEach(row => {
                const key = row.querySelector('.detail-key').value.trim().toLowerCase().replace(/\s+/g, '_');
                const value = row.querySelector('.detail-value').value.trim();
                if (key && value) details[key] = value;
            });
            return details;
        }

        // Enhanced error handler for safety warnings
        function showError(message, warningType = null) {
            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });
            
            setTimeout(() => {
                const errorModal = document.getElementById('errorModal');
                const errorMessage = document.getElementById('errorMessage');
                
                // Add warning icon and styling for specific warning types
                if (warningType === 'last_active_event' || warningType === 'no_active_events') {
                    errorMessage.innerHTML = `
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle text-warning me-3" style="font-size: 2rem;"></i>
                            <div>
                                <strong>Landing Page Protection</strong>
                                <p class="mb-0 mt-2">${message}</p>
                                ${warningType === 'last_active_event' ? 
                                    '<p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>This safety feature ensures visitors always see content on the landing page.</p>' : 
                                    '<p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle me-1"></i>Create an active event to make this change.</p>'
                                }
                            </div>
                        </div>
                    `;
                    
                    // Change modal header color to warning
                    const modalHeader = errorModal.querySelector('.modal-header');
                    modalHeader.classList.remove('bg-danger');
                    modalHeader.classList.add('bg-warning', 'text-dark');
                    modalHeader.querySelector('.modal-title').textContent = 'Action Not Allowed';
                } else {
                    errorMessage.textContent = message;
                    
                    // Reset to error styling
                    const modalHeader = errorModal.querySelector('.modal-header');
                    modalHeader.classList.remove('bg-warning', 'text-dark');
                    modalHeader.classList.add('bg-danger', 'text-white');
                    modalHeader.querySelector('.modal-title').textContent = 'Error';
                }
                
                new bootstrap.Modal(errorModal).show();
            }, 300);
        }

        function showSuccess(message) {
            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });
            setTimeout(() => {
                document.getElementById('successMessage').textContent = message;
                new bootstrap.Modal(document.getElementById('successModal')).show();
            }, 300);
        }

        // View event
        async function viewEvent(eventId) {
        try {
            const response = await fetch(`/admin/events/${eventId}`, {
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!data.success) throw new Error('Failed to load event');
            
            const event = data.event;
            
            // Build details HTML similar to archived page
            let detailsHtml = '';
            if (event.details && Object.keys(event.details).length > 0) {
                detailsHtml = `
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2">Additional Details:</h6>
                        <dl class="row">
                `;
                for (const [key, value] of Object.entries(event.details)) {
                    const displayKey = key.replace(/_/g, ' ').charAt(0).toUpperCase() + key.replace(/_/g, ' ').slice(1);
                    detailsHtml += `
                        <dt class="col-sm-4">${displayKey}:</dt>
                        <dd class="col-sm-8">${value}</dd>
                    `;
                }
                detailsHtml += `
                        </dl>
                    </div>
                `;
            }

            const html = `
                <div class="row">
                    <div class="col-md-4">
                        ${event.image ? `<img src="${event.image}" alt="${event.title}" class="img-fluid rounded">` : '<div class="bg-light p-5 text-center rounded"><i class="fas fa-image fa-3x text-muted"></i></div>'}
                    </div>
                    <div class="col-md-8">
                        <h4>${event.title}</h4>
                        <p class="text-muted">${event.description}</p>
                        <dl class="row">
                            <dt class="col-sm-4">Category:</dt>
                            <dd class="col-sm-8"><span class="badge bg-info">${event.category_label}</span></dd>
                            
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8"><span class="badge bg-${event.is_active ? 'success' : 'secondary'}">${event.is_active ? 'Active' : 'Inactive'}</span></dd>
                            
                            <dt class="col-sm-4">Display Order:</dt>
                            <dd class="col-sm-8">${event.display_order || '—'}</dd>
                            
                            <dt class="col-sm-4">Date:</dt>
                            <dd class="col-sm-8">${event.date || '—'}</dd>
                            
                            <dt class="col-sm-4">Location:</dt>
                            <dd class="col-sm-8">${event.location || '—'}</dd>
                            
                            <dt class="col-sm-4">Created:</dt>
                            <dd class="col-sm-8"><small>${new Date(event.created_at).toLocaleDateString()}</small></dd>
                            
                            <dt class="col-sm-4">Updated:</dt>
                            <dd class="col-sm-8"><small>${new Date(event.updated_at).toLocaleDateString()}</small></dd>
                        </dl>
                        ${detailsHtml}
                    </div>
                </div>
            `;
            document.getElementById('eventDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewEventModal')).show();
        } catch (error) {
            showError(error.message);
        }
    }

        // Edit event
        async function editEvent(eventId) {
            try {
                const response = await fetch(`/admin/events/${eventId}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!data.success) throw new Error('Failed to load event');
                const event = data.event;

                document.getElementById('edit_event_id').value = event.id;
                document.getElementById('edit_title').value = event.title;
                document.getElementById('edit_description').value = event.description;
                document.getElementById('edit_category').value = event.category;
                document.getElementById('edit_is_active').value = event.is_active ? '1' : '0';
                document.getElementById('edit_date').value = event.date || '';
                document.getElementById('edit_location').value = event.location || '';

                const imagePreview = document.getElementById('current_event_image');
                if (event.image) {
                    imagePreview.innerHTML = `<label class="form-label">Current Image:</label><br><img src="${event.image}" alt="${event.title}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">`;
                } else {
                    imagePreview.innerHTML = '';
                }

                const detailsContainer = document.getElementById('editDetailsContainer');
                detailsContainer.innerHTML = '';
                if (event.details && Object.keys(event.details).length > 0) {
                    for (const [key, value] of Object.entries(event.details)) {
                        const row = document.createElement('div');
                        row.className = 'detail-row mb-2';
                        row.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control form-control-sm detail-key" value="${key}">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control detail-value" value="${value}">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailRow(this)">Remove</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        detailsContainer.appendChild(row);
                    }
                }

                new bootstrap.Modal(document.getElementById('editEventModal')).show();
            } catch (error) {
                showError(error.message);
            }
        }

        // Archive event
        function archiveEvent(eventId) {
            try {
                const row = document.querySelector(`[data-event-id="${eventId}"]`) || event.target.closest('.event-row');
                const eventTitle = row ? row.querySelector('strong').textContent : 'Event';
                
                document.getElementById('archive_event_id').value = eventId;
                document.getElementById('archive_event_name').textContent = eventTitle;
                new bootstrap.Modal(document.getElementById('archiveEventModal')).show();
            } catch (error) {
                showError('Failed to prepare archive dialog');
            }
        }

        // Reset forms on modal close
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hidden.bs.modal', function() {
                this.querySelectorAll('form').forEach(form => {
                    form.classList.remove('was-validated');
                    form.reset();
                });
            });
        });


        // CREATE EVENT FORM
        document.getElementById('createEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const details = collectDetails(document.getElementById('detailsContainer'));
            formData.append('details', JSON.stringify(details));

            try {
                document.querySelector('#createEventForm .btn-text').style.display = 'none';
                document.querySelector('#createEventForm .btn-loader').style.display = 'inline';

                const response = await fetch('/admin/events', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.warning_type) {
                        throw { message: data.message, warningType: data.warning_type };
                    }
                    throw new Error(data.message || 'Failed to create event');
                }
                
                showSuccess(data.message);
            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.querySelector('#createEventForm .btn-text').style.display = 'inline';
                document.querySelector('#createEventForm .btn-loader').style.display = 'none';
            }
        });

        // EDIT EVENT FORM
        document.getElementById('editEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const eventId = document.getElementById('edit_event_id').value;
            const formData = new FormData(this);
            const details = collectDetails(document.getElementById('editDetailsContainer'));
            
            formData.append('details', JSON.stringify(details));

            try {
                document.querySelector('#editEventForm .btn-text').style.display = 'none';
                document.querySelector('#editEventForm .btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${eventId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.warning_type) {
                        throw { message: data.message, warningType: data.warning_type };
                    }
                    throw new Error(data.message || 'Failed to update event');
                }
                
                showSuccess(data.message);
            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.querySelector('#editEventForm .btn-text').style.display = 'inline';
                document.querySelector('#editEventForm .btn-loader').style.display = 'none';
            }
        });

        // ARCHIVE EVENT FORM
        document.getElementById('archiveEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const eventId = document.getElementById('archive_event_id').value;
            const reason = document.querySelector('#archiveEventForm textarea[name="reason"]').value;

            try {
                document.querySelector('#archiveEventForm .btn-text').style.display = 'none';
                document.querySelector('#archiveEventForm .btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${eventId}/archive`, {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' 
                    },
                    body: JSON.stringify({ reason: reason })
                });
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.warning_type) {
                        throw { message: data.message, warningType: data.warning_type };
                    }
                    throw new Error(data.message || 'Failed to archive event');
                }
                
                showSuccess(data.message);
            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.querySelector('#archiveEventForm .btn-text').style.display = 'inline';
                document.querySelector('#archiveEventForm .btn-loader').style.display = 'none';
            }
        });

        // TOGGLE STATUS
        async function toggleEvent(eventId) {
            try {
                const response = await fetch(`/admin/events/${eventId}/toggle-status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.warning_type) {
                        throw { message: data.message, warningType: data.warning_type };
                    }
                    throw new Error(data.message || 'Failed to update status');
                }
                
                showSuccess(data.message);
            } catch (error) {
                showError(error.message, error.warningType || null);
            }
        }

        // DELETE EVENT
        let currentDeleteEventId = null;
        function deleteEvent(eventId) {
            try {
                const row = document.querySelector(`tr[data-category]`)?.closest('tbody')?.querySelector('tr') || event.target.closest('.event-row');
                let eventTitle = 'Event';
                
                // Get event title from the row
                if (row) {
                    const titleElement = row.querySelector('strong');
                    if (titleElement) {
                        eventTitle = titleElement.textContent;
                    }
                }
                
                currentDeleteEventId = eventId;
                document.getElementById('delete_event_name').textContent = eventTitle;
                new bootstrap.Modal(document.getElementById('deleteEventModal')).show();
            } catch (error) {
                showError('Failed to prepare delete dialog');
            }
        }

        // Confirm permanent delete
        async function confirmPermanentDelete() {
            try {
                document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'none';
                document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${currentDeleteEventId}`, {
                    method: 'DELETE',
                    headers: { 
                        'X-CSRF-TOKEN': csrfToken, 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json' 
                    }
                });
                const data = await response.json();
                
                if (!response.ok) {
                    if (data.warning_type) {
                        throw { message: data.message, warningType: data.warning_type };
                    }
                    throw new Error(data.message || 'Failed to delete event');
                }
                
                showSuccess(data.message);
            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'inline';
                document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'none';
            }
        }

        // Add warning banner if only one active event exists
        document.addEventListener('DOMContentLoaded', function() {
            const activeCount = parseInt('{{ $stats["active"] ?? 0 }}');
            
            if (activeCount === 1) {
                const container = document.querySelector('.container-fluid');
                const warningBanner = document.createElement('div');
                warningBanner.className = 'alert alert-warning alert-dismissible fade show';
                warningBanner.innerHTML = `
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Landing Page Protection Active:</strong> 
                    You currently have only <strong>1 active event</strong>. 
                    This event cannot be deactivated, archived, or deleted until another event is made active. 
                    This ensures the landing page always displays content to visitors.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                container.insertBefore(warningBanner, container.firstChild.nextSibling);
            }
        });

        
        // Auto search functionality
        let searchTimeout;

        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

       // ===== EVENT DATE FILTER FUNCTIONS =====

        // Helper function to format date to YYYY-MM-DD
        function formatDateForInput(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Date range functions for event modal
        function setEventDateRangeModal(period) {
            const today = new Date();
            let startDate = new Date(today);
            let endDate = new Date(today);

            switch (period) {
                case 'today':
                    startDate = new Date(today);
                    endDate = new Date(today);
                    break;
                case 'week':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay());
                    endDate = new Date(today);
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    endDate = new Date(today);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    endDate = new Date(today);
                    break;
            }

            const startDateStr = formatDateForInput(startDate);
            const endDateStr = formatDateForInput(endDate);

            document.getElementById('event_modal_date_from').value = startDateStr;
            document.getElementById('event_modal_date_to').value = endDateStr;
        }

        function applyEventCustomDateRange() {
            const dateFrom = document.getElementById('event_modal_date_from').value;
            const dateTo = document.getElementById('event_modal_date_to').value;

            if (!dateFrom && !dateTo) {
                alert('Please select at least one date');
                return;
            }

            if (dateFrom && dateTo && dateFrom > dateTo) {
                alert('From date cannot be later than To date');
                return;
            }

            applyEventDateFilter(dateFrom, dateTo);
        }

        function applyEventDateFilter(dateFrom, dateTo) {
            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            updateEventDateFilterStatus(dateFrom, dateTo);

            const modal = bootstrap.Modal.getInstance(document.getElementById('eventDateFilterModal'));
            if (modal) {
                modal.hide();
            }

            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 300);
        }

        function clearEventDateRangeModal() {
            document.getElementById('event_modal_date_from').value = '';
            document.getElementById('event_modal_date_to').value = '';
            applyEventDateFilter('', '');
        }

        function updateEventDateFilterStatus(dateFrom, dateTo) {
            const statusElement = document.getElementById('eventDateFilterStatus');
            if (!dateFrom && !dateTo) {
                statusElement.innerHTML = 'No date filter applied - showing all events';
            } else {
                let statusText = 'Current filter: ';
                if (dateFrom) {
                    const fromDate = new Date(dateFrom).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    statusText += `From ${fromDate} `;
                }
                if (dateTo) {
                    const toDate = new Date(dateTo).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    statusText += `To ${toDate}`;
                }
                statusElement.innerHTML = statusText;
            }
        }
        console.log('✅ Admin page loaded successfully');
    </script>

    <style>
        /* statistic card */
        .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
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
        dl { margin-bottom: 0; }
        dt { font-weight: 600; color: #333; }
        dd { margin-bottom: 0.5rem; }
        .text-xs { font-size: 0.75rem; }
        .event-row { transition: all 0.2s ease; }
        .event-row:hover { background-color: #f8f9fa; }
        .badge { font-size: 0.85rem; padding: 0.5rem 0.75rem; }
        .btn-group-sm .btn { padding: 0.375rem 0.75rem; font-size: 0.85rem; }
        .detail-row { animation: slideDown 0.2s ease; }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-body { max-height: 70vh; overflow-y: auto; }
        .invalid-feedback { display: block; font-size: 0.875em; color: #dc3545; }
        .btn-group-sm {
            display: flex;
            gap: 2px;
        }
        .btn-group-sm .btn {
            flex: 1;
        }
    </style>
@endsection
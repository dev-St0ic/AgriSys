{{-- resources/views/admin/event/index.blade.php --}}
{{-- Event Management Admin Page - With Archive Functionality --}}

@extends('layouts.app')

@section('title', 'Manage Events')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-calendar-alt text-primary me-2"></i>
        <span class="text-primary fw-bold">Event Management</span>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
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
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $stats['total'] ?? 0 }}</div>
                        <div class="stat-label text-primary">Total Events</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $stats['active'] ?? 0 }}</div>
                        <div class="stat-label text-success">Active Events</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-times-circle text-warning"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $stats['inactive'] ?? 0 }}</div>
                        <div class="stat-label text-warning">Inactive Events</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card shadow h-100">
                    <div class="card-body text-center py-3">
                        <div class="stat-icon mb-2">
                            <i class="fas fa-archive text-info"></i>
                        </div>
                        <div class="stat-number mb-2">{{ $stats['archived'] ?? 0 }}</div>
                        <div class="stat-label text-info">Archived Events</div>
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
                            <select name="category" class="form-select form-select-sm"
                                onchange="document.getElementById('filterForm').submit();">
                                <option value="">All Events</option>
                                <option value="announcement" {{ request('category') == 'announcement' ? 'selected' : '' }}>
                                    Announcements</option>
                                <option value="ongoing" {{ request('category') == 'ongoing' ? 'selected' : '' }}>Ongoing
                                </option>
                                <option value="upcoming" {{ request('category') == 'upcoming' ? 'selected' : '' }}>Upcoming
                                </option>
                                <option value="past" {{ request('category') == 'past' ? 'selected' : '' }}>Past</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search title, description, location..." value="{{ request('search') }}"
                                    oninput="autoSearch()" id="searchInput">
                                <button class="btn btn-outline-secondary" type="submit" title="Search" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3">
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
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div></div>
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-day me-2"></i>Events List
                </h6>
                <div class="btn-group gap-2">
                    <a href="{{ route('admin.event.archived') }}" class="btn btn-info btn-sm me-2">
                        <i class="fas fa-archive me-2"></i>View Archive ({{ $stats['archived'] ?? 0 }})
                    </a>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#createEventModal">
                        <i class="fas fa-plus me-2"></i>Add Event
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">Image</th>
                                <th class="text-center">Title</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr data-id="{{ $event->id }}">
                                    <td>
                                        @if ($event->image_path)
                                            <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}"
                                                class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 60px; height: 60px;">
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
                                            $colors = [
                                                'announcement' => 'info',
                                                'ongoing' => 'warning',
                                                'upcoming' => 'secondary',
                                                'past' => 'danger',
                                            ];
                                            $color = $colors[$event->category] ?? 'primary';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $color }} fs-6">{{ ucfirst($event->category) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $event->is_active ? 'success' : 'secondary' }} fs-6">
                                            {{ $event->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="viewEvent({{ $event->id }})" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>

                                            <button class="btn btn-sm btn-outline-success"
                                                onclick="editEvent({{ $event->id }})" title="Edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>

                                            <button class="btn btn-sm btn-outline-info"
                                                onclick="archiveEvent({{ $event->id }})" title="Archive">
                                                <i class="fas fa-archive"></i> Archive
                                            </button>

                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                    title="More Actions">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0)"
                                                            onclick="toggleEvent({{ $event->id }})">
                                                            <i class="fas fa-power-off text-warning me-2"></i>
                                                            {{ $event->is_active ? 'Deactivate' : 'Activate' }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                                                            onclick="deleteEvent({{ $event->id }})">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>No events found matching your criteria.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($events->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm">
                                {{-- Previous Page Link --}}
                                @if ($events->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Back</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $events->previousPageUrl() }}"
                                            rel="prev">Back</a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @php
                                    $currentPage = $events->currentPage();
                                    $lastPage = $events->lastPage();
                                    $startPage = max(1, $currentPage - 2);
                                    $endPage = min($lastPage, $currentPage + 2);

                                    if ($endPage - $startPage < 4) {
                                        if ($startPage == 1) {
                                            $endPage = min($lastPage, $startPage + 4);
                                        } else {
                                            $startPage = max(1, $endPage - 4);
                                        }
                                    }
                                @endphp

                                @for ($page = $startPage; $page <= $endPage; $page++)
                                    @if ($page == $currentPage)
                                        <li class="page-item active">
                                            <span class="page-link bg-primary border-primary">{{ $page }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="{{ $events->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endfor

                                {{-- Next Page Link --}}
                                @if ($events->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $events->nextPageUrl() }}" rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @endif
            </div>
        </div>
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
                            <input type="text" name="date" class="form-control"
                                placeholder="e.g., November 15, 2025 | 6:00 AM">
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
                                        <input type="text" class="form-control form-control-sm detail-key"
                                            placeholder="Detail name">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control detail-value"
                                                placeholder="Detail value">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="removeDetailRow(this)">Remove</button>
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
                            <span class="btn-loader" style="display: none;"><span
                                    class="spinner-border spinner-border-sm me-2"></span>Creating...</span>
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
                            <span class="btn-loader" style="display: none;"><span
                                    class="spinner-border spinner-border-sm me-2"></span>Updating...</span>
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
                        <p class="text-muted mb-3">Archived events are hidden from the landing page but can be restored
                            later.</p>
                        <div class="form-group">
                            <label class="form-label">Reason for archiving (optional)</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="e.g., Event completed, Rescheduled, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            <span class="btn-text">Archive Event</span>
                            <span class="btn-loader" style="display: none;"><span
                                    class="spinner-border spinner-border-sm me-2"></span>Archiving...</span>
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
                        <p class="mb-0">This action cannot be undone. Permanently deleting <strong
                                id="delete_event_name"></strong> will:</p>
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
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDelete()"
                        id="confirm_delete_btn">
                        <span class="btn-text">Yes, Delete Permanently</span>
                        <span class="btn-loader" style="display: none;"><span
                                class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                        onclick="location.reload()">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentFilter = 'all';

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Fix modal backdrop lingering issue for ALL modals
            const modals = document.querySelectorAll('.modal');

            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Remove any lingering backdrops
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Remove modal-open class from body
                    document.body.classList.remove('modal-open');

                    // Reset body overflow
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';

                    console.log('Modal cleaned up:', this.id);
                });
            });

            // Specific fix for edit/create event modals
            const eventModals = [
                'createEventModal',
                'editEventModal',
                'viewEventModal',
                'archiveEventModal',
                'deleteEventModal',
                'errorModal',
                'successModal'
            ];

            eventModals.forEach(modalId => {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        // Force cleanup
                        const backdrops = document.querySelectorAll('.modal-backdrop.fade.show');
                        backdrops.forEach(backdrop => backdrop.remove());

                        // Remove all backdrops as fallback
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                        // Ensure body is scrollable
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';

                        // Force remove modal-open if no other modals are open
                        const openModals = document.querySelectorAll('.modal.show');
                        if (openModals.length === 0) {
                            document.body.classList.remove('modal-open');
                        }

                        console.log('Event modal cleaned up:', modalId);
                    });
                }
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
            const container = type === 'create' ? document.getElementById('detailsContainer') : document.getElementById(
                'editDetailsContainer');
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

        // Enhanced friendly notification handler
        function showError(message, warningType = null) {
            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });

            setTimeout(() => {
                // For safety warnings, use a friendly info modal style
                if (warningType === 'last_active_event' || warningType === 'no_active_events') {
                    showToast('info', message);
                } else {
                    // For actual errors, show error modal
                    const errorModal = document.getElementById('errorModal');
                    const errorMessage = document.getElementById('errorMessage');
                    errorMessage.textContent = message;

                    const modalHeader = errorModal.querySelector('.modal-header');
                    modalHeader.classList.remove('bg-warning', 'text-dark');
                    modalHeader.classList.add('bg-danger', 'text-white');
                    modalHeader.querySelector('.modal-title').textContent = 'Error';

                    new bootstrap.Modal(errorModal).show();
                }
            }, 300);
        }

        // show success
        function showSuccess(message) {
            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });
            setTimeout(() => {
                showToast('success', message || 'Operation completed successfully');
            }, 300);
        }

        // Toast notification system
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const iconMap = {
                'success': {
                    icon: 'fas fa-check-circle',
                    color: 'success'
                },
                'error': {
                    icon: 'fas fa-exclamation-circle',
                    color: 'danger'
                },
                'warning': {
                    icon: 'fas fa-exclamation-triangle',
                    color: 'warning'
                },
                'info': {
                    icon: 'fas fa-info-circle',
                    color: 'info'
                }
            };

            const config = iconMap[type] || iconMap['info'];

            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="${config.icon} me-2" style="color: var(--bs-${config.color});"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-toast ms-auto" onclick="removeToast(this.closest('.toast-notification'))"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                if (document.contains(toast)) {
                    removeToast(toast);
                }
            }, 5000);
        }

        // Create toast container if it doesn't exist
        function createToastContainer() {
            let container = document.getElementById('toastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toastContainer';
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            return container;
        }

        // Remove toast notification
        function removeToast(toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => {
                if (toastElement.parentElement) {
                    toastElement.remove();
                }
            }, 300);
        }

        // View event
        async function viewEvent(eventId) {
            try {
                const response = await fetch(`/admin/events/${eventId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
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
                        const displayKey = key.replace(/_/g, ' ').charAt(0).toUpperCase() + key.replace(/_/g, ' ')
                            .slice(1);
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
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!data.success) throw new Error('Failed to load event');
                const event = data.event;

                // Populate form fields
                document.getElementById('edit_event_id').value = event.id;
                document.getElementById('edit_title').value = event.title;
                document.getElementById('edit_description').value = event.description;
                document.getElementById('edit_category').value = event.category;
                document.getElementById('edit_is_active').value = event.is_active ? '1' : '0';
                document.getElementById('edit_date').value = event.date || '';
                document.getElementById('edit_location').value = event.location || '';

                // Store original values for change detection
                document.getElementById('edit_title').dataset.originalValue = event.title;
                document.getElementById('edit_description').dataset.originalValue = event.description;
                document.getElementById('edit_category').dataset.originalValue = event.category;
                document.getElementById('edit_is_active').dataset.originalValue = event.is_active ? '1' : '0';
                document.getElementById('edit_date').dataset.originalValue = event.date || '';
                document.getElementById('edit_location').dataset.originalValue = event.location || '';

                const imagePreview = document.getElementById('current_event_image');
                if (event.image) {
                    imagePreview.innerHTML =
                        `<label class="form-label">Current Image:</label><br><img src="${event.image}" alt="${event.title}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">`;
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
                                    <input type="text" class="form-control form-control-sm detail-key" value="${key}" data-original-key="${key}">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control detail-value" value="${value}" data-original-value="${value}">
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeDetailRow(this)">Remove</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        detailsContainer.appendChild(row);
                    }
                }

                // Initialize change detection
                initializeEventChangeDetection();

                new bootstrap.Modal(document.getElementById('editEventModal')).show();
            } catch (error) {
                showError(error.message);
            }
        }

        // Initialize change detection listeners
        function initializeEventChangeDetection() {
            const form = document.getElementById('editEventForm');
            const submitBtn = form.querySelector('.btn-primary');
            const fileInput = form.querySelector('input[type="file"]');

            // Track all input changes
            form.querySelectorAll('input[type="text"], textarea, select').forEach(input => {
                input.addEventListener('change', () => checkEventChanges(submitBtn));
                input.addEventListener('input', () => checkEventChanges(submitBtn));
            });

            // Track file input changes
            if (fileInput) {
                fileInput.addEventListener('change', () => checkEventChanges(submitBtn));
            }

            // Initial check
            checkEventChanges(submitBtn);
        }

        // Check for changes in the event form
        function checkEventChanges(submitBtn) {
            const form = document.getElementById('editEventForm');
            let hasChanges = false;

            // Check regular fields
            const fieldsToCheck = [
                'edit_title',
                'edit_description',
                'edit_category',
                'edit_is_active',
                'edit_date',
                'edit_location'
            ];

            fieldsToCheck.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input && input.value !== (input.dataset.originalValue || '')) {
                    hasChanges = true;
                    input.classList.add('form-changed');
                    input.parentElement.classList.add('change-indicator', 'changed');
                } else if (input) {
                    input.classList.remove('form-changed');
                    input.parentElement.classList.remove('change-indicator', 'changed');
                }
            });

            // Check detail rows
            const detailRows = document.querySelectorAll('#editDetailsContainer .detail-row');
            const originalDetailsContainer = document.getElementById('editDetailsContainer');

            detailRows.forEach(row => {
                const keyInput = row.querySelector('.detail-key');
                const valueInput = row.querySelector('.detail-value');

                const keyChanged = keyInput.value !== (keyInput.dataset.originalKey || '');
                const valueChanged = valueInput.value !== (valueInput.dataset.originalValue || '');

                if (keyChanged || valueChanged) {
                    hasChanges = true;
                    row.classList.add('detail-row-changed');
                } else {
                    row.classList.remove('detail-row-changed');
                }
            });

            // Check for new detail rows (rows without original values)
            detailRows.forEach(row => {
                const keyInput = row.querySelector('.detail-key');
                if (!keyInput.dataset.originalKey && keyInput.value) {
                    hasChanges = true;
                }
            });

            // Check file input
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                hasChanges = true;
            }

            // Update submit button state
            if (hasChanges) {
                submitBtn.classList.remove('no-changes');
                submitBtn.innerHTML = `
                    <span class="btn-text"><i class="fas fa-save me-1"></i>Update Event</span>
                    <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Updating...</span>
                `;
                submitBtn.disabled = false;
            } else {
                submitBtn.classList.add('no-changes');
                submitBtn.innerHTML = `
                    <span class="btn-text"><i class="fas fa-check me-1"></i>No Changes</span>
                    <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Updating...</span>
                `;
                submitBtn.disabled = true;
            }
        }

        // Update the edit form submission to check for changes first
        // EDIT EVENT FORM - WITH RELOAD
        document.getElementById('editEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            let hasChanges = false;

            const fieldsToCheck = [
                'edit_title',
                'edit_description',
                'edit_category',
                'edit_is_active',
                'edit_date',
                'edit_location'
            ];

            fieldsToCheck.forEach(fieldId => {
                const input = document.getElementById(fieldId);
                if (input && input.value !== (input.dataset.originalValue || '')) {
                    hasChanges = true;
                }
            });

            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                hasChanges = true;
            }

            const detailRows = document.querySelectorAll('#editDetailsContainer .detail-row');
            detailRows.forEach(row => {
                const keyInput = row.querySelector('.detail-key');
                const valueInput = row.querySelector('.detail-value');

                if (keyInput.value !== (keyInput.dataset.originalKey || '') ||
                    valueInput.value !== (valueInput.dataset.originalValue || '')) {
                    hasChanges = true;
                }
            });

            if (!hasChanges) {
                showError('No changes detected. Please modify the event details before updating.');
                return;
            }

            // FRONTEND VALIDATION: Announcements cannot be set to inactive
            const category = document.getElementById('edit_category').value;
            const isActive = document.getElementById('edit_is_active').value;
            const wasActive = document.getElementById('edit_is_active').dataset.originalValue;

            if (category === 'announcement' && isActive === '0') {
                showToast('warning', 'Announcements must always be active and cannot be deactivated.');
                return;
            }

            const formData = new FormData(this);
            const details = collectDetails(document.getElementById('editDetailsContainer'));
            const eventId = document.getElementById('edit_event_id').value;

            formData.append('details', JSON.stringify(details));

            try {
                document.querySelector('#editEventForm .btn-text').style.display = 'none';
                document.querySelector('#editEventForm .btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${eventId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.warning_type) {
                        throw {
                            message: data.message,
                            warningType: data.warning_type
                        };
                    }
                    throw new Error(data.message || 'Failed to update event');
                }

                showSuccess(data.message);
                setTimeout(() => location.reload(), 800);

            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.querySelector('#editEventForm .btn-text').style.display = 'inline';
                document.querySelector('#editEventForm .btn-loader').style.display = 'none';
            }
        });

        // Add CSS for visual feedback
        const style = document.createElement('style');
        style.textContent = `
            .form-changed {
                border-left: 3px solid #ffc107 !important;
                background-color: #fff3cd;
                transition: all 0.3s ease;
            }

            .no-changes {
                opacity: 0.7;
                cursor: not-allowed !important;
                transition: all 0.3s ease;
            }

            .change-indicator {
                position: relative;
            }

            .change-indicator.changed::after {
                content: "●";
                color: #ffc107;
                font-size: 12px;
                position: absolute;
                right: -15px;
                top: 50%;
                transform: translateY(-50%);
                opacity: 1;
                transition: opacity 0.3s ease;
            }

            .detail-row-changed {
                background-color: #fff3cd;
                padding: 8px;
                border-radius: 4px;
                animation: highlight 0.3s ease;
            }

            @keyframes highlight {
                from { background-color: #ffe6e6; }
                to { background-color: #fff3cd; }
            }
        `;
        document.head.appendChild(style);

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

            const category = document.querySelector('select[name="category"]').value;
            const isActive = document.querySelector('select[name="is_active"]').value;

            // FRONTEND VALIDATION: Announcements are always active
            if (category === 'announcement' && isActive === '0') {
                showToast('warning',
                    'Announcements must always be active. Status has been automatically set to Active.');
                document.querySelector('select[name="is_active"]').value = '1';
                return;
            }

            const formData = new FormData(this);
            const details = collectDetails(document.getElementById('detailsContainer'));
            formData.append('details', JSON.stringify(details));

            try {
                document.querySelector('#createEventForm .btn-text').style.display = 'none';
                document.querySelector('#createEventForm .btn-loader').style.display = 'inline';

                const response = await fetch('/admin/events', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.warning_type === 'category_limit_reached') {
                        throw {
                            message: data.message +
                                ' You can create it as inactive or deactivate an existing event first.',
                            warningType: data.warning_type
                        };
                    }
                    throw new Error(data.message || 'Failed to create event');
                }

                showSuccess(data.message);
                setTimeout(() => location.reload(), 800);

            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.querySelector('#createEventForm .btn-text').style.display = 'inline';
                document.querySelector('#createEventForm .btn-loader').style.display = 'none';
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
                    body: JSON.stringify({
                        reason: reason
                    })
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.warning_type === 'event_is_active') {
                        showToast('warning', data.message);
                        return;
                    } else if (data.warning_type === 'last_active_event') {
                        showToast('info', data.message);
                        return;
                    } else if (data.warning_type) {
                        throw {
                            message: data.message,
                            warningType: data.warning_type
                        };
                    } else {
                        throw new Error(data.message || 'Failed to archive event');
                    }
                }

                showSuccess(data.message);
                setTimeout(() => location.reload(), 800);

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
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (!response.ok) {
                    if (data.warning_type === 'announcement_always_active') {
                        showToast('warning', data.message);
                    } else if (data.warning_type === 'last_active_in_category') {
                        showToast('info', data.message);
                    } else if (data.warning_type === 'category_limit_reached') {
                        showToast('info', data.message);
                    } else if (data.warning_type === 'last_active_event') {
                        showToast('info', data.message);
                    } else {
                        throw new Error(data.message || 'Failed to update status');
                    }
                    return;
                }

                showSuccess(data.message);
                setTimeout(() => location.reload(), 800);

            } catch (error) {
                showError(error.message);
            }
        }
        // DELETE EVENT
        let currentDeleteEventId = null;

        function deleteEvent(eventId) {
            try {
                // Get event details from the table row
                const row = document.querySelector(`[data-id="${eventId}"]`);
                const eventTitle = row ? row.querySelector('strong').textContent : 'this event';

                // Set the global variable
                currentDeleteEventId = eventId;

                // Update modal with event name
                document.getElementById('delete_event_name').textContent = eventTitle;

                // Show the delete modal
                new bootstrap.Modal(document.getElementById('deleteEventModal')).show();
            } catch (error) {
                showError('Failed to prepare delete dialog: ' + error.message);
            }
        }

        // DELETE EVENT
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
                    // Close modal first
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteEventModal'));
                    if (deleteModal) deleteModal.hide();

                    // Handle different warning types
                    if (data.warning_type === 'event_is_active') {
                        showToast('warning', data.message);
                    } else if (data.warning_type === 'last_active_event') {
                        showToast('warning', data.message);
                    } else if (data.warning_type) {
                        throw {
                            message: data.message,
                            warningType: data.warning_type
                        };
                    } else {
                        throw new Error(data.message || 'Failed to delete event');
                    }
                    return;
                }

                showSuccess(data.message);

                // Reload page after 1 second
                setTimeout(() => {
                    location.reload();
                }, 800);

            } catch (error) {
                showError(error.message, error.warningType || null);
            } finally {
                document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'inline';
                document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'none';
            }
        }
        // // Confirm permanent delete
        // async function confirmPermanentDelete() {
        //     try {
        //         document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'none';
        //         document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'inline';

        //         const response = await fetch(`/admin/events/${currentDeleteEventId}`, {
        //             method: 'DELETE',
        //             headers: {
        //                 'X-CSRF-TOKEN': csrfToken,
        //                 'Content-Type': 'application/json',
        //                 'Accept': 'application/json'
        //             }
        //         });
        //         const data = await response.json();

        //         if (!response.ok) {
        //             if (data.warning_type) {
        //                 throw { message: data.message, warningType: data.warning_type };
        //             }
        //             throw new Error(data.message || 'Failed to delete event');
        //         }

        //         showSuccess(data.message);
        //         // Reload page after 1 second
        //         setTimeout(() => {
        //             location.reload();
        //         }, 1000);
        //     } catch (error) {
        //         showError(error.message, error.warningType || null);
        //     } finally {
        //         document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'inline';
        //         document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'none';
        //     }
        // }


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
                agrisysModal.warning('Please select at least one date', {
                    title: 'Date Required'
                });
                return;
            }

            if (dateFrom && dateTo && dateFrom > dateTo) {
                agrisysModal.warning('From date cannot be later than To date', {
                    title: 'Invalid Date Range'
                });
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
        /* Modern Statistics Cards */
        .stat-card {
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .stat-icon i {
            font-size: 2.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #495057;
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem;
            font-weight: 500;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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

        dl {
            margin-bottom: 0;
        }

        dt {
            font-weight: 600;
            color: #333;
        }

        dd {
            margin-bottom: 0.5rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .event-row {
            transition: all 0.2s ease;
        }

        .event-row:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .btn-group-sm .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.85rem;
        }

        .detail-row {
            animation: slideDown 0.2s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.875em;
            color: #dc3545;
        }

        .btn-group-sm {
            display: flex;
            gap: 2px;
        }

        .btn-group-sm .btn {
            flex: 1;
        }

        /* Toast Notification Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }

        /* Individual Toast Notification */
        .toast-notification {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 380px;
            max-width: 600px;
            overflow: hidden;
            opacity: 0;
            transform: translateX(400px);
            transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
            pointer-events: auto;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Toast Content */
        .toast-notification .toast-content {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            font-size: 0.95rem;
        }

        .toast-notification .toast-content i {
            font-size: 1.25rem;
            min-width: 24px;
        }

        .toast-notification .toast-content span {
            flex: 1;
            color: #333;
            margin-left: 12px;
        }

        .toast-notification .btn-close-toast {
            width: auto;
            height: auto;
            padding: 0;
            font-size: 1.2rem;
            opacity: 0.5;
            transition: opacity 0.2s;
            cursor: pointer;
            background: none;
            border: none;
            color: #333;
        }

        .toast-notification .btn-close-toast:hover {
            opacity: 1;
        }

        /* Custom Pagination Styles */
        .pagination {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 8px;
            margin: 0;
        }

        .pagination .page-item .page-link {
            color: #6c757d;
            background-color: transparent;
            border: none;
            padding: 8px 12px;
            margin: 0 2px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .pagination .page-item .page-link:hover {
            color: #495057;
            background-color: #e9ecef;
            text-decoration: none;
        }

        .pagination .page-item.active .page-link {
            color: white;
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: transparent;
            cursor: not-allowed;
        }

        /* Type-specific styles */
        .toast-notification.toast-success {
            border-left: 4px solid #28a745;
        }

        .toast-notification.toast-error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification.toast-warning {
            border-left: 4px solid #ffc107;
        }

        .toast-notification.toast-info {
            border-left: 4px solid #17a2b8;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .toast-container {
                top: 10px;
                right: 10px;
                left: 10px;
            }

            .toast-notification {
                min-width: auto;
                max-width: 100%;
            }

            .toast-notification .toast-content {
                padding: 12px 16px;
                font-size: 0.9rem;
            }
        }

        /* Modal Backdrop Fix */
        .modal-backdrop {
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .modal-backdrop.fade {
            opacity: 0;
        }

        .modal-backdrop.show {
            opacity: 0.5;
        }

        /* Ensure body scrolling is restored */
        body {
            overflow: auto !important;
            padding-right: 0 !important;
        }

        body.modal-open {
            overflow: hidden;
        }
    </style>
@endsection

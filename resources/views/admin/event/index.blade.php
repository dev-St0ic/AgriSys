{{-- resources/views/admin/event/index.blade.php --}}
{{-- Event Management Admin Page - Complete and Clean --}}

@extends('layouts.app')

@section('title', 'Manage Events')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-calendar-alt me-2"></i>Manage Events</h2>
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
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Events</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['total'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-calendar-alt fa-2x text-primary"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">Active</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['active'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-check-circle fa-2x text-success"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">Announcements</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['announcements'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-bell fa-2x text-info"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Ongoing</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['ongoing'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-spinner fa-2x text-warning"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-secondary text-uppercase mb-1">Upcoming</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['upcoming'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-clock fa-2x text-secondary"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">Past</div>
                                <div class="h5 mb-0 fw-bold">{{ $stats['past'] ?? 0 }}</div>
                            </div>
                            <div class="ms-3"><i class="fas fa-history fa-2x text-danger"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-outline-primary active" onclick="filterEvents('all')">All Events</button>
                        <button class="btn btn-outline-info" onclick="filterEvents('announcement')">Announcements</button>
                        <button class="btn btn-outline-warning" onclick="filterEvents('ongoing')">Ongoing</button>
                        <button class="btn btn-outline-secondary" onclick="filterEvents('upcoming')">Upcoming</button>
                        <button class="btn btn-outline-danger" onclick="filterEvents('past')">Past</button>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                        <i class="fas fa-plus me-2"></i>Add Event
                    </button>
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
                                <th style="width: 120px;">Status</th>
                                <th style="width: 100px;">Order</th>
                                <th style="width: 180px;">Actions</th>
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
                                    <td>
                                        <input type="number" class="form-control form-control-sm" value="{{ $event->display_order }}" onchange="updateEventOrder({{ $event->id }}, this.value)">
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-primary" onclick="editEvent({{ $event->id }})" title="Edit"><i class="fas fa-edit"></i></button>
                                            <button class="btn btn-{{ $event->is_active ? 'warning' : 'success' }}" onclick="toggleEvent({{ $event->id }})" title="Toggle"><i class="fas fa-power-off"></i></button>
                                            <button class="btn btn-danger" onclick="deleteEvent({{ $event->id }})" title="Delete"><i class="fas fa-trash"></i></button>
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
                            <small class="text-muted">Max 2MB (JPEG, PNG, GIF)</small>
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
                            <small class="text-muted">Max 2MB (JPEG, PNG, GIF)</small>
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

        function showError(message) {
            document.querySelectorAll('.modal.show').forEach(m => {
                const bsModal = bootstrap.Modal.getInstance(m);
                if (bsModal) bsModal.hide();
            });
            setTimeout(() => {
                document.getElementById('errorMessage').textContent = message;
                new bootstrap.Modal(document.getElementById('errorModal')).show();
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

        // Create event form
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
                if (!response.ok) throw new Error(data.message || 'Failed to create event');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            } finally {
                document.querySelector('#createEventForm .btn-text').style.display = 'inline';
                document.querySelector('#createEventForm .btn-loader').style.display = 'none';
            }
        });

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

        // Edit event form
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
                if (!response.ok) throw new Error(data.message || 'Failed to update event');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            } finally {
                document.querySelector('#editEventForm .btn-text').style.display = 'inline';
                document.querySelector('#editEventForm .btn-loader').style.display = 'none';
            }
        });

        // Toggle status
        async function toggleEvent(eventId) {
            try {
                const response = await fetch(`/admin/events/${eventId}/toggle-status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to update status');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }

        // Delete event
        async function deleteEvent(eventId) {
            if (!confirm('Delete this event?')) return;
            try {
                const response = await fetch(`/admin/events/${eventId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to delete event');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            }
        }

        // Update order
        async function updateEventOrder(eventId, order) {
            try {
                const response = await fetch(`/admin/events/${eventId}/order`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ display_order: parseInt(order) })
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to update order');
                const toast = document.createElement('div');
                toast.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            } catch (error) {
                showError(error.message);
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
    </script>

    <style>
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
    </style>
@endsection
{{-- resources/views/admin/event/archived.blade.php --}}
{{-- Archived Events Management Page --}}

@extends('layouts.app')

@section('title', 'Archived Events')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-archive me-2"></i>Archived Events</h2>
                <p class="text-muted">Events that have been archived and hidden from the landing page</p>
            </div>
            <a href="{{ route('admin.event.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Active Events
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <input type="text" name="search" class="form-control" placeholder="Search archived events..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="{{ route('admin.event.archived') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Clear
                    </a>
                </form>
            </div>
        </div>

        <!-- Archived Events Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Archived Events ({{ $events->total() }})</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Title</th>
                                <th style="width: 100px;">Category</th>
                                <th style="width: 140px;">Archived At</th>
                                <th>Archived Reason</th>
                                <th style="width: 150px;">Archived By</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr class="archived-event-row">
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
                                        <small class="text-muted">
                                            {{ $event->archived_at ? $event->archived_at->format('M d, Y') : '—' }}<br>
                                            <span class="text-xs">{{ $event->archived_at ? $event->archived_at->diffForHumans() : '' }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if ($event->archive_reason)
                                            <span class="badge bg-light text-dark" data-bs-toggle="tooltip" title="{{ $event->archive_reason }}">
                                                {{ Str::limit($event->archive_reason, 40) }}
                                            </span>
                                        @else
                                            <span class="text-muted text-xs">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($event->archivist)
                                            <small class="text-muted">
                                                {{ $event->archivist->name }}<br>
                                                <span class="text-xs">{{ $event->archivist->email }}</span>
                                            </small>
                                        @else
                                            <span class="text-muted text-xs">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-primary" onclick="viewEventDetails({{ $event->id }})" title="View Details" data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-success" onclick="restoreEvent({{ $event->id }}, '{{ addslashes($event->title) }}')" title="Restore" data-bs-toggle="tooltip">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="permanentlyDeleteEvent({{ $event->id }}, '{{ addslashes($event->title) }}')" title="Delete Permanently" data-bs-toggle="tooltip">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3" style="display: block;"></i>
                                        <p class="text-muted mb-0">No archived events found.</p>
                                        <small class="text-muted">Archived events will appear here.</small>
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

    <!-- VIEW EVENT DETAILS MODAL -->
    <div class="modal fade" id="eventDetailsModal" tabindex="-1">
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

    <!-- RESTORE EVENT MODAL -->
    <div class="modal fade" id="restoreEventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Restore Event</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Restore <strong id="restore_event_name"></strong> from archive?</p>
                    <p class="text-muted text-sm mt-2">The event will be reactivated and visible on the landing page.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmRestore()" id="confirm_restore_btn">
                        <span class="btn-text">Restore Event</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Restoring...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE PERMANENTLY MODAL -->
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

    <!-- ERROR MODAL -->
    <div class="modal fade" id="errorModal" tabindex="-1">
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

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentRestoreEventId = null;
        let currentDeleteEventId = null;

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

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

        // View event details
        async function viewEventDetails(eventId) {
            try {
                const response = await fetch(`/admin/events/${eventId}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!data.success) throw new Error('Failed to load event details');
                
                const event = data.event;
                let html = `
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
                                
                                <dt class="col-sm-4">Date:</dt>
                                <dd class="col-sm-8">${event.date || '—'}</dd>
                                
                                <dt class="col-sm-4">Location:</dt>
                                <dd class="col-sm-8">${event.location || '—'}</dd>
                                
                                <dt class="col-sm-4">Created:</dt>
                                <dd class="col-sm-8"><small>${new Date(event.created_at).toLocaleDateString()}</small></dd>
                                
                                <dt class="col-sm-4">Archived:</dt>
                                <dd class="col-sm-8"><small>${event.archived_at ? new Date(event.archived_at).toLocaleDateString() : '—'}</small></dd>
                                
                                ${event.archive_reason ? `
                                <dt class="col-sm-4">Reason:</dt>
                                <dd class="col-sm-8"><small>${event.archive_reason}</small></dd>
                                ` : ''}
                            </dl>
                        </div>
                    </div>
                `;
                document.getElementById('eventDetailsContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('eventDetailsModal')).show();
            } catch (error) {
                showError(error.message);
            }
        }

        // Restore event
        function restoreEvent(eventId, eventName) {
            currentRestoreEventId = eventId;
            document.getElementById('restore_event_name').textContent = eventName;
            new bootstrap.Modal(document.getElementById('restoreEventModal')).show();
        }

        async function confirmRestore() {
            try {
                document.getElementById('confirm_restore_btn').querySelector('.btn-text').style.display = 'none';
                document.getElementById('confirm_restore_btn').querySelector('.btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${currentRestoreEventId}/unarchive`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to restore event');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            } finally {
                document.getElementById('confirm_restore_btn').querySelector('.btn-text').style.display = 'inline';
                document.getElementById('confirm_restore_btn').querySelector('.btn-loader').style.display = 'none';
            }
        }

        // Delete permanently
        function permanentlyDeleteEvent(eventId, eventName) {
            currentDeleteEventId = eventId;
            document.getElementById('delete_event_name').textContent = eventName;
            new bootstrap.Modal(document.getElementById('deleteEventModal')).show();
        }

        async function confirmPermanentDelete() {
            try {
                document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'none';
                document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'inline';

                const response = await fetch(`/admin/events/${currentDeleteEventId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Failed to delete event');
                showSuccess(data.message);
            } catch (error) {
                showError(error.message);
            } finally {
                document.getElementById('confirm_delete_btn').querySelector('.btn-text').style.display = 'inline';
                document.getElementById('confirm_delete_btn').querySelector('.btn-loader').style.display = 'none';
            }
        }
    </script>

    <style>
        .text-xs { font-size: 0.75rem; }
        .archived-event-row { transition: all 0.2s ease; }
        .archived-event-row:hover { background-color: #f8f9fa; }
        .badge { font-size: 0.85rem; padding: 0.5rem 0.75rem; }
        .btn-group-sm .btn { padding: 0.375rem 0.75rem; font-size: 0.85rem; }
        .btn-group-sm {
            display: flex;
            gap: 2px;
        }
        .btn-group-sm .btn {
            flex: 1;
        }
        .modal-body { max-height: 70vh; overflow-y: auto; }
        dl { margin-bottom: 0; }
        dt { font-weight: 600; color: #333; }
        dd { margin-bottom: 0.5rem; }
    </style>
@endsection
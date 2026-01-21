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
                <i class="fas fa-arrow-left me-2"></i>Back to Events
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" id="searchForm" class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Search archived events..." value="{{ request('search') }}" oninput="autoSearch()">
                    </div>
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
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewEventDetails({{ $event->id }})" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="restoreEvent({{ $event->id }}, '{{ addslashes($event->title) }}')" title="Restore">
                                                <i class="fas fa-redo"></i> Restore
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="permanentlyDeleteEvent({{ $event->id }}, '{{ addslashes($event->title) }}')" title="Delete Permanently">
                                                <i class="fas fa-trash"></i> Delete
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i></i>Event Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="eventDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Loading event details...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- IMAGE PREVIEW MODAL  -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-image me-2"></i><span id="previewImageTitle">Image Preview</span>
                    </h5>
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="position: absolute; right: 1rem;">
                        <i class="fas fa-times fa-lg"></i>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center">
                        <img id="previewImage" 
                            src="" 
                            alt="Event Image" 
                            class="img-fluid rounded shadow-sm" 
                            style="max-height: 550px; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i></i>Close
                    </button>
                    <a id="downloadBtn" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RESTORE EVENT MODAL -->
    <div class="modal fade" id="restoreEventModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title w-100 text-center">Restore Event</h5>
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
                    <h5 class="modal-title w-100 text-center">Permanently Delete Event</h5>
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

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentRestoreEventId = null;
        let currentDeleteEventId = null;

        // Initialize tooltips and modal cleanup
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
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
                });
            });
        });

        // Toast notification system
        function showToast(type, message) {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            
            const iconMap = {
                'success': { icon: 'fas fa-check-circle', color: 'success' },
                'error': { icon: 'fas fa-exclamation-circle', color: 'danger' },
                'warning': { icon: 'fas fa-exclamation-triangle', color: 'warning' },
                'info': { icon: 'fas fa-info-circle', color: 'info' }
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

        function showError(message) {
            showToast('error', message);
        }

        function showSuccess(message) {
            showToast('success', message);
        }

        // Auto search functionality
        let searchTimeout;

        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

        // View event details - Updated for archived page
        async function viewEventDetails(eventId) {
            try {
                // Show modal with loading state
                const modal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
                modal.show();

                const response = await fetch(`/admin/events/${eventId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (!data.success) throw new Error('Failed to load event');

                const event = data.event;

                // Build details HTML
                let detailsHtml = '';
                if (event.details && Object.keys(event.details).length > 0) {
                    detailsHtml = `
                        <div class="col-12 mt-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Additional Details
                                    </h6>
                                    <dl class="row mb-0">
                            `;
                    for (const [key, value] of Object.entries(event.details)) {
                        const displayKey = key.replace(/_/g, ' ').charAt(0).toUpperCase() + key.replace(/_/g, ' ').slice(1);
                        detailsHtml += `
                            <dt class="col-sm-4 mb-2">${displayKey}:</dt>
                            <dd class="col-sm-8 mb-2">${value}</dd>
                        `;
                    }
                    detailsHtml += `
                                    </dl>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Build archive info section
                let archiveInfoHtml = '';
                if (event.archived_at || event.archive_reason) {
                    archiveInfoHtml = `
                        <div class="col-12 mt-4">
                            <div class="card border-0 bg-warning bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-warning mb-3">
                                        <i class="fas fa-archive me-2"></i>Archive Information
                                    </h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 mb-2">Archived At:</dt>
                                        <dd class="col-sm-8 mb-2">${event.archived_at ? new Date(event.archived_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : '—'}</dd>
                                        
                                        ${event.archive_reason ? `
                                        <dt class="col-sm-4 mb-2">Archive Reason:</dt>
                                        <dd class="col-sm-8 mb-2">${event.archive_reason}</dd>
                                        ` : ''}
                                        
                                        ${event.archivist ? `
                                        <dt class="col-sm-4 mb-2">Archived By:</dt>
                                        <dd class="col-sm-8 mb-2">${event.archivist.name} (${event.archivist.email})</dd>
                                        ` : ''}
                                    </dl>
                                </div>
                            </div>
                        </div>
                    `;
                }

                const html = `
                    <div class="row g-4">
                        <!-- Image Section -->
                        <div class="col-md-4">
                            ${event.image 
                                ? `<div class="position-relative" style="cursor: pointer;" onclick="previewEventImage('${event.image}', '${event.title.replace(/'/g, "\\'")}')">
                                    <img src="${event.image}" alt="${event.title}" class="img-fluid rounded shadow-sm w-100" style="max-height: 300px; object-fit: cover;">
                                    <div class="position-absolute top-50 start-50 translate-middle opacity-0 hover-overlay">
                                        <i class="fas fa-search-plus fa-2x text-white"></i>
                                    </div>
                                </div>` 
                                : `<div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center" style="height: 300px;">
                                    <div class="text-center">
                                        <i class="fas fa-image fa-4x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No image</p>
                                    </div>
                                </div>`
                            }
                        </div>

                        <!-- Details Section -->
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-3">${event.title}</h4>
                            <p class="text-muted mb-4">${event.description}</p>

                            <div class="row g-3">
                                <!-- Category -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">Category</small>
                                            <span class="badge bg-info fs-6">${event.category_label}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">Status</small>
                                            <span class="badge bg-${event.is_active ? 'success' : 'secondary'} fs-6">
                                                ${event.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date -->
                                ${event.date ? `
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-calendar me-1"></i>Date/Time
                                            </small>
                                            <span class="fw-semibold">${event.date}</span>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}

                                <!-- Location -->
                                ${event.location ? `
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-map-marker-alt me-1"></i>Location
                                            </small>
                                            <span class="fw-semibold">${event.location}</span>
                                        </div>
                                    </div>
                                </div>
                                ` : ''}

                                <!-- Created Date -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-clock me-1"></i>Created
                                            </small>
                                            <span class="text-muted small">${new Date(event.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Updated Date -->
                                <div class="col-sm-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body py-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="fas fa-edit me-1"></i>Last Updated
                                            </small>
                                            <span class="text-muted small">${new Date(event.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        ${detailsHtml}

                        <!-- Archive Information -->
                        ${archiveInfoHtml}
                    </div>
                `;
                
                document.getElementById('eventDetailsContent').innerHTML = html;
                
            } catch (error) {
                document.getElementById('eventDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        ${error.message}
                    </div>
                `;
            }
        }

        // Simple image preview - Updated version 
        function previewEventImage(imageUrl, eventTitle) {
            const previewImage = document.getElementById('previewImage');
            const titleElement = document.getElementById('previewImageTitle');
            const downloadBtn = document.getElementById('downloadBtn');
            
            previewImage.src = imageUrl;
            previewImage.alt = eventTitle;
            titleElement.textContent = eventTitle;
            
            // Download functionality
            downloadBtn.href = imageUrl;
            downloadBtn.download = `${eventTitle || 'event-image'}.jpg`;
            
            const imagePreviewModal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            imagePreviewModal.show();
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
                
                // Close modal first
                const restoreModal = bootstrap.Modal.getInstance(document.getElementById('restoreEventModal'));
                if (restoreModal) restoreModal.hide();
                
                if (!response.ok) {
                    // Handle different warning types
                    if (data.warning_type) {
                        showToast('warning', data.message);
                    } else {
                        throw new Error(data.message || 'Failed to restore event');
                    }
                    return;
                }
                
                // Show notification then reload
                setTimeout(() => {
                    showSuccess(data.message);
                    setTimeout(() => location.reload(), 800);
                }, 300);
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
                
                if (!response.ok) {
                    // Close modal first
                    const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteEventModal'));
                    if (deleteModal) deleteModal.hide();
                    
                    // Handle different warning types
                    if (data.warning_type === 'event_is_active') {
                        showToast('warning', data.message);
                    } else if (data.warning_type) {
                        showToast('warning', data.message);
                    } else {
                        throw new Error(data.message || 'Failed to delete event');
                    }
                    return;
                }
                
                showSuccess(data.message);
                setTimeout(() => location.reload(), 800);
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

        /* Image hover effect for preview */
        .position-relative:hover .hover-overlay {
            opacity: 1 !important;
            transition: opacity 0.3s ease;
        }

        .position-relative::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0.375rem;
        }

        .position-relative:hover::after {
            opacity: 1;
        }

        .hover-overlay {
            z-index: 2;
            transition: opacity 0.3s ease;
        }

        #imagePreviewModal .btn-close {
            background: rgba(255, 255, 255, 0.2);
            opacity: 1;
            padding: 0.75rem;
            border-radius: 50%;
        }

        #imagePreviewModal .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
@endsection
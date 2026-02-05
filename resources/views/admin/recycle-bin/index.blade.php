{{-- resources/views/admin/recycle-bin/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Recycle Bin - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-trash-restore text-warning me-3"></i>
        <span class="text-warning fw-bold fs-4">Recycle Bin</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Total Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow-sm h-100 border-0">
                <div class="card-body text-center py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-trash text-warning"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['total_items'] }}</div>
                    <div class="stat-label">Total Items</div>
                </div>
            </div>
        </div>

        <!-- Fishr Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow-sm h-100 border-0">
                <div class="card-body text-center py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-fish text-info"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['fishr_items'] }}</div>
                    <div class="stat-label">FishR Items</div>
                </div>
            </div>
        </div>

        <!-- BoatR Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow-sm h-100 border-0">
                <div class="card-body text-center py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-ship text-success"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['boatr_items'] }}</div>
                    <div class="stat-label">BoatR Items</div>
                </div>
            </div>
        </div>

        <!-- Supply Items Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card shadow-sm h-100 border-0">
                <div class="card-body text-center py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-boxes text-danger"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['supply_category_items'] + $stats['supply_item_items'] }}</div>
                    <div class="stat-label">Supply Items</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-filter me-2 text-primary"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.recycle-bin.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Type Filter -->
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label fw-500 mb-2">Item Type</label>
                        <select name="type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Types</option>
                            <option value="user_registration" {{ request('type') == 'user_registration' ? 'selected' : '' }}>User Registration</option>
                            <option value="fishr" {{ request('type') == 'fishr' ? 'selected' : '' }}>FishR Application</option>
                            <option value="fishr_annex" {{ request('type') == 'fishr_annex' ? 'selected' : '' }}>FishR Annex</option>
                            <option value="boatr" {{ request('type') == 'boatr' ? 'selected' : '' }}>BoatR Application</option>
                            <option value="boatr_annex" {{ request('type') == 'boatr_annex' ? 'selected' : '' }}>BoatR Annex</option>
                            <option value="rsbsa" {{ request('type') == 'rsbsa' ? 'selected' : '' }}>RSBSA</option>
                            <option value="seedlings" {{ request('type') == 'seedling' ? 'selected' : '' }}>Supplies Request</option>
                            <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>Training</option>
                            <option value="category_item" {{ request('type') == 'supply_category' ? 'selected' : '' }}>Supply Categories</option>
                            <option value="request_category" {{ request('type') == 'supply_item' ? 'selected' : '' }}>Supply Items</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-lg-5 col-md-6">
                        <label class="form-label fw-500 mb-2">Search</label>
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name or reason..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filter -->
                    <div class="col-lg-4 col-md-12">
                        <label class="form-label fw-500 mb-2" style="visibility: hidden;">Action</label>
                        <a href="{{ route('admin.recycle-bin.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-redo me-2"></i>Reset Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recycle Bin Items Table -->
    <div class="card shadow-sm border-0">
        <!-- Table Header with Actions -->
        <div class="card-header py-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-trash-restore me-2 text-warning"></i>Recycle Bin Items
                <span class="badge bg-secondary ms-2">{{ $items->total() }}</span>
            </h6>
            <div class="action-buttons d-none" id="bulkActionButtons">
                <button type="button" class="btn btn-sm btn-success me-2" onclick="bulkRestore()">
                    <i class="fas fa-undo me-1"></i>Restore Selected
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                    <i class="fas fa-trash me-1"></i>Delete Selected
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="recycleBinTable">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="text-center ps-4">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)" class="form-check-input">
                            </th>
                            <th class="text-start">Item Name</th>
                            <th class="text-center">Type</th>
                            <th class="text-start">Deleted By</th>
                            <th class="text-start">Deleted On</th>
                            <th class="text-start">Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr data-item-id="{{ $item->id }}" class="align-middle border-bottom">
                                <td class="text-center ps-4">
                                    <input type="checkbox" class="item-checkbox form-check-input" value="{{ $item->id }}" onchange="updateBulkActions()">
                                </td>
                                <td class="text-start">
                                    <div class="fw-600 text-dark">{{ $item->item_name }}</div>
                                    <small class="text-muted d-block">ID: {{ $item->id }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $item->type_name }}
                                    </span>
                                </td>
                                <td class="text-start">
                                    <small>{{ $item->deletedBy->name ?? 'Unknown' }}</small>
                                </td>
                                <td class="text-start">
                                    <small class="text-muted">{{ $item->deleted_at->format('M d, Y') }}</small>
                                    <small class="d-block text-muted">{{ $item->deleted_at->format('h:i A') }}</small>
                                </td>
                                <td class="text-start">
                                    <small class="text-muted">
                                        {{ Str::limit($item->reason ?? 'No reason provided', 50) }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                            onclick="viewItem({{ $item->id }})" title="View Details"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                            onclick="restoreItem({{ $item->id }})" title="Restore Item"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="deleteItem({{ $item->id }})" title="Permanently Delete"
                                            data-bs-toggle="tooltip">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                                    <p class="text-muted fs-5 mb-0">No deleted items in the recycle bin</p>
                                    <small class="text-muted">Deleted items will appear here</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($items->hasPages())
                <div class="card-footer bg-light py-3">
                    <div class="d-flex justify-content-center">
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Item Details Modal -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning bg-opacity-10 border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-info-circle me-2 text-warning"></i>Item Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="itemDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="restoreFromModal()">
                        <i class="fas fa-undo me-1"></i>Restore Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }

        /* Card Styling */
        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, var(--light-gray) 100%);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08) !important;
        }

        .stat-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            margin: 0 auto;
            background: rgba(13, 110, 253, 0.1);
            border-radius: 12px;
            font-size: 1.75rem;
        }

        .stat-card:nth-child(2) .stat-icon {
            background: rgba(23, 162, 184, 0.1);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: rgba(25, 135, 84, 0.1);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: rgba(220, 53, 69, 0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.02);
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid var(--border-color);
            padding: 1rem;
            vertical-align: middle;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Button Styling */
        .btn-group-sm .btn {
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-outline-success:hover {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        /* Form Styling */
        .form-label {
            color: #495057;
            font-size: 0.95rem;
        }

        .form-select,
        .form-control {
            border-color: var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Card Header Styling */
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h6 {
            color: #212529;
            margin: 0;
        }

        /* Bulk Action Buttons */
        .action-buttons {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            border-radius: 10px 10px 0 0;
        }

        /* Badge Styling */
        .badge {
            padding: 0.5rem 0.75rem;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Empty State */
        .table tbody tr td[colspan] {
            padding: 3rem 1rem !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-number {
                font-size: 2rem;
            }

            .table {
                font-size: 0.9rem;
            }

            .table td,
            .table th {
                padding: 0.75rem;
            }

            .action-buttons {
                margin-top: 1rem;
                display: flex !important;
                flex-direction: column;
                gap: 0.5rem;
            }

            .action-buttons .btn {
                width: 100%;
            }
        }

        /* Tooltip */
        .tooltip-inner {
            background-color: #212529;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        /* Sticky Header */
        .sticky-top {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
        }

        /* Text Utilities */
        .fw-600 {
            font-weight: 600;
        }

        .fw-500 {
            font-weight: 500;
        }
    </style>

    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Toggle select all
        function toggleSelectAll(checkbox) {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            itemCheckboxes.forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateBulkActions();
        }

        // Update bulk actions visibility
        function updateBulkActions() {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            const bulkActionButtons = document.getElementById('bulkActionButtons');
            
            if (checkedItems.length > 0) {
                bulkActionButtons.classList.remove('d-none');
            } else {
                bulkActionButtons.classList.add('d-none');
            }
        }

        // View item details
        function viewItem(itemId) {
            fetch(`/admin/recycle-bin/${itemId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const modal = new bootstrap.Modal(document.getElementById('itemDetailsModal'));
                    displayItemDetails(data.data);
                    window.currentItemId = itemId;
                    modal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error loading item details');
            });
        }

        // Display item details
        function displayItemDetails(item) {
            const content = document.getElementById('itemDetailsContent');
            content.innerHTML = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-600 text-muted">Item Name</label>
                            <p class="mb-0 text-dark fw-500">${item.item_name}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600 text-muted">Type</label>
                            <p class="mb-0"><span class="badge bg-primary">${item.type_name}</span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-600 text-muted">Deleted By</label>
                            <p class="mb-0 text-dark fw-500">${item.deleted_by_name}</p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-600 text-muted">Deleted On</label>
                            <p class="mb-0 text-dark fw-500">${item.deleted_at}</p>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
                <div class="mb-3">
                    <label class="form-label fw-600 text-muted">Deletion Reason</label>
                    <p class="mb-0 text-dark">${item.reason || 'No reason provided'}</p>
                </div>
                <hr class="my-3">
                <div>
                    <label class="form-label fw-600 text-muted mb-2">Original Data</label>
                    <div class="bg-light p-3 rounded border" style="max-height: 300px; overflow-y: auto;">
                        <pre class="mb-0" style="font-size: 0.85rem;"><code>${JSON.stringify(item.data, null, 2)}</code></pre>
                    </div>
                </div>
            `;
        }

        // Restore item from modal
        function restoreFromModal() {
            if (window.currentItemId) {
                restoreItem(window.currentItemId);
            }
        }

        // Restore item
        function restoreItem(itemId) {
            if (confirm('Are you sure you want to restore this item? It will be added back to the system.')) {
                fetch(`/admin/recycle-bin/${itemId}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error restoring item');
                });
            }
        }

        // Delete item from recycle bin
        function deleteItem(itemId) {
            if (confirm('This item will be permanently deleted. This action cannot be undone.')) {
                fetch(`/admin/recycle-bin/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error deleting item');
                });
            }
        }

        // Bulk restore items
        function bulkRestore() {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            if (checkedItems.length === 0) {
                showToast('error', 'Please select items to restore');
                return;
            }

            const itemIds = Array.from(checkedItems).map(cb => cb.value);
            
            if (confirm(`Restore ${itemIds.length} item(s)? They will be added back to the system.`)) {
                fetch('/admin/recycle-bin/bulk-restore', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: itemIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error restoring items');
                });
            }
        }

        // Bulk delete items
        function bulkDelete() {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            if (checkedItems.length === 0) {
                showToast('error', 'Please select items to delete');
                return;
            }

            const itemIds = Array.from(checkedItems).map(cb => cb.value);
            
            if (confirm(`Permanently delete ${itemIds.length} item(s)? This action cannot be undone.`)) {
                fetch('/admin/recycle-bin/bulk-destroy', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCSRFToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ ids: itemIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Error deleting items');
                });
            }
        }

        // Submit filter form
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Toast notification
        function showToast(type, message) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas ${icon} me-2"></i><strong>${type === 'success' ? 'Success!' : 'Error!'}</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.createElement('div');
            container.innerHTML = alertHtml;
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 350px; animation: slideIn 0.3s ease-out;';
            document.body.appendChild(container.firstElementChild);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.parentElement === document.body) {
                        alert.remove();
                    }
                });
            }, 5000);
        }
    </script>
@endsection
{{-- resources/views/admin/recycle-bin/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Recycle Bin - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-trash-restore text-warning me-2"></i>
        <span class="text-warning fw-bold">Recycle Bin</span>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Total Items Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-trash text-warning"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['total_items'] }}</div>
                    <div class="stat-label text-warning">Total Items</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Actions
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.recycle-bin.index') }}" id="filterForm">
                <div class="row g-2">
                    <!-- Type Filter -->
                    <div class="col-md-2">
                        <select name="type" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Types</option>
                            <option value="user_registration" {{ request('type') == 'user_registration' ? 'selected' : '' }}>User Registration</option>
                            <option value="fishr" {{ request('type') == 'fishr' ? 'selected' : '' }}>FishR</option>
                            <option value="fishr_annex" {{ request('type') == 'fishr_annex' ? 'selected' : '' }}>FishR Annex</option>
                            <option value="boatr" {{ request('type') == 'boatr' ? 'selected' : '' }}>BoatR</option>
                            <option value="boatr_annex" {{ request('type') == 'boatr_annex' ? 'selected' : '' }}>BoatR Annex</option>
                            <option value="rsbsa" {{ request('type') == 'rsbsa' ? 'selected' : '' }}>RSBSA</option>
                            <option value="seedlings" {{ request('type') == 'seedling' ? 'selected' : '' }}>Supplies Request</option>
                            <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>Training</option>
                            <option value="category_item" {{ request('type') == 'supply_category' ? 'selected' : '' }}>Supply Categories</option>
                            <option value="request_category" {{ request('type') == 'supply_item' ? 'selected' : '' }}>Supply Items</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search by name or reason..." value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-4 text-end">
                        <a href="{{ route('admin.recycle-bin.index') }}" class="btn btn-secondary btn-sm me-2">
                            <i class="fas fa-sync me-1"></i>Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recycle Bin Items -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-trash-restore me-2"></i>Recycle Bin Items
            </h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="recycleBinTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Deleted By</th>
                            <th>Deleted On</th>
                            <th>Reason</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr data-item-id="{{ $item->id }}">
                                <td class="text-center">
                                    <input type="checkbox" class="item-checkbox" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <strong>{{ $item->item_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->type_name }}</span>
                                </td>
                                <td>{{ $item->deletedBy->name ?? 'Unknown' }}</td>
                                <td>{{ $item->deleted_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $item->reason ?? 'No reason provided' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="viewItem({{ $item->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success"
                                            onclick="restoreItem({{ $item->id }})" title="Restore">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="deleteItem({{ $item->id }})" title="Remove from Recycle Bin">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-trash-restore fa-3x mb-3"></i>
                                    <p>No deleted items</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($items->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $items->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <!-- Item Details Modal -->
    <div class="modal fade" id="itemDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="itemDetailsContent">
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
        .stat-card {
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            transition: all 0.3s ease;
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

        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>

    <script>
        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Toggle select all
        function toggleSelectAll(checkbox) {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            itemCheckboxes.forEach(cb => cb.checked = checkbox.checked);
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
                    modal.show();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Display item details
        function displayItemDetails(item) {
            const content = document.getElementById('itemDetailsContent');
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${item.item_name}</p>
                        <p><strong>Type:</strong> ${item.type_name}</p>
                        <p><strong>Deleted By:</strong> ${item.deleted_by_name}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Deleted On:</strong> ${item.deleted_at}</p>
                    </div>
                </div>
                <hr>
                <p><strong>Reason:</strong></p>
                <p>${item.reason || 'No reason provided'}</p>
                <hr>
                <p><strong>Original Data:</strong></p>
                <div class="bg-light p-3" style="max-height: 300px; overflow-y: auto;">
                    <pre>${JSON.stringify(item.data, null, 2)}</pre>
                </div>
            `;
        }

        // Restore item
        function restoreItem(itemId) {
            if (confirm('Are you sure you want to restore this item?')) {
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
                .catch(error => showToast('error', 'Error restoring item'));
            }
        }

        // Delete item from recycle bin
        function deleteItem(itemId) {
            if (confirm('Remove this item from recycle bin?')) {
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
                .catch(error => showToast('error', 'Error deleting item'));
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
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas ${icon} me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.createElement('div');
            container.innerHTML = alertHtml;
            document.body.insertBefore(container.firstElementChild, document.body.firstChild);
        }
    </script>
@endsection
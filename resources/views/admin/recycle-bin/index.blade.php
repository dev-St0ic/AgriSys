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
        <!-- Simplified Statistics Cards -->
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

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-hourglass-end text-danger"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['expired_items'] }}</div>
                    <div class="stat-label text-danger">Expired Items</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card shadow h-100">
                <div class="card-body text-center py-3">
                    <div class="stat-icon mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div class="stat-number mb-2">{{ $stats['total_items'] - $stats['expired_items'] }}</div>
                    <div class="stat-label text-success">Active Items</div>
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

                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
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
                        <button type="button" class="btn btn-danger btn-sm" onclick="emptyRecycleBin()" title="Permanently delete expired items">
                            <i class="fas fa-broom me-1"></i>Empty Expired
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Deleted Items & Archived Items Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-trash-restore me-2"></i>Recycle Bin Items
            </h6>
            <!-- Tab Buttons -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm tab-button active" data-tab="deleted-items" onclick="switchTab('deleted-items')">
                    <i class="fas fa-trash me-1"></i>Deleted Items
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm tab-button" data-tab="archived-items" onclick="switchTab('archived-items')">
                    <i class="fas fa-archive me-1"></i>Archived Items
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- DELETED ITEMS TABLE -->
            <div id="deleted-items-section" class="tab-content active">
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
                                <th>Expires In</th>
                                <th>Reason</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @if(!$item->is_archived)
                                    <tr data-item-id="{{ $item->id }}" class="{{ $item->is_expired ? 'table-danger' : '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" class="item-checkbox" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong>
                                            @if ($item->is_expired)
                                                <span class="badge bg-danger ms-2">EXPIRED</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->type_name }}</span>
                                        </td>
                                        <td>{{ $item->deletedBy->name ?? 'Unknown' }}</td>
                                        <td>{{ $item->formatted_deleted_at }}</td>
                                        <td>
                                            @if ($item->is_expired)
                                                <span class="badge bg-danger">Expired</span>
                                            @else
                                                <span class="badge bg-warning">{{ $item->days_until_expire }} days</span>
                                            @endif
                                        </td>
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
                                                    onclick="deleteItemPermanently({{ $item->id }})" title="Archive (Permanent)">
                                                    <i class="fas fa-archive"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-trash-restore fa-3x mb-3"></i>
                                        <p>No deleted items</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination for Deleted Items -->
                @if ($items->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm">
                                @if ($items->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">Back</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $items->previousPageUrl() }}" rel="prev">Back</a>
                                    </li>
                                @endif

                                @php
                                    $currentPage = $items->currentPage();
                                    $lastPage = $items->lastPage();
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
                                            <a class="page-link" href="{{ $items->url($page) }}">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if ($items->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $items->nextPageUrl() }}" rel="next">Next</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Next</span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                @else
                    @php $deletedCount = $items->count(); @endphp
                    @if ($deletedCount > 0)
                        <div class="d-flex justify-content-center mt-3">
                            <small class="text-muted">Showing {{ $deletedCount }} deleted item(s)</small>
                        </div>
                    @endif
                @endif
            </div>

            <!-- ARCHIVED ITEMS TABLE -->
            <div id="archived-items-section" class="tab-content" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="archivedBinTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">
                                    <input type="checkbox" id="selectAllArchived" onchange="toggleSelectAllArchived(this)">
                                </th>
                                <th>Item Name</th>
                                <th>Type</th>
                                <th>Deleted By</th>
                                <th>Deleted On</th>
                                <th>Archived On</th>
                                <th>Reason</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                @if($item->is_archived)
                                    <tr data-item-id="{{ $item->id }}" class="table-secondary">
                                        <td class="text-center">
                                            <input type="checkbox" class="item-checkbox-archived" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $item->item_name }}</strong>
                                            <span class="badge bg-secondary ms-2">ARCHIVED</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $item->type_name }}</span>
                                        </td>
                                        <td>{{ $item->deletedBy->name ?? 'Unknown' }}</td>
                                        <td>{{ $item->formatted_deleted_at }}</td>
                                        <td>{{ $item->archived_at ? $item->archived_at->format('M d, Y g:i A') : 'N/A' }}</td>
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
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-archive fa-3x mb-3"></i>
                                        <p>No archived items</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->where('is_archived', true)->count() > 0)
                    <div class="d-flex justify-content-center mt-3">
                        <small class="text-muted">Showing {{ $items->where('is_archived', true)->count() }} archived item(s)</small>
                    </div>
                @endif
            </div>
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

        /* Tab Styling */
        .tab-button {
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .tab-button.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .tab-button:hover:not(.active) {
            background-color: #f8f9fa;
        }

        .tab-content {
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Pagination Styles */
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

        /* Responsive */
        @media (max-width: 576px) {
            .pagination {
                padding: 4px;
            }

            .pagination .page-item .page-link {
                padding: 6px 10px;
                margin: 0 1px;
                font-size: 0.875rem;
            }

            .btn-group {
                flex-wrap: wrap;
            }
        }
    </style>

    <script>
        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Switch between tabs
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.style.display = 'none';
            });

            // Deactivate all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName + '-section').classList.add('active');
            document.getElementById(tabName + '-section').style.display = 'block';

            // Activate selected button
            document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

            console.log('Switched to tab:', tabName);
        }

        // Toggle select all for deleted items
        function toggleSelectAll(checkbox) {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox:not(.item-checkbox-archived)');
            itemCheckboxes.forEach(cb => cb.checked = checkbox.checked);
        }

        // Toggle select all for archived items
        function toggleSelectAllArchived(checkbox) {
            const itemCheckboxes = document.querySelectorAll('.item-checkbox-archived');
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
                        <p><strong>Status:</strong> <span class="badge bg-${item.is_archived ? 'secondary' : (item.is_expired ? 'danger' : 'warning')}">${item.is_archived ? 'ARCHIVED' : (item.is_expired ? 'EXPIRED' : 'ACTIVE')}</span></p>
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

        // Delete item permanently (Archives it)
        function deleteItemPermanently(itemId) {
            if (confirm('This item will be moved to Archives and cannot be undone. Continue?')) {
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
                        showToast('success', 'Item archived successfully - cannot be undone');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast('error', data.message);
                    }
                })
                .catch(error => showToast('error', 'Error archiving item'));
            }
        }

        // Empty recycle bin
        function emptyRecycleBin() {
            if (confirm('This will permanently delete all expired items. This cannot be undone. Continue?')) {
                fetch('/admin/recycle-bin/empty', {
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
                    }
                })
                .catch(error => showToast('error', 'Error emptying recycle bin'));
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
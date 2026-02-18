{{-- resources/views/admin/recycle-bin/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Recycle Bin - AgriSys Admin')
@section('page-icon', 'fas fa-trash-restore')
@section('page-title', 'Recycle Bin')

@section('content')

    <!-- Filters Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-2"></i>Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.recycle-bin.index') }}" id="filterForm">
                <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">
                <div class="row g-2">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3">
                            <select name="type" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Types</option>
                                <option value="fishr" {{ request('type') == 'fishr' ? 'selected' : '' }}>FishR Registrations</option>
                                <option value="fishr_annex" {{ request('type') == 'fishr_annex' ? 'selected' : '' }}>FishR Annexes</option>
                                <option value="boatr" {{ request('type') == 'boatr' ? 'selected' : '' }}>BoatR Registrations</option>
                                <option value="boatr_annex" {{ request('type') == 'boatr_annex' ? 'selected' : '' }}>BoatR Annexes</option>
                                <option value="rsbsa" {{ request('type') == 'rsbsa' ? 'selected' : '' }}>RSBSA Registrations</option>
                                <option value="training" {{ request('type') == 'training' ? 'selected' : '' }}>Training Requests</option>
                                <option value="seedlings" {{ request('type') == 'seedlings' ? 'selected' : '' }}>Supply Requests</option>
                                <option value="user_registration" {{ request('type') == 'user_registration' ? 'selected' : '' }}>User Registrations</option>
                                <option value="category_item" {{ request('type') == 'category_item' ? 'selected' : '' }}>Supply Items</option>
                                <option value="request_category" {{ request('type') == 'request_category' ? 'selected' : '' }}>Supply Categories</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search item name or reason..." value="{{ request('search') }}"
                                    oninput="handleSearchInput()" id="searchInput">
                                <button class="btn btn-outline-secondary btn-sm" type="submit" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#dateFilterModal">
                                <i class="fas fa-calendar-alt me-1"></i>Date Filter
                            </button>
                        </div>

                        <div class="col-md-2">
                            <a href="{{ route('admin.recycle-bin.index') }}" class="btn btn-secondary btn-sm w-100">
                                Clear 
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recycle Bin Items Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div></div>
            <div class="text-center flex-fill">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-trash-alt me-2"></i>Recycle Bin Items
                </h6>
            </div>
            <div class="d-flex gap-2">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="selectAllItems()"
                            id="selectAllBtn"
                            title="Select All Items on This Page">
                        <i class="fas fa-check-square me-1"></i>Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="deselectAllItems()"
                            id="deselectAllBtn"
                            title="Deselect All Items"
                            style="display: none;">
                        <i class="fas fa-square me-1"></i>Deselect All
                    </button>
                </div>
                <div class="btn-group" role="group" id="bulkActionsGroup" style="display: none;">
                    <button type="button" class="btn btn-sm btn-outline-success"
                            onclick="openBulkRestoreModal()"
                            title="Restore Selected Items">
                        <i class="fas fa-undo me-1"></i>Restore
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="openBulkDeleteModal()"
                            title="Permanently Delete Selected Items">
                        <i class="fas fa-trash-alt me-1"></i>Delete Permanently
                    </button>
                </div>
                <button type="button" class="btn btn-danger btn-sm"
                        onclick="openEmptyBinModal()"
                        title="Empty Entire Recycle Bin">
                    <i class="fas fa-broom me-1"></i>Empty Bin
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="recycleBinTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" id="checkboxHeaderRecycleBin"
                                       onchange="toggleAllCheckboxes(this)">
                            </th>
                            <th class="text-center">Type</th>
                            <th class="text-start">Item Name</th>
                            <th class="text-center">Deleted Date</th>
                            <th class="text-start">Deleted From</th>
                            <th class="text-start">Deleted By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr data-item-id="{{ $item->id }}" class="recycle-item">
                                <td class="text-center">
                                    <input type="checkbox" class="item-checkbox"
                                           value="{{ $item->id }}"
                                           onchange="updateBulkActionsVisibility()">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->type_name }}</span>
                                </td>
                                <td class="text-start">
                                    <strong class="text-dark">{{ $item->item_name }}</strong>
                                </td>
                                 <td class="text-center">
                                    <small class="text-muted">{{ $item->deleted_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-start">
                                    <small class="text-muted">{{ $item->reason ?? 'No reason provided' }}</small>
                                </td>
                                <td class="text-start">
                                    <small>{{ $item->deletedBy->name ?? 'Unknown' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary"
                                                onclick="viewRecycleBinItem({{ $item->id }})"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success"
                                                onclick="restoreRecycleBinItem({{ $item->id }}, '{{ $item->item_name }}')"
                                                title="Restore Item">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn btn-outline-danger"
                                                onclick="permanentlyDeleteRecycleBinItem({{ $item->id }}, '{{ $item->item_name }}')"
                                                title="Permanently Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <p>Recycle Bin is empty</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($items->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($items->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $items->previousPageUrl() }}" rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
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

                            {{-- Next Page Link --}}
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
            @endif
        </div>
    </div>

    <!-- View Item Details Modal -->
    <div class="modal fade" id="recycleBinDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-trash-alt me-2"></i>Recycle Bin Item Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card bg-light mb-3">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-danger">
                                <i class="fas fa-info-circle me-2"></i>Item Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <small class="d-block text-muted">Type</small>
                                    <strong id="detailsTypeName" class="text-danger"></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="d-block text-muted">Item Name</small>
                                    <strong id="detailsItemName"></strong>
                                </div>
                                <div class="col-12">
                                    <small class="d-block text-muted">Deleted From</small>
                                    <p id="detailsReason" class="mb-0">-</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="d-block text-muted">Deleted By</small>
                                    <strong id="detailsDeletedBy"></strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="d-block text-muted">Deleted Date</small>
                                    <strong id="detailsDeletedAt"></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-light">
                        <div class="card-header bg-white border-0 pb-0">
                            <h6 class="mb-0 fw-semibold text-danger">
                                <i class="fas fa-database me-2"></i>Original Data
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div id="detailsData" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody id="dataTableBody">
                                        <!-- Data will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Permanent Delete Single Item Confirmation Modal -->
    <div class="modal fade" id="permanentDeleteModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-trash-alt me-2"></i>Permanently Delete Item
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">This action cannot be undone. The item will be permanently deleted from the recycle bin.</p>
                    </div>
                    <div class="alert alert-light border mb-0" role="alert">
                        <small class="text-muted d-block">Item Name:</small>
                        <strong class="text-danger" id="deleteItemName"></strong>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmPermanentDelete()"
                            id="confirmDeleteBtn">
                        <span class="btn-text"><i class="fas fa-trash-alt me-2"></i>Delete Permanently</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bulk Restore Modal -->
    <div class="modal fade" id="bulkRestoreModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title w-100 text-center">Restore Items</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3" role="alert">
                        <!-- <i class="fas fa-info-circle me-2"></i> -->
                        <p class="mb-0">You are about to restore <strong id="bulkRestoreCount">0</strong> item(s) from the recycle bin.</p>
                    </div>
                    <p class="mb-0">Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="confirmBulkRestore()"
                            id="confirmBulkRestoreBtn">
                        <span class="btn-text"><i class="fas fa-undo me-2"></i>Restore Items</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Restoring...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Delete Modal -->
    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-trash-alt me-2"></i>Permanently Delete Items
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">This action cannot be undone. You will permanently delete <strong id="bulkDeleteCount">0</strong> item(s).</p>
                    </div>
                    <div class="alert alert-light border mb-0" role="alert">
                        <small class="text-muted">Are you sure you want to proceed?</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmBulkDelete()"
                            id="confirmBulkDeleteBtn">
                        <span class="btn-text"><i class="fas fa-trash-alt me-2"></i>Delete Permanently</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty Recycle Bin Confirmation Modal -->
    <div class="modal fade" id="emptyBinModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title w-100 text-center">
                        <i class="fas fa-broom me-2"></i>Empty Recycle Bin
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger mb-3" role="alert">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Warning!</strong>
                        <p class="mb-0">This will permanently delete all items in the recycle bin. This action cannot be undone.</p>
                    </div>
                    <div class="alert alert-light border mb-0" role="alert">
                        <small class="text-muted">Total items to delete:</small>
                        <p class="mb-0"><strong class="text-danger" id="totalItemsCount">0</strong></p>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmEmptyBin()"
                            id="confirmEmptyBtn">
                        <span class="btn-text"><i class="fas fa-broom me-2"></i>Empty Bin</span>
                        <span class="btn-loader" style="display: none;"><span class="spinner-border spinner-border-sm me-2"></span>Emptying...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title w-100 text-center">Date Filter</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="modal_date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="mb-3">
                        <label for="modal_date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="modal_date_to" value="{{ request('date_to') }}">
                    </div>
                    @if(request('date_from') || request('date_to'))
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-info-circle"></i>
                            Current filter:
                            @if(request('date_from'))
                                <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}</strong>
                            @else
                                <strong>Any date</strong>
                            @endif
                            to
                            @if(request('date_to'))
                                <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}</strong>
                            @else
                                <strong>Any date</strong>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearDateFilter()">Clear</button>
                    <button type="button" class="btn btn-primary" onclick="applyDateFilter()">
                        <i class="fas fa-check"></i> Apply Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .recycle-item {
            transition: background-color 0.3s ease;
        }

        .recycle-item.selected {
            background-color: #fff3cd;
        }

        .btn-group-sm > .btn {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }
            #recycleBinDetailsModal .modal-header {
            background-color: #dc3545 !important;
        }

        #recycleBinDetailsModal .text-danger {
            color: #dc3545 !important;
        }

        #dataTableBody tr {
            border-bottom: 1px solid #dee2e6;
        }

        #dataTableBody tr:last-child {
            border-bottom: none;
        }

        #dataTableBody td {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        #detailsTypeName,
        #detailsItemName,
        #detailsDeletedBy,
        #detailsDeletedAt {
            color: #dc3545;
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

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            font-weight: 600;
        }
    </style>

    <script>
        // CSRF Token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        }

        // Toast notification function
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
            setTimeout(() => removeToast(toast), 5000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
            return container;
        }

        function removeToast(element) {
            element.classList.remove('show');
            setTimeout(() => element.remove(), 300);
        }

        // Checkbox management
        function toggleAllCheckboxes(headerCheckbox) {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = headerCheckbox.checked;
            });
            updateBulkActionsVisibility();
        }

        function selectAllItems() {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = true);
            updateBulkActionsVisibility();
        }

        function deselectAllItems() {
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
            updateBulkActionsVisibility();
        }

        function updateBulkActionsVisibility() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            const bulkActionsGroup = document.getElementById('bulkActionsGroup');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const deselectAllBtn = document.getElementById('deselectAllBtn');
            const headerCheckbox = document.getElementById('checkboxHeaderRecycleBin');

            if (checked > 0) {
                bulkActionsGroup.style.display = 'flex';
                selectAllBtn.style.display = 'none';
                deselectAllBtn.style.display = 'block';
                headerCheckbox.checked = true;

                // Update row highlighting
                document.querySelectorAll('.recycle-item').forEach(row => {
                    const checkbox = row.querySelector('.item-checkbox');
                    if (checkbox.checked) {
                        row.classList.add('selected');
                    } else {
                        row.classList.remove('selected');
                    }
                });
            } else {
                bulkActionsGroup.style.display = 'none';
                selectAllBtn.style.display = 'block';
                deselectAllBtn.style.display = 'none';
                headerCheckbox.checked = false;
                document.querySelectorAll('.recycle-item').forEach(row => row.classList.remove('selected'));
            }
        }

        // View item details
       function viewRecycleBinItem(id) {
            fetch(`/admin/recycle-bin/${id}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('detailsTypeName').textContent = data.data.type_name;
                    document.getElementById('detailsItemName').textContent = data.data.item_name;
                    document.getElementById('detailsReason').textContent = data.data.reason || 'No reason provided';
                    document.getElementById('detailsDeletedBy').textContent = data.data.deleted_by_name;
                    document.getElementById('detailsDeletedAt').textContent = data.data.deleted_at;

                    // Populate the data table with formatted data
                    const dataTableBody = document.getElementById('dataTableBody');
                    dataTableBody.innerHTML = '';

                    const itemData = data.data.data;

                    if (itemData && typeof itemData === 'object') {
                        for (const [key, value] of Object.entries(itemData)) {
                            // Skip certain system fields
                            if (['created_at', 'updated_at', 'deleted_at'].includes(key)) continue;

                            const row = document.createElement('tr');
                            const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                            const formattedValue = formatValue(value);

                            row.innerHTML = `
                                <td style="width: 35%; word-break: break-word;">
                                    <small class="text-muted">${formattedKey}</small>
                                </td>
                                <td style="width: 65%; word-break: break-word;">
                                    <strong>${formattedValue}</strong>
                                </td>
                            `;

                            dataTableBody.appendChild(row);
                        }
                    }

                    new bootstrap.Modal(document.getElementById('recycleBinDetailsModal')).show();
                }
            })
            .catch(err => showToast('error', 'Error loading item details'));
        }

        // Helper function to format values nicely
        function formatValue(value) {
            if (value === null || value === undefined) {
                return '<span class="text-muted">-</span>';
            }

            if (typeof value === 'boolean') {
                return value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
            }

            if (typeof value === 'object') {
                return JSON.stringify(value);
            }

            if (typeof value === 'string' && value.length > 100) {
                return value.substring(0, 100) + '...';
            }

            return value;
        }
        // Restore single item
        function restoreRecycleBinItem(id, name) {
            fetch(`/admin/recycle-bin/${id}/restore`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('success', `"${name}" restored successfully`);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Failed to restore item');
                }
            })
            .catch(err => showToast('error', 'Error restoring item'));
        }

        // Permanent delete single item
        let currentDeleteId = null;
        function permanentlyDeleteRecycleBinItem(id, name) {
            currentDeleteId = id;
            document.getElementById('deleteItemName').textContent = name;
            new bootstrap.Modal(document.getElementById('permanentDeleteModal')).show();
        }

        function confirmPermanentDelete() {
            if (!currentDeleteId) return;
            const btn = document.getElementById('confirmDeleteBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'inline';
            btn.disabled = true;

            fetch(`/admin/recycle-bin/${currentDeleteId}/permanently-delete`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('permanentDeleteModal'));
                modal.hide();

                if (data.success) {
                    showToast('success', data.message || 'Item deleted permanently');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Failed to delete item');
                }
            })
            .catch(err => showToast('error', 'Error deleting item'))
            .finally(() => {
                btn.querySelector('.btn-text').style.display = 'inline';
                btn.querySelector('.btn-loader').style.display = 'none';
                btn.disabled = false;
            });
        }

        // Bulk restore
        function openBulkRestoreModal() {
            const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) {
                showToast('warning', 'No items selected');
                return;
            }

            document.getElementById('bulkRestoreCount').textContent = ids.length;
            new bootstrap.Modal(document.getElementById('bulkRestoreModal')).show();
        }

        function confirmBulkRestore() {
            const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            const btn = document.getElementById('confirmBulkRestoreBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'inline';
            btn.disabled = true;

            fetch('/admin/recycle-bin/bulk/restore', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids })
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkRestoreModal'));
                modal.hide();

                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Failed to restore items');
                }
            })
            .catch(err => showToast('error', 'Error restoring items'))
            .finally(() => {
                btn.querySelector('.btn-text').style.display = 'inline';
                btn.querySelector('.btn-loader').style.display = 'none';
                btn.disabled = false;
            });
        }

        // Bulk permanent delete
        function openBulkDeleteModal() {
            const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) {
                showToast('warning', 'No items selected');
                return;
            }

            document.getElementById('bulkDeleteCount').textContent = ids.length;
            new bootstrap.Modal(document.getElementById('bulkDeleteModal')).show();
        }

        function confirmBulkDelete() {
            const ids = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            const btn = document.getElementById('confirmBulkDeleteBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'inline';
            btn.disabled = true;

            fetch('/admin/recycle-bin/bulk/permanently-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids })
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('bulkDeleteModal'));
                modal.hide();

                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Failed to delete items');
                }
            })
            .catch(err => showToast('error', 'Error deleting items'))
            .finally(() => {
                btn.querySelector('.btn-text').style.display = 'inline';
                btn.querySelector('.btn-loader').style.display = 'none';
                btn.disabled = false;
            });
        }

        // Empty recycle bin
        function openEmptyBinModal() {
            const totalItems = document.querySelectorAll('.recycle-item').length;
            if (totalItems === 0) {
                showToast('warning', 'Recycle bin is already empty');
                return;
            }

            document.getElementById('totalItemsCount').textContent = totalItems;
            new bootstrap.Modal(document.getElementById('emptyBinModal')).show();
        }

        function confirmEmptyBin() {
            const btn = document.getElementById('confirmEmptyBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'inline';
            btn.disabled = true;

            fetch('/admin/recycle-bin/empty', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('emptyBinModal'));
                modal.hide();

                if (data.success) {
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('error', data.message || 'Failed to empty recycle bin');
                }
            })
            .catch(err => showToast('error', 'Error emptying recycle bin'))
            .finally(() => {
                btn.querySelector('.btn-text').style.display = 'inline';
                btn.querySelector('.btn-loader').style.display = 'none';
                btn.disabled = false;
            });
        }
        // auto searhc
       let searchTimeout;

        // Auto search functionality
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

        // Handle search input - auto-reset when empty
        function handleSearchInput() {
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');

            if (searchInput.value.trim() === '') {
                // If search is empty, reset and submit
                filterForm.submit();
            } else {
                // If search has value, use auto-search
                autoSearch();
            }
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        function applyDateFilter() {
            const dateFrom = document.getElementById('modal_date_from').value;
            const dateTo = document.getElementById('modal_date_to').value;

            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            document.getElementById('filterForm').submit();
        }

        function clearDateFilter() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';
            document.getElementById('filterForm').submit();
        }
    </script>
@endsection

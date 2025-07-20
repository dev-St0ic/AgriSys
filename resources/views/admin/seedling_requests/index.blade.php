{{-- resources/views/admin/seedling_requests/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Seedling Requests - AgriSys Admin')
@section('page-title', 'Seedling Requests')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Requests
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-seedling fa-2x text-gray-300"></i>
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
                                    Under Review
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $underReviewCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                    Approved
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approvedCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Rejected
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $rejectedCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.seedling.requests') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Status</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                    Under Review</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Categories</option>
                                <option value="vegetables" {{ request('category') == 'vegetables' ? 'selected' : '' }}>
                                    Vegetables</option>
                                <option value="fruits" {{ request('category') == 'fruits' ? 'selected' : '' }}>Fruits
                                </option>
                                <option value="fertilizers" {{ request('category') == 'fertilizers' ? 'selected' : '' }}>
                                    Fertilizers</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="barangay" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Barangay</option>
                                @foreach ($barangays as $barangay)
                                    <option value="{{ $barangay }}"
                                        {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Search name, number, contact..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.seedling.requests') }}" class="btn btn-secondary btn-sm w-100">
                                <i class="fas fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($requests->count() > 0)
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Request #</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Barangay</th>
                                    <th>Items Requested</th>
                                    <th>Total Qty</th>
                                    <th>Inventory Status</th>
                                    <th>Status</th>
                                    <th>Date Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $request->request_number }}</strong>
                                        </td>
                                        <td>{{ $request->full_name }}</td>
                                        <td>{{ $request->contact_number }}</td>
                                        <td>{{ $request->barangay }}</td>
                                        <td>
                                            @if ($request->vegetables && count($request->vegetables) > 0)
                                                <div class="mb-1">
                                                    <strong style="color: #28a745;">🌱 Vegetables:</strong><br>
                                                    <small>{{ $request->formatted_vegetables }}</small>
                                                </div>
                                            @endif

                                            @if ($request->fruits && count($request->fruits) > 0)
                                                <div class="mb-1">
                                                    <strong style="color: #17a2b8;">🍎 Fruits:</strong><br>
                                                    <small>{{ $request->formatted_fruits }}</small>
                                                </div>
                                            @endif

                                            @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                <div class="mb-1">
                                                    <strong style="color: #ffc107;">🌿 Fertilizers:</strong><br>
                                                    <small>{{ $request->formatted_fertilizers }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fs-6 px-3 py-2">
                                                {{ $request->total_quantity ?? $request->requested_quantity }} pcs
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $inventoryStatus = $request->checkInventoryAvailability();
                                                $canFulfill = $inventoryStatus['can_fulfill'] ?? false;
                                                $unavailableItems = $inventoryStatus['unavailable_items'] ?? [];
                                            @endphp

                                            @if ($request->status === 'approved')
                                                <span class="badge bg-secondary fs-6 px-2 py-1">
                                                    <i class="fas fa-check"></i> Deducted
                                                </span>
                                            @elseif($canFulfill)
                                                <span class="badge bg-success fs-6 px-2 py-1">
                                                    <i class="fas fa-check"></i> Available
                                                </span>
                                            @else
                                                <span class="badge bg-danger fs-6 px-2 py-1">
                                                    <i class="fas fa-exclamation-triangle"></i> Low Stock
                                                </span>
                                                @if (count($unavailableItems) > 0)
                                                    <br><small class="text-danger">
                                                        Short:
                                                        {{ implode(
                                                            ', ',
                                                            array_map(function ($item) {
                                                                return $item['name'] . ' (need ' . $item['needed'] . ', have ' . $item['available'] . ')';
                                                            }, $unavailableItems),
                                                        ) }}
                                                    </small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status_color }} fs-6 px-3 py-2">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('M d, Y g:i A') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $request->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#statusModal{{ $request->id }}">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>

                                                @if ($request->hasDocuments())
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        onclick="viewDocument('{{ $request->document_path }}')"
                                                        title="View Document">
                                                        <i class="fas fa-file-alt"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Request Details -
                                                        {{ $request->request_number }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Personal Information</h6>
                                                            <p><strong>Name:</strong> {{ $request->full_name }}</p>
                                                            <p><strong>Contact:</strong> {{ $request->contact_number }}</p>
                                                            <p><strong>Barangay:</strong> {{ $request->barangay }}</p>
                                                            <p><strong>Address:</strong> {{ $request->address }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Request Information</h6>
                                                            <p><strong>Total Quantity:</strong>
                                                                {{ $request->total_quantity }} pcs</p>
                                                            <p><strong>Status:</strong>
                                                                <span class="badge bg-{{ $request->status_color }}">
                                                                    {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                                                </span>
                                                            </p>
                                                            <p><strong>Date Submitted:</strong>
                                                                {{ $request->created_at->format('F d, Y g:i A') }}</p>
                                                            @if ($request->remarks)
                                                                <p><strong>Remarks:</strong> {{ $request->remarks }}</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <hr>

                                                    <h6>Inventory Availability</h6>
                                                    @php
                                                        $inventoryStatus = $request->checkInventoryAvailability();
                                                        $canFulfill = $inventoryStatus['can_fulfill'] ?? false;
                                                        $unavailableItems = $inventoryStatus['unavailable_items'] ?? [];
                                                        $availableItems = $inventoryStatus['available_items'] ?? [];
                                                    @endphp

                                                    @if ($request->status === 'approved')
                                                        <div class="alert alert-success">
                                                            <i class="fas fa-check-circle"></i> This request has been
                                                            approved and inventory has been deducted.
                                                        </div>
                                                    @elseif($canFulfill)
                                                        <div class="alert alert-success">
                                                            <i class="fas fa-check-circle"></i> All requested items are
                                                            available in inventory.
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i> Some items have
                                                            insufficient stock for approval.
                                                        </div>
                                                    @endif

                                                    @if (count($availableItems) > 0)
                                                        <div class="mb-3">
                                                            <strong class="text-success">✓ Available Items:</strong>
                                                            <ul class="list-unstyled mt-2">
                                                                @foreach ($availableItems as $item)
                                                                    <li class="text-success">
                                                                        <i class="fas fa-check"></i> {{ $item['name'] }}
                                                                        ({{ $item['available'] }} available,
                                                                        {{ $item['needed'] }} needed)
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    @if (count($unavailableItems) > 0)
                                                        <div class="mb-3">
                                                            <strong class="text-danger">⚠ Insufficient Stock:</strong>
                                                            <ul class="list-unstyled mt-2">
                                                                @foreach ($unavailableItems as $item)
                                                                    <li class="text-danger">
                                                                        <i class="fas fa-times"></i> {{ $item['name'] }}
                                                                        ({{ $item['available'] }} available,
                                                                        {{ $item['needed'] }} needed)
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                                                    <hr>

                                                    <h6>Selected Items</h6>
                                                    @if ($request->vegetables && count($request->vegetables) > 0)
                                                        <p><strong>🌱 Vegetables:</strong>
                                                            {{ $request->formatted_vegetables }}</p>
                                                    @endif
                                                    @if ($request->fruits && count($request->fruits) > 0)
                                                        <p><strong>🍎 Fruits:</strong> {{ $request->formatted_fruits }}</p>
                                                    @endif
                                                    @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                        <p><strong>🌿 Fertilizers:</strong>
                                                            {{ $request->formatted_fertilizers }}</p>
                                                    @endif

                                                    @if ($request->hasDocuments())
                                                        <hr>
                                                        <h6>Supporting Documents</h6>
                                                        <p>
                                                            @if (($request->total_quantity ?? $request->requested_quantity) >= 100)
                                                                <span class="badge bg-success mb-2">Required Document
                                                                    Provided</span><br>
                                                            @else
                                                                <span class="badge bg-info mb-2">Optional Document
                                                                    Provided</span><br>
                                                            @endif
                                                            <a href="{{ $request->document_url }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-file-alt"></i> View Document
                                                            </a>
                                                        </p>
                                                    @else
                                                        @if (($request->total_quantity ?? $request->requested_quantity) >= 100)
                                                            <hr>
                                                            <h6>Supporting Documents</h6>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Required supporting document is missing for this request
                                                                (>100 pcs).
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status Update Modal -->
                                    <div class="modal fade" id="statusModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST"
                                                    action="{{ route('admin.seedling.update-status', $request) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Status -
                                                            {{ $request->request_number }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @php
                                                            $inventoryCheck = $request->checkInventoryAvailability();
                                                            $canFulfill = $inventoryCheck['can_fulfill'] ?? false;
                                                            $unavailableItems =
                                                                $inventoryCheck['unavailable_items'] ?? [];
                                                        @endphp

                                                        @if (!$canFulfill && count($unavailableItems) > 0 && $request->status !== 'approved')
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                <strong>Inventory Warning:</strong> Some items have
                                                                insufficient stock.
                                                                <ul class="mt-2 mb-0">
                                                                    @foreach ($unavailableItems as $item)
                                                                        <li>{{ $item['name'] }}: {{ $item['available'] }}
                                                                            available, {{ $item['needed'] }} needed</li>
                                                                    @endforeach
                                                                </ul>
                                                                <small class="text-muted">Cannot approve until inventory is
                                                                    restocked.</small>
                                                            </div>
                                                        @endif

                                                        <div class="mb-3">
                                                            <label for="status{{ $request->id }}"
                                                                class="form-label">Status</label>
                                                            <select name="status" id="status{{ $request->id }}"
                                                                class="form-select" required>
                                                                <option value="under_review"
                                                                    {{ $request->status == 'under_review' ? 'selected' : '' }}>
                                                                    Under Review</option>
                                                                <option value="approved"
                                                                    {{ $request->status == 'approved' ? 'selected' : '' }}>
                                                                    Approved</option>
                                                                <option value="rejected"
                                                                    {{ $request->status == 'rejected' ? 'selected' : '' }}>
                                                                    Rejected</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="approved_quantity{{ $request->id }}"
                                                                class="form-label">Approved Quantity</label>
                                                            <input type="number" name="approved_quantity"
                                                                id="approved_quantity{{ $request->id }}"
                                                                class="form-control"
                                                                value="{{ $request->approved_quantity ?? $request->total_quantity }}"
                                                                min="1" max="{{ $request->total_quantity }}">
                                                            <small class="text-muted">Max: {{ $request->total_quantity }}
                                                                pcs</small>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="remarks{{ $request->id }}"
                                                                class="form-label">Remarks</label>
                                                            <textarea name="remarks" id="remarks{{ $request->id }}" class="form-control" rows="3">{{ $request->remarks }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update
                                                            Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($requests->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $requests->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $requests->currentPage();
                                $lastPage = $requests->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);

                                // Ensure we always show 5 pages when possible
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
                                        <a class="page-link" href="{{ $requests->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($requests->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $requests->nextPageUrl() }}" rel="next">Next</a>
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
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No seedling requests found</h5>
                    <p class="text-muted">
                        @if (request('search') || request('status'))
                            No requests match your search criteria.
                        @else
                            There are no seedling requests yet.
                        @endif
                    </p>
                    @if (request('search') || request('status'))
                        <a href="{{ route('admin.seedling.requests') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Document Viewer Modal -->
        <div class="modal fade" id="documentModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt me-2"></i>Supporting Document
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="documentViewer">
                        <!-- Document will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
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

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.75em;
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
        let searchTimeout;

        // Auto search functionality
        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500); // Wait 500ms after user stops typing
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // View document function
        function viewDocument(path) {
            const documentViewer = document.getElementById('documentViewer');
            const fileExtension = path.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                documentViewer.innerHTML = `<img src="/storage/${path}" class="img-fluid" alt="Supporting Document">`;
            } else if (fileExtension === 'pdf') {
                documentViewer.innerHTML =
                    `<embed src="/storage/${path}" type="application/pdf" width="100%" height="600px">`;
            } else {
                documentViewer.innerHTML =
                    `<p>Document type not supported for preview. <a href="/storage/${path}" target="_blank">Download</a></p>`;
            }

            const modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();
        }
    </script>
@endsection

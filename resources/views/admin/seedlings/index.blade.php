{{-- resources/views/admin/seedlings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Seedling Requests - AgriSys Admin')
@section('page-title', 'Seedling Requests')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 mb-4">
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

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pending
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

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Fully Approved
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

            <div class="col-xl-2 col-md-4 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Partially Approved
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $partiallyApprovedCount ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-double fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4 mb-4">
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
                <form method="GET" action="{{ route('admin.seedlings.requests') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <select name="status" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Status</option>
                                <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Fully
                                    Approved
                                </option>
                                <option value="partially_approved"
                                    {{ request('status') == 'partially_approved' ? 'selected' : '' }}>Partially Approved
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
                            <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-secondary btn-sm w-100">
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
                                    <th>Vegetables</th>
                                    <th>Fruits</th>
                                    <th>Fertilizers</th>
                                    <th>Overall Status</th>
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

                                        <!-- Vegetables Column -->
                                        <td>
                                            @if ($request->vegetables && count($request->vegetables) > 0)
                                                @php
                                                    $vegStatus = $request->vegetables_status ?? 'under_review';
                                                    $vegCheck = $request->checkCategoryInventoryAvailability(
                                                        'vegetables',
                                                    );
                                                @endphp

                                                <div class="mb-1">
                                                    <small>{{ $request->formatted_vegetables }}</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <!-- Status Badge -->
                                                    <span
                                                        class="badge bg-{{ match ($vegStatus) {
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            'under_review' => 'secondary',
                                                            default => 'secondary',
                                                        } }} fs-6">
                                                        {{ $vegStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $vegStatus)) }}
                                                    </span>

                                                    <!-- Inventory Status -->
                                                    @if ($vegStatus !== 'approved')
                                                        @if ($vegCheck['can_fulfill'])
                                                            <span class="badge bg-success fs-6" title="Stock Available">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger fs-6" title="Low Stock">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <!-- Fruits Column -->
                                        <td>
                                            @if ($request->fruits && count($request->fruits) > 0)
                                                @php
                                                    $fruitStatus = $request->fruits_status ?? 'under_review';
                                                    $fruitCheck = $request->checkCategoryInventoryAvailability(
                                                        'fruits',
                                                    );
                                                @endphp

                                                <div class="mb-1">
                                                    <small>{{ $request->formatted_fruits }}</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <!-- Status Badge -->
                                                    <span
                                                        class="badge bg-{{ match ($fruitStatus) {
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            'under_review' => 'secondary',
                                                            default => 'secondary',
                                                        } }} fs-6">
                                                        {{ $fruitStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $fruitStatus)) }}
                                                    </span>

                                                    <!-- Inventory Status -->
                                                    @if ($fruitStatus !== 'approved')
                                                        @if ($fruitCheck['can_fulfill'])
                                                            <span class="badge bg-success fs-6" title="Stock Available">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger fs-6" title="Low Stock">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <!-- Fertilizers Column -->
                                        <td>
                                            @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                @php
                                                    $fertStatus = $request->fertilizers_status ?? 'under_review';
                                                    $fertCheck = $request->checkCategoryInventoryAvailability(
                                                        'fertilizers',
                                                    );
                                                @endphp

                                                <div class="mb-1">
                                                    <small>{{ $request->formatted_fertilizers }}</small>
                                                </div>

                                                <div class="d-flex align-items-center gap-1">
                                                    <!-- Status Badge -->
                                                    <span
                                                        class="badge bg-{{ match ($fertStatus) {
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            'under_review' => 'secondary',
                                                            default => 'secondary',
                                                        } }} fs-6">
                                                        {{ $fertStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $fertStatus)) }}
                                                    </span>

                                                    <!-- Inventory Status -->
                                                    @if ($fertStatus !== 'approved')
                                                        @if ($fertCheck['can_fulfill'])
                                                            <span class="badge bg-success fs-6" title="Stock Available">
                                                                <i class="fas fa-check"></i>
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger fs-6" title="Low Stock">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <!-- Overall Status -->
                                        <td>
                                            <span
                                                class="badge bg-{{ match ($request->overall_status) {
                                                    'approved' => 'success',
                                                    'partially_approved' => 'warning',
                                                    'rejected' => 'danger',
                                                    'under_review' => 'secondary',
                                                    default => 'secondary',
                                                } }} fs-6 px-3 py-2">
                                                {{ $request->overall_status == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $request->overall_status)) }}
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
                                                    data-bs-target="#categoryModal{{ $request->id }}">
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
                                                            <p><strong>Email:</strong> {{ $request->email ?? 'N/A' }}</p>
                                                            <p><strong>Barangay:</strong> {{ $request->barangay }}</p>
                                                            <p><strong>Address:</strong> {{ $request->address }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Request Information</h6>
                                                            <p><strong>Total Quantity:</strong>
                                                                {{ $request->total_quantity }} pcs</p>
                                                            <p><strong>Overall Status:</strong>
                                                                <span
                                                                    class="badge bg-{{ match ($request->overall_status) {
                                                                        'approved' => 'success',
                                                                        'partially_approved' => 'warning',
                                                                        'rejected' => 'danger',
                                                                        'under_review' => 'secondary',
                                                                        default => 'secondary',
                                                                    } }}">
                                                                    {{ $request->overall_status == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $request->overall_status)) }}
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

                                                    <h6>Category Status & Inventory</h6>

                                                    <!-- Vegetables -->
                                                    @if ($request->vegetables && count($request->vegetables) > 0)
                                                        @php
                                                            $vegStatus = $request->vegetables_status ?? 'under_review';
                                                            $vegCheck = $request->checkCategoryInventoryAvailability(
                                                                'vegetables',
                                                            );
                                                        @endphp
                                                        <div class="mb-3 p-3 border rounded">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-success">🌱 Vegetables</strong>
                                                                <span
                                                                    class="badge bg-{{ match ($vegStatus) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        'under_review' => 'secondary',
                                                                        default => 'secondary',
                                                                    } }}">
                                                                    {{ $vegStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $vegStatus)) }}
                                                                </span>
                                                            </div>
                                                            <p><small>{{ $request->formatted_vegetables }}</small></p>

                                                            @if ($vegStatus !== 'approved')
                                                                @if ($vegCheck['can_fulfill'])
                                                                    <div class="alert alert-success alert-sm">
                                                                        <i class="fas fa-check-circle"></i> All items
                                                                        available in stock
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning alert-sm">
                                                                        <i class="fas fa-exclamation-triangle"></i> Some
                                                                        items have insufficient stock
                                                                        @if (count($vegCheck['unavailable_items']) > 0)
                                                                            <ul class="mt-1 mb-0">
                                                                                @foreach ($vegCheck['unavailable_items'] as $item)
                                                                                    <li>{{ $item['name'] }}:
                                                                                        {{ $item['available'] }} available,
                                                                                        {{ $item['needed'] }} needed</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="alert alert-info alert-sm">
                                                                    <i class="fas fa-check"></i> Approved and inventory
                                                                    deducted
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Fruits -->
                                                    @if ($request->fruits && count($request->fruits) > 0)
                                                        @php
                                                            $fruitStatus = $request->fruits_status ?? 'under_review';
                                                            $fruitCheck = $request->checkCategoryInventoryAvailability(
                                                                'fruits',
                                                            );
                                                        @endphp
                                                        <div class="mb-3 p-3 border rounded">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-info">🍎 Fruits</strong>
                                                                <span
                                                                    class="badge bg-{{ match ($fruitStatus) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        'under_review' => 'secondary',
                                                                        default => 'secondary',
                                                                    } }}">
                                                                    {{ $fruitStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $fruitStatus)) }}
                                                                </span>
                                                            </div>
                                                            <p><small>{{ $request->formatted_fruits }}</small></p>

                                                            @if ($fruitStatus !== 'approved')
                                                                @if ($fruitCheck['can_fulfill'])
                                                                    <div class="alert alert-success alert-sm">
                                                                        <i class="fas fa-check-circle"></i> All items
                                                                        available in stock
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning alert-sm">
                                                                        <i class="fas fa-exclamation-triangle"></i> Some
                                                                        items have insufficient stock
                                                                        @if (count($fruitCheck['unavailable_items']) > 0)
                                                                            <ul class="mt-1 mb-0">
                                                                                @foreach ($fruitCheck['unavailable_items'] as $item)
                                                                                    <li>{{ $item['name'] }}:
                                                                                        {{ $item['available'] }} available,
                                                                                        {{ $item['needed'] }} needed</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="alert alert-info alert-sm">
                                                                    <i class="fas fa-check"></i> Approved and inventory
                                                                    deducted
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Fertilizers -->
                                                    @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                        @php
                                                            $fertStatus =
                                                                $request->fertilizers_status ?? 'under_review';
                                                            $fertCheck = $request->checkCategoryInventoryAvailability(
                                                                'fertilizers',
                                                            );
                                                        @endphp
                                                        <div class="mb-3 p-3 border rounded">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <strong class="text-warning">🌿 Fertilizers</strong>
                                                                <span
                                                                    class="badge bg-{{ match ($fertStatus) {
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        'under_review' => 'secondary',
                                                                        default => 'secondary',
                                                                    } }}">
                                                                    {{ $fertStatus == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $fertStatus)) }}
                                                                </span>
                                                            </div>
                                                            <p><small>{{ $request->formatted_fertilizers }}</small></p>

                                                            @if ($fertStatus !== 'approved')
                                                                @if ($fertCheck['can_fulfill'])
                                                                    <div class="alert alert-success alert-sm">
                                                                        <i class="fas fa-check-circle"></i> All items
                                                                        available in stock
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning alert-sm">
                                                                        <i class="fas fa-exclamation-triangle"></i> Some
                                                                        items have insufficient stock
                                                                        @if (count($fertCheck['unavailable_items']) > 0)
                                                                            <ul class="mt-1 mb-0">
                                                                                @foreach ($fertCheck['unavailable_items'] as $item)
                                                                                    <li>{{ $item['name'] }}:
                                                                                        {{ $item['available'] }} available,
                                                                                        {{ $item['needed'] }} needed</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="alert alert-info alert-sm">
                                                                    <i class="fas fa-check"></i> Approved and inventory
                                                                    deducted
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    @if ($request->hasDocuments())
                                                        <hr>
                                                        <h6>Supporting Documents</h6>
                                                        <p>
                                                            <a href="{{ $request->document_url }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-file-alt"></i> View Document
                                                            </a>
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category-specific Update Modal -->
                                    <div class="modal fade" id="categoryModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Categories -
                                                        {{ $request->request_number }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST"
                                                        action="{{ route('admin.seedlings.bulk-update-categories', $request) }}"
                                                        id="categoryForm{{ $request->id }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <!-- Vegetables Section -->
                                                        @if ($request->vegetables && count($request->vegetables) > 0)
                                                            @php
                                                                $vegCheck = $request->checkCategoryInventoryAvailability(
                                                                    'vegetables',
                                                                );
                                                                $vegStatus =
                                                                    $request->vegetables_status ?? 'under_review';
                                                            @endphp
                                                            <div class="mb-4 p-3 border rounded">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <h6 class="mb-0 text-success">🌱 Vegetables</h6>
                                                                    <span
                                                                        class="badge bg-{{ $vegCheck['can_fulfill'] ? 'success' : 'danger' }}">
                                                                        {{ $vegCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                                    </span>
                                                                </div>

                                                                <p class="mb-2">
                                                                    <small>{{ $request->formatted_vegetables }}</small></p>

                                                                <input type="hidden" name="categories[]"
                                                                    value="vegetables">
                                                                <select name="statuses[]"
                                                                    class="form-select form-select-sm">
                                                                    <option value="under_review"
                                                                        {{ $vegStatus == 'under_review' ? 'selected' : '' }}>
                                                                        Pending</option>
                                                                    <option value="approved"
                                                                        {{ $vegStatus == 'approved' ? 'selected' : '' }}
                                                                        {{ !$vegCheck['can_fulfill'] ? 'disabled' : '' }}>
                                                                        Approved
                                                                        {{ !$vegCheck['can_fulfill'] ? '(Insufficient Stock)' : '' }}
                                                                    </option>
                                                                    <option value="rejected"
                                                                        {{ $vegStatus == 'rejected' ? 'selected' : '' }}>
                                                                        Rejected</option>
                                                                </select>

                                                                @if (!$vegCheck['can_fulfill'])
                                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                                        <small>
                                                                            <i class="fas fa-exclamation-triangle"></i>
                                                                            Insufficient stock:
                                                                            @foreach ($vegCheck['unavailable_items'] as $item)
                                                                                {{ $item['name'] }} (need
                                                                                {{ $item['needed'] }}, have
                                                                                {{ $item['available'] }}){{ !$loop->last ? ', ' : '' }}
                                                                            @endforeach
                                                                        </small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- Fruits Section -->
                                                        @if ($request->fruits && count($request->fruits) > 0)
                                                            @php
                                                                $fruitCheck = $request->checkCategoryInventoryAvailability(
                                                                    'fruits',
                                                                );
                                                                $fruitStatus =
                                                                    $request->fruits_status ?? 'under_review';
                                                            @endphp
                                                            <div class="mb-4 p-3 border rounded">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <h6 class="mb-0 text-info">🍎 Fruits</h6>
                                                                    <span
                                                                        class="badge bg-{{ $fruitCheck['can_fulfill'] ? 'success' : 'danger' }}">
                                                                        {{ $fruitCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                                    </span>
                                                                </div>

                                                                <p class="mb-2">
                                                                    <small>{{ $request->formatted_fruits }}</small></p>

                                                                <input type="hidden" name="categories[]" value="fruits">
                                                                <select name="statuses[]"
                                                                    class="form-select form-select-sm">
                                                                    <option value="under_review"
                                                                        {{ $fruitStatus == 'under_review' ? 'selected' : '' }}>
                                                                        Pending</option>
                                                                    <option value="approved"
                                                                        {{ $fruitStatus == 'approved' ? 'selected' : '' }}
                                                                        {{ !$fruitCheck['can_fulfill'] ? 'disabled' : '' }}>
                                                                        Approved
                                                                        {{ !$fruitCheck['can_fulfill'] ? '(Insufficient Stock)' : '' }}
                                                                    </option>
                                                                    <option value="rejected"
                                                                        {{ $fruitStatus == 'rejected' ? 'selected' : '' }}>
                                                                        Rejected</option>
                                                                </select>

                                                                @if (!$fruitCheck['can_fulfill'])
                                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                                        <small>
                                                                            <i class="fas fa-exclamation-triangle"></i>
                                                                            Insufficient stock:
                                                                            @foreach ($fruitCheck['unavailable_items'] as $item)
                                                                                {{ $item['name'] }} (need
                                                                                {{ $item['needed'] }}, have
                                                                                {{ $item['available'] }}){{ !$loop->last ? ', ' : '' }}
                                                                            @endforeach
                                                                        </small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- Fertilizers Section -->
                                                        @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                            @php
                                                                $fertCheck = $request->checkCategoryInventoryAvailability(
                                                                    'fertilizers',
                                                                );
                                                                $fertStatus =
                                                                    $request->fertilizers_status ?? 'under_review';
                                                            @endphp
                                                            <div class="mb-4 p-3 border rounded">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center mb-3">
                                                                    <h6 class="mb-0 text-warning">Fertilizers</h6>
                                                                    <span
                                                                        class="badge bg-{{ $fertCheck['can_fulfill'] ? 'success' : 'danger' }}">
                                                                        {{ $fertCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                                    </span>
                                                                </div>

                                                                <p class="mb-2">
                                                                    <small>{{ $request->formatted_fertilizers }}</small>
                                                                </p>

                                                                <input type="hidden" name="categories[]"
                                                                    value="fertilizers">
                                                                <select name="statuses[]"
                                                                    class="form-select form-select-sm">
                                                                    <option value="under_review"
                                                                        {{ $fertStatus == 'under_review' ? 'selected' : '' }}>
                                                                        Pending</option>
                                                                    <option value="approved"
                                                                        {{ $fertStatus == 'approved' ? 'selected' : '' }}
                                                                        {{ !$fertCheck['can_fulfill'] ? 'disabled' : '' }}>
                                                                        Approved
                                                                        {{ !$fertCheck['can_fulfill'] ? '(Insufficient Stock)' : '' }}
                                                                    </option>
                                                                    <option value="rejected"
                                                                        {{ $fertStatus == 'rejected' ? 'selected' : '' }}>
                                                                        Rejected</option>
                                                                </select>

                                                                @if (!$fertCheck['can_fulfill'])
                                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                                        <small>
                                                                            <i class="fas fa-exclamation-triangle"></i>
                                                                            Insufficient stock:
                                                                            @foreach ($fertCheck['unavailable_items'] as $item)
                                                                                {{ $item['name'] }} (need
                                                                                {{ $item['needed'] }}, have
                                                                                {{ $item['available'] }}){{ !$loop->last ? ', ' : '' }}
                                                                            @endforeach
                                                                        </small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- General Remarks -->
                                                        <div class="mb-3">
                                                            <label for="remarks{{ $request->id }}"
                                                                class="form-label">General Remarks</label>
                                                            <textarea name="remarks" id="remarks{{ $request->id }}" class="form-control" rows="3"
                                                                placeholder="Add any general comments or notes...">{{ $request->remarks }}</textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" form="categoryForm{{ $request->id }}"
                                                        class="btn btn-primary">Update Categories</button>
                                                </div>
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
                                        <a class="page-link"
                                            href="{{ $requests->url($page) }}">{{ $page }}</a>
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
                        <a href="{{ route('admin.seedlings.requests') }}" class="btn btn-outline-primary">
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

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
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

        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        .alert-sm ul {
            margin: 0;
            padding-left: 1rem;
        }

        /* Custom table cell styling for category columns */
        .table td:nth-child(5),
        .table td:nth-child(6),
        .table td:nth-child(7) {
            min-width: 180px;
            vertical-align: middle;
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

        /* Category status indicators */
        .category-status {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .stock-indicator {
            font-size: 0.7rem;
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

        // Real-time inventory checking (optional enhancement)
        function checkInventoryStatus(requestId, category) {
            fetch(`/admin/seedling-requests/${requestId}/inventory/${category}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI based on inventory status
                        updateInventoryIndicator(requestId, category, data.data);
                    }
                })
                .catch(error => {
                    console.error('Error checking inventory:', error);
                });
        }

        function updateInventoryIndicator(requestId, category, inventoryData) {
            const indicator = document.querySelector(`#${category}-indicator-${requestId}`);
            if (indicator) {
                if (inventoryData.can_fulfill) {
                    indicator.className = 'badge bg-success fs-6';
                    indicator.innerHTML = '<i class="fas fa-check"></i>';
                    indicator.title = 'Stock Available';
                } else {
                    indicator.className = 'badge bg-danger fs-6';
                    indicator.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                    indicator.title = 'Low Stock';
                }
            }
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection

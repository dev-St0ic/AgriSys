{{-- resources/views/admin/seedlings/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Seedling Requests - AgriSys Admin')
@section('page-title', 'Seedling Requests')

@section('content')
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                    Total Requests
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $totalRequests }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-primary bg-opacity-10">
                                    <i class="fas fa-seedling text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                    Pending
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $underReviewCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-warning bg-opacity-10">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                    Fully Approved
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $approvedCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-success bg-opacity-10">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                    Partially Approved
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $partiallyApprovedCount ?? 0 }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-info bg-opacity-10">
                                    <i class="fas fa-check-double text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-2 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                    Rejected
                                </div>
                                <div class="h5 mb-0 fw-bold text-dark">{{ $rejectedCount }}</div>
                            </div>
                            <div class="ms-3">
                                <div class="icon-circle bg-danger bg-opacity-10">
                                    <i class="fas fa-times-circle text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-filter me-2 text-primary"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.seedlings.requests') }}" id="filterForm">
                    <!-- Hidden date inputs -->
                    <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label text-sm fw-medium text-muted">Status</label>
                            <select name="status" class="form-select form-select-sm border-light"
                                onchange="submitFilterForm()">
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
                            <label class="form-label text-sm fw-medium text-muted">Category</label>
                            <select name="category" class="form-select form-select-sm border-light"
                                onchange="submitFilterForm()">
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
                            <label class="form-label text-sm fw-medium text-muted">Barangay</label>
                            <select name="barangay" class="form-select form-select-sm border-light"
                                onchange="submitFilterForm()">
                                <option value="">All Barangay</option>
                                @foreach ($barangays as $barangay)
                                    <option value="{{ $barangay }}"
                                        {{ request('barangay') == $barangay ? 'selected' : '' }}>{{ $barangay }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-sm fw-medium text-muted">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm border-light"
                                placeholder="Search name, number, contact..." value="{{ request('search') }}"
                                oninput="autoSearch()" id="searchInput">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-sm fw-medium text-muted">&nbsp;</label>
                            <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#dateFilterModal">
                                <i class="fas fa-calendar-alt me-1"></i>Date Filter
                            </button>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-sm fw-medium text-muted">&nbsp;</label>
                            <a href="{{ route('admin.seedlings.requests') }}"
                                class="btn btn-light btn-sm w-100 d-block border">
                                <i class="fas fa-times"></i>
                            </a>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($requests->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="px-3 py-3 fw-medium text-muted border-end">Date Applied</th>
                                    <th class="px-3 py-3 fw-medium text-muted border-end">Request #</th>
                                    <th class="px-3 py-3 fw-medium text-muted border-end">Name</th>
                                    <th class="px-3 py-3 fw-medium text-muted border-end">Barangay</th>
                                    @if(!request('category') || request('category') == 'vegetables')
                                        <th class="px-3 py-3 fw-medium text-muted border-end">Vegetables</th>
                                    @endif
                                    @if(!request('category') || request('category') == 'fruits')
                                        <th class="px-3 py-3 fw-medium text-muted border-end">Fruits</th>
                                    @endif
                                    @if(!request('category') || request('category') == 'fertilizers')
                                        <th class="px-3 py-3 fw-medium text-muted border-end">Fertilizers</th>
                                    @endif
                                    <th class="px-3 py-3 fw-medium text-muted border-end">Overall Status</th>
                                    <th class="px-3 py-3 fw-medium text-muted text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $request)
                                    <tr class="border-bottom">
                                        <td class="px-3 py-3 border-end">
                                            <small
                                                class="text-muted">{{ $request->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="fw-bold text-primary">{{ $request->request_number }}</span>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="fw-medium">{{ $request->full_name }}</span>
                                        </td>
                                        <td class="px-3 py-3 border-end">
                                            <span class="text-dark">{{ $request->barangay }}</span>
                                        </td>

                                        <!-- Vegetables Column -->
                                        @if(!request('category') || request('category') == 'vegetables')
                                        <td class="px-3 py-3 border-end">
                                            @if ($request->vegetables && count($request->vegetables) > 0)
                                                @php
                                                    $vegStatus = $request->vegetables_status ?? 'under_review';
                                                    $vegCheck = $request->checkCategoryInventoryAvailability(
                                                        'vegetables',
                                                    );
                                                    $approvedItems = $request->vegetables_approved_items ?? [];
                                                    $rejectedItems = $request->vegetables_rejected_items ?? [];
                                                @endphp

                                                <!-- Item Status List -->
                                                <div class="small">
                                                    @if (count($approvedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-success fw-medium">✓ Approved:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($approvedItems as $item)
                                                                    <span
                                                                        class="badge bg-success text-white fw-normal fs-6">
                                                                        <i class="fas fa-check-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (count($rejectedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-danger fw-medium">✗ Rejected:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($rejectedItems as $item)
                                                                    <span
                                                                        class="badge bg-danger text-white fw-normal fs-6">
                                                                        <i class="fas fa-times-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $pendingItems = collect($request->vegetables)->filter(function (
                                                            $vegetable,
                                                        ) use ($approvedItems, $rejectedItems) {
                                                            $itemName = is_array($vegetable)
                                                                ? $vegetable['name']
                                                                : $vegetable;

                                                            $isApproved = collect($approvedItems)->contains(function (
                                                                $item,
                                                            ) use ($itemName) {
                                                                return (is_array($item) ? $item['name'] : $item) ===
                                                                    $itemName;
                                                            });

                                                            $isRejected = collect($rejectedItems)->contains(function (
                                                                $item,
                                                            ) use ($itemName) {
                                                                return (is_array($item) ? $item['name'] : $item) ===
                                                                    $itemName;
                                                            });

                                                            return !$isApproved && !$isRejected;
                                                        });
                                                    @endphp

                                                    @if ($pendingItems->count() > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-warning fw-medium">⏳ Pending:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($pendingItems as $item)
                                                                    <span
                                                                        class="badge bg-warning text-white fw-normal fs-6">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No Request</span>
                                            @endif
                                        </td>
                                        @endif

                                        <!-- Fruits Column -->
                                        @if(!request('category') || request('category') == 'fruits')
                                        <td class="px-3 py-3 border-end">
                                            @if ($request->fruits && count($request->fruits) > 0)
                                                @php
                                                    $fruitStatus = $request->fruits_status ?? 'under_review';
                                                    $fruitCheck = $request->checkCategoryInventoryAvailability(
                                                        'fruits',
                                                    );
                                                    $approvedItems = $request->fruits_approved_items ?? [];
                                                    $rejectedItems = $request->fruits_rejected_items ?? [];
                                                @endphp

                                                <!-- Item Status List -->
                                                <div class="small">
                                                    @if (count($approvedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-success fw-medium">✓ Approved:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($approvedItems as $item)
                                                                    <span
                                                                        class="badge bg-success text-white fw-normal fs-6">
                                                                        <i class="fas fa-check-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (count($rejectedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-danger fw-medium">✗ Rejected:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($rejectedItems as $item)
                                                                    <span
                                                                        class="badge bg-danger text-white fw-normal fs-6">
                                                                        <i class="fas fa-times-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $pendingItems = collect($request->fruits)->filter(function (
                                                            $fruit,
                                                        ) use ($approvedItems, $rejectedItems) {
                                                            $itemName = is_array($fruit) ? $fruit['name'] : $fruit;

                                                            $isApproved = collect($approvedItems)->contains(function (
                                                                $item,
                                                            ) use ($itemName) {
                                                                return (is_array($item) ? $item['name'] : $item) ===
                                                                    $itemName;
                                                            });

                                                            $isRejected = collect($rejectedItems)->contains(function (
                                                                $item,
                                                            ) use ($itemName) {
                                                                return (is_array($item) ? $item['name'] : $item) ===
                                                                    $itemName;
                                                            });

                                                            return !$isApproved && !$isRejected;
                                                        });
                                                    @endphp

                                                    @if ($pendingItems->count() > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-warning fw-medium">⏳ Pending:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($pendingItems as $item)
                                                                    <span
                                                                        class="badge bg-warning text-white fw-normal fs-6">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No Request</span>
                                            @endif
                                        </td>
                                        @endif

                                        <!-- Fertilizers Column -->
                                        @if(!request('category') || request('category') == 'fertilizers')
                                        <td class="px-3 py-3 border-end">
                                            @if ($request->fertilizers && count($request->fertilizers) > 0)
                                                @php
                                                    $fertStatus = $request->fertilizers_status ?? 'under_review';
                                                    $fertCheck = $request->checkCategoryInventoryAvailability(
                                                        'fertilizers',
                                                    );
                                                    $approvedItems = $request->fertilizers_approved_items ?? [];
                                                    $rejectedItems = $request->fertilizers_rejected_items ?? [];
                                                @endphp

                                                <!-- Item Status List -->
                                                <div class="small">
                                                    @if (count($approvedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-success fw-medium">✓ Approved:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($approvedItems as $item)
                                                                    <span
                                                                        class="badge bg-success text-white fw-normal fs-6">
                                                                        <i class="fas fa-check-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if (count($rejectedItems) > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-danger fw-medium">✗ Rejected:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($rejectedItems as $item)
                                                                    <span
                                                                        class="badge bg-danger text-white fw-normal fs-6">
                                                                        <i class="fas fa-times-circle me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @php
                                                        $pendingItems = collect($request->fertilizers)->filter(
                                                            function ($fertilizer) use (
                                                                $approvedItems,
                                                                $rejectedItems,
                                                            ) {
                                                                $itemName = is_array($fertilizer)
                                                                    ? $fertilizer['name']
                                                                    : $fertilizer;

                                                                $isApproved = collect($approvedItems)->contains(
                                                                    function ($item) use ($itemName) {
                                                                        return (is_array($item)
                                                                            ? $item['name']
                                                                            : $item) === $itemName;
                                                                    },
                                                                );

                                                                $isRejected = collect($rejectedItems)->contains(
                                                                    function ($item) use ($itemName) {
                                                                        return (is_array($item)
                                                                            ? $item['name']
                                                                            : $item) === $itemName;
                                                                    },
                                                                );

                                                                return !$isApproved && !$isRejected;
                                                            },
                                                        );
                                                    @endphp

                                                    @if ($pendingItems->count() > 0)
                                                        <div class="mb-2">
                                                            <div class="mb-1">
                                                                <span class="text-warning fw-medium">⏳ Pending:</span>
                                                            </div>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @foreach ($pendingItems as $item)
                                                                    <span
                                                                        class="badge bg-warning text-white fw-normal fs-6">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        {{ is_array($item) ? $item['name'] : $item }}
                                                                        @if (is_array($item) && isset($item['quantity']))
                                                                            ({{ $item['quantity'] }} pcs)
                                                                        @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">No Request</span>
                                            @endif
                                        </td>
                                        @endif

                                        <!-- Overall Status -->
                                        <td class="px-3 py-3 border-end">
                                            <span
                                                class="badge badge-status-lg bg-{{ match ($request->overall_status) {
                                                    'approved' => 'success',
                                                    'partially_approved' => 'warning',
                                                    'rejected' => 'danger',
                                                    'under_review' => 'secondary',
                                                    default => 'secondary',
                                                } }}">
                                                {{ $request->overall_status == 'under_review' ? 'Pending' : ucfirst(str_replace('_', ' ', $request->overall_status)) }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $request->id }}">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button type="button" class="btn btn-outline-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#categoryModal{{ $request->id }}">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                            </div>
                                        </td>
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
                                <div class="modal-header bg-light border-bottom">
                                    <h5 class="modal-title fw-bold text-dark">
                                        <i class="fas fa-eye text-primary me-2"></i>
                                        Request Details - {{ $request->request_number }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                            $vegCheck = $request->checkCategoryInventoryAvailability('vegetables');
                                        @endphp
                                        <div class="mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
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

                                            @if ($vegStatus !== '')
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
                                                    <i class="fas fa-check"></i> and inventory
                                                    deducted
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Fruits -->
                                    @if ($request->fruits && count($request->fruits) > 0)
                                        @php
                                            $fruitStatus = $request->fruits_status ?? 'under_review';
                                            $fruitCheck = $request->checkCategoryInventoryAvailability('fruits');
                                        @endphp
                                        <div class="mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
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
                                            $fertStatus = $request->fertilizers_status ?? 'under_review';
                                            $fertCheck = $request->checkCategoryInventoryAvailability('fertilizers');
                                        @endphp
                                        <div class="mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
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
                                <div class="modal-header bg-light border-bottom">
                                    <h5 class="modal-title fw-bold text-dark">
                                        <i class="fas fa-edit text-success me-2"></i>
                                        Update Categories - {{ $request->request_number }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                                $vegCheck = $request->checkCategoryInventoryAvailability('vegetables');
                                                $vegStatus = $request->vegetables_status ?? 'under_review';
                                            @endphp
                                            <div class="mb-4 p-3 border-0 bg-light rounded-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 fw-bold text-success">
                                                        <i class="fas fa-leaf me-2"></i>Vegetables
                                                    </h6>
                                                    <span
                                                        class="badge badge-status bg-{{ $vegCheck['can_fulfill'] ? 'success' : 'warning' }}">
                                                        {{ $vegCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                    </span>
                                                </div>

                                                <div class="alert alert-info alert-sm mb-3">
                                                    <strong>Item Status Management:</strong> Individual approval for each
                                                    vegetable item
                                                    <div class="mt-2">
                                                        <small>Only items with adequate stock can be approved.</small>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="categories[]" value="vegetables">

                                                <!-- Individual Item Controls -->
                                                @foreach ($request->vegetables as $index => $vegetable)
                                                    @php
                                                        $itemName = is_array($vegetable)
                                                            ? $vegetable['name']
                                                            : $vegetable;
                                                        $itemQuantity = is_array($vegetable)
                                                            ? $vegetable['quantity'] ?? 1
                                                            : 1;

                                                        // Check if item is approved/rejected
                                                        $approvedItems = $request->vegetables_approved_items ?? [];
                                                        $rejectedItems = $request->vegetables_rejected_items ?? [];

                                                        $isApproved = collect($approvedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $isRejected = collect($rejectedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $currentStatus = $isApproved
                                                            ? 'approved'
                                                            : ($isRejected
                                                                ? 'rejected'
                                                                : 'pending');

                                                        // Check stock availability for this specific item
                                                        $hasStock = collect(
                                                            $vegCheck['unavailable_items'],
                                                        )->doesntContain('name', $itemName);

                                                        // Get available stock for this item
                                                        $availableStock = 0;
                                                        $allItems = array_merge(
                                                            $vegCheck['available_items'],
                                                            $vegCheck['unavailable_items'],
                                                        );
                                                        foreach ($allItems as $stockItem) {
                                                            if ($stockItem['name'] === $itemName) {
                                                                $availableStock = $stockItem['available'];
                                                                break;
                                                            }
                                                        }
                                                    @endphp

                                                    <div
                                                        class="item-card d-flex align-items-center justify-content-between mb-3 p-3 {{ $currentStatus === 'approved' ? 'bg-success bg-opacity-10 border border-success' : ($currentStatus === 'rejected' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-white border') }} rounded-3 shadow-sm">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span
                                                                    class="fw-medium text-dark">{{ $itemName }}</span>
                                                                <span
                                                                    class="badge badge-icon bg-light text-muted ms-2">{{ $itemQuantity }}
                                                                    pcs</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <small class="text-muted">Requested:</small>
                                                                <small class="fw-medium">{{ $itemQuantity }} pcs</small>
                                                                <span class="text-muted">•</span>
                                                                <small
                                                                    class="stock-{{ $availableStock >= $itemQuantity ? 'sufficient' : 'insufficient' }}">
                                                                    <i class="fas fa-box me-1"></i>Stock:
                                                                    <span class="fw-bold">{{ $availableStock }} pcs</span>
                                                                    @if ($availableStock >= $itemQuantity)
                                                                        <i class="fas fa-check text-success ms-1"></i>
                                                                    @else
                                                                        <i
                                                                            class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            @if (!$hasStock)
                                                                <span class="badge bg-warning text-dark mt-2">
                                                                    <i
                                                                        class="fas fa-exclamation-triangle me-1"></i>Insufficient
                                                                    Stock
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="ms-3">
                                                            <select name="item_statuses[vegetables][{{ $itemName }}]"
                                                                class="form-select form-select-sm border-light"
                                                                style="min-width: 130px;">
                                                                <option value="pending"
                                                                    {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                                                    <i class="fas fa-clock"></i> Pending
                                                                </option>
                                                                <option value="approved"
                                                                    {{ $currentStatus === 'approved' ? 'selected' : '' }}
                                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                                    <i class="fas fa-check"></i>
                                                                    Approved{{ !$hasStock ? ' (No Stock)' : '' }}
                                                                </option>
                                                                <option value="rejected"
                                                                    {{ $currentStatus === 'rejected' ? 'selected' : '' }}>
                                                                    <i class="fas fa-times"></i> Rejected
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (!$vegCheck['can_fulfill'])
                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                        <small>
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Some items have insufficient stock. Only items with adequate
                                                            stock can be approved.
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Fruits Section -->
                                        @if ($request->fruits && count($request->fruits) > 0)
                                            @php
                                                $fruitCheck = $request->checkCategoryInventoryAvailability('fruits');
                                                $fruitStatus = $request->fruits_status ?? 'under_review';
                                            @endphp
                                            <div class="mb-4 p-3 border-0 bg-light rounded-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 fw-bold text-info">
                                                        <i class="fas fa-apple-alt me-2"></i>Fruits
                                                    </h6>
                                                    <span
                                                        class="badge badge-status bg-{{ $fruitCheck['can_fulfill'] ? 'success' : 'warning' }}">
                                                        {{ $fruitCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                    </span>
                                                </div>

                                                <div class="alert alert-info alert-sm mb-3">
                                                    <strong>Item Status Management:</strong> Individual approval for each
                                                    fruit item
                                                    <div class="mt-2">
                                                        <small>Only items with adequate stock can be approved.</small>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="categories[]" value="fruits">

                                                <!-- Individual Item Controls -->
                                                @foreach ($request->fruits as $index => $fruit)
                                                    @php
                                                        $itemName = is_array($fruit) ? $fruit['name'] : $fruit;
                                                        $itemQuantity = is_array($fruit) ? $fruit['quantity'] ?? 1 : 1;

                                                        // Check if item is approved/rejected
                                                        $approvedItems = $request->fruits_approved_items ?? [];
                                                        $rejectedItems = $request->fruits_rejected_items ?? [];

                                                        $isApproved = collect($approvedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $isRejected = collect($rejectedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $currentStatus = $isApproved
                                                            ? 'approved'
                                                            : ($isRejected
                                                                ? 'rejected'
                                                                : 'pending');

                                                        // Check stock availability for this specific item
                                                        $hasStock = collect(
                                                            $fruitCheck['unavailable_items'],
                                                        )->doesntContain('name', $itemName);

                                                        // Get available stock for this item
                                                        $availableStock = 0;
                                                        $allItems = array_merge(
                                                            $fruitCheck['available_items'],
                                                            $fruitCheck['unavailable_items'],
                                                        );
                                                        foreach ($allItems as $stockItem) {
                                                            if ($stockItem['name'] === $itemName) {
                                                                $availableStock = $stockItem['available'];
                                                                break;
                                                            }
                                                        }
                                                    @endphp

                                                    <div
                                                        class="item-card d-flex align-items-center justify-content-between mb-3 p-3 {{ $currentStatus === 'approved' ? 'bg-success bg-opacity-10 border border-success' : ($currentStatus === 'rejected' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-white border') }} rounded-3 shadow-sm">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span
                                                                    class="fw-medium text-dark">{{ $itemName }}</span>
                                                                <span
                                                                    class="badge badge-icon bg-light text-muted ms-2">{{ $itemQuantity }}
                                                                    pcs</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <small class="text-muted">Requested:</small>
                                                                <small class="fw-medium">{{ $itemQuantity }} pcs</small>
                                                                <span class="text-muted">•</span>
                                                                <small
                                                                    class="stock-{{ $availableStock >= $itemQuantity ? 'sufficient' : 'insufficient' }}">
                                                                    <i class="fas fa-box me-1"></i>Stock:
                                                                    <span class="fw-bold">{{ $availableStock }}
                                                                        pcs</span>
                                                                    @if ($availableStock >= $itemQuantity)
                                                                        <i class="fas fa-check text-success ms-1"></i>
                                                                    @else
                                                                        <i
                                                                            class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            @if (!$hasStock)
                                                                <span class="badge bg-warning text-dark mt-2">
                                                                    <i
                                                                        class="fas fa-exclamation-triangle me-1"></i>Insufficient
                                                                    Stock
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="ms-3">
                                                            <select name="item_statuses[fruits][{{ $itemName }}]"
                                                                class="form-select form-select-sm border-light"
                                                                style="min-width: 130px;">
                                                                <option value="pending"
                                                                    {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                                                    <i class="fas fa-clock"></i> Pending
                                                                </option>
                                                                <option value="approved"
                                                                    {{ $currentStatus === 'approved' ? 'selected' : '' }}
                                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                                    <i class="fas fa-check"></i>
                                                                    Approved{{ !$hasStock ? ' (No Stock)' : '' }}
                                                                </option>
                                                                <option value="rejected"
                                                                    {{ $currentStatus === 'rejected' ? 'selected' : '' }}>
                                                                    <i class="fas fa-times"></i> Rejected
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (!$fruitCheck['can_fulfill'])
                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                        <small>
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Some items have insufficient stock. Only items with adequate
                                                            stock can be approved.
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
                                                $fertStatus = $request->fertilizers_status ?? 'under_review';
                                            @endphp
                                            <div class="mb-4 p-3 border-0 bg-light rounded-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 fw-bold text-warning">
                                                        <i class="fas fa-seedling me-2"></i>Fertilizers
                                                    </h6>
                                                    <span
                                                        class="badge badge-status bg-{{ $fertCheck['can_fulfill'] ? 'success' : 'warning' }}">
                                                        {{ $fertCheck['can_fulfill'] ? 'Stock Available' : 'Low Stock' }}
                                                    </span>
                                                </div>

                                                <div class="alert alert-info alert-sm mb-3">
                                                    <strong>Item Status Management:</strong> Individual approval for each
                                                    fertilizer item
                                                    <div class="mt-2">
                                                        <small>Only items with adequate stock can be approved.</small>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="categories[]" value="fertilizers">

                                                <!-- Individual Item Controls -->
                                                @foreach ($request->fertilizers as $index => $fertilizer)
                                                    @php
                                                        $itemName = is_array($fertilizer)
                                                            ? $fertilizer['name']
                                                            : $fertilizer;
                                                        $itemQuantity = is_array($fertilizer)
                                                            ? $fertilizer['quantity'] ?? 1
                                                            : 1;

                                                        // Check if item is approved/rejected
                                                        $approvedItems = $request->fertilizers_approved_items ?? [];
                                                        $rejectedItems = $request->fertilizers_rejected_items ?? [];

                                                        $isApproved = collect($approvedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $isRejected = collect($rejectedItems)->contains(function (
                                                            $item,
                                                        ) use ($itemName) {
                                                            return (is_array($item) ? $item['name'] : $item) ===
                                                                $itemName;
                                                        });

                                                        $currentStatus = $isApproved
                                                            ? 'approved'
                                                            : ($isRejected
                                                                ? 'rejected'
                                                                : 'pending');

                                                        // Check stock availability for this specific item
                                                        $hasStock = collect(
                                                            $fertCheck['unavailable_items'],
                                                        )->doesntContain('name', $itemName);

                                                        // Get available stock for this item
                                                        $availableStock = 0;
                                                        $allItems = array_merge(
                                                            $fertCheck['available_items'],
                                                            $fertCheck['unavailable_items'],
                                                        );
                                                        foreach ($allItems as $stockItem) {
                                                            if ($stockItem['name'] === $itemName) {
                                                                $availableStock = $stockItem['available'];
                                                                break;
                                                            }
                                                        }
                                                    @endphp

                                                    <div
                                                        class="item-card d-flex align-items-center justify-content-between mb-3 p-3 {{ $currentStatus === 'approved' ? 'bg-success bg-opacity-10 border border-success' : ($currentStatus === 'rejected' ? 'bg-danger bg-opacity-10 border border-danger' : 'bg-white border') }} rounded-3 shadow-sm">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <span
                                                                    class="fw-medium text-dark">{{ $itemName }}</span>
                                                                <span
                                                                    class="badge badge-icon bg-light text-muted ms-2">{{ $itemQuantity }}
                                                                    pcs</span>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <small class="text-muted">Requested:</small>
                                                                <small class="fw-medium">{{ $itemQuantity }} pcs</small>
                                                                <span class="text-muted">•</span>
                                                                <small
                                                                    class="stock-{{ $availableStock >= $itemQuantity ? 'sufficient' : 'insufficient' }}">
                                                                    <i class="fas fa-box me-1"></i>Stock:
                                                                    <span class="fw-bold">{{ $availableStock }}
                                                                        pcs</span>
                                                                    @if ($availableStock >= $itemQuantity)
                                                                        <i class="fas fa-check text-success ms-1"></i>
                                                                    @else
                                                                        <i
                                                                            class="fas fa-exclamation-triangle text-warning ms-1"></i>
                                                                    @endif
                                                                </small>
                                                            </div>
                                                            @if (!$hasStock)
                                                                <span class="badge bg-warning text-dark mt-2">
                                                                    <i
                                                                        class="fas fa-exclamation-triangle me-1"></i>Insufficient
                                                                    Stock
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="ms-3">
                                                            <select
                                                                name="item_statuses[fertilizers][{{ $itemName }}]"
                                                                class="form-select form-select-sm border-light"
                                                                style="min-width: 130px;">
                                                                <option value="pending"
                                                                    {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                                                    <i class="fas fa-clock"></i> Pending
                                                                </option>
                                                                <option value="approved"
                                                                    {{ $currentStatus === 'approved' ? 'selected' : '' }}
                                                                    {{ !$hasStock ? 'disabled' : '' }}>
                                                                    <i class="fas fa-check"></i>
                                                                    Approved{{ !$hasStock ? ' (No Stock)' : '' }}
                                                                </option>
                                                                <option value="rejected"
                                                                    {{ $currentStatus === 'rejected' ? 'selected' : '' }}>
                                                                    <i class="fas fa-times"></i> Rejected
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if (!$fertCheck['can_fulfill'])
                                                    <div class="alert alert-warning mt-2 alert-sm">
                                                        <small>
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Some items have insufficient stock. Only items with adequate
                                                            stock can be approved.
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <!-- General Remarks -->
                                        <div class="mb-3">
                                            <label for="remarks{{ $request->id }}" class="form-label">General
                                                Remarks</label>
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
                            <a class="page-link" href="{{ $requests->previousPageUrl() }}" rel="prev">Back</a>
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

    <style>
        /* Consistent UI Styles */
        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
            font-weight: 500;
        }

        .badge-status-lg {
            font-size: 0.8rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
        }

        .badge-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .card {
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        .btn-group .btn {
            border-radius: 0.375rem;
        }

        .btn-group .btn+.btn {
            margin-left: 0.25rem;
        }

        .form-select,
        .form-control {
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .border-light {
            border-color: #e9ecef !important;
        }

        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Individual Item Cards in Modal */
        .item-card {
            transition: all 0.2s ease-in-out;
            border-radius: 0.5rem;
        }

        .item-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        /* Stock Status Colors */
        .stock-sufficient {
            color: #198754 !important;
        }

        .stock-insufficient {
            color: #dc3545 !important;
        }

        /* Modal Enhancements */
        .modal-content {
            border: none;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
        }
    </style>

    <!-- Date Filter Modal -->
    <div class="modal fade" id="dateFilterModal" tabindex="-1" aria-labelledby="dateFilterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="dateFilterModalLabel">
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
                                        <label for="modal_date_from" class="form-label">From Date</label>
                                        <input type="date" id="modal_date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="modal_date_to" class="form-label">To Date</label>
                                        <input type="date" id="modal_date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                    <button type="button" class="btn btn-primary w-100"
                                        onclick="applyCustomDateRange()">
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
                                            onclick="setDateRangeModal('today')">
                                            <i class="fas fa-calendar-day me-2"></i>Today
                                        </button>
                                        <button type="button" class="btn btn-outline-info"
                                            onclick="setDateRangeModal('week')">
                                            <i class="fas fa-calendar-week me-2"></i>This Week
                                        </button>
                                        <button type="button" class="btn btn-outline-warning"
                                            onclick="setDateRangeModal('month')">
                                            <i class="fas fa-calendar me-2"></i>This Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary"
                                            onclick="setDateRangeModal('year')">
                                            <i class="fas fa-calendar-alt me-2"></i>This Year
                                        </button>
                                        <hr class="my-3">
                                        <button type="button" class="btn btn-outline-danger"
                                            onclick="clearDateRangeModal()">
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
                            <div class="alert alert-info mb-0" id="currentDateFilter">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="dateFilterStatus">
                                    @if (request('date_from') || request('date_to'))
                                        Current filter:
                                        @if (request('date_from'))
                                            From {{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}
                                        @endif
                                        @if (request('date_to'))
                                            To {{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}
                                        @endif
                                    @else
                                        No date filter applied - showing all requests
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Date filter functions
        function setDateRangeModal(range) {
            const today = new Date();
            let startDate, endDate;

            switch (range) {
                case 'today':
                    startDate = new Date(today);
                    endDate = new Date(today);
                    break;
                case 'week':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6); // End of week (Saturday)
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                    endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1); // First day of year
                    endDate = new Date(today.getFullYear(), 11, 31); // Last day of year
                    break;
            }

            // Format dates to YYYY-MM-DD
            const startDateStr = startDate.toISOString().split('T')[0];
            const endDateStr = endDate.toISOString().split('T')[0];

            // Update modal inputs
            document.getElementById('modal_date_from').value = startDateStr;
            document.getElementById('modal_date_to').value = endDateStr;

            // Apply the filter immediately
            applyDateFilter(startDateStr, endDateStr);
        }

        function applyCustomDateRange() {
            const dateFrom = document.getElementById('modal_date_from').value;
            const dateTo = document.getElementById('modal_date_to').value;

            if (dateFrom && dateTo && dateFrom > dateTo) {
                alert('From date cannot be later than To date');
                return;
            }

            applyDateFilter(dateFrom, dateTo);
        }

        function applyDateFilter(dateFrom, dateTo) {
            // Update hidden inputs
            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            // Update status display
            updateDateFilterStatus(dateFrom, dateTo);

            // Close modal and submit form
            const modalElement = document.getElementById('dateFilterModal');
            if (modalElement) {
                let modal = bootstrap.Modal.getInstance(modalElement);
                if (!modal) {
                    modal = new bootstrap.Modal(modalElement);
                }
                modal.hide();
            }

            submitFilterForm();
        }

        function clearDateRangeModal() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            applyDateFilter('', '');
        }

        function updateDateFilterStatus(dateFrom, dateTo) {
            const statusElement = document.getElementById('dateFilterStatus');
            if (!dateFrom && !dateTo) {
                statusElement.innerHTML = 'No date filter applied - showing all requests';
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
    </script>
@endsection

@extends('layouts.app')

@section('title', 'Activity Logs')

@section('page-icon', 'fas fa-history')
@section('page-title', 'Activity Logs')

@section('content')
    <div class="container-fluid py-4">

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error:</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter me-2"></i>Filters & Search
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" id="filterForm">
                    <!-- Hidden date inputs -->
                    <input type="hidden" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" id="date_to" value="{{ request('date_to') }}">

                    <div class="row g-2">
                        <div class="col-md-3">
                            <select name="module" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All Modules</option>
                                <option value="Event" {{ request('module') == 'Event' ? 'selected' : '' }}>Events</option>
                                <option value="SlideshowImage"
                                    {{ request('module') == 'SlideshowImage' ? 'selected' : '' }}>
                                    Slideshow Management</option>
                                <option value="User" {{ request('module') == 'User' ? 'selected' : '' }}>User Management
                                </option>
                                <option value="Authentication"
                                    {{ request('module') == 'Authentication' ? 'selected' : '' }}>
                                    Authentication (Login/Logout)</option>
                                <option value="RsbsaApplication"
                                    {{ request('module') == 'RsbsaApplication' ? 'selected' : '' }}>RSBSA Registrations
                                </option>
                                <option value="SeedlingRequest"
                                    {{ request('module') == 'SeedlingRequest' ? 'selected' : '' }}>
                                    Supply Requests</option>
                                <option value="CategoryItem" {{ request('module') == 'CategoryItem' ? 'selected' : '' }}>
                                    Supply
                                    Management</option>
                                <option value="FishrApplication"
                                    {{ request('module') == 'FishrApplication' ? 'selected' : '' }}>FishR Registrations
                                </option>
                                <option value="BoatrApplication"
                                    {{ request('module') == 'BoatrApplication' ? 'selected' : '' }}>BoatR Registrations
                                </option>
                                <option value="TrainingApplication"
                                    {{ request('module') == 'TrainingApplication' ? 'selected' : '' }}>Training Requests
                                </option>
                                <option value="RecycleBin" {{ request('module') == 'RecycleBin' ? 'selected' : '' }}>
                                    Recycle
                                    Bin</option>
                                <option value="Barangay" {{ request('module') == 'Barangay' ? 'selected' : '' }}>Barangay
                                    Management</option>
                                <option value="DSSReport" {{ request('module') == 'DSSReport' ? 'selected' : '' }}>DSS
                                    Reports
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="event" class="form-select form-select-sm" onchange="submitFilterForm()">
                                <option value="">All</option>
                                <optgroup label="Basic Actions">
                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created —
                                        All Services</option>
                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated —
                                        All Services</option>
                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted —
                                        All Services</option>
                                </optgroup>
                                <optgroup label="Authentication">
                                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login —
                                        Admin / User Portal</option>
                                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout —
                                        Admin / User Portal</option>
                                    <option value="login_failed"
                                        {{ request('event') == 'login_failed' ? 'selected' : '' }}>Login Failed — Admin /
                                        User Portal</option>
                                </optgroup>
                                <optgroup label="Submissions">
                                    <option value="submitted" {{ request('event') == 'submitted' ? 'selected' : '' }}>
                                        Submitted — FishR / RSBSA / Training / Supply</option>
                                </optgroup>
                                <optgroup label="Approvals &amp; Status">
                                    <option value="approved" {{ request('event') == 'approved' ? 'selected' : '' }}>
                                        Approved — RSBSA / Training / User Reg.</option>
                                    <option value="rejected" {{ request('event') == 'rejected' ? 'selected' : '' }}>
                                        Rejected — RSBSA / Training / User Reg.</option>
                                    <option value="status_changed"
                                        {{ request('event') == 'status_changed' ? 'selected' : '' }}>Status Changed — FishR
                                        / BoatR / RSBSA / Training</option>
                                    <option value="inspection_completed"
                                        {{ request('event') == 'inspection_completed' ? 'selected' : '' }}>Inspection
                                        Completed — BoatR</option>
                                    <option value="marked_claimed"
                                        {{ request('event') == 'marked_claimed' ? 'selected' : '' }}>Marked as Claimed —
                                        Supply Request</option>
                                </optgroup>
                                <optgroup label="FishR &amp; BoatR">
                                    <option value="fishr_number_assigned"
                                        {{ request('event') == 'fishr_number_assigned' ? 'selected' : '' }}>FishR #
                                        Assigned — FishR</option>
                                    <option value="annex_uploaded"
                                        {{ request('event') == 'annex_uploaded' ? 'selected' : '' }}>File Uploaded — FishR
                                        / BoatR</option>
                                    <option value="annex_deleted"
                                        {{ request('event') == 'annex_deleted' ? 'selected' : '' }}>File Deleted — FishR /
                                        BoatR</option>
                                </optgroup>
                                <optgroup label="Supply Management">
                                    <option value="supply_added"
                                        {{ request('event') == 'supply_added' ? 'selected' : '' }}>Supply Added — Supply
                                        Mgmt.</option>
                                    <option value="supply_adjusted"
                                        {{ request('event') == 'supply_adjusted' ? 'selected' : '' }}>Supply Adjusted —
                                        Supply Mgmt.</option>
                                    <option value="supply_loss" {{ request('event') == 'supply_loss' ? 'selected' : '' }}>
                                        Supply Loss — Supply Mgmt.</option>
                                    <option value="updated_items"
                                        {{ request('event') == 'updated_items' ? 'selected' : '' }}>Updated Items — Supply
                                        Request</option>
                                </optgroup>
                                <optgroup label="User Management">
                                    <option value="resent_verification"
                                        {{ request('event') == 'resent_verification' ? 'selected' : '' }}>Resent
                                        Verification — Admin</option>
                                </optgroup>
                                <optgroup label="Reports &amp; Exports">
                                    <option value="exported" {{ request('event') == 'exported' ? 'selected' : '' }}>
                                        Exported — All Services</option>
                                    <option value="dss_report_viewed"
                                        {{ request('event') == 'dss_report_viewed' ? 'selected' : '' }}>Viewed — DSS
                                        Reports</option>
                                    <option value="dss_report_downloaded"
                                        {{ request('event') == 'dss_report_downloaded' ? 'selected' : '' }}>Downloaded —
                                        DSS Reports</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-sm"
                                    placeholder="Search what changed..." value="{{ request('search') }}" id="searchInput"
                                    oninput="autoSearch()">
                                <button class="btn btn-outline-secondary btn-sm" type="submit" title="Search"
                                    id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#dateFilterModal">
                                <i class="fas fa-calendar-alt me-1"></i>Date Filter
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-sm w-100">
                                <i></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Activity Logs Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div></div>
                <div class="text-center flex-fill">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Audit Logs
                        <span class="text-muted fw-normal ms-2" style="font-size: 0.85rem;">
                            Total: <strong>{{ $activities->total() }}</strong>
                        </span>
                    </h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.activity-logs.export', request()->query()) }}"
                        class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-dark" style="white-space: nowrap;">
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>Service/Module</th>
                                <th>What Changed</th>
                                <th class="text-center">Full Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $activity->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @php
                                            // Get the ACTUAL user who performed the action
                                            $actionUser = $activity->causer;
                                            $actionUserName = 'System';
                                            $actionUserEmail = 'N/A';

                                            if ($actionUser) {
                                                // Check if it's a UserRegistration or User model
                                            if ($actionUser instanceof \App\Models\UserRegistration) {
                                                // UserRegistration: just show username
                                                $actionUserName = $actionUser->username ?? 'Unknown User';
                                                $actionUserEmail = ucfirst($actionUser->user_type ?? 'Portal User'); // shows "Farmer", "Fisherfolk", etc.
                                                } else {
                                                    // User model has: name, email
                                                    $actionUserName = $actionUser->name ?? 'Unknown';
                                                    $actionUserEmail = $actionUser->email ?? 'N/A';
                                                }
                                            } elseif (in_array($activity->event, ['login', 'logout', 'login_failed'])) {
                                                // Fallback: check properties for login/logout activities without causer
                                                $properties = $activity->properties->all() ?? [];
                                                $actionUserName =
                                                    $properties['name'] ??
                                                    ($properties['email'] ??
                                                        ($properties['username'] ?? 'Unknown User'));
                                                $actionUserEmail =
                                                    $properties['email'] ?? ($properties['first_name'] ?? 'N/A');
                                            }
                                        @endphp

                                        @if ($actionUserName !== 'System')
                                            <div class="fw-bold">{{ $actionUserName }}</div>
                                            @if ($actionUserEmail)
                                                <small class="text-muted">{{ $actionUserEmail }}</small>
                                            @endif
                                        @else
                                            <em class="text-muted">System</em>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Show the ACTUAL user's role, not the current superadmin
                                            $roleBg = 'secondary';
                                            $roleText = 'System';

                                            if ($actionUser) {
                                                $roleText = ucfirst($actionUser->role ?? 'user');

                                                if ($actionUser->role === 'superadmin') {
                                                    $roleBg = 'danger';
                                                } elseif ($actionUser->role === 'admin') {
                                                    $roleBg = 'warning';
                                                } elseif ($actionUser->role === 'user') {
                                                    $roleBg = 'info';
                                                }
                                            } elseif (
                                                !$actionUser &&
                                                in_array($activity->event, ['login', 'logout', 'login_failed'])
                                            ) {
                                                // For login/logout without causer, check properties
                                                $properties = $activity->properties->all() ?? [];
                                                if (isset($properties['role'])) {
                                                    $roleText = ucfirst($properties['role']);
                                                    if ($properties['role'] === 'superadmin') {
                                                        $roleBg = 'danger';
                                                    } elseif ($properties['role'] === 'admin') {
                                                        $roleBg = 'warning';
                                                    } elseif ($properties['role'] === 'user') {
                                                        $roleBg = 'info';
                                                    }
                                                }
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $roleBg }}">{{ $roleText }}</span>
                                    </td>
                                    <td>
                                        @php
                                            // Extract action from description if event is empty
                                            $action = $activity->event;
                                            if (!$action && $activity->description) {
                                                // Parse "marked_claimed - Model" or "login - User"
                                                if (preg_match('/^(\w+)\s*-/', $activity->description, $matches)) {
                                                    $action = $matches[1];
                                                }
                                            }

                                            // Map actions to badge styling
                                            $actionMap = [
                                                'created' => [
                                                    'label' => 'Created',
                                                    'color' => 'success',
                                                    'icon' => 'fa-plus-circle',
                                                ],
                                                'updated' => [
                                                    'label' => 'Updated',
                                                    'color' => 'info',
                                                    'icon' => 'fa-edit',
                                                ],
                                                'deleted' => [
                                                    'label' => 'Deleted',
                                                    'color' => 'danger',
                                                    'icon' => 'fa-trash',
                                                ],
                                                'login' => [
                                                    'label' => 'Login',
                                                    'color' => 'primary',
                                                    'icon' => 'fa-sign-in-alt',
                                                ],
                                                'logout' => [
                                                    'label' => 'Logout',
                                                    'color' => 'secondary',
                                                    'icon' => 'fa-sign-out-alt',
                                                ],
                                                'login_failed' => [
                                                    'label' => 'Login Failed',
                                                    'color' => 'danger',
                                                    'icon' => 'fa-times-circle',
                                                ],
                                                'marked_claimed' => [
                                                    'label' => 'Claimed',
                                                    'color' => 'success',
                                                    'icon' => 'fa-check-circle',
                                                ],
                                                'approved' => [
                                                    'label' => 'Approved',
                                                    'color' => 'success',
                                                    'icon' => 'fa-thumbs-up',
                                                ],
                                                'rejected' => [
                                                    'label' => 'Rejected',
                                                    'color' => 'danger',
                                                    'icon' => 'fa-thumbs-down',
                                                ],
                                                'exported' => [
                                                    'label' => 'Exported',
                                                    'color' => 'info',
                                                    'icon' => 'fa-file-export',
                                                ],
                                                'downloaded' => [
                                                    'label' => 'Downloaded',
                                                    'color' => 'info',
                                                    'icon' => 'fa-download',
                                                ],
                                                'viewed' => [
                                                    'label' => 'Viewed',
                                                    'color' => 'secondary',
                                                    'icon' => 'fa-eye',
                                                ],
                                                'status_changed' => [
                                                    'label' => 'Status Changed',
                                                    'color' => 'warning',
                                                    'icon' => 'fa-exchange-alt',
                                                ],
                                                'supply_added' => [
                                                    'label' => 'Supply Added',
                                                    'color' => 'success',
                                                    'icon' => 'fa-plus',
                                                ],
                                                'supply_adjusted' => [
                                                    'label' => 'Adjusted',
                                                    'color' => 'warning',
                                                    'icon' => 'fa-adjust',
                                                ],
                                                'supply_loss' => [
                                                    'label' => 'Supply Loss',
                                                    'color' => 'danger',
                                                    'icon' => 'fa-minus-circle',
                                                ],
                                                'inspection_completed' => [
                                                    'label' => 'Inspected',
                                                    'color' => 'info',
                                                    'icon' => 'fa-clipboard-check',
                                                ],
                                                'annex_uploaded' => [
                                                    'label' => 'Uploaded',
                                                    'color' => 'success',
                                                    'icon' => 'fa-upload',
                                                ],
                                                'annex_deleted' => [
                                                    'label' => 'File Deleted',
                                                    'color' => 'danger',
                                                    'icon' => 'fa-file-times',
                                                ],
                                                'dss_report_viewed' => [
                                                    'label' => 'Report Viewed',
                                                    'color' => 'info',
                                                    'icon' => 'fa-chart-line',
                                                ],
                                                'dss_report_downloaded' => [
                                                    'label' => 'Report Downloaded',
                                                    'color' => 'success',
                                                    'icon' => 'fa-file-download',
                                                ],
                                            ];

                                            $actionData = $actionMap[$action] ?? [
                                                'label' => ucfirst(str_replace('_', ' ', $action)),
                                                'color' => 'secondary',
                                                'icon' => 'fa-circle',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $actionData['color'] }}">
                                            <i class="fas {{ $actionData['icon'] }}"></i> {{ $actionData['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            // Map model types to service/module names
                                            $modelType = class_basename($activity->subject_type ?? 'System');

                                            // If no subject_type, try to extract from description for old logs
                                            if (!$activity->subject_type && $activity->description) {
                                                // Parse description like "marked_claimed - SeedlingRequest (ID: 143)"
                                                if (preg_match('/- (\w+)/', $activity->description, $matches)) {
                                                    $modelType = $matches[1];
                                                }
                                            }

                                            // Special handling for authentication activities without subject
                                            if (
                                                !$activity->subject_type &&
                                                (str_contains($activity->description, 'login') ||
                                                    str_contains($activity->description, 'logout'))
                                            ) {
                                                $modelType = 'Authentication';
                                            }

                                            $serviceMap = [
                                                'Event' => [
                                                    'name' => 'Events',
                                                    'icon' => 'fa-calendar-alt',
                                                    'color' => 'info',
                                                ],
                                                'SlideshowImage' => [
                                                    'name' => 'Slideshow Management',
                                                    'icon' => 'fa-images',
                                                    'color' => 'warning',
                                                ],
                                                'User' => [
                                                    'name' => 'User Management',
                                                    'icon' => 'fa-users',
                                                    'color' => 'primary',
                                                ],
                                                'UserRegistration' => [
                                                    'name' => 'User Management',
                                                    'icon' => 'fa-users',
                                                    'color' => 'primary',
                                                ],
                                                'Authentication' => [
                                                    'name' => 'Authentication',
                                                    'icon' => 'fa-sign-in-alt',
                                                    'color' => 'secondary',
                                                ],
                                                'RsbsaApplication' => [
                                                    'name' => 'RSBSA Registrations',
                                                    'icon' => 'fa-file-alt',
                                                    'color' => 'success',
                                                ],
                                                'SeedlingRequest' => [
                                                    'name' => 'Supplies Requests',
                                                    'icon' => 'fa-seedling',
                                                    'color' => 'success',
                                                ],
                                                'SeedlingRequestItem' => [
                                                    'name' => 'Supplies Requests',
                                                    'icon' => 'fa-seedling',
                                                    'color' => 'success',
                                                ],
                                                'CategoryItem' => [
                                                    'name' => 'Supply Management',
                                                    'icon' => 'fa-boxes',
                                                    'color' => 'warning',
                                                ],
                                                'RequestCategory' => [
                                                    'name' => 'Supply Management',
                                                    'icon' => 'fa-boxes',
                                                    'color' => 'warning',
                                                ],
                                                'FishrApplication' => [
                                                    'name' => 'FishR Registrations',
                                                    'icon' => 'fa-fish',
                                                    'color' => 'info',
                                                ],
                                                'FishrAnnex' => [
                                                    'name' => 'FishR Registrations',
                                                    'icon' => 'fa-fish',
                                                    'color' => 'info',
                                                ],
                                                'BoatrApplication' => [
                                                    'name' => 'BoatR Registrations',
                                                    'icon' => 'fa-ship',
                                                    'color' => 'primary',
                                                ],
                                                'BoatrAnnex' => [
                                                    'name' => 'BoatR Registrations',
                                                    'icon' => 'fa-ship',
                                                    'color' => 'primary',
                                                ],
                                                'TrainingApplication' => [
                                                    'name' => 'Training Requests',
                                                    'icon' => 'fa-graduation-cap',
                                                    'color' => 'danger',
                                                ],
                                                'RecycleBin' => [
                                                    'name' => 'Recycle Bin',
                                                    'icon' => 'fa-trash-restore',
                                                    'color' => 'secondary',
                                                ],
                                                'Barangay' => [
                                                    'name' => 'Barangay Management',
                                                    'icon' => 'fa-map-marker-alt',
                                                    'color' => 'info',
                                                ],
                                                'DSSReport' => [
                                                    'name' => 'DSS Reports',
                                                    'icon' => 'fa-chart-line',
                                                    'color' => 'primary',
                                                ],
                                            ];

                                            $service = $serviceMap[$modelType] ?? [
                                                'name' => $modelType,
                                                'icon' => 'fa-cog',
                                                'color' => 'secondary',
                                            ];
                                        @endphp

                                        <span>
                                            <i class="fas {{ $service['icon'] }} text-{{ $service['color'] }}"></i>
                                            {{ $service['name'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $subject = $activity->subject;
                                            $properties = $activity->properties->all() ?? [];
                                            $changes = $properties['attributes'] ?? [];
                                            $oldValues = $properties['old'] ?? [];

                                            // Get changed fields
                                            $changedFields = [];
                                            if (!empty($changes) && !empty($oldValues)) {
                                                $changedFields = array_keys(array_diff_assoc($changes, $oldValues));
                                            }

                                            // Get subject identifier with friendly names
                                            $subjectName = '';
                                            $subjectLabel = '';
                                            $parsedModelType = null;
                                            $parsedModelId = null;

                                            if ($subject) {
                                                // Map model types to friendly labels
                                                $modelType = class_basename(get_class($subject));
                                                $labelMap = [
                                                    'SeedlingRequest' => 'Supply Request',
                                                    'SeedlingRequestItem' => 'Supply Item',
                                                    'RsbsaApplication' => 'RSBSA Application',
                                                    'FishrApplication' => 'FishR Application',
                                                    'BoatrApplication' => 'BoatR Application',
                                                    'TrainingApplication' => 'Training Application',
                                                    'CategoryItem' => 'Supply Item',
                                                    'User' => 'User',
                                                    'UserRegistration' => 'User Registration',
                                                    'Event' => 'Event',
                                                ];

                                                $subjectLabel = $labelMap[$modelType] ?? $modelType;

                                                // Get the display identifier based on model type
                                                if ($modelType === 'User') {
                                                    // For User model, show name or email
                                                    $userName =
                                                        $subject->name ??
                                                        ($subject->first_name && $subject->last_name
                                                            ? trim($subject->first_name . ' ' . $subject->last_name)
                                                            : null);
                                                    $subjectName = $userName ?: $subject->email ?? '#' . $subject->id;
                                                } elseif ($modelType === 'UserRegistration') {
                                                    // For UserRegistration model, show full name or username
                                                    $userName =
                                                        $subject->first_name && $subject->last_name
                                                            ? trim($subject->first_name . ' ' . $subject->last_name)
                                                            : null;
                                                    $subjectName =
                                                        $userName ?:
                                                        ($subject->username ?:
                                                        ($subject->contact_number ?:
                                                        '#' . $subject->id));
                                                } elseif ($modelType === 'SeedlingRequestItem') {
                                                    // For SeedlingRequestItem, show item name and quantity
                                                    $itemName =
                                                        $subject->item_name ??
                                                        ($subject->categoryItem->item_name ?? 'Unknown Item');
                                                    $quantity =
                                                        $subject->approved_quantity ??
                                                        ($subject->requested_quantity ?? 0);
                                                    $subjectName =
                                                        $itemName . ' (' . number_format($quantity) . ' pcs)';
                                                } else {
                                                    // For other models
                                                    $subjectName =
                                                        $subject->request_number ??
                                                        ($subject->application_number ??
                                                            ($subject->name ??
                                                                ($subject->title ??
                                                                    ($subject->email ?? ('#' . $subject->id ?? '')))));
                                                }
                                            } else {
                                                // For old logs without subject, parse from description
                                                if (
                                                    preg_match(
                                                        '/- (\w+) \(ID: (\d+)\)/',
                                                        $activity->description,
                                                        $matches,
                                                    )
                                                ) {
                                                    $parsedModelType = $matches[1];
                                                    $parsedModelId = $matches[2];

                                                    $labelMap = [
                                                        'SeedlingRequest' => 'Supply Request',
                                                        'SeedlingRequestItem' => 'Supply Item',
                                                        'RsbsaApplication' => 'RSBSA Application',
                                                        'FishrApplication' => 'FishR Application',
                                                        'BoatrApplication' => 'BoatR Application',
                                                        'TrainingApplication' => 'Training Application',
                                                        'CategoryItem' => 'Supply Item',
                                                        'User' => 'User',
                                                        'UserRegistration' => 'User Registration',
                                                    ];

                                                    $subjectLabel = $labelMap[$parsedModelType] ?? $parsedModelType;
                                                    $subjectName = '#' . $parsedModelId;

                                                    // Try to load the actual subject to get proper identifiers
                                                    if ($parsedModelType === 'SeedlingRequest') {
                                                        try {
                                                            $subject = \App\Models\SeedlingRequest::withTrashed()->find(
                                                                $parsedModelId,
                                                            );
                                                            // Update subject name with request_number if loaded
                                                            if ($subject && $subject->request_number) {
                                                                $subjectName = $subject->request_number;
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Subject not found
                                                        }
                                                    } elseif (
                                                        in_array($parsedModelType, [
                                                            'RsbsaApplication',
                                                            'FishrApplication',
                                                            'BoatrApplication',
                                                            'TrainingApplication',
                                                        ])
                                                    ) {
                                                        try {
                                                            $modelClass = "\\App\\Models\\{$parsedModelType}";
                                                            if (class_exists($modelClass)) {
                                                                $subject = $modelClass
                                                                    ::withTrashed()
                                                                    ->find($parsedModelId);
                                                                // Update subject name with application_number if loaded
                                                                if ($subject && $subject->application_number) {
                                                                    $subjectName = $subject->application_number;
                                                                }
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Subject not found
                                                        }
                                                    } elseif ($parsedModelType === 'UserRegistration') {
                                                        try {
                                                            $subject = \App\Models\UserRegistration::withTrashed()->find(
                                                                $parsedModelId,
                                                            );
                                                            if ($subject) {
                                                                // Show full name or username
                                                                $userName =
                                                                    $subject->first_name && $subject->last_name
                                                                        ? trim(
                                                                            $subject->first_name .
                                                                                ' ' .
                                                                                $subject->last_name,
                                                                        )
                                                                        : null;
                                                                $subjectName =
                                                                    $userName ?:
                                                                    ($subject->username ?:
                                                                    ($subject->contact_number ?:
                                                                    '#' . $subject->id));
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Subject not found
                                                        }
                                                    } elseif ($parsedModelType === 'User') {
                                                        try {
                                                            $subject = \App\Models\User::withTrashed()->find(
                                                                $parsedModelId,
                                                            );
                                                            if ($subject) {
                                                                // Show name or email
                                                                $userName =
                                                                    $subject->name ??
                                                                    ($subject->first_name && $subject->last_name
                                                                        ? trim(
                                                                            $subject->first_name .
                                                                                ' ' .
                                                                                $subject->last_name,
                                                                        )
                                                                        : null);
                                                                $subjectName =
                                                                    $userName ?:
                                                                    ($subject->email ?:
                                                                    '#' . $subject->id);
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Subject not found
                                                        }
                                                    } elseif ($parsedModelType === 'SeedlingRequestItem') {
                                                        try {
                                                            $subject = \App\Models\SeedlingRequestItem::with(
                                                                'categoryItem',
                                                            )
                                                                ->withTrashed()
                                                                ->find($parsedModelId);
                                                            if ($subject) {
                                                                // Show item name and quantity
                                                                $itemName =
                                                                    $subject->item_name ??
                                                                    ($subject->categoryItem->item_name ??
                                                                        'Unknown Item');
                                                                $quantity =
                                                                    $subject->approved_quantity ??
                                                                    ($subject->requested_quantity ?? 0);
                                                                $subjectName =
                                                                    $itemName .
                                                                    ' (' .
                                                                    number_format($quantity) .
                                                                    ' pcs)';
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Subject not found
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp

                                        <div class="small">
                                            @php
                                                // Check if this is a login/logout activity
                                                $isAuthActivity =
                                                    stripos($activity->description, 'login') !== false ||
                                                    stripos($activity->description, 'logout') !== false ||
                                                    $activity->event === 'login' ||
                                                    $activity->event === 'logout';
                                            @endphp

                                            @if ($isAuthActivity)
                                                {{-- For login/logout, show descriptive message instead of User: #1 --}}
                                                <strong class="text-dark">
                                                    @if (stripos($activity->description, 'login') !== false || $activity->event === 'login')
                                                        Logged into the system
                                                    @elseif (stripos($activity->description, 'logout') !== false || $activity->event === 'logout')
                                                        Logged out of the system
                                                    @else
                                                        {{ $activity->description }}
                                                    @endif
                                                </strong>
                                            @elseif ($subjectLabel && $subjectName)
                                                <strong class="text-dark">{{ $subjectLabel }}: <code
                                                        class="bg-light px-1">{{ $subjectName }}</code></strong>

                                                {{-- Show supply items inline for SeedlingRequest --}}
                                                @if ($subject && get_class($subject) === 'App\Models\SeedlingRequest')
                                                    @php
                                                        try {
                                                            $items = $subject->items()->with('categoryItem')->get();
                                                        } catch (\Exception $e) {
                                                            $items = collect();
                                                        }
                                                    @endphp
                                                    @if ($items && $items->count() > 0)
                                                        <div class="text-muted mt-1 small">
                                                            @foreach ($items->take(2) as $item)
                                                                <div>
                                                                    <i class="fas fa-leaf text-success"></i>
                                                                    {{ $item->item_name ?? ($item->categoryItem->item_name ?? 'Unknown Item') }}
                                                                    <span
                                                                        class="badge bg-secondary">{{ number_format($item->approved_quantity ?? $item->requested_quantity) }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if ($items->count() > 2)
                                                                <div class="text-primary">
                                                                    +{{ $items->count() - 2 }} more
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endif
                                            @else
                                                <strong class="text-dark">{{ $activity->description }}</strong>
                                            @endif

                                            @if (!empty($changedFields))
                                                <div class="mt-2 small">
                                                    @php
                                                        // Define important fields that should show old → new values
                                                        $importantFields = [
                                                            'status',
                                                            'claimed_by',
                                                            'approved_by',
                                                            'rejected_by',
                                                            'is_active',
                                                            'role',
                                                            'email',
                                                            'quantity',
                                                        ];
                                                        $displayCount = 0;
                                                        $maxDisplay = 4;
                                                    @endphp

                                                    @foreach ($changedFields as $field)
                                                        @if ($displayCount < $maxDisplay)
                                                            @php
                                                                $fieldLabel = ucfirst(str_replace('_', ' ', $field));
                                                                $oldVal = $oldValues[$field] ?? 'null';
                                                                $newVal = $changes[$field] ?? 'null';

                                                                // Helper function to format timestamps
                                                                $formatValue = function ($val) {
                                                                    if (is_bool($val)) {
                                                                        return $val ? 'Yes' : 'No';
                                                                    }
                                                                    if (is_null($val)) {
                                                                        return 'None';
                                                                    }
                                                                    // Check if value looks like a timestamp
                                                                    if (
                                                                        is_string($val) &&
                                                                        preg_match(
                                                                            '/^\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2}/',
                                                                            $val,
                                                                        )
                                                                    ) {
                                                                        try {
                                                                            $date = new \DateTime($val);
                                                                            return $date->format('M d, Y H:i');
                                                                        } catch (\Exception $e) {
                                                                            return $val;
                                                                        }
                                                                    }
                                                                    // Truncate long strings
                                                                    if (is_string($val) && strlen($val) > 30) {
                                                                        return substr($val, 0, 30) . '...';
                                                                    }
                                                                    return $val;
                                                                };

                                                                $oldDisplay = $formatValue($oldVal);
                                                                $newDisplay = $formatValue($newVal);

                                                                $displayCount++;
                                                            @endphp

                                                            <div class="text-muted mb-1">
                                                                @if (in_array($field, $importantFields))
                                                                    <i class="fas fa-arrow-right text-primary"></i>
                                                                    <strong
                                                                        class="text-dark">{{ $fieldLabel }}:</strong>
                                                                    <span class="text-danger">{{ $oldDisplay }}</span>
                                                                    <i class="fas fa-long-arrow-alt-right mx-1"></i>
                                                                    <span
                                                                        class="text-success fw-bold">{{ $newDisplay }}</span>
                                                                @else
                                                                    <i class="fas fa-edit"></i>
                                                                    <strong>{{ $fieldLabel }}:</strong>
                                                                    <code class="bg-light px-1">{{ $newDisplay }}</code>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach

                                                    @if (count($changedFields) > $maxDisplay)
                                                        <div class="text-primary small">
                                                            <i class="fas fa-plus-circle"></i>
                                                            {{ count($changedFields) - $maxDisplay }} more field(s) changed
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            {{-- Show login details for authentication activities --}}
                                            @php
                                                $isLoginActivity =
                                                    stripos($activity->description, 'login') !== false ||
                                                    $activity->event === 'login';
                                            @endphp
                                            @if ($isLoginActivity)
                                                @php
                                                    $ipAddress =
                                                        $properties['ip_address'] ??
                                                        ($properties['attributes']['ip_address'] ?? null);
                                                    $userAgent =
                                                        $properties['user_agent'] ??
                                                        ($properties['attributes']['user_agent'] ?? null);

                                                    // Parse user agent to get browser and OS
                                                    $browser = 'Unknown';
                                                    $os = 'Unknown';
                                                    if ($userAgent) {
                                                        // Detect browser
                                                        if (stripos($userAgent, 'Edge') !== false) {
                                                            $browser = 'Edge';
                                                        } elseif (stripos($userAgent, 'Chrome') !== false) {
                                                            $browser = 'Chrome';
                                                        } elseif (stripos($userAgent, 'Firefox') !== false) {
                                                            $browser = 'Firefox';
                                                        } elseif (stripos($userAgent, 'Safari') !== false) {
                                                            $browser = 'Safari';
                                                        } elseif (stripos($userAgent, 'Opera') !== false) {
                                                            $browser = 'Opera';
                                                        }

                                                        // Detect OS
                                                        if (stripos($userAgent, 'Windows') !== false) {
                                                            $os = 'Windows';
                                                        } elseif (stripos($userAgent, 'Mac') !== false) {
                                                            $os = 'macOS';
                                                        } elseif (stripos($userAgent, 'Linux') !== false) {
                                                            $os = 'Linux';
                                                        } elseif (stripos($userAgent, 'Android') !== false) {
                                                            $os = 'Android';
                                                        } elseif (
                                                            stripos($userAgent, 'iOS') !== false ||
                                                            stripos($userAgent, 'iPhone') !== false
                                                        ) {
                                                            $os = 'iOS';
                                                        }
                                                    }
                                                @endphp

                                                @if ($ipAddress || $userAgent)
                                                    <div class="mt-2 pt-2 border-top small">
                                                        @if ($ipAddress)
                                                            <div class="text-muted">
                                                                <i class="fas fa-map-marker-alt text-info"></i>
                                                                <strong>IP:</strong> <code
                                                                    class="bg-light px-1">{{ $ipAddress }}</code>
                                                            </div>
                                                        @endif
                                                        @if ($userAgent)
                                                            <div class="text-muted">
                                                                <i class="fas fa-desktop text-primary"></i>
                                                                <strong>Device:</strong> {{ $os }} ·
                                                                {{ $browser }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="showDetails({{ $activity->id }})" data-bs-toggle="tooltip"
                                            title="View details">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        <strong>No activity logs found</strong>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if ($activities->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            {{-- Previous Page Link --}}
                            @if ($activities->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link">Back</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->previousPageUrl() }}"
                                        rel="prev">Back</a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $activities->currentPage();
                                $lastPage = $activities->lastPage();
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
                                            href="{{ $activities->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($activities->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->nextPageUrl() }}" rel="next">Next</a>
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

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title"><i class="fas fa-info-circle"></i> Activity Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
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
                        <input type="date" class="form-control" id="modal_date_from">
                    </div>
                    <div class="mb-3">
                        <label for="modal_date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="modal_date_to">
                    </div>
                    @if (request('date_from') || request('date_to'))
                        <div class="alert alert-info small mb-0">
                            <i class="fas fa-info-circle"></i>
                            Current filter:
                            @if (request('date_from'))
                                <strong>{{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}</strong>
                            @else
                                <strong>Any date</strong>
                            @endif
                            to
                            @if (request('date_to'))
                                <strong>{{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}</strong>
                            @else
                                <strong>Any date</strong>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearDateFilter()"><i></i> Clear</button>
                    <button type="button" class="btn btn-primary" onclick="applyDateFilter()"><i
                            class="fas fa-check"></i> Apply Filter</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            color: #666;
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
        function showDetails(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const content = document.getElementById('modalContent');

            fetch(`/admin/activity-logs/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const d = data.data;

                        let actionColor = 'success';
                        if (d.action === 'Updated') actionColor = 'info';
                        if (d.action === 'Deleted') actionColor = 'danger';

                        // Build changes section
                        let changesHtml = '';
                        if (d.has_changes && (d.changes || d.properties_old)) {
                            changesHtml = `
                        <hr class="my-2">
                        <div class="col-12">
                            <small class="text-muted d-block mb-2"><strong><i class="fas fa-exchange-alt"></i> WHAT CHANGED</strong></small>
                    `;

                            if (d.changes) {
                                // Display formatted changes
                                Object.keys(d.changes).forEach(field => {
                                    const change = d.changes[field];
                                    changesHtml += `
                                <div class="mb-2 p-2 bg-light rounded">
                                    <strong class="text-dark">${field.replace(/_/g, ' ').toUpperCase()}:</strong><br>
                                    <span class="text-danger"><i class="fas fa-arrow-right"></i> Old: ${change.old || 'N/A'}</span><br>
                                    <span class="text-success"><i class="fas fa-arrow-right"></i> New: ${change.new || 'N/A'}</span>
                                </div>
                            `;
                                });
                            } else if (d.properties_old || d.properties_new) {
                                // Parse and display changes in human-readable format
                                const oldProps = d.properties_old || {};
                                const newProps = d.properties_new || {};

                                // Get all unique keys from both objects
                                const allKeys = new Set([...Object.keys(oldProps), ...Object.keys(newProps)]);

                                allKeys.forEach(key => {
                                    const oldValue = oldProps[key];
                                    const newValue = newProps[key];

                                    // Skip if values are the same
                                    if (JSON.stringify(oldValue) === JSON.stringify(newValue)) return;

                                    // Format the field name
                                    const fieldName = key.replace(/_/g, ' ').toUpperCase();

                                    // Format values (handle dates, nulls, etc.)
                                    const formatValue = (val) => {
                                        if (val === null || val === undefined)
                                            return '<em class="text-muted">None</em>';
                                        if (typeof val === 'string' && val.match(
                                                /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/)) {
                                            // Format datetime
                                            const date = new Date(val);
                                            return date.toLocaleString('en-US', {
                                                year: 'numeric',
                                                month: 'short',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit',
                                                second: '2-digit'
                                            });
                                        }
                                        if (typeof val === 'boolean') return val ? 'Yes' : 'No';
                                        if (typeof val === 'object') return JSON.stringify(val);
                                        return val;
                                    };

                                    changesHtml += `
                                <div class="mb-2 p-2 bg-light rounded">
                                    <strong class="text-dark"><i class="fas fa-pen"></i> ${fieldName}:</strong>
                                    <div class="ms-3 mt-1">
                                        <div class="text-danger small">
                                            <i class="fas fa-minus-circle"></i> <strong>Before:</strong> ${formatValue(oldValue)}
                                        </div>
                                        <div class="text-success small">
                                            <i class="fas fa-plus-circle"></i> <strong>After:</strong> ${formatValue(newValue)}
                                        </div>
                                    </div>
                                </div>
                            `;
                                });
                            }

                            changesHtml += `</div>`;
                        }

                        // Build additional properties section
                        let propertiesHtml = '';
                        if (d.properties && Object.keys(d.properties).length > 0) {
                            // Show non-change properties
                            const excludeKeys = ['old', 'attributes', 'changes', 'ip_address', 'user_agent'];
                            const otherProps = Object.keys(d.properties)
                                .filter(key => !excludeKeys.includes(key))
                                .reduce((obj, key) => {
                                    obj[key] = d.properties[key];
                                    return obj;
                                }, {});

                            if (Object.keys(otherProps).length > 0) {
                                propertiesHtml = `
                            <hr class="my-2">
                            <div class="col-12">
                                <small class="text-muted d-block mb-2"><strong><i class="fas fa-info-circle"></i> ADDITIONAL DETAILS</strong></small>
                        `;

                                Object.keys(otherProps).forEach(key => {
                                    const value = otherProps[key];
                                    const displayValue = typeof value === 'object' ? JSON.stringify(value) :
                                        value;
                                    propertiesHtml += `
                                <div class="mb-1">
                                    <strong class="text-muted small">${key.replace(/_/g, ' ').toUpperCase()}:</strong>
                                    <span class="ms-2">${displayValue}</span>
                                </div>
                            `;
                                });

                                propertiesHtml += `</div>`;
                            }
                        }

                        content.innerHTML = `
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-5">
                                    <small class="text-muted d-block">DATE & TIME</small>
                                    <strong>${d.date}</strong>
                                </div>
                                <div class="col-7">
                                    <small class="text-muted d-block">ACTION</small>
                                    <span class="badge bg-${actionColor}"><i class="fas fa-circle"></i> ${d.action}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-2">

                        <div class="col-12">
                            <small class="text-muted d-block">WHO DID IT</small>
                            <strong>${d.user}</strong>
                            <div class="small text-muted">${d.email}</div>
                            <span class="badge bg-secondary mt-1">${d.role}</span>
                        </div>

                        <div class="col-12">
                            <small class="text-muted d-block">WHAT CHANGED</small>
                            <code class="d-block p-2 bg-light rounded">${d.description}</code>
                        </div>

                        <div class="col-6">
                            <small class="text-muted d-block">RECORD TYPE</small>
                            <code>${d.model}</code>
                        </div>

                        <div class="col-6">
                            <small class="text-muted d-block">IP ADDRESS</small>
                            <code>${d.ip}</code>
                        </div>

                        ${changesHtml}
                        ${propertiesHtml}
                    </div>
                `;
                    } else {
                        content.innerHTML = '<div class="alert alert-danger mb-0">Failed to load details</div>';
                    }
                    modal.show();
                })
                .catch(err => {
                    content.innerHTML = '<div class="alert alert-danger mb-0">Error loading details</div>';
                    modal.show();
                });
        }

        // Auto search
        let searchTimeout;

        function autoSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        }

        // Submit filter form when dropdowns change
        function submitFilterForm() {
            document.getElementById('filterForm').submit();
        }

        // Date filter modal functionality
        function applyDateFilter() {
            const dateFrom = document.getElementById('modal_date_from').value;
            const dateTo = document.getElementById('modal_date_to').value;

            document.getElementById('date_from').value = dateFrom;
            document.getElementById('date_to').value = dateTo;

            // Close modal and submit form
            const modal = bootstrap.Modal.getInstance(document.getElementById('dateFilterModal'));
            modal.hide();

            document.getElementById('filterForm').submit();
        }

        function clearDateFilter() {
            document.getElementById('modal_date_from').value = '';
            document.getElementById('modal_date_to').value = '';
            document.getElementById('date_from').value = '';
            document.getElementById('date_to').value = '';
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize modal date inputs with current filter values
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            if (dateFrom) document.getElementById('modal_date_from').value = dateFrom;
            if (dateTo) document.getElementById('modal_date_to').value = dateTo;
        });
    </script>

@endsection

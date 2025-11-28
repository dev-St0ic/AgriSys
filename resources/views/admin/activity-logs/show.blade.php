{{-- resources/views/admin/activity-logs/show.blade.php - Fixed Version --}}

@extends('layouts.app')

@section('title', 'Activity Details - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center gap-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-magnifying-glass text-primary me-2"></i>
            <span class="text-primary fw-bold">Activity Details</span>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- Navigation Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Details
            </li>
        </ol>
    </nav>

    <!-- Main Detail Card -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-info-circle"></i> Activity Summary
            </h5>
            <small>{{ $activity->created_at->format('M d, Y g:i A') }}</small>
        </div>
        
        <div class="card-body">
            <!-- Basic Information Grid -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted small mb-2">Date & Time</h6>
                        <p class="mb-0">
                            <strong>{{ $activity->created_at->format('F d, Y') }}</strong><br>
                            <span class="text-muted small">{{ $activity->created_at->format('g:i A') }}</span>
                        </p>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted small mb-2">User</h6>
                        <p class="mb-0">
                            @if($activity->causer)
                                <a href="{{ route('admin.activity-logs.by-user', $activity->causer_id) }}" 
                                    class="text-decoration-none">
                                    <strong>{{ $activity->causer->name }}</strong>
                                </a>
                                <br>
                                <small class="text-muted">{{ $activity->causer->email }}</small>
                            @else
                                <em class="text-secondary">System (Automatic)</em>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted small mb-2">Action Type</h6>
                        <p class="mb-0">
                            @php
                                $eventColor = match($activity->event ?? 'default') {
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    'restored' => 'warning',
                                    'login' => 'primary',
                                    'failed_login' => 'danger',
                                    'export' => 'secondary',
                                    default => 'secondary'
                                };
                                $actionText = match($activity->event ?? 'default') {
                                    'created' => 'Created New',
                                    'updated' => 'Updated',
                                    'deleted' => 'Deleted',
                                    'restored' => 'Restored',
                                    'login' => 'Logged In',
                                    'failed_login' => 'Login Failed',
                                    'export' => 'Downloaded',
                                    default => 'Changed'
                                };
                            @endphp
                            <span class="badge bg-{{ $eventColor }} p-2">
                                {{ $activity->description ?? 'N/A' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-muted small mb-2">Record Type</h6>
                        <p class="mb-0">
                            @if($activity->subject_type)
                                <strong>{{ class_basename($activity->subject_type) }}</strong>
                                @if($activity->subject_id)
                                    <br>
                                    <small class="text-muted">ID: #{{ $activity->subject_id }}</small>
                                @endif
                            @else
                                <em class="text-muted">General Activity</em>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Related Record Information -->
            @if($activity->subject)
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6 class="text-muted mb-3">Related Record</h6>
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ class_basename($activity->subject_type) }}</strong><br>
                                    <small class="text-muted">Record ID: #{{ $activity->subject_id }}</small>
                                </div>
                                @if($activity->subject_type && $activity->subject_id)
                                    <a href="{{ route('admin.activity-logs.for-model', [
                                        'modelType' => $activity->subject_type,
                                        'modelId' => $activity->subject_id
                                    ]) }}" class="btn btn-sm btn-info ms-3">
                                        <i class="fas fa-history"></i> View Full History
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Changes/Properties -->
            @if($activity->properties && ($activity->properties->has('attributes') || $activity->properties->has('old')))
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6 class="text-muted mb-3">Fields Changed</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Field Name</th>
                                    <th style="width: 35%;">Previous Value</th>
                                    <th style="width: 35%;">New Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activity->properties->get('attributes', []) as $key => $newValue)
                                    @php
                                        $oldValue = $activity->properties->get('old.' . $key, 'N/A');
                                        $fieldName = ucfirst(str_replace('_', ' ', $key));
                                        
                                        // Format values properly - handle arrays and objects
                                        $formattedOldValue = is_array($oldValue) || is_object($oldValue) 
                                            ? json_encode($oldValue) 
                                            : $oldValue;
                                        
                                        $formattedNewValue = is_array($newValue) || is_object($newValue) 
                                            ? json_encode($newValue) 
                                            : $newValue;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $fieldName }}</strong>
                                        </td>
                                        <td>
                                            @if($oldValue !== 'N/A')
                                                @if(is_array($oldValue) || is_object($oldValue))
                                                    <code class="small">{{ $formattedOldValue }}</code>
                                                @else
                                                    <span class="badge bg-light text-dark">{{ $formattedOldValue }}</span>
                                                @endif
                                            @else
                                                <em class="text-muted">New Field</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if(is_array($newValue) || is_object($newValue))
                                                <code class="small">{{ $formattedNewValue }}</code>
                                            @else
                                                <span class="badge bg-success text-white">{{ $formattedNewValue }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No field changes recorded
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Security Information (IP, User Agent) -->
            @if($activity->properties && ($activity->properties->has('ip_address') || $activity->properties->has('user_agent')))
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6 class="text-muted mb-3">Connection Details</h6>
                    <div class="row">
                        @if($activity->properties->has('ip_address'))
                        <div class="col-md-6 mb-3">
                            <div class="card border-light">
                                <div class="card-body">
                                    <h6 class="text-muted small mb-2">IP Address</h6>
                                    <p class="mb-0">
                                        <code class="bg-light p-2 rounded d-block">{{ $activity->properties['ip_address'] }}</code>
                                    </p>
                                    <small class="text-muted">Identifies the origin of this action</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($activity->properties->has('user_agent'))
                        <div class="col-md-6 mb-3">
                            <div class="card border-light">
                                <div class="card-body">
                                    <h6 class="text-muted small mb-2">Device & Browser</h6>
                                    <p class="mb-0">
                                        <small class="text-muted d-block text-truncate">{{ $activity->properties['user_agent'] }}</small>
                                    </p>
                                    <small class="text-muted">The device used for this action</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Technical Details (Collapsible) -->
            @if($activity->properties)
            <div class="row">
                <div class="col-md-12">
                    <h6 class="text-muted mb-3 cursor-pointer" data-bs-toggle="collapse" data-bs-target="#technicalDetails">
                        <i class="fas fa-code"></i> Technical Details
                        <small class="text-muted float-end">Click to expand</small>
                    </h6>
                    <div class="collapse" id="technicalDetails">
                        <div class="card bg-dark text-light">
                            <div class="card-body p-0">
                                <pre class="mb-0 p-3"><code class="text-light small">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> No additional details recorded
            </div>
            @endif
        </div>
    </div>

    <!-- Related Activity Logs -->
    @if($activity->subject_type && $activity->subject_id)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-history"></i> Record History
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                View all activities related to this {{ class_basename($activity->subject_type) }}.
            </p>
            <a href="{{ route('admin.activity-logs.for-model', [
                'modelType' => $activity->subject_type,
                'modelId' => $activity->subject_id
            ]) }}" class="btn btn-info">
                <i class="fas fa-external-link-alt"></i> View Complete History
            </a>
        </div>
    </div>
    @endif

    <!-- Footer Navigation -->
    <div class="mt-4">
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
        <a href="{{ route('admin.activity-logs.export', ['search' => $activity->description]) }}" 
            class="btn btn-success me-2">
            <i class="fas fa-download"></i> Download Similar
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary">
            <i class="fas fa-print"></i> Print
        </button>
    </div>

</div>

<style>
    code {
        font-family: 'Courier New', monospace;
        word-break: break-all;
    }
    
    pre code {
        color: inherit;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
</style>

@endsection
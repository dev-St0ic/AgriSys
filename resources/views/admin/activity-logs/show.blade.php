{{-- resources/views/admin/activity-logs/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Activity Log Detail - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-history text-primary me-2"></i>
            <span class="text-primary fw-bold">Activity Log Detail #{{ $activity->id }}</span>
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
                Log #{{ $activity->id }}
            </li>
        </ol>
    </nav>

    <!-- Main Detail Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-file-alt"></i> Activity Log #{{ $activity->id }}
            </h5>
            <small>{{ $activity->created_at->format('M d, Y h:i A') }}</small>
        </div>
        
        <div class="card-body">
            <!-- Basic Information -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <h6 class="text-muted">
                        <i class="fas fa-calendar"></i> Date/Time
                    </h6>
                    <p class="mb-0">
                        <strong>{{ $activity->created_at->format('Y-m-d H:i:s') }}</strong>
                    </p>
                </div>

                <div class="col-md-3">
                    <h6 class="text-muted">
                        <i class="fas fa-user"></i> User
                    </h6>
                    <p class="mb-0">
                        @if($activity->causer)
                            <a href="{{ route('admin.activity-logs.by-user', $activity->causer_id) }}" 
                                class="text-decoration-none">
                                <strong>{{ $activity->causer->name }}</strong>
                            </a>
                            <br>
                            <small class="text-muted">{{ $activity->causer->email }}</small>
                        @else
                            <em class="text-secondary">System</em>
                        @endif
                    </p>
                </div>

                <div class="col-md-3">
                    <h6 class="text-muted">
                        <i class="fas fa-tag"></i> Action
                    </h6>
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
                        @endphp
                        <span class="badge bg-{{ $eventColor }}">
                            {{ $activity->description ?? 'N/A' }}
                        </span>
                        @if($activity->event)
                            <br>
                            <small class="text-muted">Event: <code>{{ $activity->event }}</code></small>
                        @endif
                    </p>
                </div>

                <div class="col-md-3">
                    <h6 class="text-muted">
                        <i class="fas fa-cube"></i> Model
                    </h6>
                    <p class="mb-0">
                        @if($activity->subject_type)
                            <strong>{{ class_basename($activity->subject_type) }}</strong>
                            @if($activity->subject_id)
                                <br>
                                <code class="small">#{{ $activity->subject_id }}</code>
                            @endif
                        @else
                            <em class="text-muted">-</em>
                        @endif
                    </p>
                </div>
            </div>

            <hr>

            <!-- Subject Information -->
            @if($activity->subject)
            <div class="row mb-4">
                <div class="col-md-12">
                    <h6 class="text-muted">
                        <i class="fas fa-link"></i> Related Record
                    </h6>
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ class_basename($activity->subject_type) }}:</strong> 
                                    <code>ID #{{ $activity->subject_id }}</code>
                                </div>
                                @if($activity->subject_type && $activity->subject_id)
                                    <a href="{{ route('admin.activity-logs.for-model', [
                                        'modelType' => $activity->subject_type,
                                        'modelId' => $activity->subject_id
                                    ]) }}" class="btn btn-sm btn-info ms-3">
                                        <i class="fas fa-history"></i> View History
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
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-edit"></i> Changes Made
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 30%;">Field</th>
                                    <th style="width: 35%;">Old Value</th>
                                    <th style="width: 35%;">New Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activity->properties->get('attributes', []) as $key => $newValue)
                                    @php
                                        $oldValue = $activity->properties->get('old.' . $key, 'N/A');
                                        $fieldName = ucfirst(str_replace('_', ' ', $key));
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $fieldName }}</strong>
                                        </td>
                                        <td>
                                            @if($oldValue !== 'N/A')
                                                <code class="small bg-light p-2 rounded d-block">{{ $oldValue }}</code>
                                            @else
                                                <em class="text-muted">New field</em>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="small bg-light p-2 rounded d-block">{{ $newValue }}</code>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No specific field changes recorded
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
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-shield-alt"></i> Security Information (ISO 27001 A.8.16)
                    </h6>
                    <div class="row">
                        @if($activity->properties->has('ip_address'))
                        <div class="col-md-6">
                            <div class="card border-light">
                                <div class="card-body">
                                    <h6 class="text-muted small">IP Address</h6>
                                    <code class="d-block">{{ $activity->properties['ip_address'] }}</code>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($activity->properties->has('user_agent'))
                        <div class="col-md-6">
                            <div class="card border-light">
                                <div class="card-body">
                                    <h6 class="text-muted small">User Agent</h6>
                                    <code class="small d-block text-truncate">{{ $activity->properties['user_agent'] }}</code>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Raw Data (JSON) -->
            @if($activity->properties)
            <div class="row">
                <div class="col-md-12">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-code"></i> Raw Data (JSON)
                    </h6>
                    <div class="card bg-dark text-light">
                        <div class="card-body p-0">
                            <pre class="mb-0 p-3"><code class="text-light">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> No additional properties recorded
            </div>
            @endif
        </div>
    </div>

    <!-- Related Activity Logs -->
    @if($activity->subject_type && $activity->subject_id)
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-history"></i> Related Activities for this Record
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">
                Other activities related to {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
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
            <i class="fas fa-arrow-left"></i> Back to All Logs
        </a>
        <a href="{{ route('admin.activity-logs.export', ['search' => $activity->description]) }}" 
            class="btn btn-success">
            <i class="fas fa-download"></i> Export Similar Activities
        </a>
    </div>

</div>

<style>
    code {
        color: #d73852;
        background-color: #f5f5f5;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
    
    pre code {
        color: inherit;
        background-color: transparent;
        padding: 0;
    }
    
    .card-body h6 {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
</style>

@endsection
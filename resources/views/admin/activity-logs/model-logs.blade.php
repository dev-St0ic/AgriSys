{{-- resources/views/admin/activity-logs/model-logs.blade.php --}}

@extends('layouts.app')

@section('title', 'Record History - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <i class="fas fa-history text-primary me-2"></i>
            <span class="text-primary fw-bold">Record Activity History</span>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Logs
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.activity-logs.index') }}">Activity Logs</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Record History
            </li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="card mb-4 bg-light">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h3>Complete Activity History</h3>
                    <p class="text-muted mb-0">
                        <strong>Model:</strong> {{ $activities->first()?->subject_type ? class_basename($activities->first()->subject_type) : 'Unknown' }}
                    </p>
                    <p class="text-muted mb-0">
                        <strong>Record ID:</strong> #{{ request('modelId') }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="stats">
                        <h5 class="text-primary">{{ $activities->total() }}</h5>
                        <small class="text-muted">Total Activities</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Timeline -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-timeline"></i> Activity Timeline
            </h5>
        </div>
        <div class="card-body">
            @if($activities->count() > 0)
                <div class="timeline">
                    @foreach($activities as $activity)
                        <div class="timeline-item mb-4">
                            <div class="timeline-marker">
                                @php
                                    $eventIcon = match($activity->event ?? 'default') {
                                        'created' => 'fa-plus-circle',
                                        'updated' => 'fa-edit',
                                        'deleted' => 'fa-trash',
                                        'restored' => 'fa-undo',
                                        default => 'fa-circle'
                                    };
                                    $eventColor = match($activity->event ?? 'default') {
                                        'created' => 'success',
                                        'updated' => 'info',
                                        'deleted' => 'danger',
                                        'restored' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <div class="badge bg-{{ $eventColor }}" style="font-size: 1.2rem; padding: 0.5rem 0.75rem;">
                                    <i class="fas {{ $eventIcon }}"></i>
                                </div>
                            </div>

                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $activity->description }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar"></i>
                                            {{ $activity->created_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                    <a href="{{ route('admin.activity-logs.show', $activity->id) }}" 
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </div>

                                <!-- User Info -->
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i>
                                        @if($activity->causer)
                                            <strong>{{ $activity->causer->name }}</strong> ({{ $activity->causer->email }})
                                        @else
                                            <em>System</em>
                                        @endif
                                    </small>
                                </div>

                                <!-- Changes Summary -->
                                @if($activity->properties && $activity->properties->has('attributes'))
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>Fields Changed:</strong>
                                            {{ count($activity->properties->get('attributes')) }}
                                        </small>
                                        <div class="mt-1">
                                            @foreach(array_slice(array_keys($activity->properties->get('attributes', [])), 0, 3) as $field)
                                                <small class="badge bg-light text-dark">{{ $field }}</small>
                                            @endforeach
                                            @if(count($activity->properties->get('attributes', [])) > 3)
                                                <small class="badge bg-light text-dark">
                                                    +{{ count($activity->properties->get('attributes', [])) - 3 }} more
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($activities->hasPages())
                <nav aria-label="pagination" class="mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing <strong>{{ $activities->firstItem() }}</strong> to 
                            <strong>{{ $activities->lastItem() }}</strong> of 
                            <strong>{{ $activities->total() }}</strong> activities
                        </div>
                        <div>
                            {{ $activities->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </nav>
                @endif

            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-3">No activities found for this record</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer Navigation -->
    <div class="mt-4 mb-4">
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to All Logs
        </a>
    </div>

</div>

<style>
    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .timeline-item {
        position: relative;
    }
/* 
    .timeline-marker {
        position: absolute;
        left: -1.75rem;
        top: 0.25rem;
    } */

    .timeline-content {
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 0.25rem;
        border-left: 3px solid #007bff;
    }

    .timeline-item:last-child .timeline-content {
        margin-bottom: 0;
    }

    .badge {
        display: inline-block;
        min-width: 2.5rem;
        text-align: center;
    }
</style>

@endsection
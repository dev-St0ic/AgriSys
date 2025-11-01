{{-- resources/views/admin/activity-logs/show.blade.php --}}

@extends('layouts.app')

@section('title', 'Activity Log Detail - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-history text-primary me-2"></i>
        <span class="text-primary fw-bold">Activity Log Detail</span>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Logs
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Log #{{ $activity->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Date/Time</h6>
                    <p>{{ $activity->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">User</h6>
                    <p>
                        @if($activity->causer)
                            <a href="{{ route('admin.activity-logs.by-user', $activity->causer_id) }}">
                                {{ $activity->causer->name }}
                            </a>
                        @else
                            <em>System</em>
                        @endif
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6 class="text-muted">Action</h6>
                    <p>
                        <span class="badge bg-info">{{ $activity->description }}</span>
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Model</h6>
                    <p>
                        @if($activity->subject_type)
                            {{ class_basename($activity->subject_type) }}
                            @if($activity->subject_id)
                                #{{ $activity->subject_id }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>

            @if($activity->subject)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Subject Link</h6>
                        <p>
                            {{ class_basename($activity->subject_type) }}: 
                            <strong>{{ $activity->subject_id }}</strong>
                        </p>
                    </div>
                </div>
            @endif

            @if($activity->properties)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Changes</h6>
                        @if($activity->properties->has('attributes') || $activity->properties->has('old'))
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Field</th>
                                            <th>Old Value</th>
                                            <th>New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($activity->properties->has('attributes'))
                                            @foreach($activity->properties->get('attributes', []) as $key => $value)
                                                @php
                                                    $oldValue = $activity->properties->get('old.' . $key, 'N/A');
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                    <td>
                                                        <code>{{ $oldValue }}</code>
                                                    </td>
                                                    <td>
                                                        <code>{{ $value }}</code>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No changes recorded</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">No change details available</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h6 class="text-muted">Raw Data</h6>
                        <pre class="bg-light p-3 rounded"><code>{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</code></pre>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
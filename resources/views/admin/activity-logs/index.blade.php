{{-- resources/views/admin/activity-logs/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Activity Logs - AgriSys Admin')
@section('page-title')
    <div class="d-flex align-items-center">
        <i class="fas fa-history text-primary me-2"></i>
        <span class="text-primary fw-bold">Activity Logs</span>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h2>Activity Logs</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Search description..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="subject_type" class="form-control">
                            <option value="">All Models</option>
                            @foreach($subjectTypes as $type)
                                <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Changes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($activity->causer)
                                    <a href="{{ route('admin.activity-logs.by-user', $activity->causer_id) }}">
                                        {{ $activity->causer->name }}
                                    </a>
                                @else
                                    <em>System</em>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $activity->description }}</span>
                            </td>
                            <td>
                                @if($activity->subject_type)
                                    {{ class_basename($activity->subject_type) }}
                                    @if($activity->subject_id)
                                        #{{ $activity->subject_id }}
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($activity->properties->has('attributes'))
                                    <small class="text-muted">
                                        {{ count($activity->properties->get('attributes')) }} field(s) changed
                                    </small>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.activity-logs.show', $activity->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No activity logs found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Showing <strong>{{ $activities->firstItem() }}</strong> to <strong>{{ $activities->lastItem() }}</strong> of <strong>{{ $activities->total() }}</strong> results
                </div>
                <nav>
                    {{ $activities->links('pagination::bootstrap-4') }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
@extends('layouts.master', ['activePage' => 'classes', 'titlePage' => 'Class Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Class Details</h5>
                    <div class="d-flex">
                        <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Class Name</label>
                                    <p class="form-control-static">{{ $class->class_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trainer</label>
                                    <p class="form-control-static">
                                        {{ $class->trainer->full_name ?? 'No Trainer Assigned' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Schedule</label>
                                    <p class="form-control-static">
                                        {{ $class->schedule_day->format('l, F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration</label>
                                    <p class="form-control-static">{{ $class->duration_minutes }} minutes</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Capacity</label>
                                    <p class="form-control-static">
                                        {{ $class->current_capacity }} / {{ $class->max_capacity }} ({{ $class->available_spots }} available)
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Class Type</label>
                                    <p class="form-control-static">{{ $class->class_type ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price</label>
                                    <p class="form-control-static">${{ number_format($class->price, 2) }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Difficulty Level</label>
                                    <p class="form-control-static">
                                        @if($class->difficulty_level)
                                            {{ ucfirst($class->difficulty_level) }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-{{ $class->is_active ? 'success' : 'secondary' }}">
                                            {{ $class->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <div class="border p-3 rounded">
                                        {!! nl2br(e($class->description)) ?? 'No description available' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Class Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Registration Rate</label>
                                        <div class="progress mb-1" style="height: 20px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ ($class->current_capacity / $class->max_capacity) * 100 }}%" 
                                                 aria-valuenow="{{ $class->current_capacity }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="{{ $class->max_capacity }}">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ number_format(($class->current_capacity / $class->max_capacity) * 100, 1) }}% filled</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Time Until Class</label>
                                        <p class="form-control-static">
                                            @if($class->schedule_day > now())
                                                {{ $class->schedule_day->diffForHumans() }}
                                            @else
                                                Class has already occurred
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Registered Members</h5>
                </div>
                <div class="card-body">
                    @if($class->classRegistrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Member Name</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($class->classRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->member->full_name ?? 'Unknown Member' }}</td>
                                        <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-success">Registered</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No members registered for this class yet.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
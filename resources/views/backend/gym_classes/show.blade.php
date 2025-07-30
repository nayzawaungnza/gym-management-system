@extends('layouts.master', ['activePage' => 'gymclasses', 'titlePage' => 'Gym Class Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Classes /</span> Gym Class Details
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gym Class Information</h5>
                    <div class="d-flex">
                        <a href="{{ route('gymclasses.edit', $gymclass->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('gymclasses.destroy', $gymclass->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this gym class?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class Name</label>
                            <p class="form-control-static">{{ $gymclass->class_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trainer</label>
                            <p class="form-control-static">{{ $gymclass->trainer->full_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Class Type</label>
                            <p class="form-control-static">{{ $gymclass->class_type ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Schedule</label>
                            <p class="form-control-static">
                                {{ ucfirst($gymclass->schedule_day) }} ({{ \Carbon\Carbon::parse($gymclass->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($gymclass->end_time)->format('h:i A') }})
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration</label>
                            <p class="form-control-static">{{ $gymclass->duration_minutes }} minutes</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Capacity</label>
                            <p class="form-control-static">{{ $gymclass->current_capacity }} / {{ $gymclass->max_capacity }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <p class="form-control-static">${{ number_format($gymclass->price, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Room</label>
                            <p class="form-control-static">{{ $gymclass->room ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Difficulty Level</label>
                            <p class="form-control-static">{{ ucfirst($gymclass->difficulty_level ?? 'N/A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <p class="form-control-static">
                                <span class="badge bg-{{ $gymclass->is_active ? 'success' : 'secondary' }}">
                                    {{ $gymclass->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <div class="border p-3 rounded">
                                {!! nl2br(e($gymclass->description)) ?? 'No description available' !!}
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Equipment Needed</label>
                            <div class="border p-3 rounded">
                                {!! nl2br(e($gymclass->equipment_needed)) ?? 'No equipment listed' !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="form-control-static">{{ $gymclass->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Updated At</label>
                            <p class="form-control-static">{{ $gymclass->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Registered Members ({{ $gymclass->classRegistrations->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($gymclass->classRegistrations->count() > 0)
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
                                    @foreach($gymclass->classRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->member->full_name ?? 'N/A' }}</td>
                                        <td>{{ $registration->created_at->format('M d, Y H:i A') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $registration->status == 'confirmed' ? 'success' : ($registration->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
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

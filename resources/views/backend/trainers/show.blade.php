@extends('layouts.master', ['activePage' => 'trainers', 'titlePage' => 'Trainer Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Trainer Details</h5>
                    <div class="d-flex">
                        <a href="{{ route('trainers.edit', $trainer->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('trainers.destroy', $trainer->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this trainer?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if($trainer->profile_photo)
                                <img src="{{ asset('storage/' . $trainer->profile_photo) }}" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px;">
                                    <span class="text-white display-4">{{ substr($trainer->first_name, 0, 1) }}{{ substr($trainer->last_name, 0, 1) }}</span>
                                </div>
                            @endif
                            <h4 class="mb-1">{{ $trainer->full_name }}</h4>
                            <span class="badge bg-{{ $trainer->is_active ? 'success' : 'danger' }}">
                                {{ $trainer->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <p class="form-control-static">{{ $trainer->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <p class="form-control-static">{{ $trainer->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Specialization</label>
                                    <p class="form-control-static">{{ $trainer->specialization ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-12 mb-3">
    <label class="form-label">Certifications</label>
    @if(!empty($trainer->certifications))
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Certification</th>
                        <th>Year Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainer->certifications as $cert)
                    <tr>
                        <td>{{ $cert['name'] }}</td>
                        <td>{{ $cert['year'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted">No certifications listed</p>
    @endif
</div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hire Date</label>
                                    <p class="form-control-static">{{ $trainer->hire_date->format('M d, Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hourly Rate</label>
                                    <p class="form-control-static">${{ number_format($trainer->hourly_rate, 2) }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Bio</label>
                                    <div class="border p-3 rounded">
                                        {!! nl2br(e($trainer->bio)) ?? 'No bio available' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Classes Assigned</h5>
                </div>
                <div class="card-body">
                    @if($trainer->classes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Schedule</th>
                                        <th>Duration</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trainer->classes as $class)
                                    <tr>
                                        <td>{{ $class->name }}</td>
                                        <td>
                                            {{ $class->schedule_day }}<br>
                                            {{ $class->start_time }} - {{ $class->end_time }}
                                        </td>
                                        <td>{{ $class->duration }} minutes</td>
                                        <td>{{ $class->current_capacity }} / {{ $class->max_capacity }}</td>
                                        <td>
                                            <span class="badge bg-{{ $class->is_active ? 'success' : 'danger' }}">
                                                {{ $class->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No classes assigned to this trainer.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.master', ['activePage' => 'attendance', 'titlePage' => 'Attendance Management'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Attendance
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="filter_date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="filter_date_from" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="filter_date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="filter_date_to" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="filter_member" class="form-label">Member</label>
                        <select class="form-select" id="filter_member" name="member_id">
                            <option value="">All Members</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" @selected(request('member_id') == $member->id)>
                                    {{ $member->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="filter_status" class="form-label">Status</label>
                        <select class="form-select" id="filter_status" name="status">
                            <option value="">All</option>
                            <option value="checked_in" @selected(request('status') == 'checked_in')>Checked In Only</option>
                            <option value="checked_out" @selected(request('status') == 'checked_out')>Checked Out Only</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary me-2">Reset</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Attendance Records</h5>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member Name</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td>{{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}</td>
                            <td>
                                <a href="#">{{ $attendance->member?->full_name ?? 'N/A' }}</a>
                            </td>
                            <td>{{ $attendance->check_in_time?->format('Y-m-d H:i A') ?? 'N/A' }}</td>
                            <td>{{ $attendance->check_out_time?->format('Y-m-d H:i A') ?? 'Not checked out' }}</td>
                            <td>{{ $attendance->duration !== null ? $attendance->duration . ' mins' : 'N/A' }}</td>
                            <td>
                                @if ($attendance->isCheckedOut())
                                    <span class="badge bg-label-success me-1">Checked Out</span>
                                @else
                                    <span class="badge bg-label-warning me-1">Checked In</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @if (!$attendance->isCheckedOut())
                                        <button type="button" class="btn btn-sm btn-icon btn-danger check-out-btn" data-id="{{ $attendance->id }}" title="Check Out">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </button>
                                    @endif
                                    {{-- <a href="#" class="btn btn-sm btn-icon btn-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer d-flex justify-content-center">
            {{ $attendances->links() }}
        </div>
    </div>
</div>
@endsection
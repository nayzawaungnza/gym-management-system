@extends('layouts.master', ['activePage' => 'attendance', 'titlePage' => 'Attendance Management'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    $('.attendance-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('attendance.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'member_name', name: 'member.first_name'},
            {data: 'check_in_formatted', name: 'check_in_time'},
            {data: 'check_out_formatted', name: 'check_out_time'},
            {data: 'duration', name: 'duration', orderable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: true,
        order: [[2, 'desc']]
    });
});

function checkInMember() {
    const memberId = $('#member_select').val();
    if (!memberId) {
        alert('Please select a member');
        return;
    }

    $.ajax({
        url: '{{ route("attendance.check-in") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            member_id: memberId
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('.attendance-data-table').DataTable().ajax.reload();
                $('#member_select').val('');
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert('Error checking in member');
        }
    });
}

function checkOut(attendanceId) {
    $.ajax({
        url: '{{ route("attendance.check-out") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            attendance_id: attendanceId
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('.attendance-data-table').DataTable().ajax.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert('Error checking out member');
        }
    });
}

function exportAttendance() {
    const dateFrom = $('#filter_date_from').val();
    const dateTo = $('#filter_date_to').val();
    const memberId = $('#filter_member').val();
    const status = $('#filter_status').val();
    
    const format = prompt('Export format (xlsx or csv):', 'xlsx');
    if (!format || !['xlsx', 'csv'].includes(format)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("members.export-attendance") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    if (dateFrom) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'date_from';
        input.value = dateFrom;
        form.appendChild(input);
    }
    
    if (dateTo) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'date_to';
        input.value = dateTo;
        form.appendChild(input);
    }
    
    if (memberId) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'member_id';
        input.value = memberId;
        form.appendChild(input);
    }
    
    if (status) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'status';
        input.value = status;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Attendance
    </h4>

    <!-- Check-in Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Member Check-in</h5>
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label for="member_select" class="form-label">Select Member</label>
                    <select class="form-select" id="member_select">
                        <option value="">Choose a member...</option>
                        @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" onclick="checkInMember()">
                        <i class="bx bx-log-in me-1"></i> Check In
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Today's Attendance</h5>
            @can('export-data')
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-success btn-sm" onclick="exportAttendance()">
                    <i class="bx bx-export me-1"></i> Export Current View
                </button>
                <a href="{{ route('exports.index') }}" class="btn btn-outline-info btn-sm">
                    <i class="bx bx-download me-1"></i> Advanced Export
                </a>
            </div>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover attendance-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Member Name</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Duration</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master', ['activePage' => 'exports', 'titlePage' => 'Data Export'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script src="{{ url('/assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">System /</span> Data Export
    </h4>

    <!-- Quick Export Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Export</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="quickExport('members_today', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                Today's New Members
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" onclick="quickExport('attendance_today', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                Today's Attendance
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-info w-100" onclick="quickExport('active_members', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                All Active Members
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-warning w-100" onclick="quickExport('recent_members', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                Last 30 Days Members
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Members Export -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-group me-2"></i>
                        Export Members
                    </h5>
                </div>
                <div class="card-body">
                    <form id="membersExportForm" action="{{ route('export.members') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" value="xlsx" id="members_xlsx" checked>
                                        <label class="form-check-label" for="members_xlsx">
                                            <i class="bx bx-file me-1"></i> Excel (.xlsx)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" value="csv" id="members_csv">
                                        <label class="form-check-label" for="members_csv">
                                            <i class="bx bx-file-blank me-1"></i> CSV (.csv)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="membership_type_id" class="form-label">Membership Type</label>
                            <select class="form-select" name="membership_type_id" id="membership_type_id">
                                <option value="">All Membership Types</option>
                                @foreach($membershipTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="member_status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="member_status">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="join_date_from" class="form-label">Join Date From</label>
                                <input type="text" class="form-control flatpickr-date" name="join_date_from" id="join_date_from" placeholder="Select date">
                            </div>
                            <div class="col-6">
                                <label for="join_date_to" class="form-label">Join Date To</label>
                                <input type="text" class="form-control flatpickr-date" name="join_date_to" id="join_date_to" placeholder="Select date">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="member_search" class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" id="member_search" placeholder="Search by name or email">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_stats" value="1" id="include_stats" checked>
                                <label class="form-check-label" for="include_stats">
                                    Include Statistics & Summary
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-download me-1"></i>
                            Export Members
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Attendance Export -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-time me-2"></i>
                        Export Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <form id="attendanceExportForm" action="{{ route('export.attendance') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" value="xlsx" id="attendance_xlsx" checked>
                                        <label class="form-check-label" for="attendance_xlsx">
                                            <i class="bx bx-file me-1"></i> Excel (.xlsx)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="format" value="csv" id="attendance_csv">
                                        <label class="form-check-label" for="attendance_csv">
                                            <i class="bx bx-file-blank me-1"></i> CSV (.csv)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Export Type</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" value="detailed" id="detailed_export" checked>
                                        <label class="form-check-label" for="detailed_export">
                                            Detailed Report
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="export_type" value="summary" id="summary_export">
                                        <label class="form-check-label" for="summary_export">
                                            Summary Report
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="text" class="form-control flatpickr-date" name="date_from" id="date_from" placeholder="Select date">
                            </div>
                            <div class="col-6">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="text" class="form-control flatpickr-date" name="date_to" id="date_to" placeholder="Select date">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attendance_member" class="form-label">Specific Member</label>
                            <select class="form-select select2" name="member_id" id="attendance_member">
                                <option value="">All Members</option>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }} ({{ $member->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="attendance_status" class="form-label">Status</label>
                            <select class="form-select" name="status" id="attendance_status">
                                <option value="">All Statuses</option>
                                <option value="checked_out">Checked Out</option>
                                <option value="still_inside">Still Inside</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bx bx-download me-1"></i>
                            Export Attendance
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Templates -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bx bx-upload me-2"></i>
                        Import Templates
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Download template files for bulk data import</p>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="downloadTemplate('members', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                Members Template (Excel)
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-primary w-100" onclick="downloadTemplate('members', 'csv')">
                                <i class="bx bx-download me-1"></i>
                                Members Template (CSV)
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" onclick="downloadTemplate('attendance', 'xlsx')">
                                <i class="bx bx-download me-1"></i>
                                Attendance Template (Excel)
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-success w-100" onclick="downloadTemplate('attendance', 'csv')">
                                <i class="bx bx-download me-1"></i>
                                Attendance Template (CSV)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize flatpickr for date inputs
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true
    });

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Form submissions with loading states
    $('#membersExportForm').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Exporting...');
        
        setTimeout(() => {
            btn.prop('disabled', false).html('<i class="bx bx-download me-1"></i> Export Members');
        }, 3000);
    });

    $('#attendanceExportForm').on('submit', function() {
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Exporting...');
        
        setTimeout(() => {
            btn.prop('disabled', false).html('<i class="bx bx-download me-1"></i> Export Attendance');
        }, 3000);
    });
});

function quickExport(type, format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("export.quick") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = type;
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    
    form.appendChild(csrfToken);
    form.appendChild(typeInput);
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function downloadTemplate(type, format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("export.template") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = type;
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    
    form.appendChild(csrfToken);
    form.appendChild(typeInput);
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endsection

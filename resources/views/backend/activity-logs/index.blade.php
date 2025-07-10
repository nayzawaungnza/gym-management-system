@extends('layouts.master', ['activePage' => 'activity-logs', 'titlePage' => 'Activity Logs'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize date pickers
    $('.flatpickr-date').flatpickr({
        dateFormat: 'Y-m-d'
    });

    // Initialize DataTable
    var table = $('.activity-logs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('activity_logs.index') }}",
            data: function(d) {
                d.log_name = $('#filter_log_name').val();
                d.event = $('#filter_event').val();
                d.causer_id = $('#filter_causer').val();
                d.subject_type = $('#filter_subject_type').val();
                d.date_from = $('#filter_date_from').val();
                d.date_to = $('#filter_date_to').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'datetime', name: 'created_at'},
            {data: 'event_badge', name: 'event'},
            {data: 'description', name: 'description'},
            {data: 'causer_name', name: 'causer.name'},
            {data: 'subject_type_formatted', name: 'subject_type'},
            {data: 'subject_name', name: 'subject_name', orderable: false},
            {data: 'ip_location', name: 'ip_address'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: true,
        order: [[1, 'desc']],
        pageLength: 25,
        dom: 'Bfrtip',
        buttons: [
            {
                text: '<i class="bx bx-export me-1"></i> Export',
                className: 'btn btn-outline-primary',
                action: function(e, dt, node, config) {
                    exportLogs();
                }
            },
            {
                text: '<i class="bx bx-trash me-1"></i> Cleanup',
                className: 'btn btn-outline-danger',
                action: function(e, dt, node, config) {
                    showCleanupModal();
                }
            }
        ]
    });

    // Filter functionality
    $('#filter_log_name, #filter_event, #filter_causer, #filter_subject_type, #filter_date_from, #filter_date_to').on('change', function() {
        table.draw();
    });

    $('#btn_reset_filters').on('click', function() {
        $('#filter_log_name, #filter_event, #filter_causer, #filter_subject_type, #filter_date_from, #filter_date_to').val('');
        table.draw();
    });
});

function viewDetails(id) {
    $.ajax({
        url: '/admin/activity-logs/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                showDetailsModal(response.data);
            }
        },
        error: function() {
            alert('Error loading activity log details');
        }
    });
}

function showDetailsModal(data) {
    $('#modal_log_id').text(data.id);
    $('#modal_datetime').text(data.created_at);
    $('#modal_event').html('<span class="badge bg-primary">' + data.event + '</span>');
    $('#modal_description').text(data.description);
    $('#modal_causer').text(data.causer_name);
    $('#modal_subject_type').text(data.subject_type);
    $('#modal_subject_name').text(data.subject_name);
    $('#modal_ip_address').text(data.ip_address || 'N/A');
    $('#modal_user_agent').text(data.user_agent || 'N/A');
    $('#modal_batch_uuid').text(data.batch_uuid || 'N/A');
    
    // Properties
    if (data.properties && Object.keys(data.properties).length > 0) {
        let propertiesHtml = '<pre>' + JSON.stringify(data.properties, null, 2) + '</pre>';
        $('#modal_properties').html(propertiesHtml);
    } else {
        $('#modal_properties').html('<span class="text-muted">No additional properties</span>');
    }
    
    $('#activityDetailsModal').modal('show');
}

function exportLogs() {
    const params = new URLSearchParams({
        log_name: $('#filter_log_name').val(),
        event: $('#filter_event').val(),
        causer_id: $('#filter_causer').val(),
        subject_type: $('#filter_subject_type').val(),
        date_from: $('#filter_date_from').val(),
        date_to: $('#filter_date_to').val()
    });
    
    window.location.href = '{{ route("activity_logs.export") }}?' + params.toString();
}

function showCleanupModal() {
    $('#cleanupModal').modal('show');
}

function performCleanup() {
    const days = $('#cleanup_days').val();
    
    if (!days || days < 1) {
        alert('Please enter a valid number of days');
        return;
    }
    
    if (!confirm(`Are you sure you want to delete all activity logs older than ${days} days? This action cannot be undone.`)) {
        return;
    }
    
    $.ajax({
        url: '{{ route("activity_logs.cleanup") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            days: days
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                $('#cleanupModal').modal('hide');
                $('.activity-logs-table').DataTable().ajax.reload();
            }
        },
        error: function() {
            alert('Error performing cleanup');
        }
    });
}
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">System /</span> Activity Logs
    </h4>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Log Name</label>
                    <select id="filter_log_name" class="form-select">
                        <option value="">All Logs</option>
                        @foreach($logNames as $logName)
                        <option value="{{ $logName }}">{{ ucfirst($logName) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Event</label>
                    <select id="filter_event" class="form-select">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                        <option value="{{ $event }}">{{ ucfirst($event) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select id="filter_causer" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Subject Type</label>
                    <select id="filter_subject_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($subjectTypes as $type)
                        <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="text" id="filter_date_from" class="form-control flatpickr-date" placeholder="YYYY-MM-DD">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="text" id="filter_date_to" class="form-control flatpickr-date" placeholder="YYYY-MM-DD">
                </div>
            </div>
            <div class="mt-3">
                <button type="button" id="btn_reset_filters" class="btn btn-outline-secondary">
                    <i class="bx bx-refresh me-1"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Activity Logs</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover activity-logs-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Date/Time</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>User</th>
                        <th>Subject Type</th>
                        <th>Subject</th>
                        <th>IP/Location</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activity Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Log ID:</strong></td>
                                <td id="modal_log_id"></td>
                            </tr>
                            <tr>
                                <td><strong>Date/Time:</strong></td>
                                <td id="modal_datetime"></td>
                            </tr>
                            <tr>
                                <td><strong>Event:</strong></td>
                                <td id="modal_event"></td>
                            </tr>
                            <tr>
                                <td><strong>Description:</strong></td>
                                <td id="modal_description"></td>
                            </tr>
                            <tr>
                                <td><strong>User:</strong></td>
                                <td id="modal_causer"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Subject Type:</strong></td>
                                <td id="modal_subject_type"></td>
                            </tr>
                            <tr>
                                <td><strong>Subject:</strong></td>
                                <td id="modal_subject_name"></td>
                            </tr>
                            <tr>
                                <td><strong>IP Address:</strong></td>
                                <td id="modal_ip_address"></td>
                            </tr>
                            <tr>
                                <td><strong>User Agent:</strong></td>
                                <td id="modal_user_agent" class="text-break"></td>
                            </tr>
                            <tr>
                                <td><strong>Batch UUID:</strong></td>
                                <td id="modal_batch_uuid"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Properties:</h6>
                        <div id="modal_properties" class="border rounded p-2 bg-light"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cleanup Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bx bx-warning me-2"></i>
                    This action will permanently delete old activity logs and cannot be undone.
                </div>
                <div class="mb-3">
                    <label for="cleanup_days" class="form-label">Delete logs older than (days):</label>
                    <input type="number" class="form-control" id="cleanup_days" min="1" max="365" value="90">
                    <div class="form-text">Recommended: 90 days for regular cleanup</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="performCleanup()">
                    <i class="bx bx-trash me-1"></i> Delete Logs
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

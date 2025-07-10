@extends('layouts.master', ['activePage' => 'members', 'titlePage' => 'Members'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
<style>
/* Hide DataTables responsive toggle icon */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before {
    display: none !important;
}
/* Ensure no control column is added */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control {
    display: none !important;
}
</style>
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ url('/assets/js/delete-record.js') }}"></script>
<script>
$(document).ready(function() {
    $('.members-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('members.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'membership_type', name: 'membershipType.type_name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'status_badge', name: 'status', orderable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: {
            details: {
                type: 'none', // Disable default inline responsive behavior
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        var data = row.data();
                        return 'Details for ' + data.full_name;
                    }
                }),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                    tableClass: 'table'
                })
            }
        },
        columnDefs: [
            {
                responsivePriority: 1,
                targets: [1, 2, 7] // Prioritize Full Name, Email, and Actions for visibility
            }
        ],
        order: [[1, 'asc']],
        language: {
            paginate: {
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        }
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Members
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Members List</h5>
            <div class="d-flex gap-2">
                @can('export-members')
                <a href="{{ route('exports.index') }}" class="btn btn-outline-info btn-sm">
                    <i class="tf-icons ti ti-download me-1"></i> Advanced Export
                </a>
                @else
                <a href="{{ route('exports.index') }}" class="btn btn-outline-info btn-sm">
                    <i class="tf-icons ti ti-download me-1"></i> Advanced Export (No Permission)
                </a>
                @endcan
                @can('member-create')
                <a href="{{ route('members.create') }}" class="btn btn-primary">
                    <i class="tf-icons ti ti-plus me-1"></i> Add Member
                </a>
                @endcan
            </div>
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover members-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Membership Type</th>
                        <th>Created Date</th>
                        <th>Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
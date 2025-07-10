@extends('layouts.master', ['activePage' => 'roles', 'titlePage' => 'Roles & Permissions'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script src="{{ url('/assets/js/delete-record.js') }}"></script>
<script>
$(document).ready(function() {
    $('.roles-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'users_count', name: 'users_count'},
            {data: 'permissions_count', name: 'permissions_count'},
            {data: 'created_at', name: 'created_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: true,
        order: [[1, 'asc']]
    });
});


</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User Management /</span> Roles & Permissions
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Roles List</h5>
            @can('role-create')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="tf-icons ti ti-plus me-1"></i> Add Role
            </a>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover roles-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Role Name</th>
                        <th>Users Count</th>
                        <th>Permissions Count</th>
                        <th>Created Date</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

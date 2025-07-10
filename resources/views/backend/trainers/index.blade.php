@extends('layouts.master', ['activePage' => 'trainers', 'titlePage' => 'Trainers'])

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
    $('.trainers-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('trainers.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'full_name', name: 'full_name'},
            {data: 'email', name: 'email'},
            {data: 'phone', name: 'phone'},
            {data: 'specialization', name: 'specialization'},
            {data: 'hire_date_formatted', name: 'hire_date'},
            {data: 'classes_count', name: 'classes_count', orderable: false},
            {data: 'status_badge', name: 'is_active'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: false,
        scrollX: true,
        order: [[1, 'asc']]
    });
});


function changeStatus(id) {
    if (confirm('Are you sure you want to change trainer status?')) {
        $.ajax({
            url: '/admin/trainers/' + id + '/change-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('.trainers-data-table').DataTable().ajax.reload();
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error changing trainer status');
            }
        });
    }
}
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Trainers
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Trainers List</h5>
            @can('trainer-create')
            <a href="{{ route('trainers.create') }}" class="btn btn-primary">
                <i class="  ti ti-plus me-1"></i> Add Trainer
            </a>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover trainers-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Specialization</th>
                        <th>Hire Date</th>
                        <th>Classes</th>
                        <th>Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

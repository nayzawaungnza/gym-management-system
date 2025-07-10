@extends('layouts.master', ['activePage' => 'classes', 'titlePage' => 'Classes'])

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
function deleteClass(id) {
    if (confirm('Are you sure you want to delete this class?')) {
        $.ajax({
            url: '/admin/classes/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('.classes-data-table').DataTable().ajax.reload();
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error deleting class');
            }
        });
    }
}

$(document).ready(function() {
    $('.classes-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('classes.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'class_name', name: 'class_name'},
            {data: 'trainer_name', name: 'trainer.full_name'},
            {data: 'schedule_formatted', name: 'schedule_day'},
            {data: 'capacity_info', name: 'capacity_info', orderable: false},
            {data: 'status_badge', name: 'is_active'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: false,
        scrollX: true,
        order: [[3, 'asc']] // Default sort by schedule date
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Classes
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Classes List</h5>
            @can('class-create')
            <a href="{{ route('classes.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Class
            </a>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover classes-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Class Name</th>
                        <th>Trainer</th>
                        <th>Schedule</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection
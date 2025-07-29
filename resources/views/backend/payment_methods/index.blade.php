@extends('layouts.master', ['activePage' => 'paymentmethods', 'titlePage' => 'Payment Methods'])

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
    $('.paymentmethods-data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('paymentmethods.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'display_name', name: 'display_name'},
            {data: 'provider_name', name: 'provider_name'},
            {data: 'method_name', name: 'method_name'},
            {data: 'expire_minutes', name: 'expire_minutes'},
            {data: 'payment_logo_display', name: 'payment_logo', orderable: false, searchable: false},
            {data: 'status_badge', name: 'is_active'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        responsive: false,
        scrollX: true,
        order: [[1, 'asc']]
    });
});

function changeStatus(id) {
    if (confirm('Are you sure you want to change payment method status?')) {
        $.ajax({
            url: '/admin/paymentmethods/' + id + '/change-status',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('.paymentmethods-data-table').DataTable().ajax.reload();
                    alert(response.message);
                } else {
                    alert(response.message); // Display error message from controller
                }
            },
            error: function(xhr) {
                alert('Error changing payment method status');
            }
        });
    }
}
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Payment Methods
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment Methods List</h5>
            @can('paymentmethod-create')
            <a href="{{ route('paymentmethods.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Payment Method
            </a>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover paymentmethods-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Display Name</th>
                        <th>Provider</th>
                        <th>Method</th>
                        <th>Expire (Minutes)</th>
                        <th>Logo</th>
                        <th>Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

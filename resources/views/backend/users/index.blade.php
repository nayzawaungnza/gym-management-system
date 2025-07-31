@extends('layouts.master', ['activePage' => 'users', 'titlePage' => 'Users'])

@section('vendor-style')
{{-- Assuming these are for DataTables, keep if you intend to use DataTables --}}
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
{{-- Assuming these are for DataTables, keep if you intend to use DataTables --}}
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
{{-- Assuming this is for a global delete confirmation script --}}
<script src="{{ url('/assets/js/delete-record.js') }}"></script>
<script>
    // If you are using DataTables, you would initialize it here.
    // Otherwise, this section can be empty or removed.
    // Example DataTables initialization (requires backend for AJAX data):
    // $(function () {
    //     $('.users-data-table').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         ajax: "{{ route('users.index') }}", // Adjust this route if needed
    //         columns: [
    //             {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
    //             {data: 'name', name: 'name'},
    //             {data: 'email', name: 'email'},
    //             {data: 'phone', name: 'phone'},
    //             {data: 'roles', name: 'roles', orderable: false, searchable: false}, // Render roles
    //             {data: 'is_active', name: 'is_active', render: function(data) {
    //                 return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
    //             }},
    //             {data: 'action', name: 'action', orderable: false, searchable: false},
    //         ]
    //     });
    // });
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User Management /</span> Users
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">User List</h5>
            @can('member-create') {{-- Assuming a Spatie permission for creating users --}}
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add User
            </a>
            @endcan
        </div>
        
        <div class="card-datatable table-responsive">
            <table class="table table-bordered table-hover users-data-table" width="100%">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th width="10%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- This section will be populated by DataTables via AJAX if configured. --}}
                    {{-- If not using DataTables, you would loop through $users here: --}}
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @forelse ($user->getRoleNames() as $role)
                                    <span class="badge bg-info text-dark me-1">{{ $role }}</span>
                                @empty
                                    <span class="badge bg-secondary">No Roles</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info me-1">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1">
                                    <i class="ti ti-edit"></i>
                                </a>
                                {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form> --}}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Add pagination links if not using server-side DataTables --}}
        @if (isset($users) && method_exists($users, 'links'))
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

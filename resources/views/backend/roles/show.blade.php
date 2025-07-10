@extends('layouts.master', ['activePage' => 'roles', 'titlePage' => 'Role Details'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Roles /</span> {{ $role->name }}
    </h4>

    <div class="row">
        <!-- Role Information -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Role Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <p class="form-control-plaintext">{{ $role->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Users</label>
                        <p class="form-control-plaintext">{{ $role->users->count() }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total Permissions</label>
                        <p class="form-control-plaintext">{{ $role->permissions->count() }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Created Date</label>
                        <p class="form-control-plaintext">{{ $role->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        @can('role-edit')
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i> Edit Role
                        </a>
                        @endcan
                        
                        @can('role-delete')
                        @if(!in_array($role->name, ['Admin', 'Super Admin', 'Member', 'Trainer']))
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRole('{{ $role->id }}')">
                            <i class="bx bx-trash me-1"></i> Delete
                        </button>
                        @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Permissions ({{ $role->permissions->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                    <div class="row">
                        @php
                            $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                return explode('-', $permission->name)[0];
                            });
                        @endphp
                        
                        @foreach($groupedPermissions as $module => $permissions)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary">{{ ucfirst($module) }} Module</h6>
                            <ul class="list-unstyled ms-3">
                                @foreach($permissions as $permission)
                                <li class="mb-1">
                                    <i class="bx bx-check text-success me-1"></i>
                                    {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-shield-x display-4 text-muted"></i>
                        <p class="text-muted mt-2">No permissions assigned to this role</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Users with this Role -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Users with this Role ({{ $role->users->count() }})</h5>
                    @can('role-edit')
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignUsersModal">
                        <i class="bx bx-plus me-1"></i> Assign Users
                    </button>
                    @endcan
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($role->users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($user->name, 0, 2) }}
                                                </span>
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                        @else
                                        <span class="badge bg-warning">Unverified</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @can('role-edit')
                                        @if(!in_array($role->name, ['Admin', 'Super Admin']) || $user->id !== auth()->id())
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="removeUserFromRole('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="bx bx-x me-1"></i> Remove
                                        </button>
                                        @endif
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-user-x display-4 text-muted"></i>
                        <p class="text-muted mt-2">No users assigned to this role</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Users Modal -->
<div class="modal fade" id="assignUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign Users to {{ $role->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignUsersForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Users</label>
                        <select class="form-select" name="users[]" multiple size="8" required>
                            @foreach(\App\Models\User::whereDoesntHave('roles', function($query) use ($role) {
                                $query->where('id', $role->id);
                            })->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">Hold Ctrl/Cmd to select multiple users</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Users</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteRole(id) {
    if (confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
        $.ajax({
            url: '/admin/roles/' + id,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = '{{ route("roles.index") }}';
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error deleting role');
            }
        });
    }
}

function removeUserFromRole(userId, userName) {
    if (confirm(`Are you sure you want to remove ${userName} from this role?`)) {
        $.ajax({
            url: '{{ route("roles.remove-user", $role->id) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error removing user from role');
            }
        });
    }
}

document.getElementById('assignUsersForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const users = Array.from(formData.getAll('users[]'));
    
    if (users.length === 0) {
        alert('Please select at least one user');
        return;
    }
    
    $.ajax({
        url: '{{ route("roles.assign-users", $role->id) }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            users: users
        },
        success: function(response) {
            if (response.success) {
                $('#assignUsersModal').modal('hide');
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert('Error assigning users to role');
        }
    });
});
</script>
@endsection

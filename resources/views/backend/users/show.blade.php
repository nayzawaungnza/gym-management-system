@extends('layouts.master', ['activePage' => 'users', 'titlePage' => 'User Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User Management /</span> User Details
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Information</h5>
                    <div class="d-flex">
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px;">
                                    <span class="text-white display-4">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <p class="form-control-static">{{ $user->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <p class="form-control-static">{{ $user->phone ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <p class="form-control-static">{{ $user->address ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <p class="form-control-static">{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <p class="form-control-static">{{ ucfirst($user->gender ?? 'N/A') }}</p>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Roles</label>
                                    <p class="form-control-static">
                                        @forelse ($user->getRoleNames() as $role)
                                            <span class="badge bg-primary me-1">{{ $role }}</span>
                                        @empty
                                            <span class="badge bg-secondary">No Roles Assigned</span>
                                        @endforelse
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Verified At</label>
                                    <p class="form-control-static">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i A') : 'Not Verified' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Login At</label>
                                    <p class="form-control-static">{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i A') : 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Login IP</label>
                                    <p class="form-control-static">{{ $user->last_login_ip ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Login Count</label>
                                    <p class="form-control-static">{{ $user->login_count }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Created At</label>
                                    <p class="form-control-static">{{ $user->created_at->format('M d, Y H:i A') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Updated At</label>
                                    <p class="form-control-static">{{ $user->updated_at->format('M d, Y H:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

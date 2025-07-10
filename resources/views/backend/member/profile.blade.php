@extends('layouts.master', ['activePage' => 'profile', 'titlePage' => 'My Profile'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Member /</span> My Profile
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <h5 class="card-header">Profile Information</h5>
                <div class="card-body">
                    <form action="{{ route('member.profile.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="{{ $member->email }}" disabled>
                                <small class="form-text text-muted">Email cannot be changed</small>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $member->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                            <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header">Membership Details</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Membership Type</label>
                        <p class="form-control-plaintext">{{ $member->membershipType?->type_name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Join Date</label>
                        <p class="form-control-plaintext">{{ $member->join_date->format('M d, Y') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-{{ $member->status === 'Active' ? 'success' : ($member->status === 'Suspended' ? 'danger' : 'secondary') }}">
                                {{ $member->status }}
                            </span>
                        </p>
                    </div>
                    @if($member->membershipType)
                    <div class="mb-3">
                        <label class="form-label">Monthly Fee</label>
                        <p class="form-control-plaintext">${{ number_format($member->membershipType->price, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

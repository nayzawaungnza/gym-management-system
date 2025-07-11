@extends('layouts.master', ['activePage' => 'members', 'titlePage' => 'Edit Member'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Edit Member
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Edit Member Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $member->email) }}" required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone *</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $member->phone) }}" required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth *</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth->format('Y-m-d')) }}" required>
                                    @error('date_of_birth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender *</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" 
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address *</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                           id="address" name="address" value="{{ old('address', $member->address) }}" required>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Membership & Additional Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="membership_type_id" class="form-label">Membership Type *</label>
                                    <select class="form-select @error('membership_type_id') is-invalid @enderror" 
                                            id="membership_type_id" name="membership_type_id" required>
                                        <option value="">Select Membership Type</option>
                                        @foreach($membershipTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('membership_type_id', $member->membership_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->type_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('membership_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="membership_start_date" class="form-label">Membership Start Date *</label>
                                    <input type="date" class="form-control @error('membership_start_date') is-invalid @enderror" 
                                           id="membership_start_date" name="membership_start_date" value="{{ old('membership_start_date', $member->membership_start_date->format('Y-m-d')) }}" required>
                                    @error('membership_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="membership_end_date" class="form-label">Membership End Date *</label>
                                    <input type="date" class="form-control @error('membership_end_date') is-invalid @enderror" 
                                           id="membership_end_date" name="membership_end_date" value="{{ old('membership_end_date', $member->membership_end_date->format('Y-m-d')) }}" required>
                                    @error('membership_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $member->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="expired" {{ old('status', $member->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                           id="profile_photo" name="profile_photo">
                                    @error('profile_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($member->profile_photo)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/'.$member->profile_photo) }}" width="100" class="img-thumbnail">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="remove_profile_photo" name="remove_profile_photo">
                                                <label class="form-check-label" for="remove_profile_photo">Remove current photo</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                           id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}">
                                    @error('emergency_contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                    <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                           id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}">
                                    @error('emergency_contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Additional Fields -->
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="medical_conditions" class="form-label">Medical Conditions (comma separated)</label>
                                        <input type="text" class="form-control @error('medical_conditions') is-invalid @enderror" 
                                               id="medical_conditions" name="medical_conditions" 
                                               value="{{ old('medical_conditions', $member->medical_conditions ? implode(', ', json_decode($member->medical_conditions, true)) : '') }}"
                                               placeholder="e.g., Asthma, High blood pressure">
                                        @error('medical_conditions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="fitness_goals" class="form-label">Fitness Goals (comma separated)</label>
                                        <input type="text" class="form-control @error('fitness_goals') is-invalid @enderror" 
                                               id="fitness_goals" name="fitness_goals" 
                                               value="{{ old('fitness_goals', $member->fitness_goals ? implode(', ', json_decode($member->fitness_goals, true)) : '') }}"
                                               placeholder="e.g., Weight loss, Muscle gain">
                                        @error('fitness_goals')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="preferred_workout_time" class="form-label">Preferred Workout Time</label>
                                        <input type="text" class="form-control @error('preferred_workout_time') is-invalid @enderror" 
                                               id="preferred_workout_time" name="preferred_workout_time" 
                                               value="{{ old('preferred_workout_time', $member->preferred_workout_time) }}"
                                               placeholder="e.g., Morning, Evening">
                                        @error('preferred_workout_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="referral_source" class="form-label">Referral Source</label>
                                        <input type="text" class="form-control @error('referral_source') is-invalid @enderror" 
                                               id="referral_source" name="referral_source" 
                                               value="{{ old('referral_source', $member->referral_source) }}"
                                               placeholder="How did they hear about us?">
                                        @error('referral_source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Member</button>
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
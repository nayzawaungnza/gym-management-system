@extends('layouts.master', ['activePage' => 'members', 'titlePage' => 'Edit Member'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ url('/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
<script src="{{ url('/assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2();
    
    // Initialize Flatpickr
    $('.flatpickr').flatpickr({
        dateFormat: 'Y-m-d'
    });
    
    // Calculate membership end date
    $('#membership_type_id, #membership_start_date').on('change', function() {
        calculateEndDate();
    });
    
    function calculateEndDate() {
        const membershipType = $('#membership_type_id').val();
        const startDate = $('#membership_start_date').val();
        
        if (membershipType && startDate) {
            $.ajax({
                url: '{{ route("members.calculate-end-date") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    membership_type_id: membershipType,
                    start_date: startDate
                },
                success: function(response) {
                    $('#membership_end_date').val(response.end_date);
                }
            });
        }
    }
    
    // Profile photo preview
    $('#profile_photo').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#photo-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Status change confirmation
    $('#status').on('change', function() {
        const newStatus = $(this).val();
        const currentStatus = '{{ $member->status }}';
        
        if (newStatus === 'suspended' && currentStatus !== 'suspended') {
            if (!confirm('Are you sure you want to suspend this member? This will restrict their gym access.')) {
                $(this).val(currentStatus);
            }
        }
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Members /</span> Edit Member
    </h4>

    <!-- Member Status Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center">
                <i class="bx bx-info-circle me-2"></i>
                <div>
                    <strong>Member ID:</strong> {{ $member->member_id }} | 
                    <strong>Status:</strong> 
                    <span class="badge bg-{{ $member->status === 'active' ? 'success' : ($member->status === 'suspended' ? 'danger' : 'warning') }}">
                        {{ ucfirst($member->status) }}
                    </span> |
                    <strong>Member Since:</strong> {{ $member->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Personal Information -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <img id="photo-preview" 
                                         src="{{ $member->profile_photo ? Storage::url($member->profile_photo) : asset('assets/img/avatars/default-avatar.png') }}" 
                                         alt="Profile Photo" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                                    <div>
                                        <label for="profile_photo" class="btn btn-primary btn-sm">
                                            <i class="bx bx-upload me-1"></i> Change Photo
                                        </label>
                                        <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*">
                                    </div>
                                    @error('profile_photo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required>
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required>
                                        @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $member->email) }}" required>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone *</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $member->phone) }}" required>
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control flatpickr @error('date_of_birth') is-invalid @enderror" 
                                               id="date_of_birth" name="date_of_birth" 
                                               value="{{ old('date_of_birth', $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '') }}">
                                        @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="other" {{ old('gender', $member->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $member->address) }}</textarea>
                                @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership Information -->
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Membership Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="membership_type_id" class="form-label">Membership Type *</label>
                                <select class="form-select select2 @error('membership_type_id') is-invalid @enderror" 
                                        id="membership_type_id" name="membership_type_id" required>
                                    <option value="">Select Membership Type</option>
                                    @foreach($membershipTypes as $type)
                                    <option value="{{ $type->id }}" 
                                            data-price="{{ $type->price }}" 
                                            data-duration="{{ $type->duration_months }}"
                                            {{ old('membership_type_id', $member->membership_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} - ${{ number_format($type->price, 2) }}/{{ $type->duration_months }} months
                                    </option>
                                    @endforeach
                                </select>
                                @error('membership_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $member->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $member->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $member->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="membership_start_date" class="form-label">Membership Start Date *</label>
                                <input type="date" class="form-control flatpickr @error('membership_start_date') is-invalid @enderror" 
                                       id="membership_start_date" name="membership_start_date" 
                                       value="{{ old('membership_start_date', $member->membership_start_date ? $member->membership_start_date->format('Y-m-d') : '') }}" required>
                                @error('membership_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="membership_end_date" class="form-label">Membership End Date</label>
                                <input type="date" class="form-control flatpickr @error('membership_end_date') is-invalid @enderror" 
                                       id="membership_end_date" name="membership_end_date" 
                                       value="{{ old('membership_end_date', $member->membership_end_date ? $member->membership_end_date->format('Y-m-d') : '') }}">
                                @error('membership_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Emergency Contact</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="emergency_contact_name" class="form-label">Contact Name</label>
                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                   id="emergency_contact_name" name="emergency_contact_name" 
                                   value="{{ old('emergency_contact_name', $member->emergency_contact_name) }}">
                            @error('emergency_contact_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="emergency_contact_phone" class="form-label">Contact Phone</label>
                            <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                   id="emergency_contact_phone" name="emergency_contact_phone" 
                                   value="{{ old('emergency_contact_phone', $member->emergency_contact_phone) }}">
                            @error('emergency_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" 
                                   id="emergency_contact_relationship" name="emergency_contact_relationship" 
                                   value="{{ old('emergency_contact_relationship', $member->emergency_contact_relationship) }}" 
                                   placeholder="e.g., Spouse, Parent, Sibling">
                            @error('emergency_contact_relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Health & Fitness Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="medical_conditions" class="form-label">Medical Conditions</label>
                            <textarea class="form-control @error('medical_conditions') is-invalid @enderror" 
                                      id="medical_conditions" name="medical_conditions" rows="3" 
                                      placeholder="Any medical conditions or allergies">{{ old('medical_conditions', $member->medical_conditions) }}</textarea>
                            @error('medical_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="fitness_goals" class="form-label">Fitness Goals</label>
                            <textarea class="form-control @error('fitness_goals') is-invalid @enderror" 
                                      id="fitness_goals" name="fitness_goals" rows="3" 
                                      placeholder="Member's fitness goals and objectives">{{ old('fitness_goals', $member->fitness_goals) }}</textarea>
                            @error('fitness_goals')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="{{ route('members.show', $member->id) }}" class="btn btn-outline-secondary">
                                <i class="bx bx-x me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Update Member
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

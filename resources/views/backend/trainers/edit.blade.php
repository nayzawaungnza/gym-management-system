@extends('layouts.master', ['activePage' => 'trainers', 'titlePage' => 'Edit Trainer'])

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add certification
    $(document).on('click', '.add-certification', function() {
        const container = $('#certifications-container');
        const index = $('.certification-entry').length;
        const template = `
        <div class="certification-entry border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Certification Name</label>
                    <input type="text" 
                           class="form-control" 
                           name="certifications[${index}][name]" 
                           required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year Obtained</label>
                    <input type="number" 
                           class="form-control" 
                           name="certifications[${index}][year]" 
                           min="1900" 
                           max="{{ date('Y') }}"
                           required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-certification">
                        <i class="ti ti-minus"></i> Remove
                    </button>
                </div>
            </div>
        </div>`;
        container.append(template);
    });

    // Remove certification
    $(document).on('click', '.remove-certification', function() {
        $(this).closest('.certification-entry').remove();
        // Reindex remaining certifications
        $('.certification-entry').each(function(index) {
            $(this).find('[name*="[name]"]').attr('name', `certifications[${index}][name]`);
            $(this).find('[name*="[year]"]').attr('name', `certifications[${index}][year]`);
        });
    });
});
</script>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Trainers /</span> Edit Trainer
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Trainer Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('trainers.update', $trainer->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $trainer->first_name) }}" required>
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $trainer->last_name) }}" required>
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $trainer->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $trainer->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                       id="specialization" name="specialization" value="{{ old('specialization', $trainer->specialization) }}"
                                       placeholder="e.g., Weight Training, Yoga, Cardio">
                                @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
    <label class="form-label">Certifications</label>
    <div id="certifications-container">
        @php
            $oldCerts = old('certifications', isset($trainer) ? $trainer->certifications : []);
            $defaultCert = ['name' => '', 'year' => ''];
        @endphp

        @foreach(empty($oldCerts) ? [$defaultCert] : $oldCerts as $index => $cert)
        <div class="certification-entry border p-3 mb-3 rounded">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Certification Name</label>
                    <input type="text" 
                           class="form-control" 
                           name="certifications[{{ $index }}][name]" 
                           value="{{ $cert['name'] ?? '' }}"
                           required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year Obtained</label>
                    <input type="number" 
                           class="form-control" 
                           name="certifications[{{ $index }}][year]" 
                           value="{{ $cert['year'] ?? '' }}"
                           min="1900" 
                           max="{{ date('Y') }}"
                           required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    @if($loop->first)
                        <button type="button" class="btn btn-success add-certification">
                            <i class="ti ti-plus"></i> Add
                        </button>
                    @else
                        <button type="button" class="btn btn-danger remove-certification">
                            <i class="ti ti-minus"></i> Remove
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @error('certifications')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>


                            <div class="mb-3 col-md-6">
                                <label for="hire_date" class="form-label">Hire Date *</label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                       id="hire_date" name="hire_date" value="{{ old('hire_date', $trainer->hire_date->format('Y-m-d')) }}" required>
                                @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                <input type="number" step="0.01" class="form-control @error('hourly_rate') is-invalid @enderror" 
                                       id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $trainer->hourly_rate) }}">
                                @error('hourly_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" name="bio" rows="3">{{ old('bio', $trainer->bio) }}</textarea>
                                @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="profile_photo" class="form-label">Profile Photo</label>
                                <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" 
                                       id="profile_photo" name="profile_photo">
                                @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($trainer->profile_photo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $trainer->profile_photo) }}" alt="Current Profile Photo" width="100">
                                    <p class="text-muted small mt-1">Current photo</p>
                                </div>
                                @endif
                            </div>
                            <div class="mb-3 col-md-6">
                                <div class="form-check mt-4 pt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $trainer->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Trainer</button>
                            <a href="{{ route('trainers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.master', ['activePage' => 'membershiptypes', 'titlePage' => 'Edit Membership Type'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Membership Types /</span> Edit Membership Type
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Membership Type Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('membershiptypes.update', $membershiptype->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="type_name" class="form-label">Type Name *</label>
                                <input type="text" class="form-control @error('type_name') is-invalid @enderror" 
                                       id="type_name" name="type_name" value="{{ old('type_name', $membershiptype->type_name) }}" required>
                                @error('type_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="duration_months" class="form-label">Duration (Months) *</label>
                                <input type="number" class="form-control @error('duration_months') is-invalid @enderror" 
                                       id="duration_months" name="duration_months" value="{{ old('duration_months', $membershiptype->duration_months) }}" min="1" required>
                                @error('duration_months')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="price" class="form-label">Price ($) *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $membershiptype->price) }}" min="0" required>
                                @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description', $membershiptype->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
    <div class="form-check mt-4 pt-2">
        <input class="form-check-input" type="radio" id="is_active_true" name="is_active" value="1" 
               {{ old('is_active', $membershiptype->is_active) == 1 ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active_true">
            Active
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" id="is_active_false" name="is_active" value="0"
               {{ old('is_active', $membershiptype->is_active) == 0 ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active_false">
            Inactive
        </label>
    </div>
</div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Membership Type</button>
                            <a href="{{ route('membershiptypes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

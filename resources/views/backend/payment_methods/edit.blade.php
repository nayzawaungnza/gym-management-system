@extends('layouts.master', ['activePage' => 'paymentmethods', 'titlePage' => 'Edit Payment Method'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Payment Methods /</span> Edit Payment Method
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Payment Method Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('paymentmethods.update', $paymentmethod->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="display_name" class="form-label">Display Name *</label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" name="display_name" value="{{ old('display_name', $paymentmethod->display_name) }}" required>
                                @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="provider_name" class="form-label">Provider Name</label>
                                <input type="text" class="form-control @error('provider_name') is-invalid @enderror" 
                                       id="provider_name" name="provider_name" value="{{ old('provider_name', $paymentmethod->provider_name) }}">
                                @error('provider_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="method_name" class="form-label">Method Name</label>
                                <input type="text" class="form-control @error('method_name') is-invalid @enderror" 
                                       id="method_name" name="method_name" value="{{ old('method_name', $paymentmethod->method_name) }}">
                                @error('method_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="expire_minutes" class="form-label">Expire Minutes</label>
                                <input type="number" class="form-control @error('expire_minutes') is-invalid @enderror" 
                                       id="expire_minutes" name="expire_minutes" value="{{ old('expire_minutes', $paymentmethod->expire_minutes) }}" min="0">
                                @error('expire_minutes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="payment_logo" class="form-label">Payment Logo</label>
                                <input type="file" class="form-control @error('payment_logo') is-invalid @enderror" 
                                       id="payment_logo" name="payment_logo">
                                @error('payment_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($paymentmethod->payment_logo)
                                    <div class="mt-2">
                                        @if(Str::startsWith($paymentmethod->payment_logo, 'assets/'))
                                            <img src="{{ asset($paymentmethod->payment_logo) }}" alt="Current Logo" width="100">
                                        @elseif(Str::contains($paymentmethod->payment_logo, 'payment_methods/'))
                                            <img src="{{ asset($paymentmethod->payment_logo) }}" alt="Current Logo" width="100">
                                        @else
                                            <img src="{{ asset('storage/' . $paymentmethod->payment_logo) }}" alt="Current Logo" width="100">
                                        @endif
                                        <p class="text-muted small mt-1">Current logo</p>
                                    </div>
                                @endif
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <div class="form-check mt-4 pt-2">
                                    <input class="form-check-input" type="radio" id="is_active_true" name="is_active" value="1" 
                                           {{ old('is_active', $paymentmethod->is_active) == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active_true">
                                        Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" id="is_active_false" name="is_active" value="0"
                                           {{ old('is_active', $paymentmethod->is_active) == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active_false">
                                        Inactive
                                    </label>
                                </div>  
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Update Payment Method</button>
                            <a href="{{ route('paymentmethods.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.master', ['activePage' => 'paymentmethods', 'titlePage' => 'Add Payment Method'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Payment Methods /</span> Add New Payment Method
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Method Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('paymentmethods.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="display_name" class="form-label">Display Name *</label>
                                <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                       id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                                @error('display_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="provider_name" class="form-label">Provider Name</label>
                                <input type="text" class="form-control @error('provider_name') is-invalid @enderror" 
                                       id="provider_name" name="provider_name" value="{{ old('provider_name') }}">
                                @error('provider_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="method_name" class="form-label">Method Name</label>
                                <input type="text" class="form-control @error('method_name') is-invalid @enderror" 
                                       id="method_name" name="method_name" value="{{ old('method_name') }}">
                                @error('method_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="expire_minutes" class="form-label">Expire Minutes</label>
                                <input type="number" class="form-control @error('expire_minutes') is-invalid @enderror" 
                                       id="expire_minutes" name="expire_minutes" value="{{ old('expire_minutes', 0) }}" min="0">
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
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Create Payment Method</button>
                            <a href="{{ route('paymentmethods.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

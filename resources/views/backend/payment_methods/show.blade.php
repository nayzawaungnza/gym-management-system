@extends('layouts.master', ['activePage' => 'paymentmethods', 'titlePage' => 'Payment Method Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Payment Methods /</span> Payment Method Details
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Payment Method Information</h5>
                    <div class="d-flex">
                        <a href="{{ route('paymentmethods.edit', $paymentmethod->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('paymentmethods.destroy', $paymentmethod->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this payment method?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            @if($paymentmethod->payment_logo)
                                @php
                                    $logoPath = Str::startsWith($paymentmethod->payment_logo, 'assets/') 
                                        ? asset($paymentmethod->payment_logo)
                                        : (Str::contains($paymentmethod->payment_logo, 'payment_methods/')
                                            ? asset($paymentmethod->payment_logo)
                                            : asset('storage/' . $paymentmethod->payment_logo));
                                @endphp
                                <img src="{{ $logoPath }}" alt="Payment Logo" class="img-fluid mb-3" style="max-width: 150px; height: auto;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 100px; border-radius: 8px;">
                                    <span class="text-white fs-5">{{ $paymentmethod->display_name }}</span>
                                </div>
                            @endif
                            <h4 class="mb-1">{{ $paymentmethod->display_name }}</h4>
                            <span class="badge bg-{{ $paymentmethod->is_active ? 'success' : 'secondary' }}">
                                {{ $paymentmethod->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Provider Name</label>
                                    <p class="form-control-static">{{ $paymentmethod->provider_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Method Name</label>
                                    <p class="form-control-static">{{ $paymentmethod->method_name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expire Minutes</label>
                                    <p class="form-control-static">{{ $paymentmethod->expire_minutes ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-{{ $paymentmethod->is_active ? 'success' : 'secondary' }}">
                                            {{ $paymentmethod->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Created At</label>
                                    <p class="form-control-static">{{ $paymentmethod->created_at->format('M d, Y H:i A') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Updated At</label>
                                    <p class="form-control-static">{{ $paymentmethod->updated_at->format('M d, Y H:i A') }}</p>
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

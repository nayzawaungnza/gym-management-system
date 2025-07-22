@extends('layouts.master', ['activePage' => 'payments', 'titlePage' => 'Payment Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management / Payments /</span> Payment Details
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payment #{{ $payment->id }}</h5>
            <div>
                <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-info" target="_blank">
                    <i class="bx bx-receipt me-1"></i> View Receipt
                </a>
                @can('payment-edit')
                    <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit Payment
                    </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Member</h6>
                    <p>{{ $payment->member?->full_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Membership Type</h6>
                    <p>{{ $payment->membershipType?->type_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Amount</h6>
                    <p>{{ '$' . number_format($payment->amount, 2) }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Payment Method</h6>
                    <p>{{ $payment->paymentMethod?->display_name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Payment Date</h6>
                    <p>{{ $payment->payment_date->format('Y-m-d H:i A') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Status</h6>
                    {!! $payment->status_badge !!}
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Transaction ID</h6>
                    <p>{{ $payment->transaction_id ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <h6 class="text-muted">Receipt Number</h6>
                    <p>{{ $payment->receipt_number ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <h6 class="text-muted">Notes</h6>
                    <p>{{ $payment->notes ?? 'No notes available' }}</p>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Back to Payments</a>
            </div>
        </div>
    </div>
</div>
@endsection
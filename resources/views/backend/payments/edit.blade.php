@extends('layouts.master', ['activePage' => 'payments', 'titlePage' => 'Edit Payment'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management / Payments /</span> Edit Payment
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Edit Payment Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('payments.update', $payment->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="member_id" class="form-label">Member</label>
                        <select class="form-select" id="member_id" name="member_id" required>
                            <option value="">Select Member</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}" @selected($payment->member_id == $member->id)>
                                    {{ $member->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('member_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="membership_type_id" class="form-label">Membership Type</label>
                        <select class="form-select" id="membership_type_id" name="membership_type_id" required>
                            <option value="">Select Membership Type</option>
                            @foreach($membershipTypes as $type)
                                <option value="{{ $type->id }}" @selected($payment->membership_type_id == $type->id)>
                                    {{ $type->type_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('membership_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" value="{{ old('amount', $payment->amount) }}" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_method_id" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" @selected($payment->payment_method_id == $method->id)>
                                    {{ $method->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="datetime-local" class="form-control" id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d\TH:i')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" @selected($payment->status == 'pending')>Pending</option>
                            <option value="completed" @selected($payment->status == 'completed')>Completed</option>
                            <option value="failed" @selected($payment->status == 'failed')>Failed</option>
                            <option value="refunded" @selected($payment->status == 'refunded')>Refunded</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $payment->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
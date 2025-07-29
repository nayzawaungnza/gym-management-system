@extends('layouts.master', ['activePage' => 'payments', 'titlePage' => 'Payment Management'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Gym Management /</span> Payments Transactions
    </h4>

    <div class="card mb-4">
       {{-- <div class="card-header d-flex justify-content-between align-items-center">
            {{-- <h5 class="mb-0">Payment Filters</h5> --}}
            {{-- @can('payment-create')
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i> Add Payment
                </a>
            @endcan 
        </div>
        <div class="card-body">
            {{-- <form method="GET" action="{{ route('payments.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="member_id" class="form-label">Member</label>
            <select name="member_id" id="member_id" class="form-select">
                <option value="">All Members</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }}>
                        {{ $member->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="membership_type_id" class="form-label">Membership Type</label>
            <select name="membership_type_id" id="membership_type_id" class="form-select">
                <option value="">All Types</option>
                @foreach($membershipTypes as $type)
                    <option value="{{ $type->id }}" {{ request('membership_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->type_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="payment_method_id" class="form-label">Payment Method</label>
            <select name="payment_method_id" id="payment_method_id" class="form-select">
                <option value="">All Methods</option>
                @foreach($paymentMethods as $method)
                    <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                        {{ $method->display_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['pending', 'completed', 'failed', 'refunded'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>

        <div class="col-md-3 align-self-end">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form> 

        </div> --}}
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Payment Records</h5>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        {{-- <th>#</th> --}}
                        <th>Member Name</th>
                        <th>Membership Type</th>
                        <th>Class</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($payments as $payment)
                        <tr>
                            {{-- <td>{{ $payment->id }}</td> --}}
                            <td>
                                <a href="{{ route('payments.show', $payment->id) }}">{{ $payment->member?->full_name ?? 'N/A' }}</a>
                            </td>
                            <td>{{ $payment->membershipType?->type_name ?? 'N/A' }}</td>
                            <td>{{ $payment->classRegistration?->gymClass?->class_name ?? 'N/A' }}</td>
                            <td>{{ '$' . number_format($payment->amount, 2) }}</td>
                            <td><span><img src="{{ asset('' . $payment->paymentMethod->payment_logo) }}" alt="{{ $payment->paymentMethod->display_name }}" class="me-50 thumbnail mr-1" height="20" </span><span>{{ $payment->paymentMethod->display_name ?? 'N/A' }}</span></td>
                            <td>{{ $payment->payment_date->format('Y-m-d H:i A') }}</td>
                            <td>{!! $payment->status_badge !!}</td>

                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-icon btn-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('payment-edit')
                                        {{-- <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-icon btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a> --}}
                                    @endcan
                                    <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-sm btn-icon btn-info" title="Receipt" target="_blank">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No payment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer d-flex justify-content-center">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
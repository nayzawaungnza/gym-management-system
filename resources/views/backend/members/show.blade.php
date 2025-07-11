@extends('layouts.master', ['activePage' => 'members', 'titlePage' => 'Member Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Member Details - {{ $member->full_name }}</h5>
                    <div class="d-flex">
                        <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this member?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Member ID</label>
                                    <p class="form-control-static">{{ $member->member_id }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <p class="form-control-static">{{ $member->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <p class="form-control-static">{{ $member->email }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <p class="form-control-static">{{ $member->phone }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <p class="form-control-static">{{ $member->date_of_birth->format('M d, Y') }} (Age: {{ $member->date_of_birth->age }})</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <p class="form-control-static">{{ ucfirst($member->gender) }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Membership Type</label>
                                    <p class="form-control-static">{{ $member->membershipType->name ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Membership Period</label>
                                    <p class="form-control-static">
                                        {{ $member->membership_start_date->format('M d, Y') }} to {{ $member->membership_end_date->format('M d, Y') }}
                                        @if($member->membership_end_date->isPast())
                                            <span class="badge bg-danger">Expired</span>
                                        @elseif($member->membership_end_date->diffInDays(now()) <= 30)
                                            <span class="badge bg-warning">Expires in {{ $member->membership_end_date->diffInDays(now()) }} days</span>
                                        @else
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Emergency Contact</label>
                                    <p class="form-control-static">
                                        {{ $member->emergency_contact_name ?? 'N/A' }}<br>
                                        {{ $member->emergency_contact_phone ?? '' }}
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <p class="form-control-static">
                                        <span class="badge bg-{{ $member->status_color }}">
                                            {{ ucfirst($member->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <p class="form-control-static">{{ $member->address }}</p>
                                </div>
                                @if($member->medical_conditions)
                                <div class="col-12 mb-3">
                                    <label class="form-label">Medical Conditions</label>
                                    <div class="border p-3 rounded">
                                        <ul>
                                            @foreach(json_decode($member->medical_conditions, true) as $condition)
                                                <li>{{ $condition }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                                @if($member->fitness_goals)
                                <div class="col-12 mb-3">
                                    <label class="form-label">Fitness Goals</label>
                                    <div class="border p-3 rounded">
                                        <ul>
                                            @foreach(json_decode($member->fitness_goals, true) as $goal)
                                                <li>{{ $goal }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Member Statistics</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        @if($member->profile_photo)
                                            <img src="{{ asset('storage/'.$member->profile_photo) }}" class="rounded-circle" width="150" height="150" alt="Profile Photo">
                                        @else
                                            <div class="avatar avatar-xl rounded-circle bg-secondary">
                                                <span class="avatar-initials">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Classes Attended</label>
                                        <p class="form-control-static">{{ $member->classRegistrations->count() }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Last Payment</label>
                                        <p class="form-control-static">
                                            @if($member->payments->count() > 0)
                                                {{ $member->payments->last()->amount }} on {{ $member->payments->last()->payment_date->format('M d, Y') }}
                                            @else
                                                No payments recorded
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Preferred Workout Time</label>
                                        <p class="form-control-static">{{ $member->preferred_workout_time ?? 'Not specified' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Referral Source</label>
                                        <p class="form-control-static">{{ $member->referral_source ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Class Registrations</h5>
                </div>
                <div class="card-body">
                    @if($member->classRegistrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Class Name</th>
                                        <th>Trainer</th>
                                        <th>Schedule</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->classRegistrations as $registration)
                                    <tr>
                                        <td>{{ $registration->gymClass->class_name ?? 'Unknown Class' }}</td>
                                        <td>{{ $registration->gymClass->trainer->full_name ?? 'No Trainer' }}</td>
                                        <td>{{ $registration->gymClass->schedule_day->format('M d, Y H:i') }}</td>
                                        <td>{{ $registration->registration_date->format('M d, Y') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $registration->status === 'Registered' ? 'info' : ($registration->status === 'Attended' ? 'success' : 'danger') }}">
                                                {{ $registration->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">This member hasn't registered for any classes yet.</div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    @if($member->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($member->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst($payment->payment_method) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->notes ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No payment history available for this member.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
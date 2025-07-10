@extends('layouts.master', ['activePage' => 'members', 'titlePage' => 'Member Details'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Initialize attendance table
    $('#attendance-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('members.attendance', $member->id) }}",
        columns: [
            {data: 'check_in_time', name: 'check_in_time'},
            {data: 'check_out_time', name: 'check_out_time'},
            {data: 'duration', name: 'duration'},
            {data: 'method', name: 'method'},
            {data: 'date', name: 'date'}
        ],
        order: [[4, 'desc']],
        pageLength: 10
    });
});

function generateQRCode() {
    $.ajax({
        url: '{{ route("members.generate-qr", $member->id) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                $('#qr-code-modal .modal-body').html(response.qr_code);
                $('#qr-code-modal').modal('show');
            }
        },
        error: function() {
            alert('Error generating QR code');
        }
    });
}
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Members /</span> {{ $member->first_name }} {{ $member->last_name }}
    </h4>

    <div class="row">
        <!-- Member Profile Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img src="{{ $member->profile_photo ? Storage::url($member->profile_photo) : asset('assets/img/avatars/default-avatar.png') }}" 
                             alt="Profile Photo" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    </div>
                    
                    <h5 class="mb-1">{{ $member->first_name }} {{ $member->last_name }}</h5>
                    <p class="text-muted mb-3">{{ $member->member_id }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-{{ $member->status === 'active' ? 'success' : ($member->status === 'suspended' ? 'danger' : 'warning') }} fs-6">
                            {{ ucfirst($member->status) }}
                        </span>
                    </div>

                    <!-- Quick Stats -->
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-primary fs-4">{{ $member->attendances->count() }}</span>
                                <small class="text-muted">Total Visits</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-success fs-4">{{ $member->attendances->where('created_at', '>=', now()->startOfMonth())->count() }}</span>
                                <small class="text-muted">This Month</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-info fs-4">{{ $member->created_at->diffInDays(now()) }}</span>
                                <small class="text-muted">Days Member</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @can('member-edit')
                        <a href="{{ route('members.edit', $member->id) }}" class="btn btn-primary btn-sm">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                        @endcan
                        
                        @can('attendance-view')
                        <button type="button" class="btn btn-info btn-sm" onclick="generateQRCode()">
                            <i class="bx bx-qr me-1"></i> QR Code
                        </button>
                        @endcan
                        
                        @can('payment-view')
                        <a href="{{ route('payments.member', $member->id) }}" class="btn btn-success btn-sm">
                            <i class="bx bx-credit-card me-1"></i> Payments
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Information -->
        <div class="col-md-8 mb-4">
            <div class="row">
                <!-- Personal Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Email:</td>
                                            <td>{{ $member->email }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Phone:</td>
                                            <td>{{ $member->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Date of Birth:</td>
                                            <td>{{ $member->date_of_birth ? $member->date_of_birth->format('M d, Y') : 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Gender:</td>
                                            <td>{{ $member->gender ? ucfirst($member->gender) : 'Not specified' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Address:</td>
                                            <td>{{ $member->address ?: 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Join Date:</td>
                                            <td>{{ $member->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Last Updated:</td>
                                            <td>{{ $member->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membership Information -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Membership Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Type:</td>
                                    <td>{{ $member->membershipType->name ?? 'Not assigned' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Start Date:</td>
                                    <td>{{ $member->membership_start_date ? $member->membership_start_date->format('M d, Y') : 'Not set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">End Date:</td>
                                    <td>
                                        @if($member->membership_end_date)
                                            {{ $member->membership_end_date->format('M d, Y') }}
                                            @if($member->membership_end_date->isPast())
                                                <span class="badge bg-danger ms-2">Expired</span>
                                            @elseif($member->membership_end_date->diffInDays(now()) <= 7)
                                                <span class="badge bg-warning ms-2">Expires Soon</span>
                                            @endif
                                        @else
                                            Not set
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Price:</td>
                                    <td>${{ $member->membershipType->price ?? '0.00' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Emergency Contact</h5>
                        </div>
                        <div class="card-body">
                            @if($member->emergency_contact_name)
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Name:</td>
                                    <td>{{ $member->emergency_contact_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Phone:</td>
                                    <td>{{ $member->emergency_contact_phone ?: 'Not provided' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Relationship:</td>
                                    <td>{{ $member->emergency_contact_relationship ?: 'Not specified' }}</td>
                                </tr>
                            </table>
                            @else
                            <div class="text-center py-3">
                                <i class="bx bx-user-x display-4 text-muted"></i>
                                <p class="text-muted mt-2">No emergency contact information provided</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Health Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Health & Fitness Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Medical Conditions</h6>
                                    <p class="text-muted">
                                        {{ $member->medical_conditions ?: 'No medical conditions reported' }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">Fitness Goals</h6>
                                    <p class="text-muted">
                                        {{ $member->fitness_goals ?: 'No fitness goals specified' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Attendance Activity</h5>
                    @can('attendance-view')
                    <a href="{{ route('attendance.member', $member->id) }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-history me-1"></i> View All
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($member->attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover" id="attendance-table">
                            <thead>
                                <tr>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Duration</th>
                                    <th>Method</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bx bx-time display-4 text-muted"></i>
                        <p class="text-muted mt-2">No attendance records found</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qr-code-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Member QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <!-- QR Code will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

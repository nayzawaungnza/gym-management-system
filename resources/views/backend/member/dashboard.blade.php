@extends('layouts.master', ['activePage' => 'member-dashboard', 'titlePage' => 'Member Dashboard'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Monthly Attendance Chart
    const monthlyAttendanceOptions = {
        series: [{
            name: 'Visits',
            data: @json(array_column($monthlyAttendance, 'visits'))
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#71dd37'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
            categories: @json(array_column($monthlyAttendance, 'month'))
        },
        yaxis: {
            labels: {
                formatter: function (val) {
                    return Math.floor(val);
                }
            }
        }
    };
    
    const monthlyAttendanceChart = new ApexCharts(document.querySelector("#monthlyAttendanceChart"), monthlyAttendanceOptions);
    monthlyAttendanceChart.render();

    // Check-in/Check-out functionality
    $('#checkInBtn').on('click', function() {
        $.ajax({
            url: '{{ route("member.attendance.check-in") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'An error occurred');
            }
        });
    });

    $('#checkOutBtn').on('click', function() {
        $.ajax({
            url: '{{ route("member.attendance.check-out") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'An error occurred');
            }
        });
    });

    // Class registration
    $('.register-class-btn').on('click', function() {
        const classId = $(this).data('class-id');
        
        $.ajax({
            url: `/member/classes/${classId}/register`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'An error occurred');
            }
        });
    });
});
</script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome {{ $member->full_name }}! 🏋️</h5>
                            <p class="mb-4">
                                You've visited <span class="fw-bold">{{ $stats['this_month_visits'] }}</span> times this month.
                                @if($membershipStatus['days_remaining'] !== null)
                                    Your membership expires in <span class="fw-bold {{ $membershipStatus['days_remaining'] < 30 ? 'text-warning' : 'text-success' }}">{{ $membershipStatus['days_remaining'] }} days</span>.
                                @endif
                            </p>
                            <div class="d-flex gap-2">
                                @if($currentlyCheckedIn)
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="checkOutBtn">
                                        <i class="bx bx-log-out"></i> Check Out
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-success" id="checkInBtn">
                                        <i class="bx bx-log-in"></i> Check In
                                    </button>
                                @endif
                                <a href="{{ route('member.qr-checkin') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bx bx-qr"></i> QR Check-in
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-trending-up bx-sm text-success"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Total Visits</span>
                            <h3 class="card-title mb-2">{{ $stats['total_visits'] }}</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                {{ $stats['this_month_visits'] }} This Month
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-calendar bx-sm text-primary"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Classes</span>
                            <h3 class="card-title mb-2">{{ $stats['registered_classes'] }}</h3>
                            <small class="text-primary fw-semibold">
                                <i class="bx bx-check"></i>
                                {{ $stats['completed_classes'] }} Completed
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Status -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Membership Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-crown"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $membershipStatus['type'] }}</h6>
                                    <small class="text-muted">Membership Type</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-success">
                                        <i class="bx bx-calendar-check"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $membershipStatus['start_date'] ? $membershipStatus['start_date']->format('M d, Y') : 'N/A' }}</h6>
                                    <small class="text-muted">Start Date</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $membershipStatus['is_expired'] ? 'danger' : 'warning' }}">
                                        <i class="bx bx-calendar-x"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $membershipStatus['end_date'] ? $membershipStatus['end_date']->format('M d, Y') : 'N/A' }}</h6>
                                    <small class="text-muted">End Date</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $membershipStatus['days_remaining'] > 30 ? 'success' : ($membershipStatus['days_remaining'] > 0 ? 'warning' : 'danger') }}">
                                        <i class="bx bx-time"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        @if($membershipStatus['days_remaining'] !== null)
                                            {{ $membershipStatus['days_remaining'] > 0 ? $membershipStatus['days_remaining'] . ' days' : 'Expired' }}
                                        @else
                                            N/A
                                        @endif
                                    </h6>
                                    <small class="text-muted">Days Remaining</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($membershipStatus['is_expired'])
                        <div class="alert alert-danger mt-3">
                            <i class="bx bx-error-circle"></i>
                            Your membership has expired. Please renew to continue accessing gym facilities.
                        </div>
                    @elseif($membershipStatus['days_remaining'] !== null && $membershipStatus['days_remaining'] <= 30)
                        <div class="alert alert-warning mt-3">
                            <i class="bx bx-info-circle"></i>
                            Your membership expires in {{ $membershipStatus['days_remaining'] }} days. Consider renewing soon.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Classes & Upcoming Classes -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Today's Classes</h5>
                    <span class="badge bg-primary">{{ $todayClasses->count() }}</span>
                </div>
                <div class="card-body">
                    @if($todayClasses->count() > 0)
                        @foreach($todayClasses as $class)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $class->class_name }}</h6>
                                <small class="text-muted">
                                    <i class="bx bx-time"></i> {{ $class->start_time->format('H:i') }} - {{ $class->end_time->format('H:i') }}
                                    <br>
                                    <i class="bx bx-user"></i> {{ $class->trainer->full_name }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-label-success">Registered</span>
                                @if($class->start_time->diffInHours() <= 1 && $class->start_time->isFuture())
                                    <br><small class="text-warning">Starting soon!</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-calendar text-muted"></i>
                            <p class="text-muted mt-2">No classes scheduled for today</p>
                            <a href="{{ route('member.schedule') }}" class="btn btn-sm btn-outline-primary">Browse Classes</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Classes</h5>
                    <a href="{{ route('member.schedule') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($upcomingClasses->count() > 0)
                        @foreach($upcomingClasses as $class)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h6 class="mb-1">{{ $class->class_name }}</h6>
                                <small class="text-muted">
                                    <i class="bx bx-time"></i> {{ $class->start_time->format('M d, H:i') }}
                                    <br>
                                    <i class="bx bx-user"></i> {{ $class->trainer->full_name }}
                                </small>
                            </div>
                            <span class="badge bg-label-primary">{{ $class->start_time->diffForHumans() }}</span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-calendar-plus text-muted"></i>
                            <p class="text-muted mt-2">No upcoming classes</p>
                            <a href="{{ route('member.schedule') }}" class="btn btn-sm btn-outline-primary">Register for Classes</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Attendance Chart & Recent Activity -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Attendance</h5>
                    <small class="text-muted">Your gym visits over the last 6 months</small>
                </div>
                <div class="card-body">
                    <div id="monthlyAttendanceChart"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                    <a href="{{ route('member.attendance') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAttendance->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($recentAttendance->take(5) as $attendance)
                            <li class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">Gym Visit</h6>
                                    <small class="text-muted">
                                        <i class="bx bx-time"></i> {{ $attendance->check_in_time->format('M d, H:i') }}
                                        @if($attendance->check_out_time)
                                            <br><i class="bx bx-log-out"></i> {{ $attendance->check_out_time->format('H:i') }}
                                        @endif
                                    </small>
                                </div>
                                <span class="badge bg-label-{{ $attendance->check_out_time ? 'success' : 'primary' }}">
                                    {{ $attendance->check_out_time ? 'Completed' : 'Active' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-history text-muted"></i>
                            <p class="text-muted mt-2">No recent activity</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Payments</h5>
                    <a href="{{ route('member.payments') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                        <td>{{ $payment->description }}</td>
                                        <td>${{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-credit-card text-muted"></i>
                            <p class="text-muted mt-2">No recent payments</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Current Check-in Status -->
    @if($currentlyCheckedIn)
    <div class="row">
        <div class="col-12 mb-4">
            <div class="alert alert-success d-flex align-items-center">
                <i class="bx bx-check-circle me-2"></i>
                <div class="flex-grow-1">
                    <strong>Currently Checked In</strong>
                    <br>
                    <small>Checked in at {{ $currentlyCheckedIn->check_in_time->format('H:i') }} via {{ ucfirst(str_replace('_', ' ', $currentlyCheckedIn->check_in_method)) }}</small>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" id="checkOutBtn">
                    <i class="bx bx-log-out"></i> Check Out
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

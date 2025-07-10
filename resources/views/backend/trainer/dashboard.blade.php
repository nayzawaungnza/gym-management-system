@extends('layouts.master', ['activePage' => 'trainer-dashboard', 'titlePage' => 'Trainer Dashboard'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ url('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    // Monthly Statistics Chart
    const monthlyStatsOptions = {
        series: [{
            name: 'Classes',
            data: @json(array_column($monthlyStats, 'classes'))
        }, {
            name: 'Attendance',
            data: @json(array_column($monthlyStats, 'attendance'))
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#696cff', '#71dd37'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth' },
        xaxis: {
            categories: @json(array_column($monthlyStats, 'month'))
        }
    };
    
    const monthlyStatsChart = new ApexCharts(document.querySelector("#monthlyStatsChart"), monthlyStatsOptions);
    monthlyStatsChart.render();

    // Check-in functionality
    $('.check-in-btn').on('click', function() {
        const memberId = $(this).data('member-id');
        const classId = $(this).data('class-id');
        
        $.ajax({
            url: '{{ route("trainer.attendance.check-in") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                member_id: memberId,
                class_id: classId
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
                toastr.error('An error occurred');
            }
        });
    });

    // Mark attendance for class
    $('.mark-attendance-btn').on('click', function() {
        const classId = $(this).data('class-id');
        const memberIds = [];
        
        $(`.attendance-checkbox[data-class-id="${classId}"]:checked`).each(function() {
            memberIds.push($(this).data('member-id'));
        });
        
        if (memberIds.length === 0) {
            toastr.warning('Please select at least one member');
            return;
        }
        
        $.ajax({
            url: '{{ route("trainer.attendance.mark") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                class_id: classId,
                member_ids: memberIds
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
                toastr.error('An error occurred');
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
                            <h5 class="card-title text-primary">Welcome {{ $trainer->full_name }}! 💪</h5>
                            <p class="mb-4">
                                You have <span class="fw-bold">{{ $stats['today_classes'] }}</span> classes scheduled for today.
                                Total students: <span class="fw-bold">{{ $stats['total_students'] }}</span>
                            </p>
                            <a href="{{ route('trainer.schedule') }}" class="btn btn-sm btn-outline-primary">View Schedule</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/girl-doing-yoga-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/girl-doing-yoga-dark.png" data-app-light-img="illustrations/girl-doing-yoga-light.png">
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
                                    <i class="bx bx-calendar bx-sm text-primary"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Total Classes</span>
                            <h3 class="card-title mb-2">{{ $stats['total_classes'] }}</h3>
                            <small class="text-primary fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                {{ $stats['week_classes'] }} This Week
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="bx bx-group bx-sm text-success"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Total Students</span>
                            <h3 class="card-title mb-2">{{ $stats['total_students'] }}</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                Active Learners
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Classes -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Today's Classes</h5>
                    <span class="badge bg-primary">{{ $todayClasses->count() }} Classes</span>
                </div>
                <div class="card-body">
                    @if($todayClasses->count() > 0)
                        <div class="row">
                            @foreach($todayClasses as $class)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">{{ $class->class_name }}</h6>
                                            <span class="badge bg-label-info">{{ $class->start_time->format('H:i') }}</span>
                                        </div>
                                        <p class="card-text small text-muted mb-2">{{ Str::limit($class->description, 60) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bx bx-group"></i> {{ $class->classRegistrations->count() }}/{{ $class->max_participants }}
                                            </small>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#classModal{{ $class->id }}">
                                                    <i class="bx bx-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-sm mark-attendance-btn" data-class-id="{{ $class->id }}">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Class Modal -->
                            <div class="modal fade" id="classModal{{ $class->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $class->class_name }} - Attendance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <strong>Time:</strong> {{ $class->start_time->format('H:i') }} - {{ $class->end_time->format('H:i') }}<br>
                                                <strong>Location:</strong> {{ $class->location }}<br>
                                                <strong>Participants:</strong> {{ $class->classRegistrations->count() }}/{{ $class->max_participants }}
                                            </div>
                                            
                                            <h6>Registered Members:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <input type="checkbox" class="form-check-input" id="selectAll{{ $class->id }}">
                                                            </th>
                                                            <th>Member</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($class->classRegistrations as $registration)
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="form-check-input attendance-checkbox" 
                                                                       data-member-id="{{ $registration->member->id }}" 
                                                                       data-class-id="{{ $class->id }}">
                                                            </td>
                                                            <td>{{ $registration->member->full_name }}</td>
                                                            <td>{{ $registration->member->email }}</td>
                                                            <td>
                                                                <span class="badge bg-label-{{ $registration->status === 'registered' ? 'success' : 'secondary' }}">
                                                                    {{ ucfirst($registration->status) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary mark-attendance-btn" data-class-id="{{ $class->id }}">
                                                Mark Selected as Present
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-calendar-x bx-lg text-muted"></i>
                            <p class="text-muted mt-2">No classes scheduled for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Classes & Recent Attendance -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Classes</h5>
                    <a href="{{ route('trainer.schedule') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($upcomingClasses->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($upcomingClasses as $class)
                            <li class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <h6 class="mb-1">{{ $class->class_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bx bx-time"></i> {{ $class->start_time->format('M d, H:i') }}
                                        <br>
                                        <i class="bx bx-group"></i> {{ $class->classRegistrations->count() }} participants
                                    </small>
                                </div>
                                <span class="badge bg-label-primary">{{ $class->start_time->diffForHumans() }}</span>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-calendar text-muted"></i>
                            <p class="text-muted mt-2">No upcoming classes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Attendance</h5>
                    <a href="{{ route('trainer.attendance') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentAttendance->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($recentAttendance as $attendance)
                            <li class="d-flex align-items-center mb-3 pb-2 border-bottom">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="{{ $attendance->member->profile_photo ? Storage::url($attendance->member->profile_photo) : asset('assets/img/avatars/default-avatar.png') }}" alt="User" class="rounded">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $attendance->member->full_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bx bx-time"></i> {{ $attendance->check_in_time->format('M d, H:i') }}
                                    </small>
                                </div>
                                <span class="badge bg-label-{{ $attendance->check_out_time ? 'secondary' : 'success' }}">
                                    {{ $attendance->check_out_time ? 'Completed' : 'Active' }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-3">
                            <i class="bx bx-user-check text-muted"></i>
                            <p class="text-muted mt-2">No recent attendance</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Statistics</h5>
                    <small class="text-muted">Classes conducted and attendance over the last 6 months</small>
                </div>
                <div class="card-body">
                    <div id="monthlyStatsChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Overview -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">This Week's Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($weeklySchedule as $day)
                        <div class="col-md-12 col-lg-6 col-xl-4 mb-3">
                            <div class="card border">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">{{ $day['date']->format('l, M d') }}</h6>
                                </div>
                                <div class="card-body py-2">
                                    @if($day['classes']->count() > 0)
                                        @foreach($day['classes'] as $class)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <small class="fw-semibold">{{ $class->class_name }}</small>
                                                <br>
                                                <small class="text-muted">{{ $class->start_time->format('H:i') }} - {{ $class->end_time->format('H:i') }}</small>
                                            </div>
                                            <span class="badge bg-label-primary">{{ $class->classRegistrations->count() }}</span>
                                        </div>
                                        @endforeach
                                    @else
                                        <small class="text-muted">No classes scheduled</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

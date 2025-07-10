@extends('layouts.master', ['activePage' => 'dashboard', 'titlePage' => 'Admin Dashboard'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('vendor-script')
<script src="{{ url('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Welcome {{ auth()->user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                You have <span class="fw-bold">{{ $stats['total_members'] }}</span> total members in your gym.
                                <span class="fw-bold">{{ $stats['active_members'] }}</span> are currently active.
                            </p>
                            <a href="{{ route('members.index') }}" class="btn btn-sm btn-outline-primary">View Members</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ url('/assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png" />
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
                                    <i class="menu-icon tf-icons bx bx-group text-primary" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Total Members</span>
                            <h3 class="card-title mb-2">{{ $stats['total_members'] }}</h3>
                            <small class="text-success fw-semibold">
                                <i class="bx bx-up-arrow-alt"></i>
                                {{ $stats['active_members'] }} Active
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <i class="menu-icon tf-icons bx bx-user text-info" style="font-size: 2rem;"></i>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Trainers</span>
                            <h3 class="card-title mb-2">{{ $stats['total_trainers'] }}</h3>
                            <small class="text-success fw-semibold">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Classes</h6>
                            <h4 class="mb-1">{{ $stats['total_classes'] }}</h4>
                            <small class="text-muted">Total Active Classes</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="bx bx-calendar text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Monthly Revenue</h6>
                            <h4 class="mb-1">${{ number_format($stats['monthly_revenue'], 2) }}</h4>
                            <small class="text-muted">This Month</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="bx bx-dollar text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Equipment</h6>
                            <h4 class="mb-1">{{ $stats['equipment_operational'] }}</h4>
                            <small class="text-muted">{{ $stats['equipment_maintenance'] }} Under Maintenance</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="bx bx-dumbbell text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-2">Today's Attendance</h6>
                            <h4 class="mb-1">{{ $stats['todays_attendance'] }}</h4>
                            <small class="text-muted">Check-ins Today</small>
                        </div>
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="bx bx-time text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Members -->
        <div class="col-md-6 col-lg-6 col-xl-6 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Recent Members</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <small>Latest Registrations</small>
                        </div>
                    </div>
                    <ul class="p-0 m-0">
                        @foreach($recentMembers as $member)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                                </span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $member->full_name }}</h6>
                                    <small class="text-muted">{{ $member->membershipType?->type_name ?? 'N/A' }}</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $member->join_date->format('M d') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Classes -->
        <div class="col-md-6 col-lg-6 col-xl-6 order-1 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Upcoming Classes</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($upcomingClasses as $class)
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-calendar"></i>
                                </span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">{{ $class->class_name }}</h6>
                                    <small class="text-muted">{{ $class->trainer?->full_name ?? 'No Trainer' }}</small>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold">{{ $class->schedule_day->format('M d, H:i') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@extends('layouts.master', ['activePage' => 'dashboard', 'titlePage' => 'Dashboard Management'])


@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
            <p class="mb-0 text-muted">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.exports.comprehensive') }}">Comprehensive Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.exports.financial') }}">Financial Report</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.exports.analytics') }}">Analytics Report</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Members</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-members">{{ $stats['total_members'] }}</div>
                            <div class="text-xs text-success">
                                <i class="fas fa-arrow-up"></i> {{ $stats['new_members_this_month'] }} new this month ({{ $stats['member_growth_percentage'] }}%)
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Today's Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="today-revenue">${{ number_format($stats['today_revenue'], 2) }}</div>
                            <div class="text-xs text-muted">
                                Monthly: ${{ number_format($stats['month_revenue'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Members Now</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-members-now">{{ $stats['active_members_now'] }}</div>
                            <div class="text-xs text-muted">
                                Today's Check-ins: {{ $stats['today_checkins'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Equipment Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="equipment-available">{{ $equipmentStatus['available'] }}/{{ $equipmentStatus['total'] }}</div>
                            <div class="text-xs text-danger">
                                {{ $equipmentStatus['maintenance'] }} in maintenance, {{ $equipmentStatus['out_of_order'] }} out of order
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow">
                        <select class="form-select form-select-sm" id="revenue-period" onchange="updateRevenueChart()">
                            <option value="week">Last 7 Days</option>
                            <option value="month" selected>Last 30 Days</option>
                            <option value="year">Last 12 Months</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member Growth Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Member Growth</h6>
                    <div class="dropdown no-arrow">
                        <select class="form-select form-select-sm" id="member-period" onchange="updateMemberChart()">
                            <option value="month">Last 30 Days</option>
                            <option value="year" selected>Last 12 Months</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="memberChart" height="245"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Membership Type Distribution -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Membership Type Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="membershipTypeChart" height="245"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Overview -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Attendance Overview</h6>
                    <div class="dropdown no-arrow">
                        <select class="form-select form-select-sm" id="attendance-period" onchange="updateAttendanceChart()">
                            <option value="week" selected>Last 7 Days</option>
                            <option value="month">Last 30 Days</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="attendanceChart" height="320"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Upcoming Classes -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Classes</h6>
                </div>
                <div class="card-body">
                    @if($upcomingClasses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Trainer</th>
                                        <th>Date/Time</th>
                                        <th>Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingClasses as $class)
                                    <tr>
                                        <td>
                                            <strong>{{ $class['name'] }}</strong><br>
                                            <small class="text-muted">{{ $class['location'] }}</small>
                                        </td>
                                        <td>{{ $class['trainer_name'] }}</td>
                                        <td>
                                            {{ Carbon\Carbon::parse($class['start_date'])->format('M d') }}<br>
                                            <small>{{ Carbon\Carbon::parse($class['start_time'])->format('g:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $class['registered'] >= $class['capacity'] ? 'danger' : 'success' }}">
                                                {{ $class['registered'] }}/{{ $class['capacity'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No upcoming classes scheduled.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="activity-feed" style="max-height: 400px; overflow-y: auto;">
                            @foreach($recentActivities as $activity)
                            <div class="activity-item d-flex align-items-start mb-3">
                                <div class="activity-icon me-3">
                                    <i class="fas fa-{{ $activity['event'] === 'created' ? 'plus' : ($activity['event'] === 'updated' ? 'edit' : 'trash') }} text-{{ $activity['event'] === 'created' ? 'success' : ($activity['event'] === 'updated' ? 'info' : 'danger') }}"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <p class="mb-1">{{ $activity['description'] }}</p>
                                    <small class="text-muted">
                                        by {{ $activity['causer_name'] }} • {{ $activity['created_at'] }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No recent activities.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flagged Attendances -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Flagged Attendance Verifications</h6>
                </div>
                <div class="card-body">
                    @if($flaggedAttendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Check-in Time</th>
                                        <th>Verification Method</th>
                                        <th>Flag Reason</th>
                                        <th>Flagged By</th>
                                        <th>Flagged At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($flaggedAttendances as $verification)
                                    <tr>
                                        <td>{{ $verification['member_name'] }}</td>
                                        <td>{{ $verification['check_in_time'] ? Carbon\Carbon::parse($verification['check_in_time'])->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>{{ $verification['verification_method'] }}</td>
                                        <td>{{ $verification['flag_reason'] }}</td>
                                        <td>{{ $verification['flagged_by'] }}</td>
                                        <td>{{ $verification['flagged_at'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No flagged attendances.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let revenueChart, memberChart, attendanceChart, membershipTypeChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    // Auto-refresh every 5 minutes
    setInterval(refreshDashboard, 300000);
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Revenue ($)',
                data: [],
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Revenue Overview',
                    color: '#333',
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Member Growth Chart
    const memberCtx = document.getElementById('memberChart').getContext('2d');
    memberChart = new Chart(memberCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'New Members',
                data: [],
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Member Growth',
                    color: '#333',
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Check-ins',
                data: [],
                backgroundColor: '#36b9cc',
                borderColor: '#36b9cc',
                borderWidth: 1
            }, {
                label: 'Check-outs',
                data: [],
                backgroundColor: '#f6c23e',
                borderColor: '#f6c23e',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Attendance Overview',
                    color: '#333',
                    font: { size: 16 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Membership Type Chart
    const membershipTypeCtx = document.getElementById('membershipTypeChart').getContext('2d');
    membershipTypeChart = new Chart(membershipTypeCtx, {
        type: 'pie',
        data: {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#E7E9ED'],
                borderColor: ['#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#E7E9ED'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: { size: 14 }
                    }
                },
                title: {
                    display: true,
                    text: 'Membership Type Distribution',
                    color: '#333',
                    font: { size: 16 }
                }
            }
        }
    });

    // Load initial data
    updateRevenueChart();
    updateMemberChart();
    updateAttendanceChart();
    updateMembershipTypeChart();
}

function updateRevenueChart() {
    const period = document.getElementById('revenue-period').value;
    
    fetch(`{{ route('admin.revenue-chart') }}?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                revenueChart.data.labels = data.data.labels;
                revenueChart.data.datasets[0].data = data.data.data;
                revenueChart.update();
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating revenue chart:', error);
            showAlert('Error updating revenue chart', 'error');
        });
}

function updateMemberChart() {
    const period = document.getElementById('member-period').value;
    
    fetch(`{{ route('admin.member-growth') }}?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                memberChart.data.labels = data.data.labels;
                memberChart.data.datasets[0].data = data.data.data;
                memberChart.update();
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating member chart:', error);
            showAlert('Error updating member chart', 'error');
        });
}

function updateAttendanceChart() {
    const period = document.getElementById('attendance-period').value;
    
    fetch(`{{ route('admin.attendance-overview') }}?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                attendanceChart.data.labels = data.data.labels;
                attendanceChart.data.datasets[0].data = data.data.checkins;
                attendanceChart.data.datasets[1].data = data.data.checkouts;
                attendanceChart.update();
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating attendance chart:', error);
            showAlert('Error updating attendance chart', 'error');
        });
}

function updateMembershipTypeChart() {
    fetch('{{ route('admin.membership-type-distribution') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                membershipTypeChart.data.labels = data.data.labels;
                membershipTypeChart.data.datasets[0].data = data.data.data;
                membershipTypeChart.update();
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error updating membership type chart:', error);
            showAlert('Error updating membership type chart', 'error');
        });
}

function refreshDashboard() {
    fetch('{{ route('admin.stats') }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats cards
                document.getElementById('total-members').textContent = data.stats.total_members;
                document.getElementById('today-revenue').textContent = '$' + parseFloat(data.stats.today_revenue).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('active-members-now').textContent = data.stats.active_members_now;
                document.getElementById('equipment-available').textContent = `${data.stats.equipment_available}/${data.stats.total_equipment}`;
                
                // Update charts
                revenueChart.data.labels = data.revenue_data.labels;
                revenueChart.data.datasets[0].data = data.revenue_data.data;
                revenueChart.update();

                memberChart.data.labels = data.member_growth.labels;
                memberChart.data.datasets[0].data = data.member_growth.data;
                memberChart.update();

                attendanceChart.data.labels = data.attendance_data.labels;
                attendanceChart.data.datasets[0].data = data.attendance_data.data;
                attendanceChart.update();

                membershipTypeChart.data.labels = data.membership_type_data.labels;
                membershipTypeChart.data.datasets[0].data = data.membership_type_data.data;
                membershipTypeChart.update();

                showAlert('Dashboard refreshed successfully!', 'success');
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error refreshing dashboard:', error);
            showAlert('Error refreshing dashboard', 'error');
        });
}

function showAlert(message, type) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
}
</script>
@endpush
@endsection
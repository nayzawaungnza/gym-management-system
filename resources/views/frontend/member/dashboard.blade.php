@extends('frontend.layouts.app')

@section('title', 'Member Dashboard - FitZone')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $member->first_name }}!</h1>
            <p class="text-gray-600 mt-2">Here's your fitness journey overview</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i data-lucide="calendar" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Classes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_classes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i data-lucide="clock" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Upcoming Classes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_classes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i data-lucide="activity" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Visits</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_attendance'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full">
                        <i data-lucide="trending-up" class="h-6 w-6 text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['this_month_attendance'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Check-in/Check-out Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Gym Check-in</h2>
            
            <div id="attendance-section">
                @if($currentCheckIn)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-800 font-medium">Currently Checked In</p>
                                <p class="text-green-600 text-sm">Since {{ $currentCheckIn->check_in_time->format('g:i A') }}</p>
                            </div>
                            <button onclick="checkOut()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                <i data-lucide="log-out" class="h-4 w-4 inline mr-2"></i>
                                Check Out
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">Ready to start your workout?</p>
                        <button onclick="checkIn()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="log-in" class="h-5 w-5 inline mr-2"></i>
                            Check In to Gym
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="{{ route('member.classes') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="p-3 bg-blue-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i data-lucide="calendar-plus" class="h-8 w-8 text-blue-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Browse Classes</h3>
                    <p class="text-gray-600 text-sm">Find and enroll in fitness classes</p>
                </div>
            </a>

            <a href="{{ route('member.my-classes') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="p-3 bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i data-lucide="list" class="h-8 w-8 text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">My Classes</h3>
                    <p class="text-gray-600 text-sm">View your registered classes</p>
                </div>
            </a>

            <a href="{{ route('member.attendance') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <div class="p-3 bg-purple-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i data-lucide="bar-chart" class="h-8 w-8 text-purple-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Attendance</h3>
                    <p class="text-gray-600 text-sm">Track your gym visits</p>
                </div>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Classes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Classes</h3>
                @if($recentClasses->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentClasses as $registration)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $registration->gymClass->class_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $registration->gymClass->trainer->full_name ?? 'TBA' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $registration->status === 'registered' ? 'bg-green-100 text-green-800' : 
                                       ($registration->status === 'attended' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No classes registered yet</p>
                @endif
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Visits</h3>
                @if($recentAttendance->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentAttendance as $attendance)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $attendance->check_in_time->format('M j, Y') }}</p>
                                    <p class="text-sm text-gray-600">{{ $attendance->check_in_time->format('g:i A') }} - {{ $attendance->check_out_time ? $attendance->check_out_time->format('g:i A') : 'Still here' }}</p>
                                </div>
                                @if($attendance->duration_minutes)
                                    <span class="text-sm text-gray-600">{{ floor($attendance->duration_minutes / 60) }}h {{ $attendance->duration_minutes % 60 }}m</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No gym visits yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function checkIn() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by this browser.');
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            fetch('{{ route("attendance.check-in") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    method: 'manual'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Check-in failed. Please try again.');
            });
        }, function(error) {
            alert('Unable to get your location. Please enable location services.');
        });
    }

    function checkOut() {
        fetch('{{ route("attendance.check-out") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Check-out failed. Please try again.');
        });
    }
</script>
@endsection

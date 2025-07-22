@extends('frontend.layouts.app')

@section('title', 'Gym Attendance')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Gym Attendance</h1>
            <p class="text-gray-600">Check in and out of the gym using your location</p>
        </div>

        <!-- Check-in/Check-out Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <div id="attendance-status" class="mb-6">
                    @if($currentCheckIn)
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            Checked In
                        </div>
                    @else
                        <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                            Not Checked In
                        </div>
                    @endif
                </div>

                <div id="location-info" class="mb-6 text-sm text-gray-600"></div>

                <div class="space-y-4">
                    @if($currentCheckIn)
                        <button id="check-out-btn" class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="btn-text">Check Out</span>
                            <div class="loading-spinner hidden inline-block ml-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                            </div>
                        </button>
                        <div id="current-session" class="mt-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-900 mb-2">Current Session</h3>
                                <p class="text-blue-700">
                                    <span class="font-medium">Check-in time:</span> 
                                    <span id="check-in-time">{{ $currentCheckIn->check_in_time->format('M d, Y h:i A') }}</span>
                                </p>
                                <p class="text-blue-700">
                                    <span class="font-medium">Duration:</span> 
                                    <span id="session-duration">--</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <button id="check-in-btn" class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <span class="btn-text">Check In</span>
                            <div class="loading-spinner hidden inline-block ml-2">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance History -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Attendance</h2>
            
            @if($attendances->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in_time->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in_time->format('h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '--' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($attendance->check_out_time)
                                            {{ $attendance->check_in_time->diffForHumans($attendance->check_out_time, true) }}
                                        @else
                                            <span class="text-blue-600 font-medium">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->check_out_time)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Completed
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $attendances->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-4">
                        <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No attendance records</h3>
                    <p class="text-gray-600">Your gym attendance history will appear here once you start checking in.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Notification Toast -->
<div id="notification" class="fixed top-4 right-4 z-50 w-2/4 hidden">
    <div class="bg-white rounded-lg shadow-lg border p-4 max-w-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div id="notification-icon" class="w-5 h-5"></div>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p id="notification-message" class="text-sm font-medium text-gray-900"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button id="notification-close" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInBtn = document.getElementById('check-in-btn');
    const checkOutBtn = document.getElementById('check-out-btn');
    const attendanceStatus = document.getElementById('attendance-status');
    const locationInfo = document.getElementById('location-info');
    const currentSession = document.getElementById('current-session');
    const checkInTime = document.getElementById('check-in-time');
    const sessionDuration = document.getElementById('session-duration');

    let currentLocation = null;
    let sessionTimer = null;

    // CSRF token for requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize
    getCurrentLocation();

    // If there's a current check-in, start the timer
    @if($currentCheckIn)
        startSessionTimer(new Date('{{ $currentCheckIn->check_in_time }}'));
    @endif

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            locationInfo.textContent = 'Getting your location...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    currentLocation = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    
                    locationInfo.innerHTML = `
                        <div class="flex items-center justify-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Location detected
                        </div>
                    `;
                    
                    enableButtons();
                },
                function(error) {
                    let errorMessage = 'Location access denied';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied. Please enable location services.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                    }
                    
                    locationInfo.innerHTML = `
                        <div class="flex items-center justify-center text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            ${errorMessage}
                        </div>
                    `;
                    
                    showNotification(errorMessage, 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                }
            );
        } else {
            locationInfo.innerHTML = `
                <div class="flex items-center justify-center text-red-600">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Geolocation not supported
                </div>
            `;
            showNotification('Geolocation is not supported by this browser.', 'error');
        }
    }

    // Enable buttons when location is available
    function enableButtons() {
        if (currentLocation) {
            if (checkInBtn) checkInBtn.disabled = false;
            if (checkOutBtn) checkOutBtn.disabled = false;
        }
    }

    // Start session timer
    function startSessionTimer(checkInTime) {
        if (sessionTimer) {
            clearInterval(sessionTimer);
        }
        
        updateDuration(); // Initial update
        
        sessionTimer = setInterval(updateDuration, 1000);
        
        function updateDuration() {
            const now = new Date();
            const diff = now - checkInTime;
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            if (sessionDuration) {
                sessionDuration.textContent = hours > 0 ? `${hours}h ${minutes}m` : `${minutes}m`;
            }
        }
    }

    // Check in
    if (checkInBtn) {
        checkInBtn.addEventListener('click', function() {
            if (!currentLocation) {
                showNotification('Location not available. Please enable location services.', 'error');
                return;
            }

            setButtonLoading(checkInBtn, true);

            fetch('{{ route("attendance.check-in") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    latitude: currentLocation.latitude,
                    longitude: currentLocation.longitude,
                    method: 'manual'
                })
            })
            .then(response => response.json())
            .then(data => {
                setButtonLoading(checkInBtn, false);
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                setButtonLoading(checkInBtn, false);
                console.error('Check-in error:', error);
                showNotification('Check-in failed. Please try again.', 'error');
            });
        });
    }

    // Check out
    if (checkOutBtn) {
        checkOutBtn.addEventListener('click', function() {
            setButtonLoading(checkOutBtn, true);

            const requestBody = {};
            if (currentLocation) {
                requestBody.latitude = currentLocation.latitude;
                requestBody.longitude = currentLocation.longitude;
            }

            fetch('{{ route("attendance.check-out") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(requestBody)
            })
            .then(response => response.json())
            .then(data => {
                setButtonLoading(checkOutBtn, false);
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    window.location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                setButtonLoading(checkOutBtn, false);
                console.error('Check-out error:', error);
                showNotification('Check-out failed. Please try again.', 'error');
            });
        });
    }

    // Set button loading state
    function setButtonLoading(button, loading) {
        if (!button) return;
        
        const btnText = button.querySelector('.btn-text');
        const spinner = button.querySelector('.loading-spinner');
        
        if (loading) {
            button.disabled = true;
            btnText.textContent = 'Processing...';
            spinner.classList.remove('hidden');
        } else {
            button.disabled = false;
            btnText.textContent = button.id === 'check-in-btn' ? 'Check In' : 'Check Out';
            spinner.classList.add('hidden');
        }
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        const notificationIcon = document.getElementById('notification-icon');
        
        if (!notification || !notificationMessage || !notificationIcon) return;
        
        notificationMessage.textContent = message;
        
        // Set icon based on type
        let iconHTML = '';
        let iconColor = '';
        
        switch(type) {
            case 'success':
                iconColor = 'text-green-400';
                iconHTML = `<svg class="w-5 h-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>`;
                break;
            case 'error':
                iconColor = 'text-red-400';
                iconHTML = `<svg class="w-5 h-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>`;
                break;
            default:
                iconColor = 'text-blue-400';
                iconHTML = `<svg class="w-5 h-5 ${iconColor}" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>`;
        }
        
        notificationIcon.innerHTML = iconHTML;
        notification.classList.remove('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }

    // Close notification
    const notificationClose = document.getElementById('notification-close');
    if (notificationClose) {
        notificationClose.addEventListener('click', function() {
            const notification = document.getElementById('notification');
            if (notification) notification.classList.add('hidden');
        });
    }
});
</script>
@endpush
@endsection
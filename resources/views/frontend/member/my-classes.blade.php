@extends('frontend.layouts.app')

@section('title', 'My Classes - FitZone')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Classes</h1>
            <p class="text-gray-600 mt-2">Manage your registered fitness classes</p>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <a href="{{ route('member.classes') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="plus" class="h-4 w-4 mr-2"></i>
                Browse More Classes
            </a>
        </div>

        <!-- Classes List -->
        @if($registrations->count() > 0)
            <div class="space-y-6">
                @foreach($registrations as $registration)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <!-- Class Info -->
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $registration->gymClass->class_name }}</h3>
                                            <p class="text-gray-600">{{ $registration->gymClass->trainer->full_name ?? 'TBA' }}</p>
                                        </div>
                                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                                            {{ $registration->status === 'registered' ? 'bg-green-100 text-green-800' : 
                                               ($registration->status === 'attended' ? 'bg-blue-100 text-blue-800' : 
                                               ($registration->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </div>
                                    
                                    <!-- Class Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i data-lucide="calendar" class="h-4 w-4 mr-2 text-blue-600"></i>
                                            {{ $registration->class_date ? $registration->class_date->format('M j, Y') : 'TBA' }}
                                        </div>
                                        <div class="flex items-center">
                                            <i data-lucide="clock" class="h-4 w-4 mr-2 text-blue-600"></i>
                                            {{ $registration->gymClass->start_time }} - {{ $registration->gymClass->end_time }}
                                        </div>
                                        <div class="flex items-center">
                                            <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-blue-600"></i>
                                            {{ $registration->gymClass->room }}
                                        </div>
                                    </div>
                                    
                                    @if($registration->gymClass->description)
                                        <p class="text-gray-600 mt-3 text-sm">{{ Str::limit($registration->gymClass->description, 150) }}</p>
                                    @endif
                                    
                                    <div class="mt-3 text-sm text-gray-500">
                                        Registered on {{ $registration->registration_date->format('M j, Y g:i A') }}
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col sm:flex-row gap-2">
                                    @if($registration->status === 'registered')
                                        @if($registration->class_date && $registration->class_date->isFuture())
                                            <button onclick="cancelClass('{{ $registration->id }}', '{{ $registration->gymClass->class_name }}')" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                                <i data-lucide="x-circle" class="h-4 w-4 inline mr-1"></i>
                                                Cancel
                                            </button>
                                        @else
                                            <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                                Class Started
                                            </span>
                                        @endif
                                    @elseif($registration->status === 'cancelled')
                                        <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg text-sm">
                                            Cancelled
                                        </span>
                                    @elseif($registration->status === 'attended')
                                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm">
                                            <i data-lucide="check-circle" class="h-4 w-4 inline mr-1"></i>
                                            Completed
                                        </span>
                                    @endif
                                    
                                    <button onclick="showClassDetails('{{ $registration->gymClass->id }}')" 
                                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                                        <i data-lucide="info" class="h-4 w-4 inline mr-1"></i>
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $registrations->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i data-lucide="calendar-x" class="h-16 w-16 text-gray-400 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Classes Registered</h3>
                <p class="text-gray-600 mb-6">You haven't registered for any classes yet. Browse our available classes to get started!</p>
                <a href="{{ route('member.classes') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="search" class="h-5 w-5 mr-2"></i>
                    Browse Classes
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    function cancelClass(registrationId, className) {
        if (confirm(`Are you sure you want to cancel your registration for "${className}"?`)) {
            fetch(`/classes/${registrationId}/cancel`, {
                method: 'DELETE',
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
                    alert(data.error || 'Cancellation failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Cancellation failed. Please try again.');
            });
        }
    }

    function showClassDetails(classId) {
        // You can implement a modal or redirect to class details page
        window.open(`/classes/${classId}`, '_blank');
    }
</script>
@endsection

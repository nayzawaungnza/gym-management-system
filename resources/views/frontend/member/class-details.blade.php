@extends('frontend.layouts.app')

@section('title', $class->class_name . ' - GYM')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('member.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('member.classes') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Classes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $class->class_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Class Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="md:flex">
            <!-- Class Image (placeholder - replace with actual image) -->
            <div class="md:w-1/3 bg-gray-200 h-64 md:h-auto flex items-center justify-center">
                <svg class="w-20 h-20 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            
            <!-- Class Info -->
            <div class="p-6 md:w-2/3">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $class->class_name }}</h1>
                
                <div class="flex items-center mb-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded mr-2">{{ $class->difficulty_level }}</span>
                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $class->class_type }}</span>
                </div>
                
                <p class="text-gray-600 mb-4">{{ $class->description }}</p>
                
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($class->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($class->end_time)->format('g:i A') }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ \Carbon\Carbon::parse($class->schedule_day)->format('l') }}s</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>{{ $class->current_capacity }}/{{ $class->max_capacity }} spots filled</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Room: {{ $class->room }}</span>
                    </div>
                </div>
                
                <!-- Trainer Info -->
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0">
                        <svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Instructor</p>
                        <p class="text-lg font-semibold text-gray-800">
                            @if($class->trainer)
                                {{ $class->trainer->user->name }}
                            @else
                                TBA
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Next Class and Enrollment -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Next Class</h3>
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-700">
                                <span class="font-medium">{{ \Carbon\Carbon::parse($nextClassDate)->format('l, F j, Y') }}</span>
                                at {{ \Carbon\Carbon::parse($class->start_time)->format('g:i A') }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $class->available_spots }} spots available
                            </p>
                        </div>
                        @if($alreadyEnrolled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Already Enrolled
                            </span>
                        @else
                            <button onclick="showEnrollModal('{{ $class->id }}', '{{ $class->class_name }}', {{ $class->price }})" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition duration-150 ease-in-out"
                                @if($class->isFull()) disabled @endif>
                                @if($class->isFull())
                                    Class Full
                                @else
                                    Enroll Now
                                @endif
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Details Section -->
    <div class="grid md:grid-cols-3 gap-6 mb-8">
        <!-- Equipment Needed -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Equipment Needed</h3>
            <p class="text-gray-600">
                @if($class->equipment_needed)
                    {{ $class->equipment_needed }}
                @else
                    No special equipment required
                @endif
            </p>
        </div>
        
        <!-- Pricing -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Pricing</h3>
            <div class="flex items-baseline mb-2">
                <span class="text-2xl font-bold text-gray-900">${{ number_format($class->price, 2) }}</span>
                <span class="ml-1 text-gray-500">per session</span>
            </div>
            <p class="text-gray-600 text-sm">
                Payment will be processed upon enrollment.
            </p>
        </div>
        
        <!-- Class Policies -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Class Policies</h3>
            <ul class="list-disc pl-5 text-gray-600 space-y-2">
                <li>Cancel at least 24 hours before class to avoid charges</li>
                <li>Arrive 10 minutes early to set up</li>
                <li>Bring your membership card for check-in</li>
                <li>No refunds for no-shows</li>
            </ul>
        </div>
    </div>

    <!-- Related Classes -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">You Might Also Like</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($relatedClasses as $relatedClass)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-gray-200 h-40 flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $relatedClass->class_name }}</h3>
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($relatedClass->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($relatedClass->end_time)->format('g:i A') }}
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-blue-600">${{ number_format($relatedClass->price, 2) }}</span>
                            <a href="{{ route('member.classes.details', $relatedClass) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Enrollment Modal -->
<div id="enrollModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- This element is to trick the browser into centering the modal contents -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg shadow-xl text-left overflow-hidden transform sm:my-8 sm:align-middle sm:max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Enroll in Class</h3>
                    <button onclick="closeEnrollModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-600">You are enrolling in:</p>
                    <p class="font-semibold text-gray-900" id="modalClassName"></p>
                    <p class="text-blue-600 font-bold text-lg" id="modalClassPrice"></p>
                    <p class="text-sm text-gray-500 mt-1">Class Date: {{ \Carbon\Carbon::parse($nextClassDate)->format('l, F j, Y') }}</p>
                </div>

                <div class="mb-6 max-h-[60vh] overflow-y-auto">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 sm:grid-cols-2 gap-2">
                        @foreach($paymentMethods as $paymentMethod)
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="{{ $paymentMethod->id }}" class="mr-3 text-blue-600 focus:ring-blue-500">
                                <div class="flex items-center">
                                    @if($paymentMethod->payment_logo)
                                        <img src="{{ url($paymentMethod->payment_logo) }}" alt="{{ $paymentMethod->display_name }}" class="h-6 w-6 mr-3">
                                    @else
                                        <div class="h-6 w-6 bg-gray-300 rounded mr-3 flex items-center justify-center">
                                            <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $paymentMethod->display_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $paymentMethod->method_name }}</p>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex space-x-3">
                    <button onclick="closeEnrollModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmEnrollment()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Confirm Enrollment
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentClassId = null;
let originalBodyOverflow = '';

function showEnrollModal(classId, className, price) {
    currentClassId = classId;
    document.getElementById('modalClassName').textContent = className;
    document.getElementById('modalClassPrice').textContent = price > 0 ? '$' + price : 'Free';

    // Store original body overflow value and disable scrolling
    originalBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    document.getElementById('enrollModal').classList.remove('hidden');
    
    // Clear any previously selected payment method
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.checked = false;
    });
}

function closeEnrollModal() {
    document.getElementById('enrollModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow;
    currentClassId = null;
}

function confirmEnrollment() {
    const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
    
    if (!selectedPaymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    const paymentMethodId = selectedPaymentMethod.value;

    fetch('/classes/' + currentClassId + '/enroll', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            payment_method_id: paymentMethodId,
            class_date: '{{ $nextClassDate->format('Y-m-d') }}'
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.error || 'Enrollment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.error || 'Enrollment failed. Please try again.');
    })
    .finally(() => {
        closeEnrollModal();
    });
}

// Close modal when clicking outside
document.getElementById('enrollModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEnrollModal();
    }
});
</script>
@endsection
@extends('frontend.layouts.app')

@section('title', 'Available Classes - FitZone')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Available Classes</h1>
            <p class="text-gray-600 mt-2">Find and enroll in fitness classes that match your goals</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class Type</label>
                    <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="cardio" {{ request('type') === 'cardio' ? 'selected' : '' }}>Cardio</option>
                        <option value="strength" {{ request('type') === 'strength' ? 'selected' : '' }}>Strength</option>
                        <option value="flexibility" {{ request('type') === 'flexibility' ? 'selected' : '' }}>Flexibility</option>
                        <option value="dance" {{ request('type') === 'dance' ? 'selected' : '' }}>Dance</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                    <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('difficulty') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('difficulty') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('difficulty') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="search" class="h-4 w-4 inline mr-2"></i>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Class Header -->
                    <div class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600">
                        <div class="absolute inset-0 bg-black/20"></div>
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full 
                                {{ $class->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : 
                                   ($class->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($class->difficulty_level) }}
                            </span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/90 text-gray-800 text-xs font-medium rounded-full">
                                ${{ number_format($class->price, 0) }}
                            </span>
                        </div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <h3 class="text-xl font-bold mb-1">{{ $class->class_name }}</h3>
                            <p class="text-sm opacity-90">{{ $class->trainer->full_name ?? 'TBA' }}</p>
                        </div>
                    </div>

                    <!-- Class Details -->
                    <div class="p-6">
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                            {{ Str::limit($class->description, 100) }}
                        </p>
                        
                        <!-- Class Info -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <i data-lucide="calendar" class="h-4 w-4 mr-2 text-blue-600"></i>
                                {{ ucfirst($class->schedule_day) }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i data-lucide="clock" class="h-4 w-4 mr-2 text-blue-600"></i>
                                {{ $class->start_time }} - {{ $class->end_time }} ({{ $class->duration_minutes }}min)
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i data-lucide="map-pin" class="h-4 w-4 mr-2 text-blue-600"></i>
                                {{ $class->room }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i data-lucide="users" class="h-4 w-4 mr-2 text-blue-600"></i>
                                {{ $class->current_capacity }}/{{ $class->max_capacity }} enrolled
                            </div>
                        </div>

                        <!-- Capacity Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Capacity</span>
                                <span>{{ $class->available_spots }} spots left</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ ($class->current_capacity / $class->max_capacity) * 100 }}%"></div>
                            </div>
                        </div>

                        <!-- Enrollment Button -->
                        @if(in_array($class->id, $registeredClassIds))
                            <button class="w-full bg-green-100 text-green-800 py-3 px-4 rounded-lg font-medium cursor-not-allowed" disabled>
                                <i data-lucide="check-circle" class="h-4 w-4 inline mr-2"></i>
                                Enrolled
                            </button>
                        @elseif($class->isFull())
                            <button class="w-full bg-gray-100 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed" disabled>
                                <i data-lucide="x-circle" class="h-4 w-4 inline mr-2"></i>
                                Class Full
                            </button>
                        @else
                            <button onclick="showEnrollModal('{{ $class->id }}', '{{ $class->class_name }}', {{ $class->price }})" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                                <i data-lucide="plus-circle" class="h-4 w-4 inline mr-2"></i>
                                Enroll Now
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $classes->links() }}
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
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-gray-600">You are enrolling in:</p>
                    <p class="font-semibold text-gray-900" id="modalClassName"></p>
                    <p class="text-blue-600 font-bold text-lg" id="modalClassPrice"></p>
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
                                            <i data-lucide="credit-card" class="h-4 w-4 text-gray-600"></i>
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
            payment_method_id: paymentMethodId
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
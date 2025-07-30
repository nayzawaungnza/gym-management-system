<section id="classes" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Fitness Classes</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Join our diverse range of group fitness classes led by certified instructors.
            </p>
        </div>

        <!-- Class Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-12">
                <form id="class-filters" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="cardio">Cardio</option>
                            <option value="strength">Strength</option>
                            <option value="flexibility">Flexibility</option>
                            <option value="dance">Dance</option>
                        </select>
                    </div>
                    
                    <div class="flex-1 min-w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                        <select name="difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="classes-grid">
            @if(isset($classes) && $classes->count() > 0)
                @foreach($classes as $class)
                    <div class="class-card bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-category="{{ strtolower($class->class_type) }}">
                        <!-- Class Image -->
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600">
                            <div class="absolute inset-0 bg-black/20"></div>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full 
                                    {{ $class->difficulty_level === 'Beginner' ? 'bg-green-100 text-green-800' : 
                                       ($class->difficulty_level === 'Intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $class->difficulty_level }}
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
                            @auth
                                @if(Auth::user()->member)
                                    @php
                                        $isEnrolled = Auth::user()->member->classRegistrations()
                                            ->where('class_id', $class->id)
                                            ->where('status', 'Registered')
                                            ->exists();
                                    @endphp
                                    
                                    @if($isEnrolled)
                                        <button type="button" class="w-full bg-green-100 text-green-800 py-3 px-4 rounded-lg font-medium cursor-not-allowed" disabled>
                                            <i data-lucide="check-circle" class="h-4 w-4 inline mr-2"></i>
                                            Enrolled
                                        </button>
                                    @elseif($class->isFull())
                                        <button type="button" class="w-full bg-gray-100 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed" disabled>
                                            <i data-lucide="x-circle" class="h-4 w-4 inline mr-2"></i>
                                            Class Full
                                        </button>
                                    @else
                                        <button type="button" 
                                                data-class-id="{{ $class->id }}" 
                                                data-class-name="{{ $class->class_name }}" 
                                                data-class-price="{{ $class->price }}"
                                                class="enroll-now-btn w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200">
                                            <i data-lucide="plus-circle" class="h-4 w-4 inline mr-2"></i>
                                            Enroll Now
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('member.profile.create') }}" class="block w-full bg-orange-600 text-white py-3 px-4 rounded-lg font-medium text-center hover:bg-orange-700 transition-colors duration-200">
                                        <i data-lucide="user-plus" class="h-4 w-4 inline mr-2"></i>
                                        Complete Profile to Enroll
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium text-center hover:bg-blue-700 transition-colors duration-200">
                                    <i data-lucide="log-in" class="h-4 w-4 inline mr-2"></i>
                                    Login to Enroll
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Default Classes when no database data -->

                <div class="col-span-full text-center py-8">
                    <p class="text-gray-600 text-lg mb-4">No classes found in the database. Displaying example classes.</p>
                    <p class="text-sm text-gray-500">These example classes cannot be enrolled in directly. Please add classes via the admin panel.</p>
                </div>

                
            @endif
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
                        <button type="button" class="close-modal-btn text-gray-400 hover:text-gray-600">
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
                            @foreach($paymentMethods ?? [] as $paymentMethod)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="payment_method" value="{{ $paymentMethod->id ?? $paymentMethod['id'] }}" class="mr-3 text-blue-600 focus:ring-blue-500">
                                    <div class="flex items-center">
                                        @if(isset($paymentMethod->payment_logo) && $paymentMethod->payment_logo)
                                            <img src="{{ url($paymentMethod->payment_logo) }}" alt="{{ $paymentMethod->display_name }}" class="h-6 w-6 mr-3">
                                        @else
                                            <div class="h-6 w-6 bg-gray-300 rounded mr-3 flex items-center justify-center">
                                                <i data-lucide="credit-card" class="h-4 w-4 text-gray-600"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $paymentMethod->display_name ?? $paymentMethod['display_name'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $paymentMethod->method_name ?? $paymentMethod['method_name'] }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" class="close-modal-btn flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="confirmEnrollmentBtn" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Confirm Enrollment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
let currentClassId = null;
let originalBodyOverflow1 = '';

function showEnrollModal(classId, className, price) {
    currentClassId = classId;
    document.getElementById('modalClassName').textContent = className;
    document.getElementById('modalClassPrice').textContent = price > 0 ? '$' + price : 'Free';

    originalBodyOverflow1 = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    document.getElementById('enrollModal').classList.remove('hidden');
    
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.checked = false;
    });
}

function closeEnrollModal() {
    document.getElementById('enrollModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow1;
    currentClassId = null;
}

document.addEventListener('DOMContentLoaded', function() {
    // Attach event listeners to "Enroll Now" buttons for actual classes
    document.querySelectorAll('.enroll-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const classId = this.dataset.classId;
            const className = this.dataset.className;
            const classPrice = parseFloat(this.dataset.classPrice);
            showEnrollModal(classId, className, classPrice);
        });
    });

    // Attach event listeners to close modal buttons
    document.querySelectorAll('.close-modal-btn').forEach(button => {
        button.addEventListener('click', closeEnrollModal);
    });

    // Close modal when clicking outside
    document.getElementById('enrollModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEnrollModal();
        }
    });

    // Confirm Enrollment button click handler
    document.getElementById('confirmEnrollmentBtn').addEventListener('click', function() {
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
    });

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Filter form logic
    const filterForm = document.getElementById('class-filters');
    const classCards = document.querySelectorAll('.class-card');
    
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            filterClasses();
        });
        
        filterForm.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', filterClasses);
        });
    }
    
    function filterClasses() {
        const typeFilter = filterForm.querySelector('[name="type"]').value.toLowerCase();
        const difficultyFilter = filterForm.querySelector('[name="difficulty"]').value.toLowerCase();
        
        classCards.forEach(card => {
            const cardType = card.getAttribute('data-category');
            // For default classes, difficulty is in the span, for actual classes it's in the model
            const cardDifficultyElement = card.querySelector('.absolute.top-4.left-4 span');
            const cardDifficulty = cardDifficultyElement ? cardDifficultyElement.textContent.toLowerCase() : '';
            
            const typeMatch = !typeFilter || cardType.includes(typeFilter);
            const difficultyMatch = !difficultyFilter || cardDifficulty.includes(difficultyFilter);
            
            if (typeMatch && difficultyMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const typeParam = urlParams.get('type');
    const difficultyParam = urlParams.get('difficulty');
    
    if (typeParam) {
        filterForm.querySelector('[name="type"]').value = typeParam;
    }
    if (difficultyParam) {
        filterForm.querySelector('[name="difficulty"]').value = difficultyParam;
    }
    
    if (typeParam || difficultyParam) {
        filterClasses();
    }
});
</script>
@endpush

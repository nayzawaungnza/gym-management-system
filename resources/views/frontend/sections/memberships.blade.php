<section id="membership" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Choose Your Plan</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Select the membership that fits your lifestyle and fitness goals.
            </p>
        </div>

        @if(isset($membershipTypes) && $membershipTypes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach($membershipTypes as $membershipType)
                    @php
                        $currentSubscription = null;
                        $hasActiveSubscription = false;
                        $hasCancelledSubscription = false;
                        $isExpiringSoon = false;
                        
                        if(Auth::check() && Auth::user()->member) {
                            $currentSubscription = Auth::user()->member->subscriptions()
                                ->where('membership_type_id', $membershipType->id)
                                ->where('status', 'active')
                                ->where('end_date', '>=', now())
                                ->first();
                                
                            $hasActiveSubscription = (bool)$currentSubscription;
                            
                            if ($currentSubscription) {
                                $isExpiringSoon = $currentSubscription->isExpiringSoon();
                            }
                            
                            $hasCancelledSubscription = Auth::user()->member->subscriptions()
                                ->where('membership_type_id', $membershipType->id)
                                ->where('status', 'cancelled')
                                ->exists();
                        }
                        
                        $isPopular = $membershipType->type_name === 'Premium'; // Adjust as needed
                    @endphp

                    <div class="relative bg-white rounded-lg shadow-md {{ $isPopular ? 'border-2 border-blue-600 transform scale-105' : 'border border-gray-200' }}">
                        @if($isPopular)
                            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">Most Popular</span>
                            </div>
                        @endif
                        
                        @if($hasActiveSubscription)
                            <div class="absolute -top-3 right-4">
                                <span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm font-medium">Active</span>
                            </div>
                        @endif
                        
                        <div class="p-6 text-center">
                            <h3 class="text-2xl font-bold mb-2">{{ $membershipType->type_name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $membershipType->description }}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold">${{ number_format($membershipType->price, 0) }}</span>
                                <span class="text-gray-600">/{{ $membershipType->duration_months }} month{{ $membershipType->duration_months > 1 ? 's' : '' }}</span>
                            </div>
                            
                            @if($hasActiveSubscription)
                                <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-800">
                                        <i data-lucide="calendar" class="h-4 w-4 inline mr-1"></i>
                                        Active until {{ $currentSubscription->end_date->format('M d, Y') }}
                                    </p>
                                    @if($isExpiringSoon)
                                        <p class="text-sm text-orange-600 mt-1">
                                            <i data-lucide="alert-triangle" class="h-4 w-4 inline mr-1"></i>
                                            Expires soon!
                                        </p>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Action Buttons -->
                            @auth
                                @if(Auth::user()->member)
                                    @if($hasActiveSubscription)
                                        <div class="space-y-2">
                                            @if($isExpiringSoon)
                                                <button onclick="showRenewalModal('{{ $currentSubscription->id }}', '{{ $membershipType->type_name }}', {{ $membershipType->price }})" 
                                                        class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 transition-colors">
                                                    <i data-lucide="refresh-cw" class="h-4 w-4 inline mr-2"></i>
                                                    Renew Now
                                                </button>
                                            @endif
                                            <button onclick="showCancellationModal('{{ $currentSubscription->id }}')" 
                                                    class="w-full px-6 py-3 bg-red-100 text-red-800 font-medium rounded-md hover:bg-red-200 transition-colors">
                                                <i data-lucide="x-circle" class="h-4 w-4 inline mr-2"></i>
                                                Cancel Membership
                                            </button>
                                        </div>
                                    @else
                                        <button onclick="showMembershipModal('{{ $membershipType->id }}', '{{ $membershipType->type_name }}', {{ $membershipType->price }}, {{ $hasCancelledSubscription ? 'true' : 'false' }})" 
                                                class="w-full px-6 py-3 {{ $isPopular ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} font-medium rounded-md hover:{{ $isPopular ? 'bg-blue-700' : 'bg-gray-200' }} transition-colors">
                                            <i data-lucide="plus-circle" class="h-4 w-4 inline mr-2"></i>
                                            {{ $hasCancelledSubscription ? 'Rejoin Now' : 'Enroll Now' }}
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('member.profile.create') }}" class="w-full inline-block px-6 py-3 bg-orange-600 text-white text-center font-medium rounded-md hover:bg-orange-700 transition-colors">
                                        <i data-lucide="user-plus" class="h-4 w-4 inline mr-2"></i>
                                        Complete Profile to Enroll
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="w-full inline-block px-6 py-3 {{ $isPopular ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} text-center font-medium rounded-md hover:{{ $isPopular ? 'bg-blue-700' : 'bg-gray-200' }} transition-colors">
                                    <i data-lucide="log-in" class="h-4 w-4 inline mr-2"></i>
                                    Get Started
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="max-w-2xl mx-auto bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                <i data-lucide="alert-triangle" class="h-12 w-12 text-yellow-600 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-yellow-800 mb-2">Membership Plans Coming Soon</h3>
                <p class="text-yellow-700 mb-4">We're currently updating our membership plans. Please contact us directly for membership information.</p>
                <a href="#contact" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i data-lucide="phone" class="h-4 w-4 mr-2"></i>
                    Contact Us
                </a>
            </div>
        @endif
    </div>

    <!-- Membership Enrollment Modal -->
    <div id="membershipModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg shadow-xl text-left overflow-hidden transform sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Enroll in Membership</h3>
                        <button onclick="closeMembershipModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="h-6 w-6"></i>
                        </button>
                    </div>
                    
                    <div id="reactivationMessage" class="hidden bg-blue-50 text-blue-800 p-3 rounded-md mb-4 text-sm">
                        <i data-lucide="info" class="h-4 w-4 mr-2 inline"></i>
                        <span>Welcome back! You're reactivating your membership.</span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-600">You are enrolling in:</p>
                        <p class="font-semibold text-gray-900" id="modalMembershipName"></p>
                        <p class="text-blue-600 font-bold text-lg" id="modalMembershipPrice"></p>
                    </div>

                    <div class="mb-6 max-h-[60vh] overflow-y-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @foreach($paymentMethods ?? [] as $paymentMethod)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="membership_payment_method" value="{{ $paymentMethod->id }}" class="mr-3 text-blue-600 focus:ring-blue-500">
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
                        <button onclick="closeMembershipModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmMembership()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Confirm Membership
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Renewal Modal -->
    <div id="renewalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg shadow-xl text-left overflow-hidden transform sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Renew Membership</h3>
                        <button onclick="closeRenewalModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="h-6 w-6"></i>
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-gray-600">You are renewing:</p>
                        <p class="font-semibold text-gray-900" id="renewalMembershipName"></p>
                        <p class="text-blue-600 font-bold text-lg" id="renewalMembershipPrice"></p>
                    </div>

                    <div class="mb-6 max-h-[60vh] overflow-y-auto">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method <span class="text-red-500">*</span></label>
                        <div class="space-y-2">
                            @foreach($paymentMethods ?? [] as $paymentMethod)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="radio" name="renewal_payment_method" value="{{ $paymentMethod->id }}" class="mr-3 text-blue-600 focus:ring-blue-500">
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
                        <button onclick="closeRenewalModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button onclick="confirmRenewal()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Confirm Renewal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <div id="cancellationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-lg shadow-xl text-left overflow-hidden transform sm:my-8 sm:align-middle sm:max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Cancel Membership</h3>
                        <button onclick="closeCancellationModal()" class="text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="h-6 w-6"></i>
                        </button>
                    </div>
                    
                    <div class="mb-6">
                        <p class="text-gray-600 mb-4">Are you sure you want to cancel your membership? This action cannot be undone.</p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for cancellation <span class="text-red-500">*</span>
                        </label>
                        <textarea id="cancellationReason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Please let us know why you're cancelling..."></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button onclick="closeCancellationModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Go Back
                        </button>
                        <button onclick="confirmCancellation()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Confirm Cancellation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
let currentMembershipId = null;
let currentSubscriptionId = null;
let originalBodyOverflow = '';
let isReactivation = false;

function showMembershipModal(membershipId, membershipName, price, isReactivate) {
    currentMembershipId = membershipId;
    isReactivation = isReactivate;
    
    document.getElementById('modalTitle').textContent = isReactivate ? 'Reactivate Membership' : 'Enroll in Membership';
    document.getElementById('modalMembershipName').textContent = membershipName;
    document.getElementById('modalMembershipPrice').textContent = '$' + price;

    // Show/hide reactivation message
    const reactivationMsg = document.getElementById('reactivationMessage');
    reactivationMsg.classList.toggle('hidden', !isReactivate);

    // Store original body overflow value and disable scrolling
    originalBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    document.getElementById('membershipModal').classList.remove('hidden');
    
    // Clear any previously selected payment method
    document.querySelectorAll('input[name="membership_payment_method"]').forEach(input => {
        input.checked = false;
    });
}

function closeMembershipModal() {
    document.getElementById('membershipModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow;
    currentMembershipId = null;
    isReactivation = false;
}

function confirmMembership() {
    const selectedPaymentMethod = document.querySelector('input[name="membership_payment_method"]:checked');
    
    if (!selectedPaymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    const paymentMethodId = selectedPaymentMethod.value;

    fetch('/memberships/' + currentMembershipId + '/enroll', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            payment_method_id: paymentMethodId,
            is_reactivation: isReactivation
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
            alert(data.error || 'Membership enrollment failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.error || 'Membership enrollment failed. Please try again.');
    })
    .finally(() => {
        closeMembershipModal();
    });
}

function showRenewalModal(subscriptionId, membershipName, price) {
    currentSubscriptionId = subscriptionId;
    
    document.getElementById('renewalMembershipName').textContent = membershipName;
    document.getElementById('renewalMembershipPrice').textContent = '$' + price;

    originalBodyOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    document.getElementById('renewalModal').classList.remove('hidden');
    
    // Clear any previously selected payment method
    document.querySelectorAll('input[name="renewal_payment_method"]').forEach(input => {
        input.checked = false;
    });
}

function closeRenewalModal() {
    document.getElementById('renewalModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow;
    currentSubscriptionId = null;
}

function confirmRenewal() {
    const selectedPaymentMethod = document.querySelector('input[name="renewal_payment_method"]:checked');
    
    if (!selectedPaymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    const paymentMethodId = selectedPaymentMethod.value;

    fetch('/memberships/' + currentSubscriptionId + '/renew', {
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
            alert(data.error || 'Membership renewal failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.error || 'Membership renewal failed. Please try again.');
    })
    .finally(() => {
        closeRenewalModal();
    });
}

function showCancellationModal(subscriptionId) {
    currentSubscriptionId = subscriptionId;
    document.getElementById('cancellationModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    document.getElementById('cancellationReason').value = '';
}

function closeCancellationModal() {
    document.getElementById('cancellationModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow;
    currentSubscriptionId = null;
}

function confirmCancellation() {
    const reason = document.getElementById('cancellationReason').value.trim();
    
    if (!reason) {
        alert('Please provide a cancellation reason.');
        return;
    }

    fetch('/memberships/' + currentSubscriptionId + '/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            cancellation_reason: reason
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
            alert(data.error || 'Cancellation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.error || 'Cancellation failed. Please try again.');
    })
    .finally(() => {
        closeCancellationModal();
    });
}

// Close modals when clicking outside
document.getElementById('membershipModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMembershipModal();
    }
});

document.getElementById('renewalModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRenewalModal();
    }
});

document.getElementById('cancellationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancellationModal();
    }
});

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

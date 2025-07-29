@extends('frontend.layouts.app')

@section('title', 'Member Subscription - GYM')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Membership</h1>
        <p class="text-gray-600">Manage your gym membership and view subscription details</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="h-5 w-5 mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="h-5 w-5 mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Current Membership Status -->
        <div class="lg:col-span-2">
            @if($activeSubscription)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Current Membership</h2>
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                {{ $activeSubscription->isExpiringSoon() ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                {{ $activeSubscription->isExpiringSoon() ? 'Expiring Soon' : 'Active' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-bold text-blue-600 mb-2">
                                    {{ $activeSubscription->membershipType->type_name }}
                                </h3>
                                <p class="text-gray-600 mb-4">{{ $activeSubscription->membershipType->description }}</p>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center">
                                        <i data-lucide="calendar" class="h-4 w-4 text-gray-500 mr-2"></i>
                                        <span class="text-gray-700">Started: {{ $activeSubscription->start_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="calendar-x" class="h-4 w-4 text-gray-500 mr-2"></i>
                                        <span class="text-gray-700">Expires: {{ $activeSubscription->end_date->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="dollar-sign" class="h-4 w-4 text-gray-500 mr-2"></i>
                                        <span class="text-gray-700">Amount Paid: ${{ number_format($activeSubscription->amount_paid, 2) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="refresh-cw" class="h-4 w-4 text-gray-500 mr-2"></i>
                                        <span class="text-gray-700">Auto-Renew: {{ $activeSubscription->auto_renew ? 'Enabled' : 'Disabled' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <!-- Days Remaining -->
                                @php
                                    $daysRemaining = now()->diffInDays($activeSubscription->end_date, false);
                                @endphp
                                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold {{ $daysRemaining <= 7 ? 'text-red-600' : ($daysRemaining <= 30 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ $daysRemaining > 0 ? $daysRemaining : 0 }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $daysRemaining > 1 ? 'days remaining' : ($daysRemaining == 1 ? 'day remaining' : 'expired') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                @php
                                    $totalDays = $activeSubscription->start_date->diffInDays($activeSubscription->end_date);
                                    $usedDays = $activeSubscription->start_date->diffInDays(now());
                                    $progress = $totalDays > 0 ? min(($usedDays / $totalDays) * 100, 100) : 100;
                                @endphp
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>Membership Progress</span>
                                        <span>{{ number_format($progress, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-y-2">
                                    @if($activeSubscription->isExpiringSoon() || $daysRemaining <= 0)
                                        <button onclick="showRenewalModal('{{ $activeSubscription->id }}', '{{ $activeSubscription->membershipType->type_name }}', {{ $activeSubscription->membershipType->price }})" 
                                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i data-lucide="refresh-cw" class="h-4 w-4 inline mr-2"></i>
                                            Renew Membership
                                        </button>
                                    @endif
                                    
                                    <button onclick="showCancellationModal('{{ $activeSubscription->id }}')" 
                                            class="w-full px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-colors">
                                        <i data-lucide="x-circle" class="h-4 w-4 inline mr-2"></i>
                                        Cancel Membership
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- No Active Membership -->
                <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-8">
                    <div class="p-6 text-center">
                        <div class="mb-4">
                            <i data-lucide="user-x" class="h-16 w-16 text-gray-400 mx-auto mb-4"></i>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">No Active Membership</h2>
                            <p class="text-gray-600 mb-6">You don't have an active membership. Choose a plan to get started!</p>
                        </div>
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i data-lucide="plus-circle" class="h-4 w-4 mr-2"></i>
                            Browse Membership Plans
                        </a>
                    </div>
                </div>
            @endif

            <!-- Subscription History -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription History</h2>
                    
                    @if($subscriptions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Membership Type</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Period</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Amount</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                        <th class="text-left py-3 px-4 font-medium text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $subscription)
                                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                                            <td class="py-3 px-4">
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $subscription->membershipType->type_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $subscription->membershipType->duration_months }} month{{ $subscription->membershipType->duration_months > 1 ? 's' : '' }}</div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="text-sm">
                                                    <div>{{ $subscription->start_date->format('M d, Y') }}</div>
                                                    <div class="text-gray-500">to {{ $subscription->end_date->format('M d, Y') }}</div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="font-medium">${{ number_format($subscription->amount_paid, 2) }}</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                       ($subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                        'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <button onclick="showSubscriptionDetails('{{ $subscription->id }}')" 
                                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <i data-lucide="eye" class="h-4 w-4 inline mr-1"></i>
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($subscriptions->hasPages())
                            <div class="mt-4">
                                {{ $subscriptions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="file-text" class="h-12 w-12 text-gray-400 mx-auto mb-4"></i>
                            <p class="text-gray-500">No subscription history found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Subscriptions</span>
                            <span class="font-semibold">{{ $subscriptions->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Spent</span>
                            <span class="font-semibold">${{ number_format($totalSpent, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-semibold">{{ $member->join_date ? $member->join_date->format('M Y') : 'N/A' }}</span>
                        </div>
                        @if($activeSubscription)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Current Plan</span>
                                <span class="font-semibold">{{ $activeSubscription->membershipType->type_name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h3>
                    
                    @if($recentPayments->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentPayments as $payment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-medium text-sm">{{ $payment->description }}</div>
                                        <div class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-sm">${{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-xs">
                                            <span class="px-2 py-1 rounded-full
                                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                    'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- <div class="mt-4">
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                                View All Payments →
                            </a>
                        </div> --}}
                    @else
                        <div class="text-center py-4">
                            <i data-lucide="credit-card" class="h-8 w-8 text-gray-400 mx-auto mb-2"></i>
                            <p class="text-gray-500 text-sm">No payments found.</p>
                        </div>
                    @endif
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

                <div class="mb-6">
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

<!-- Subscription Details Modal -->
<div id="subscriptionDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg shadow-xl text-left overflow-hidden transform sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Subscription Details</h3>
                    <button onclick="closeSubscriptionDetailsModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="h-6 w-6"></i>
                    </button>
                </div>
                
                <div id="subscriptionDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSubscriptionId = null;
let originalBodyOverflow = '';

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

function showSubscriptionDetails(subscriptionId) {
    fetch('/subscriptions/' + subscriptionId + '/details', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('subscriptionDetailsContent').innerHTML = data.html;
            document.getElementById('subscriptionDetailsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            alert('Failed to load subscription details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load subscription details');
    });
}

function closeSubscriptionDetailsModal() {
    document.getElementById('subscriptionDetailsModal').classList.add('hidden');
    document.body.style.overflow = originalBodyOverflow;
}

// Close modals when clicking outside
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

document.getElementById('subscriptionDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSubscriptionDetailsModal();
    }
});

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection

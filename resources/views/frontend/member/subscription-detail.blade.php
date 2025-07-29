<div class="space-y-4">
    <!-- Subscription Info -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="font-semibold text-gray-900 mb-3">{{ $subscription->membershipType->type_name }}</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Start Date:</span>
                <span class="font-medium">{{ $subscription->start_date->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-600">End Date:</span>
                <span class="font-medium">{{ $subscription->end_date->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-600">Amount Paid:</span>
                <span class="font-medium">${{ number_format($subscription->amount_paid, 2) }}</span>
            </div>
            <div>
                <span class="text-gray-600">Status:</span>
                <span class="px-2 py-1 text-xs font-medium rounded-full
                    {{ $subscription->status === 'active' ? 'bg-green-100 text-green-800' : 
                       ($subscription->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                        'bg-gray-100 text-gray-800') }}">
                    {{ ucfirst($subscription->status) }}
                </span>
            </div>
        </div>
        
        @if($subscription->status === 'cancelled' && $subscription->cancellation_reason)
            <div class="mt-3 pt-3 border-t border-gray-200">
                <span class="text-gray-600 text-sm">Cancellation Reason:</span>
                <p class="text-sm text-gray-800 mt-1">{{ $subscription->cancellation_reason }}</p>
                @if($subscription->cancelled_at)
                    <p class="text-xs text-gray-500 mt-1">Cancelled on {{ $subscription->cancelled_at->format('M d, Y') }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Related Payments -->
    @if($payments->count() > 0)
        <div>
            <h4 class="font-semibold text-gray-900 mb-3">Related Payments</h4>
            <div class="space-y-2">
                @foreach($payments as $payment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-medium text-sm">{{ $payment->description }}</div>
                            <div class="text-xs text-gray-500">
                                {{ $payment->payment_date->format('M d, Y') }} • 
                                Receipt: {{ $payment->receipt_number }}
                            </div>
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
        </div>
    @endif
</div>
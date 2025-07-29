<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\MemberSubscription;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MemberSubscriptionController extends Controller
{
    public function index()
    {
        $member = Auth::user()->member;
        
        if (!$member) {
            return redirect()->route('member.profile.create')
                ->with('error', 'Please complete your member profile first.');
        }

        // Get active subscription
        $activeSubscription = $member->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->with('membershipType')
            ->first();

        // Get all subscriptions with pagination
        $subscriptions = $member->subscriptions()
            ->with('membershipType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get recent payments
        $recentPayments = $member->payments()
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        // Calculate total spent
        $totalSpent = $member->payments()
            ->where('status', 'completed')
            ->sum('amount');

        // Get payment methods for modals
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('frontend.member.my-membersubscription', compact(
            'member',
            'activeSubscription',
            'subscriptions',
            'recentPayments',
            'totalSpent',
            'paymentMethods'
        ));
    }

    public function subscriptionDetails(Request $request, MemberSubscription $subscription)
    {
        // Verify the subscription belongs to the current user
        if ($subscription->member_id !== Auth::user()->member->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized access'
            ], 403);
        }

        // Get related payments
        $payments = $subscription->payments()->orderBy('payment_date', 'desc')->get();

        $html = view('frontend.member.subscription-detail', compact('subscription', 'payments'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}
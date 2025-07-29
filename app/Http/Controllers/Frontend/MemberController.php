<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Payment;
use App\Models\GymClass;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\MembershipType;
use App\Models\ClassRegistration;
use App\Models\MemberSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Member Dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();
        //dd($member);
        if (!$member) {
            return redirect()->route('member.profile.create')
                ->with('info', 'Please complete your member profile first.');
        }

        // Get dashboard stats
        $stats = [
            'total_classes' => $member->classRegistrations()->count(),
            'upcoming_classes' => $member->classRegistrations()
                ->whereHas('gymClass', function($q) {
                    $q->where('schedule_day', '>=', now()->format('l'));
                })
                ->where('status', 'registered')
                ->count(),
            'total_attendance' => $member->attendances()->count(),
            'this_month_attendance' => $member->attendances()
                ->whereMonth('check_in_time', now()->month)
                ->count(),
        ];

        // Get recent activities
        $recentClasses = $member->classRegistrations()
            ->with(['gymClass', 'gymClass.trainer'])
            ->latest()
            ->limit(5)
            ->get();

        $recentAttendance = $member->attendances()
            ->latest('check_in_time')
            ->limit(5)
            ->get();

        // Check if currently checked in
        $currentCheckIn = $member->attendances()
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', today()) // Only today's sessions
            ->latest()
            ->first();

        return view('frontend.member.dashboard', compact(
            'member', 'stats', 'recentClasses', 'recentAttendance', 'currentCheckIn'
        ));
    }

    /**
     * Show member profile
     */
    public function profile()
    {
        $user = Auth::user();
        $member = $user->member;
        $membershipTypes = MembershipType::where('is_active', true)->get();

        return view('frontend.member.profile', compact('member', 'membershipTypes'));
    }

    /**
     * Show create profile form
     */
    public function createProfile()
    {
        $user = Auth::user();
        
        if ($user->member) {
            return redirect()->route('member.dashboard');
        }

        $membershipTypes = MembershipType::where('is_active', true)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        // Check if membership types are available
        if ($membershipTypes->isEmpty()) {
            return redirect()->route('home')
                ->with('error', 'No membership plans are currently available. Please contact us for assistance.');
        }

        // Check if payment methods are available
        if ($paymentMethods->isEmpty()) {
            return redirect()->route('home')
                ->with('error', 'Payment system is currently unavailable. Please contact us for assistance.');
        }
        
        return view('frontend.member.create-profile', compact('membershipTypes', 'paymentMethods'));
    }

    /**
     * Store/Update member profile
     */
    public function storeProfile(Request $request)
    {
        // Check if membership types are available
        $membershipTypes = MembershipType::where('is_active', true)->get();
        if ($membershipTypes->isEmpty()) {
            return back()->with('error', 'No membership plans are currently available.');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'membership_type_id' => 'required|exists:membership_types,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'medical_conditions' => 'nullable|array',
            'fitness_goals' => 'nullable|array',
            'preferred_workout_time' => 'nullable|string|max:50',
            'referral_source' => 'nullable|string|max:100',
        ], [
            'membership_type_id.required' => 'Please select a membership plan.',
            'membership_type_id.exists' => 'The selected membership plan is invalid.',
            'payment_method_id.required' => 'Please select a payment method.',
            'payment_method_id.exists' => 'The selected payment method is invalid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $membershipType = MembershipType::findOrFail($request->membership_type_id);
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Ensure the selected membership type is active
        if (!$membershipType->is_active) {
            return back()->with('error', 'The selected membership plan is no longer available.');
        }

        // Ensure the selected payment method is active
        if (!$paymentMethod->is_active) {
            return back()->with('error', 'The selected payment method is no longer available.');
        }

        DB::beginTransaction();
        try {
            // Check if member already exists
            $existingMember = $user->member;
            
            if ($existingMember) {
                DB::rollback();
                return redirect()->route('member.dashboard')
                    ->with('info', 'You already have a member profile.');
            }

            // Generate unique member ID
            $memberCount = Member::withTrashed()->count();
            $memberId = 'MEM' . str_pad($memberCount + 1, 6, '0', STR_PAD_LEFT);

            // Create new member profile
            $member = Member::create([
                'user_id' => $user->id,
                'membership_type_id' => $request->membership_type_id,
                'member_id' => $memberId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $user->email,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'join_date' => now()->toDateString(),
                'membership_start_date' => now()->toDateString(),
                'membership_end_date' => now()->addMonths($membershipType->duration_months)->toDateString(),
                'status' => 'active',
                'medical_conditions' => $request->medical_conditions ?: [],
                'fitness_goals' => $request->fitness_goals ?: [],
                'preferred_workout_time' => $request->preferred_workout_time,
                'referral_source' => $request->referral_source,
            ]);

            // Create payment record
            Payment::create([
                'member_id' => $member->id,
                'membership_type_id' => $membershipType->id,
                'amount' => $membershipType->price,
                'payment_date' => now(),
                'payment_method_id' => $paymentMethod->id,
                'status' => 'completed',
                'description' => 'Membership Registration - ' . $membershipType->type_name,
                'receipt_number' => 'RCP' . time(),
                'processed_by' => $user->id,
            ]);

            DB::commit();

            Log::info('Member profile created successfully', [
                'member_id' => $member->id,
                'user_id' => $user->id,
                'membership_type' => $membershipType->type_name,
                'payment_method' => $paymentMethod->method_name
            ]);

            return redirect()->route('member.dashboard')
                ->with('success', 'Profile created successfully! Welcome to FitZone! Your payment has been processed.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Member profile creation failed: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Show edit profile form
     */
    public function editProfile()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        $membershipTypes = MembershipType::where('is_active', true)->get();
        
        return view('frontend.member.edit-profile', compact('member', 'membershipTypes'));
    }

    /**
     * Update member profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'medical_conditions' => 'nullable|array',
            'fitness_goals' => 'nullable|array',
            'preferred_workout_time' => 'nullable|string|max:50',
            'referral_source' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $member->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'medical_conditions' => $request->medical_conditions ?: [],
                'fitness_goals' => $request->fitness_goals ?: [],
                'preferred_workout_time' => $request->preferred_workout_time,
                'referral_source' => $request->referral_source,
            ]);

            return redirect()->route('member.profile')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Member profile update failed: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Show available classes
     */
    public function classes(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        $query = GymClass::with(['trainer', 'classRegistrations'])
            ->where('is_active', true);

        // Filter by class type
        if ($request->filled('type')) {
            $query->where('class_type', $request->type);
        }

        // Filter by difficulty
        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Filter by day
        if ($request->filled('day')) {
            $query->where('schedule_day', $request->day);
        }

        $classes = $query->paginate(12);

        // Get member's registered classes
        $registeredClassIds = $member->classRegistrations()
            ->where('status', 'registered')
            ->pluck('class_id')
            ->toArray();

        // Get available payment methods for class enrollment
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('frontend.member.classes', compact('classes', 'registeredClassIds', 'paymentMethods'));
    }

    /**
     * Enroll in a class
     */
    public function enrollClass(Request $request, GymClass $class)
    {
        try {
            $user = Auth::user();
            $member = $user->member;

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'error' => 'Member profile required'
                ], 400);
            }

            // Validate payment method
            $validator = Validator::make($request->all(), [
                'payment_method_id' => 'required|exists:payment_methods,id'
            ]);

            if ($validator->fails()) {
                Log::error('Class enrollment validation failed', [
                    'member_id' => $member->id,
                    'class_id' => $class->id,
                    'errors' => $validator->errors()->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Please select a valid payment method'
                ], 400);
            }

            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

            if (!$paymentMethod->is_active) {
                return response()->json([
                    'success' => false,
                    'error' => 'Selected payment method is not available'
                ], 400);
            }

            // Check if class is active
            if (!$class->is_active) {
                return response()->json([
                    'success' => false,
                    'error' => 'Class is not available'
                ], 400);
            }

            // Check if class is full
            if ($class->isFull()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Class is full'
                ], 400);
            }

            // Calculate next class date
            $nextClassDate = $this->calculateNextClassDate($class);

            // Check if already enrolled for this date
            $existingRegistration = ClassRegistration::where([
                'member_id' => $member->id,
                'class_id' => $class->id,
                'class_date' => $nextClassDate
            ])->whereIn('status', ['registered', 'attended'])
            ->first();

            if ($existingRegistration) {
                return response()->json([
                    'success' => false,
                    'error' => 'Already enrolled in this class session'
                ], 400);
            }

            DB::beginTransaction();

            // Create class registration
            $registration = ClassRegistration::create([
                'member_id' => $member->id,
                'class_id' => $class->id,
                'registration_date' => now(),
                'class_date' => $nextClassDate,
                'status' => 'registered',
                'payment_status' => $class->price > 0 ? 'pending' : 'free',
            ]);

            // Update class capacity
            $class->increment('current_capacity');

            // Verify capacity wasn't exceeded by concurrent requests
            if ($class->fresh()->current_capacity > $class->max_capacity) {
                throw new \Exception('Class capacity exceeded');
            }

            // Create payment record if class has a price
            if ($class->price > 0) {
                $payment = Payment::create([
                    'member_id' => $member->id,
                    'amount' => $class->price,
                    'payment_date' => now(),
                    'payment_method_id' => $paymentMethod->id,
                    'status' => 'completed',
                    'description' => 'Class Registration - ' . $class->class_name,
                    'receipt_number' => 'CLS' . time(),
                    'processed_by' => $user->id,
                    'class_registration_id' => $registration->id,
                ]);

                $registration->update(['payment_status' => 'paid']);
            }

            DB::commit();

            Log::info('Class enrollment successful', [
                'member_id' => $member->id,
                'class_id' => $class->id,
                'registration_id' => $registration->id,
                'payment_method' => $paymentMethod->method_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in ' . $class->class_name . '! Payment processed via ' . $paymentMethod->method_name . '.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Class enrollment failed', [
                'member_id' => $member->id ?? null,
                'class_id' => $class->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Enrollment failed. Please try again.'
            ], 500);
        }
    }
    /**
     * Enroll in a membership plan
     */
    public function enrollMembership(Request $request, MembershipType $membershipType)
{
    try {
        $user = Auth::user();
        $member = $user->member;
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'error' => 'Member profile required'
            ], 400);
        }

        // Check if user already has an active subscription for this membership type
        $activeSubscription = $member->subscriptions()
            ->where('membership_type_id', $membershipType->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'success' => false,
                'error' => 'You already have an active subscription for this membership type'
            ], 400);
        }

        // Validate payment method
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please select a valid payment method'
            ], 400);
        }

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        // Check if membership type is active
        if (!$membershipType->is_active) {
            Log::error('Membership enrollment failed - inactive membership type', [
                'member_id' => $member->id,
                'membership_type_id' => $membershipType->id,
                'membershipType' => $membershipType->type_name
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Membership type is not available'

            ], 400);
        }

        DB::beginTransaction();

        // Check for existing cancelled membership of the same type
        $cancelledSubscription = MemberSubscription::where('member_id', $member->id)
            ->where('membership_type_id', $membershipType->id)
            ->where('status', 'cancelled')
            ->latest()
            ->first();

        // Calculate dates
        $startDate = now();
        $endDate = now()->addMonths($membershipType->duration_months);

        // Create new subscription
        $subscription = MemberSubscription::create([
            'member_id' => $member->id,
            'membership_type_id' => $membershipType->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount_paid' => $membershipType->price,
            'status' => 'active',
            'auto_renew' => $request->auto_renew ?? false,
            'previous_subscription_id' => $cancelledSubscription ? $cancelledSubscription->id : null,
        ]);

        // Process payment
        $payment = Payment::create([
            'member_id' => $member->id,
            'amount' => $membershipType->price,
            'payment_date' => now(),
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'description' => 'Membership Enrollment - ' . $membershipType->type_name,
            'receipt_number' => 'MEM' . time(),
            'processed_by' => $user->id,
            'member_subscription_id' => $subscription->id,
        ]);

        // Update member's membership info
        $member->update([
            'membership_type_id' => $membershipType->id,
            'membership_start_date' => $startDate,
            'membership_end_date' => $endDate,
            'status' => 'active'
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in ' . $membershipType->type_name . ' membership!',
            'is_reactivation' => (bool)$cancelledSubscription,
            'subscription_id' => $subscription->id
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        \Log::error('Membership enrollment failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Enrollment failed. Please try again.'
        ], 500);
    }
}

public function cancelMembership(Request $request, MemberSubscription $subscription)
{
    try {
        $user = Auth::user();
        
        // Verify the subscription belongs to the current user
        if ($subscription->member_id !== $user->member->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized action'
            ], 403);
        }

        // Check if subscription is already cancelled
        if ($subscription->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'error' => 'Subscription is already cancelled'
            ], 400);
        }

        // Validate cancellation reason
        $validator = Validator::make($request->all(), [
            'cancellation_reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please provide a cancellation reason'
            ], 400);
        }

        DB::beginTransaction();

        // Update the subscription
        $subscription->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
            'auto_renew' => false
        ]);

        // Update member status if this was their active membership
        if ($user->member->membership_type_id === $subscription->membership_type_id) {
            $user->member->update([
                'status' => 'inactive'
            ]);
        }

        DB::commit();

        \Log::info('Membership cancelled successfully', [
            'subscription_id' => $subscription->id,
            'member_id' => $user->member->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership successfully cancelled'
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        
        \Log::error('Membership cancellation failed', [
            'subscription_id' => $subscription->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Cancellation failed. Please try again.'
        ], 500);
    }
}

public function renewMembership(Request $request, MemberSubscription $subscription)
{
    try {
        $user = Auth::user();
        
        // Verify the subscription belongs to the current user
        if ($subscription->member_id !== $user->member->id) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized action'
            ], 403);
        }

        // Check if subscription can be renewed
        if ($subscription->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'error' => 'Cannot renew a cancelled subscription'
            ], 400);
        }

        // Validate payment method
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Please select a valid payment method'
            ], 400);
        }

        DB::beginTransaction();

        $membershipType = $subscription->membershipType;
        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        if (!$paymentMethod->is_active) {
            throw new \Exception('Selected payment method is not available');
        }

        // Calculate new dates
        $newStartDate = $subscription->end_date->isPast() ? now() : $subscription->end_date;
        $newEndDate = (clone $newStartDate)->addMonths($membershipType->duration_months);

        // Process payment
        $payment = Payment::create([
            'member_id' => $user->member->id,
            'amount' => $membershipType->price,
            'payment_date' => now(),
            'payment_method_id' => $paymentMethod->id,
            'status' => 'completed',
            'description' => 'Membership Renewal - ' . $membershipType->type_name,
            'receipt_number' => 'REN' . time(),
            'processed_by' => $user->id,
        ]);

        // Create new subscription
        $newSubscription = MemberSubscription::create([
            'member_id' => $user->member->id,
            'membership_type_id' => $membershipType->id,
            'start_date' => $newStartDate,
            'end_date' => $newEndDate,
            'amount_paid' => $membershipType->price,
            'status' => 'active',
            'auto_renew' => $request->auto_renew ?? $subscription->auto_renew,
            'previous_subscription_id' => $subscription->id,
        ]);

        // Link payment to new subscription
        $payment->update(['member_subscription_id' => $newSubscription->id]);

        // Update old subscription
        $subscription->update([
            'renewed_at' => now(),
            'renewed_into_id' => $newSubscription->id
        ]);

        // Update member info
        $user->member->update([
            'membership_end_date' => $newEndDate,
            'status' => 'active'
        ]);

        DB::commit();

        \Log::info('Membership renewed successfully', [
            'old_subscription_id' => $subscription->id,
            'new_subscription_id' => $newSubscription->id,
            'member_id' => $user->member->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Membership successfully renewed until ' . $newEndDate->format('M d, Y'),
            'new_end_date' => $newEndDate->format('Y-m-d'),
            'subscription_id' => $newSubscription->id
        ]);

    } catch (\Exception $e) {
        DB::rollback();
        
        \Log::error('Membership renewal failed', [
            'subscription_id' => $subscription->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => 'Renewal failed. Please try again.'
        ], 500);
    }
}

    /**
     * Cancel class registration
     */
    public function cancelClass(ClassRegistration $registration)
    {
        $user = Auth::user();
        
        if ($registration->member_id !== $user->member->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($registration->status === 'cancelled') {
            return response()->json(['error' => 'Registration already cancelled'], 400);
        }

        DB::beginTransaction();
        try {
            $class = $registration->gymClass;
            $shouldRefund = $class->price > 0 && $registration->payment_status === 'paid';

            // Only refund if payment was made and class has price
            if ($shouldRefund) {
                Payment::create([
                    'member_id' => $registration->member_id,
                    'amount' => -$class->price,
                    'payment_date' => now(),
                    'payment_method_id' => $registration->payment->payment_method_id,
                    'status' => 'refunded',
                    'description' => 'Class Cancellation - ' . $class->class_name,
                    'receipt_number' => 'REF' . time(),
                    'processed_by' => $user->id,
                    'class_registration_id' => $registration->id,
                ]);
            }
            
            $registration->update([
                'status' => 'cancelled',
                'payment_status' => $shouldRefund ? 'refunded' : $registration->payment_status
            ]);
            
            $class->decrement('current_capacity');
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $shouldRefund ? 'Successful! Cancelled with refund' : 'Registration cancelled'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Class cancellation failed', [
                'registration_id' => $registration->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Cancellation failed'], 500);
        }
    }
    public function classDetails(GymClass $class)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        if (!$class->is_active) {
            return redirect()->route('member.classes')->with('error', 'This class is not available.');
        }

        // Ensure nextClassDate is a Carbon instance
        $nextClassDate = \Carbon\Carbon::parse($this->calculateNextClassDate($class));

        $alreadyEnrolled = $member->classRegistrations()
            ->where('class_id', $class->id)
            ->where('class_date', $nextClassDate->format('Y-m-d'))
            ->whereIn('status', ['registered', 'attended'])
            ->exists();

        $relatedClasses = GymClass::where('class_type', $class->class_type)
            ->where('id', '!=', $class->id)
            ->active()
            ->limit(3)
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('frontend.member.class-details', compact(
            'class', 
            'nextClassDate', 
            'alreadyEnrolled', 
            'relatedClasses',
            'paymentMethods'
        ));
    }

    /**
     * Show member's registered classes
     */
    public function myClasses()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        $registrations = $member->classRegistrations()
            ->with(['gymClass', 'gymClass.trainer'])
            ->where('class_date', '>=', now()->subDay())
            ->latest()
            ->paginate(10);

        return view('frontend.member.my-classes', compact('registrations'));
    }

    /**
     * Calculate next occurrence of a class
     */
    private function calculateNextClassDate(GymClass $class)
    {
        if (!$class->schedule_day) {
            return now()->toDateString();
        }

        $today = now()->dayOfWeekIso; // 1 (Monday) through 7 (Sunday)
        $classDay = array_search(strtolower($class->schedule_day), ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']) + 1;

        $daysUntilNext = $classDay - $today;
        if ($daysUntilNext < 0) {
            $daysUntilNext += 7;
        }

        return now()->addDays($daysUntilNext)->toDateString();
    }

    /**
     * Show attendance history
     */
    // public function attendance()
    // {
    //     $user = Auth::user();
    //     $member = $user->member;
    //     //dd($member->toArray());
    //     if (!$member) {
    //         return redirect()->route('member.profile.create');
    //     }

    //     $attendances = $member->attendances()
    //         ->latest('check_in_time')
    //         ->paginate(15);

    //     // Check if currently checked in
    //     $currentCheckIn = $member->attendances()
    //         ->whereNull('check_out_time')
    //         ->latest()
    //         ->first();
    //        // dd($currentCheckIn->toArray(), $attendances->toArray());

    //     return view('frontend.member.attendance', compact('attendances', 'currentCheckIn'));
    // }

    public function attendance()
{
    $user = Auth::user();
    $member = $user->member;
    
    if (!$member) {
        return redirect()->route('member.profile.create')->with('error', 'Please complete your profile first');
    }

    // Add member status check
    if (!$member->status) {
        return redirect()->route('member.dashboard')->with('error', 'Your account is not active');
    }

    $attendances = $member->attendances()
        ->latest('check_in_time')
        ->paginate(15);

    $currentCheckIn = $member->attendances()
        ->whereNull('check_out_time')
        ->whereDate('check_in_time', today()) // Only today's sessions
        ->latest()
        ->first();

    return view('frontend.member.attendance', compact('attendances', 'currentCheckIn'));
}
}
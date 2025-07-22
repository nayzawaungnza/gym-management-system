<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Models\Payment;
use App\Models\MembershipType;
use App\Models\PaymentMethod;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $member = $user->member;

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
            $memberId = 'GYM' . str_pad($memberCount + 1, 6, '0', STR_PAD_LEFT);

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

            return redirect()->route('member.dashboard')
                ->with('success', 'Profile created successfully! Welcome to FitZone! Your payment has been processed.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Member profile creation failed: ' . $e->getMessage());
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
            \Log::error('Member profile update failed: ' . $e->getMessage());
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
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return response()->json(['error' => 'Member profile required'], 400);
        }

        // Validate payment method
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Please select a valid payment method'], 400);
        }

        $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

        if (!$paymentMethod->is_active) {
            return response()->json(['error' => 'Selected payment method is not available'], 400);
        }

        // Check if class is active
        if (!$class->is_active) {
            return response()->json(['error' => 'Class is not available'], 400);
        }

        // Check if class is full
        if ($class->current_capacity >= $class->max_capacity) {
            return response()->json(['error' => 'Class is full'], 400);
        }

        // Check if already enrolled
        $existingRegistration = ClassRegistration::where([
            'member_id' => $member->id,
            'class_id' => $class->id,
            'status' => 'registered'
        ])->first();

        if ($existingRegistration) {
            return response()->json(['error' => 'Already enrolled in this class'], 400);
        }

        DB::beginTransaction();
        try {
            // Create class registration
            $registration = ClassRegistration::create([
                'member_id' => $member->id,
                'class_id' => $class->id,
                'registration_date' => now(),
                'class_date' => now()->toDateString(),
                'status' => 'registered',
                'payment_status' => 'paid',
            ]);

            // Update class capacity
            $class->increment('current_capacity');

            // Create payment record if class has a price
            if ($class->price > 0) {
                Payment::create([
                    'member_id' => $member->id,
                    'amount' => $class->price,
                    'payment_date' => now(),
                    'payment_method_id' => $paymentMethod->id,
                    'status' => 'completed',
                    'description' => 'Class Registration - ' . $class->class_name,
                    'receipt_number' => 'CLS' . time(),
                    'processed_by' => $user->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in ' . $class->class_name . '! Payment processed via ' . $paymentMethod->display_name . '.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Class enrollment failed: ' . $e->getMessage());
            return response()->json(['error' => 'Enrollment failed'], 500);
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

        DB::beginTransaction();
        try {
            // Update registration status
            $registration->update(['status' => 'cancelled']);

            // Decrease class capacity
            $registration->gymClass->decrement('current_capacity');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Class registration cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Class cancellation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Cancellation failed'], 500);
        }
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
            ->latest()
            ->paginate(10);

        return view('frontend.member.my-classes', compact('registrations'));
    }

    /**
     * Show attendance history
     */
    public function attendance()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create');
        }

        $attendances = $member->attendances()
            ->latest('check_in_time')
            ->paginate(15);

        // Check if currently checked in
        $currentCheckIn = $member->attendances()
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        return view('frontend.member.attendance', compact('attendances', 'currentCheckIn'));
    }
}
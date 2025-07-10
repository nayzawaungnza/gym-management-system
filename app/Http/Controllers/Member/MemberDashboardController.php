<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Models\MembershipType;
use App\Services\MemberService;
use App\Services\ClassService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MemberDashboardController extends Controller
{
    protected $memberService;
    protected $classService;
    protected $paymentService;

    public function __construct(
        MemberService $memberService,
        ClassService $classService,
        PaymentService $paymentService
    ) {
        $this->middleware('role:Member');
        $this->memberService = $memberService;
        $this->classService = $classService;
        $this->paymentService = $paymentService;
    }

    public function profile()
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Member profile not found.');
        }

        return view('member.profile', compact('member'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15'
        ]);

        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Member profile not found.');
        }

        $this->memberService->updateMember($member, $request->only(['first_name', 'last_name', 'phone']));

        return redirect()->route('member.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function schedule()
    {
        $upcomingClasses = GymClass::with('trainer')
            ->upcoming()
            ->active()
            ->orderBy('schedule')
            ->get();

        $member = Member::where('email', auth()->user()->email)->first();
        $registeredClassIds = $member ? $member->classRegistrations()
            ->whereHas('gymClass', function($query) {
                $query->where('schedule', '>', Carbon::now());
            })
            ->pluck('class_id')
            ->toArray() : [];

        return view('member.schedule', compact('upcomingClasses', 'registeredClassIds'));
    }

    public function registerClass(Request $request, GymClass $class)
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member profile not found.'
            ]);
        }

        // Check if class is full
        if ($class->isFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Class is full.'
            ]);
        }

        // Check if already registered
        $existingRegistration = ClassRegistration::where('member_id', $member->id)
            ->where('class_id', $class->id)
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this class.'
            ]);
        }

        // Register for class
        ClassRegistration::create([
            'member_id' => $member->id,
            'class_id' => $class->id,
            'registration_date' => now(),
            'status' => 'Registered'
        ]);

        // Update class capacity
        $class->increment('current_capacity');

        // Log activity
        $activity_data['subject'] = $class;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) registered for class (%s).', $member->full_name, $class->class_name);
        saveActivityLog($activity_data);

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for class.'
        ]);
    }

    public function cancelRegistration(ClassRegistration $registration)
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member || $registration->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ]);
        }

        $class = $registration->gymClass;
        
        // Cancel registration
        $registration->update(['status' => 'Cancelled']);
        
        // Update class capacity
        $class->decrement('current_capacity');

        // Log activity
        $activity_data['subject'] = $class;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) cancelled registration for class (%s).', $member->full_name, $class->class_name);
        saveActivityLog($activity_data);

        return response()->json([
            'success' => true,
            'message' => 'Registration cancelled successfully.'
        ]);
    }

    public function payments()
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Member profile not found.');
        }

        $payments = $member->payments()
            ->with('membershipType')
            ->orderBy('payment_date', 'desc')
            ->get();

        $membershipTypes = MembershipType::active()->get();

        return view('member.payments', compact('payments', 'membershipTypes'));
    }

    public function makePayment(Request $request)
    {
        $request->validate([
            'membership_type_id' => 'required|exists:membership_types,id',
            'payment_method' => 'required|in:Cash,Credit Card,Bank Transfer'
        ]);

        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member profile not found.'
            ]);
        }

        $membershipType = MembershipType::findOrFail($request->membership_type_id);

        $paymentData = [
            'member_id' => $member->id,
            'amount' => $membershipType->price,
            'payment_method' => $request->payment_method,
            'membership_type_id' => $request->membership_type_id,
            'payment_date' => now(),
            'status' => 'Completed' // In real app, this would be 'Pending' until payment gateway confirms
        ];

        $payment = $this->paymentService->createPayment($paymentData);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully.',
            'payment_id' => $payment->id
        ]);
    }

    public function attendance()
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Member profile not found.');
        }

        $attendance = $member->attendance()
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);

        return view('member.attendance', compact('attendance'));
    }
}

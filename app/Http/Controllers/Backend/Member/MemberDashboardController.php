<?php

namespace App\Http\Controllers\Backend\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Models\Attendance;
use App\Models\Payment;
use App\Services\AttendanceService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberDashboardController extends Controller
{
    protected $attendanceService;
    protected $paymentService;

    public function __construct(AttendanceService $attendanceService, PaymentService $paymentService)
    {
        $this->middleware(['auth', 'role:Member']);
        $this->attendanceService = $attendanceService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $member = Auth::user()->member;
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Basic Statistics
        $stats = [
            'total_visits' => Attendance::where('member_id', $member->id)->count(),
            'this_month_visits' => Attendance::where('member_id', $member->id)
                ->where('check_in_time', '>=', $thisMonth)
                ->count(),
            'registered_classes' => ClassRegistration::where('member_id', $member->id)
                ->where('status', 'registered')
                ->count(),
            'completed_classes' => ClassRegistration::where('member_id', $member->id)
                ->where('status', 'attended')
                ->count(),
        ];

        // Membership Status
        $membershipStatus = [
            'type' => $member->membershipType->name ?? 'N/A',
            'start_date' => $member->membership_start_date,
            'end_date' => $member->membership_end_date,
            'days_remaining' => $member->membership_end_date ? 
                Carbon::now()->diffInDays($member->membership_end_date, false) : null,
            'is_expired' => $member->membership_end_date ? 
                Carbon::now()->gt($member->membership_end_date) : false
        ];

        // Today's Classes
        $todayClasses = GymClass::whereHas('classRegistrations', function($query) use ($member) {
            $query->where('member_id', $member->id)
                ->where('status', 'registered');
        })
        ->whereDate('start_time', $today)
        ->with('trainer')
        ->orderBy('start_time')
        ->get();

        // Upcoming Classes
        $upcomingClasses = GymClass::whereHas('classRegistrations', function($query) use ($member) {
            $query->where('member_id', $member->id)
                ->where('status', 'registered');
        })
        ->where('start_time', '>', now())
        ->where('start_time', '<=', now()->addDays(7))
        ->with('trainer')
        ->orderBy('start_time')
        ->take(5)
        ->get();

        // Recent Attendance
        $recentAttendance = Attendance::where('member_id', $member->id)
            ->latest()
            ->take(10)
            ->get();

        // Monthly Attendance Chart
        $monthlyAttendance = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyAttendance[] = [
                'month' => $month->format('M Y'),
                'visits' => Attendance::where('member_id', $member->id)
                    ->whereYear('check_in_time', $month->year)
                    ->whereMonth('check_in_time', $month->month)
                    ->count()
            ];
        }

        // Recent Payments
        $recentPayments = Payment::where('member_id', $member->id)
            ->latest()
            ->take(5)
            ->get();

        // Check if currently checked in
        $currentlyCheckedIn = Attendance::where('member_id', $member->id)
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        return view('backend.member.dashboard', compact(
            'member',
            'stats',
            'membershipStatus',
            'todayClasses',
            'upcomingClasses',
            'recentAttendance',
            'monthlyAttendance',
            'recentPayments',
            'currentlyCheckedIn'
        ));
    }

    public function profile()
    {
        $member = Auth::user()->member;
        return view('backend.member.profile', compact('member'));
    }

    public function updateProfile(Request $request)
    {
        $member = Auth::user()->member;
        
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:15',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'medical_conditions' => 'nullable|string|max:1000',
            'fitness_goals' => 'nullable|string|max:1000',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->only([
            'first_name', 'last_name', 'phone', 'address',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
            'medical_conditions', 'fitness_goals'
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('member_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $member->update($data);

        return redirect()->route('member.profile')
            ->with('success', 'Profile updated successfully');
    }

    public function schedule()
    {
        $member = Auth::user()->member;
        
        // Available classes (not registered)
        $availableClasses = GymClass::whereDoesntHave('classRegistrations', function($query) use ($member) {
            $query->where('member_id', $member->id);
        })
        ->where('start_time', '>', now())
        ->where('max_participants', '>', function($query) {
            $query->selectRaw('count(*)')
                ->from('class_registrations')
                ->whereColumn('class_registrations.gym_class_id', 'gym_classes.id')
                ->where('status', 'registered');
        })
        ->with('trainer')
        ->orderBy('start_time')
        ->paginate(10, ['*'], 'available');

        // Registered classes
        $registeredClasses = GymClass::whereHas('classRegistrations', function($query) use ($member) {
            $query->where('member_id', $member->id)
                ->where('status', 'registered');
        })
        ->where('start_time', '>', now())
        ->with('trainer')
        ->orderBy('start_time')
        ->paginate(10, ['*'], 'registered');

        return view('backend.member.schedule', compact('availableClasses', 'registeredClasses', 'member'));
    }

    public function registerClass(Request $request, GymClass $class)
    {
        $member = Auth::user()->member;

        // Check if already registered
        $existingRegistration = ClassRegistration::where('member_id', $member->id)
            ->where('gym_class_id', $class->id)
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'You are already registered for this class'
            ]);
        }

        // Check if class is full
        $currentParticipants = ClassRegistration::where('gym_class_id', $class->id)
            ->where('status', 'registered')
            ->count();

        if ($currentParticipants >= $class->max_participants) {
            return response()->json([
                'success' => false,
                'message' => 'This class is full'
            ]);
        }

        // Check if class has already started
        if (Carbon::now()->gt($class->start_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot register for a class that has already started'
            ]);
        }

        ClassRegistration::create([
            'member_id' => $member->id,
            'gym_class_id' => $class->id,
            'registration_date' => now(),
            'status' => 'registered'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully registered for the class'
        ]);
    }

    public function cancelRegistration(Request $request, ClassRegistration $registration)
    {
        $member = Auth::user()->member;

        if ($registration->member_id !== $member->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Check if class has already started
        if (Carbon::now()->gt($registration->gymClass->start_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel registration for a class that has already started'
            ]);
        }

        $registration->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Registration cancelled successfully'
        ]);
    }

    public function payments()
    {
        $member = Auth::user()->member;
        $payments = Payment::where('member_id', $member->id)
            ->latest()
            ->paginate(15);

        return view('backend.member.payments', compact('payments', 'member'));
    }

    public function makePayment(Request $request)
    {
        $member = Auth::user()->member;

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'description' => 'nullable|string|max:255'
        ]);

        try {
            $payment = $this->paymentService->createPayment([
                'member_id' => $member->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'description' => $request->description ?? 'Membership payment',
                'status' => 'pending'
            ]);

            // Simulate payment processing (since no bank integration)
            $this->paymentService->processPayment($payment);

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'payment_id' => $payment->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function attendance()
    {
        $member = Auth::user()->member;
        $attendanceRecords = Attendance::where('member_id', $member->id)
            ->latest()
            ->paginate(20);

        return view('backend.member.attendance', compact('attendanceRecords', 'member'));
    }

    public function checkIn(Request $request)
    {
        $member = Auth::user()->member;

        try {
            $result = $this->attendanceService->checkIn(
                $member->id,
                $request->class_id,
                'member_self',
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Checked in successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function checkOut(Request $request)
    {
        $member = Auth::user()->member;

        try {
            $result = $this->attendanceService->checkOut(
                $member->id,
                'member_self',
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function qrCheckin()
    {
        $member = Auth::user()->member;
        
        // Generate QR code token (valid for 5 minutes)
        $token = encrypt([
            'member_id' => $member->id,
            'expires_at' => now()->addMinutes(5)->timestamp
        ]);

        $qrCode = QrCode::size(200)->generate(route('member.qr-verify', ['token' => $token]));

        return view('backend.member.qr-checkin', compact('member', 'qrCode', 'token'));
    }

    public function verifyQR(Request $request)
    {
        try {
            $data = decrypt($request->token);
            
            if (now()->timestamp > $data['expires_at']) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code has expired'
                ]);
            }

            $result = $this->attendanceService->checkIn(
                $data['member_id'],
                null,
                'qr_code',
                Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => 'QR check-in successful',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code'
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display available classes
     */
    public function index()
    {
        $classes = GymClass::with(['trainer'])
            ->where('status', 'active')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->paginate(12);

        return view('frontend.member.classes', compact('classes'));
    }

    /**
     * Show class details
     */
    public function show($id)
    {
        $class = GymClass::with(['trainer', 'registrations.member'])
            ->findOrFail($id);

        $user = Auth::user();
        $member = $user->member;
        
        $isEnrolled = false;
        if ($member) {
            $isEnrolled = ClassRegistration::where('member_id', $member->id)
                ->where('gym_class_id', $class->id)
                ->where('status', 'active')
                ->exists();
        }

        $availableSpots = $class->max_participants - $class->registrations()->where('status', 'active')->count();

        return view('frontend.member.class-details', compact('class', 'isEnrolled', 'availableSpots'));
    }

    /**
     * Enroll in a class
     */
    public function enroll(Request $request, $classId)
    {
        try {
            Log::info('Class enrollment attempt', [
                'user_id' => Auth::id(),
                'class_id' => $classId,
                'request_data' => $request->all()
            ]);

            $user = Auth::user();
            $member = $user->member;

            if (!$member) {
                Log::warning('Enrollment attempted without member profile', [
                    'user_id' => Auth::id(),
                    'class_id' => $classId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Please complete your member profile first.'
                ], 400);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'payment_method_id' => 'required|exists:payment_methods,id'
            ]);

            if ($validator->fails()) {
                Log::warning('Enrollment validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'user_id' => Auth::id(),
                    'class_id' => $classId
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Please select a valid payment method.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find the class
            $class = GymClass::findOrFail($classId);

            // Check if class is available for enrollment
            if ($class->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This class is not available for enrollment.'
                ], 400);
            }

            // Check if class is full
            $currentEnrollments = ClassRegistration::where('gym_class_id', $class->id)
                ->where('status', 'active')
                ->count();

            if ($currentEnrollments >= $class->max_participants) {
                return response()->json([
                    'success' => false,
                    'message' => 'This class is full. Please try another class.'
                ], 400);
            }

            // Check if already enrolled
            $existingRegistration = ClassRegistration::where('member_id', $member->id)
                ->where('gym_class_id', $class->id)
                ->where('status', 'active')
                ->first();

            if ($existingRegistration) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this class.'
                ], 400);
            }

            // Verify payment method exists and is active
            $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
                ->where('status', 'active')
                ->first();

            if (!$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected payment method is not available.'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Create class registration
                $registration = ClassRegistration::create([
                    'member_id' => $member->id,
                    'gym_class_id' => $class->id,
                    'registration_date' => now(),
                    'status' => 'active',
                    'payment_status' => 'pending',
                    'payment_method_id' => $paymentMethod->id,
                    'amount' => $class->price ?? 0,
                    'notes' => 'Online enrollment'
                ]);

                DB::commit();

                Log::info('Class enrollment successful', [
                    'member_id' => $member->id,
                    'class_id' => $class->id,
                    'registration_id' => $registration->id,
                    'payment_method_id' => $paymentMethod->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Successfully enrolled in ' . $class->name . '!',
                    'data' => [
                        'registration_id' => $registration->id,
                        'class_name' => $class->name,
                        'start_date' => $class->start_date->format('Y-m-d H:i:s'),
                        'payment_method' => $paymentMethod->name
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Class enrollment failed', [
                'user_id' => Auth::id(),
                'class_id' => $classId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll in class. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Cancel class enrollment
     */
    public function cancel(Request $request, $classId)
    {
        try {
            $user = Auth::user();
            $member = $user->member;

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member profile required.'
                ], 400);
            }

            $registration = ClassRegistration::where('member_id', $member->id)
                ->where('gym_class_id', $classId)
                ->where('status', 'active')
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active enrollment found for this class.'
                ], 400);
            }

            $class = $registration->gymClass;

            // Check if cancellation is allowed (e.g., at least 24 hours before class)
            if ($class->start_date->diffInHours(now()) < 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel enrollment less than 24 hours before class starts.'
                ], 400);
            }

            $registration->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Member requested cancellation'
            ]);

            Log::info('Class enrollment cancelled', [
                'member_id' => $member->id,
                'class_id' => $classId,
                'registration_id' => $registration->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully cancelled enrollment in ' . $class->name . '.'
            ]);

        } catch (\Exception $e) {
            Log::error('Class cancellation failed', [
                'user_id' => Auth::id(),
                'class_id' => $classId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel enrollment. Please try again.'
            ], 500);
        }
    }

    /**
     * Get member's enrolled classes
     */
    public function myClasses()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create')
                ->with('error', 'Please complete your member profile first.');
        }

        $enrolledClasses = ClassRegistration::with(['gymClass.trainer'])
            ->where('member_id', $member->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.member.my-classes', compact('enrolledClasses'));
    }
}

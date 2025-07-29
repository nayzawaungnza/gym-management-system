<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Member;
use App\Models\Trainer;
use App\Models\GymClass;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\MembershipType;
use App\Models\ClassRegistration;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function index()
    {
        // Get some basic stats for the homepage - ensure all values are strings/numbers
        $memberCount = Member::where('status', 'active')->count();
        $trainerCount = Trainer::where('is_active', true)->count();
        $classCount = GymClass::where('is_active', true)->count();
        //$membershipTypes = MembershipType::where('is_active', true)->count();

        $stats = [
            'members' => $memberCount > 0 ? (string)$memberCount . '+' : '500+',
            'trainers' => $trainerCount > 0 ? (string)$trainerCount . '+' : '15+',
            'classes' => $classCount > 0 ? (string)$classCount : '50',
            'weekly_classes' => $classCount > 0 ? (string)$classCount . '+' : '50+',
        ];

        // Get featured trainers (limit to 4 for the homepage)
        $trainers = Trainer::where('is_active', true)
            ->limit(4)
            ->get()
            ->map(function ($trainer) {
                // Ensure certifications is always an array
                if (is_string($trainer->certifications)) {
                    $trainer->certifications = json_decode($trainer->certifications, true) ?: [];
                } elseif (!is_array($trainer->certifications)) {
                    $trainer->certifications = [];
                }
                return $trainer;
            });

        $paymentMethods = PaymentMethod::where('is_active', true)->get();


        // Get upcoming gym classes
        $gymClasses = GymClass::with(['trainer'])
            ->where('is_active', true)
            ->where('schedule_day', '>=', now())
            ->orderBy('schedule_day')
            ->limit(6)
            ->get();

        // Check if membership types are available for registration
        $membershipTypes = MembershipType::where('is_active', true)->get();

        return view('frontend.main', compact('paymentMethods','stats', 'trainers', 'gymClasses', 'membershipTypes'));
    }

    public function enrollInClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:gym_classes,id',
            'payment_method' => 'required|string',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to enroll in classes.');
        }

        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('member.profile.create')->with('error', 'Please complete your member profile first.');
        }

        $gymClass = GymClass::findOrFail($request->class_id);

        // Check if class is full
        if ($gymClass->isFull()) {
            return back()->with('error', 'This class is already full.');
        }

        // Check if already enrolled
        $existingRegistration = ClassRegistration::where('member_id', $member->id)
            ->where('class_id', $gymClass->id)
            ->where('status', 'Registered')
            ->first();

        if ($existingRegistration) {
            return back()->with('error', 'You are already enrolled in this class.');
        }

        try {
            DB::beginTransaction();

            // Create class registration
            $registration = ClassRegistration::create([
                'member_id' => $member->id,
                'class_id' => $gymClass->id,
                'registration_date' => now(),
                'status' => 'Registered'
            ]);

            // Create payment record
            $payment = Payment::create([
                'member_id' => $member->id,
                'amount' => $gymClass->price,
                'class_registration_id' => $registration->id,
                
                'payment_date' => now(),
                'status' => 'Completed',
                'description' => 'Class enrollment: ' . $gymClass->class_name,
                'receipt_number' => 'CLS-' . strtoupper(uniqid()),
                'processed_by' => $user->id,
                'notes' => 'Payment method: ' . $request->payment_method
            ]);

            // Update class capacity
            $gymClass->increment('current_capacity');

            DB::commit();

            return back()->with('success', 'Successfully enrolled in ' . $gymClass->class_name . '!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to enroll in class. Please try again.');
        }
    }

    public function contact(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        // Here you can save to database or send email
        // For now, we'll just redirect back with success message
        
        // Optional: Send email notification
        // Mail::to('admin@fitzone.com')->send(new ContactFormMail($request->all()));

        return redirect()->back()->with('success', 'Thank you for your message! We\'ll get back to you soon.');
    }
}
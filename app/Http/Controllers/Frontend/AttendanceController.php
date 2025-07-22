<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // Gym location coordinates (your real gym location) 16.8336967,96.1752302
    const GYM_LATITUDE = 16.8336967;
    const GYM_LONGITUDE = 96.1752302;
    const ALLOWED_RADIUS_YARDS = 1550; // 100 yards radius

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display attendance page
     */
    public function index()
    {
        $user = Auth::user();
        $member = $user->member;
        //dd($member);
        if (!$member) {
            return redirect()->route('member.profile.create')
                ->with('error', 'Please complete your member profile first.');
        }

        // Get current check-in
        $currentCheckIn = Attendance::where('member_id', $member->id)
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', today()) // Only today's sessions
            ->latest()
            ->first();

        // Get attendance history
        $attendances = Attendance::where('member_id', $member->id)
            ->orderBy('check_in_time', 'desc')
            ->paginate(15);

        return view('frontend.member.attendance', compact('currentCheckIn', 'attendances'));
    }

    /**
     * Check-in member with location verification
     */
    public function checkIn(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Check-in attempt', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'method' => 'nullable|in:manual,qr_code',
            ]);

            if ($validator->fails()) {
                Log::warning('Check-in validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid location data provided.',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $member = $user->member;

            if (!$member) {
                Log::warning('Check-in attempted without member profile', [
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Member profile required. Please complete your profile first.'
                ], 400);
            }

            // Check if member is already checked in
            $existingCheckIn = Attendance::where('member_id', $member->id)
                ->whereNull('check_out_time')
                ->latest()
                ->first();

            if ($existingCheckIn) {
                Log::info('Member already checked in', [
                    'member_id' => $member->id,
                    'existing_check_in' => $existingCheckIn->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'You are already checked in. Please check out first.'
                ], 400);
            }

            // Verify location is within allowed radius
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                self::GYM_LATITUDE,
                self::GYM_LONGITUDE
            );

            $distanceInYards = $distance * 1093.61; // Convert km to yards

            Log::info('Location verification', [
                'member_id' => $member->id,
                'distance_yards' => $distanceInYards,
                'allowed_radius' => self::ALLOWED_RADIUS_YARDS
            ]);

            if ($distanceInYards > self::ALLOWED_RADIUS_YARDS) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be within ' . self::ALLOWED_RADIUS_YARDS . ' yards of the gym to check in.',
                    'distance' => round($distanceInYards, 2) . ' yards away'
                ], 400);
            }

            // Create attendance record
            $attendance = Attendance::create([
                'member_id' => $member->id,
                'check_in_time' => now(),
                'check_in_method' => $request->method ?? 'manual',
                'location' => 'Gym Floor',
                'notes' => 'Mobile check-in'
            ]);

            // Inside checkIn method
            AttendanceVerification::create([
                'attendance_id' => $attendance->id,
                'member_id' => $member->id,
                'verification_method' => 'manual', // Changed from 'location'
                'verification_data' => json_encode([
                    'user_location' => [$request->latitude, $request->longitude],
                    'gym_location' => [self::GYM_LATITUDE, self::GYM_LONGITUDE],
                    'distance_yards' => round($distanceInYards, 2),
                    'timestamp' => now()->toISOString()
                ]),
                'verification_status' => 'approved',
                'location_lat' => $request->latitude,
                'location_lng' => $request->longitude,
                'ip_address' => $request->ip(),
                'device_info' => $request->userAgent(),
                'confidence_score' => 100.00,
                'is_flagged' => false,
                'approved_at' => now(),
                'verification_notes' => 'Manual check-in with location verification (within allowed radius)'
            ]);
            DB::commit();

            Log::info('Check-in successful', [
                'member_id' => $member->id,
                'attendance_id' => $attendance->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully checked in!',
                'check_in_time' => $attendance->check_in_time->format('Y-m-d H:i:s'),
                'distance' => round($distanceInYards, 2) . ' yards from gym'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Check-in failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Check-in failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Check-out member
     */
    public function checkOut(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Check-out attempt', [
                'user_id' => Auth::id()
            ]);

            $user = Auth::user();
            $member = $user->member;

            if (!$member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member profile required.'
                ], 400);
            }

            // Find the latest check-in without check-out
            $attendance = Attendance::where('member_id', $member->id)
                ->whereNull('check_out_time')
                ->latest()
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active check-in found.'
                ], 400);
            }

            // Update attendance record
            $checkOutTime = now();
            $duration = $attendance->check_in_time->diffInMinutes($checkOutTime);

            $attendance->update([
                'check_out_time' => $checkOutTime,
                'duration_minutes' => $duration
            ]);

            DB::commit();

            Log::info('Check-out successful', [
                'member_id' => $member->id,
                'attendance_id' => $attendance->id,
                'duration_minutes' => $duration
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully checked out!',
                'check_out_time' => $checkOutTime->format('Y-m-d H:i:s'),
                'duration' => $this->formatDuration($duration)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Check-out failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Check-out failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get current check-in status
     */
    public function status()
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

            $currentCheckIn = Attendance::where('member_id', $member->id)
                ->whereNull('check_out_time')
                ->latest()
                ->first();

            if ($currentCheckIn) {
                $duration = $currentCheckIn->check_in_time->diffInMinutes(now());
                
                return response()->json([
                    'success' => true,
                    'checked_in' => true,
                    'check_in_time' => $currentCheckIn->check_in_time->format('Y-m-d H:i:s'),
                    'duration' => $this->formatDuration($duration)
                ]);
            }

            return response()->json([
                'success' => true,
                'checked_in' => false
            ]);

        } catch (\Exception $e) {
            Log::error('Status check failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to get status.'
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Format duration in human readable format
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'm';
        }
        
        return $mins . 'm';
    }
}
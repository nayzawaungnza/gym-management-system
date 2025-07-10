<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\AttendanceVerification;
use App\Services\AttendanceVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceVerificationController extends Controller
{
    protected $verificationService;

    public function __construct(AttendanceVerificationService $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Generate QR Code for member check-in
     */
    public function generateQRCode(Member $member)
    {
        $qrData = $this->verificationService->generateQRCode($member);
        
        return response()->json([
            'success' => true,
            'qr_code' => $qrData['qr_code'],
            'token' => $qrData['token'],
            'expires_at' => $qrData['expires_at']
        ]);
    }

    /**
     * Verify QR Code and check-in member
     */
    public function verifyQRCheckIn(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric',
            'device_info' => 'nullable|string'
        ]);

        $result = $this->verificationService->verifyQRCheckIn(
            $request->qr_token,
            $request->location_lat,
            $request->location_lng,
            $request->device_info,
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * Photo verification check-in
     */
    public function photoVerificationCheckIn(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'photo' => 'required|image|max:2048',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric'
        ]);

        $result = $this->verificationService->photoVerificationCheckIn(
            $request->member_id,
            $request->file('photo'),
            $request->location_lat,
            $request->location_lng,
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * Biometric verification check-in
     */
    public function biometricCheckIn(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'fingerprint_data' => 'required|string',
            'location_lat' => 'nullable|numeric',
            'location_lng' => 'nullable|numeric'
        ]);

        $result = $this->verificationService->biometricCheckIn(
            $request->member_id,
            $request->fingerprint_data,
            $request->location_lat,
            $request->location_lng,
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * RFID card check-in
     */
    public function rfidCheckIn(Request $request)
    {
        $request->validate([
            'rfid_code' => 'required|string',
            'reader_location' => 'required|string'
        ]);

        $result = $this->verificationService->rfidCheckIn(
            $request->rfid_code,
            $request->reader_location,
            $request->ip()
        );

        return response()->json($result);
    }

    /**
     * Get attendance verification logs
     */
    public function verificationLogs(Request $request)
    {
        $logs = AttendanceVerification::with(['member', 'attendance'])
            ->when($request->member_id, function($query, $memberId) {
                return $query->where('member_id', $memberId);
            })
            ->when($request->verification_method, function($query, $method) {
                return $query->where('verification_method', $method);
            })
            ->when($request->status, function($query, $status) {
                return $query->where('verification_status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('backend.attendance.verification-logs', compact('logs'));
    }

    /**
     * Flag suspicious attendance
     */
    public function flagSuspicious(AttendanceVerification $verification)
    {
        $verification->update([
            'is_flagged' => true,
            'flagged_by' => auth()->id(),
            'flagged_at' => now(),
            'flag_reason' => 'Manual review required'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance flagged for review.'
        ]);
    }

    /**
     * Approve flagged attendance
     */
    public function approveFlagged(AttendanceVerification $verification)
    {
        $verification->update([
            'is_flagged' => false,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'verification_status' => 'approved'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance approved.'
        ]);
    }
}

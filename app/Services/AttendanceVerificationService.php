<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Attendance;
use App\Models\AttendanceVerification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceVerificationService
{
    // Gym location coordinates (set your actual gym coordinates)
    private const GYM_LAT = 40.7128;
    private const GYM_LNG = -74.0060;
    private const MAX_DISTANCE_METERS = 100; // Maximum distance from gym to allow check-in

    /**
     * Generate QR Code for member check-in
     */
    public function generateQRCode(Member $member): array
    {
        $token = Str::random(32);
        $expiresAt = now()->addMinutes(5); // QR code expires in 5 minutes
        
        $qrData = [
            'member_id' => $member->id,
            'token' => $token,
            'expires_at' => $expiresAt->timestamp,
            'type' => 'check_in'
        ];

        // Store QR token in cache for verification
        cache()->put("qr_token_{$token}", $qrData, $expiresAt);

        // Generate QR code image
        $qrCode = QrCode::size(200)->generate(json_encode($qrData));

        return [
            'qr_code' => $qrCode,
            'token' => $token,
            'expires_at' => $expiresAt
        ];
    }

    /**
     * Verify QR Code and process check-in
     */
    public function verifyQRCheckIn(string $qrToken, ?float $lat, ?float $lng, ?string $deviceInfo, string $ipAddress): array
    {
        // Retrieve QR data from cache
        $qrData = cache()->get("qr_token_{$qrToken}");
        
        if (!$qrData) {
            return [
                'success' => false,
                'message' => 'Invalid or expired QR code.'
            ];
        }

        // Check if QR code has expired
        if (now()->timestamp > $qrData['expires_at']) {
            cache()->forget("qr_token_{$qrToken}");
            return [
                'success' => false,
                'message' => 'QR code has expired.'
            ];
        }

        $member = Member::find($qrData['member_id']);
        if (!$member) {
            return [
                'success' => false,
                'message' => 'Member not found.'
            ];
        }

        // Verify location if provided
        $locationVerified = $this->verifyLocation($lat, $lng);
        
        // Check for duplicate check-ins
        $existingAttendance = $this->checkDuplicateAttendance($member->id);
        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Member is already checked in today.'
            ];
        }

        // Create attendance record
        $attendance = Attendance::create([
            'member_id' => $member->id,
            'check_in_time' => now()
        ]);

        // Create verification record
        $verification = AttendanceVerification::create([
            'attendance_id' => $attendance->id,
            'member_id' => $member->id,
            'verification_method' => 'qr_code',
            'verification_data' => [
                'qr_token' => $qrToken,
                'location_verified' => $locationVerified,
                'device_info' => $deviceInfo
            ],
            'verification_status' => $locationVerified ? 'approved' : 'pending',
            'location_lat' => $lat,
            'location_lng' => $lng,
            'ip_address' => $ipAddress,
            'device_info' => $deviceInfo,
            'qr_token' => $qrToken,
            'confidence_score' => $locationVerified ? 95.0 : 70.0
        ]);

        // Remove QR token from cache
        cache()->forget("qr_token_{$qrToken}");

        // Log activity
        $activity_data['subject'] = $attendance;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Member (%s) checked in via QR code.', $member->full_name);
        saveActivityLog($activity_data);

        return [
            'success' => true,
            'message' => 'Check-in successful.',
            'attendance_id' => $attendance->id,
            'verification_status' => $verification->verification_status
        ];
    }

    /**
     * Photo verification check-in
     */
    public function photoVerificationCheckIn(string $memberId, UploadedFile $photo, ?float $lat, ?float $lng, string $ipAddress): array
    {
        $member = Member::find($memberId);
        if (!$member) {
            return [
                'success' => false,
                'message' => 'Member not found.'
            ];
        }

        // Check for duplicate check-ins
        $existingAttendance = $this->checkDuplicateAttendance($member->id);
        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Member is already checked in today.'
            ];
        }

        // Store photo
        $photoPath = $photo->store('attendance_photos', 'public');
        
        // Verify location
        $locationVerified = $this->verifyLocation($lat, $lng);
        
        // Simulate photo verification (in real app, use facial recognition API)
        $photoVerified = $this->simulatePhotoVerification($photo);

        // Create attendance record
        $attendance = Attendance::create([
            'member_id' => $member->id,
            'check_in_time' => now()
        ]);

        // Create verification record
        $verification = AttendanceVerification::create([
            'attendance_id' => $attendance->id,
            'member_id' => $member->id,
            'verification_method' => 'photo',
            'verification_data' => [
                'photo_verified' => $photoVerified,
                'location_verified' => $locationVerified
            ],
            'verification_status' => ($photoVerified && $locationVerified) ? 'approved' : 'pending',
            'location_lat' => $lat,
            'location_lng' => $lng,
            'ip_address' => $ipAddress,
            'photo_path' => $photoPath,
            'confidence_score' => $photoVerified ? 85.0 : 50.0
        ]);

        return [
            'success' => true,
            'message' => 'Check-in successful. Photo verification in progress.',
            'attendance_id' => $attendance->id,
            'verification_status' => $verification->verification_status
        ];
    }

    /**
     * Biometric verification check-in
     */
    public function biometricCheckIn(string $memberId, string $fingerprintData, ?float $lat, ?float $lng, string $ipAddress): array
    {
        $member = Member::find($memberId);
        if (!$member) {
            return [
                'success' => false,
                'message' => 'Member not found.'
            ];
        }

        // Check for duplicate check-ins
        $existingAttendance = $this->checkDuplicateAttendance($member->id);
        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Member is already checked in today.'
            ];
        }

        // Verify biometric data (simulate verification)
        $biometricVerified = $this->verifyBiometric($member, $fingerprintData);
        $locationVerified = $this->verifyLocation($lat, $lng);

        // Create attendance record
        $attendance = Attendance::create([
            'member_id' => $member->id,
            'check_in_time' => now()
        ]);

        // Create verification record
        $verification = AttendanceVerification::create([
            'attendance_id' => $attendance->id,
            'member_id' => $member->id,
            'verification_method' => 'biometric',
            'verification_data' => [
                'biometric_verified' => $biometricVerified,
                'location_verified' => $locationVerified
            ],
            'verification_status' => ($biometricVerified && $locationVerified) ? 'approved' : 'rejected',
            'location_lat' => $lat,
            'location_lng' => $lng,
            'ip_address' => $ipAddress,
            'biometric_hash' => hash('sha256', $fingerprintData),
            'confidence_score' => $biometricVerified ? 98.0 : 20.0
        ]);

        return [
            'success' => $biometricVerified,
            'message' => $biometricVerified ? 'Biometric verification successful.' : 'Biometric verification failed.',
            'attendance_id' => $biometricVerified ? $attendance->id : null,
            'verification_status' => $verification->verification_status
        ];
    }

    /**
     * RFID card check-in
     */
    public function rfidCheckIn(string $rfidCode, string $readerLocation, string $ipAddress): array
    {
        // Find member by RFID code
        $member = Member::where('rfid_code', $rfidCode)->first();
        if (!$member) {
            return [
                'success' => false,
                'message' => 'Invalid RFID card.'
            ];
        }

        // Check for duplicate check-ins
        $existingAttendance = $this->checkDuplicateAttendance($member->id);
        if ($existingAttendance) {
            return [
                'success' => false,
                'message' => 'Member is already checked in today.'
            ];
        }

        // Verify reader location
        $validReader = $this->verifyRFIDReader($readerLocation);

        // Create attendance record
        $attendance = Attendance::create([
            'member_id' => $member->id,
            'check_in_time' => now()
        ]);

        // Create verification record
        $verification = AttendanceVerification::create([
            'attendance_id' => $attendance->id,
            'member_id' => $member->id,
            'verification_method' => 'rfid',
            'verification_data' => [
                'reader_location' => $readerLocation,
                'reader_verified' => $validReader
            ],
            'verification_status' => $validReader ? 'approved' : 'pending',
            'ip_address' => $ipAddress,
            'rfid_code' => $rfidCode,
            'confidence_score' => $validReader ? 90.0 : 60.0
        ]);

        return [
            'success' => true,
            'message' => 'RFID check-in successful.',
            'attendance_id' => $attendance->id,
            'verification_status' => $verification->verification_status
        ];
    }

    /**
     * Verify location against gym coordinates
     */
    private function verifyLocation(?float $lat, ?float $lng): bool
    {
        if (!$lat || !$lng) {
            return false;
        }

        $distance = $this->calculateDistance(self::GYM_LAT, self::GYM_LNG, $lat, $lng);
        return $distance <= self::MAX_DISTANCE_METERS;
    }

    /**
     * Calculate distance between two coordinates in meters
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }

    /**
     * Check for duplicate attendance today
     */
    private function checkDuplicateAttendance(string $memberId): ?Attendance
    {
        return Attendance::where('member_id', $memberId)
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', Carbon::today())
            ->first();
    }

    /**
     * Simulate photo verification (replace with actual facial recognition)
     */
    private function simulatePhotoVerification(UploadedFile $photo): bool
    {
        // In a real application, you would:
        // 1. Extract face from uploaded photo
        // 2. Compare with stored member photo using facial recognition API
        // 3. Return confidence score
        
        // For simulation, we'll randomly approve 80% of photos
        return rand(1, 100) <= 80;
    }

    /**
     * Verify biometric data against stored member data
     */
    private function verifyBiometric(Member $member, string $fingerprintData): bool
    {
        // In a real application, you would:
        // 1. Compare fingerprint data with stored biometric template
        // 2. Use biometric matching algorithms
        // 3. Return match confidence
        
        // For simulation, check if member has biometric data stored
        return !empty($member->biometric_hash) && 
               hash('sha256', $fingerprintData) === $member->biometric_hash;
    }

    /**
     * Verify RFID reader location
     */
    private function verifyRFIDReader(string $readerLocation): bool
    {
        $validReaders = [
            'entrance_main',
            'entrance_side',
            'gym_floor_1',
            'gym_floor_2'
        ];

        return in_array($readerLocation, $validReaders);
    }

    /**
     * Detect suspicious patterns
     */
    public function detectSuspiciousPatterns(Member $member): array
    {
        $suspiciousFlags = [];

        // Check for rapid consecutive check-ins
        $recentCheckIns = Attendance::where('member_id', $member->id)
            ->where('check_in_time', '>=', now()->subHours(2))
            ->count();

        if ($recentCheckIns > 3) {
            $suspiciousFlags[] = 'Multiple check-ins in short period';
        }

        // Check for unusual check-in times
        $currentHour = now()->hour;
        if ($currentHour < 5 || $currentHour > 23) {
            $suspiciousFlags[] = 'Check-in outside normal hours';
        }

        // Check for location inconsistencies
        $recentVerifications = AttendanceVerification::where('member_id', $member->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('location_lat')
            ->get();

        $locationVariance = $this->calculateLocationVariance($recentVerifications);
        if ($locationVariance > 1000) { // More than 1km variance
            $suspiciousFlags[] = 'Inconsistent check-in locations';
        }

        return $suspiciousFlags;
    }

    /**
     * Calculate location variance for suspicious pattern detection
     */
    private function calculateLocationVariance($verifications): float
    {
        if ($verifications->count() < 2) {
            return 0;
        }

        $distances = [];
        foreach ($verifications as $verification) {
            $distance = $this->calculateDistance(
                self::GYM_LAT, 
                self::GYM_LNG, 
                $verification->location_lat, 
                $verification->location_lng
            );
            $distances[] = $distance;
        }

        $mean = array_sum($distances) / count($distances);
        $variance = array_sum(array_map(function($x) use ($mean) { 
            return pow($x - $mean, 2); 
        }, $distances)) / count($distances);

        return sqrt($variance);
    }
}

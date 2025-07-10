<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Member;
use App\Repositories\Backend\AttendanceRepository;
use App\Services\Interfaces\AttendanceServiceInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AttendanceService implements AttendanceServiceInterface
{
    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository)
    {
        $this->attendanceRepository = $attendanceRepository;
    }

    /**
     * Get paginated attendance records with filters
     */
    public function getAttendance(Request $request): LengthAwarePaginator
    {
        return $this->attendanceRepository->getAttendanceEloquent($request);
    }

    /**
     * Check in a member
     */
    public function checkIn(string $memberId, array $additionalData = []): Attendance
    {
        // Validate member exists and is active
        $validation = $this->validateMemberForCheckIn($memberId);
        
        if (!$validation['success']) {
            throw new \Exception($validation['message']);
        }

        // Check if member is already checked in
        if ($this->isMemberCheckedIn($memberId)) {
            throw new \Exception('Member is already checked in.');
        }

        $data = array_merge([
            'member_id' => $memberId,
            'check_in_time' => now(),
            'check_in_method' => 'manual',
            'location' => 'main_entrance',
        ], $additionalData);

        $attendance = $this->attendanceRepository->create($data);

        // Log the check-in
        Log::info('Member checked in', [
            'member_id' => $memberId,
            'attendance_id' => $attendance->id,
            'check_in_time' => $attendance->check_in_time,
            'method' => $attendance->check_in_method,
        ]);

        return $attendance;
    }

    /**
     * Check out a member
     */
    public function checkOut(Attendance $attendance): bool
    {
        if ($attendance->check_out_time) {
            throw new \Exception('Member is already checked out.');
        }

        $data = [
            'check_out_time' => now(),
        ];

        $result = $this->attendanceRepository->update($attendance, $data);

        if ($result) {
            // Log the check-out
            Log::info('Member checked out', [
                'member_id' => $attendance->member_id,
                'attendance_id' => $attendance->id,
                'check_out_time' => now(),
                'duration_minutes' => $attendance->fresh()->duration_minutes,
            ]);
        }

        return $result;
    }

    /**
     * Check out member by member ID
     */
    public function checkOutByMemberId(string $memberId): bool
    {
        $attendance = $this->getMemberCurrentCheckIn($memberId);
        
        if (!$attendance) {
            throw new \Exception('Member is not currently checked in.');
        }

        return $this->checkOut($attendance);
    }

    /**
     * Get today's attendance
     */
    public function getTodaysAttendance(): Collection
    {
        return $this->attendanceRepository->getTodaysAttendance();
    }

    /**
     * Get currently checked-in members
     */
    public function getCurrentlyCheckedIn(): Collection
    {
        return $this->attendanceRepository->getCurrentlyCheckedIn();
    }

    /**
     * Get member's attendance history
     */
    public function getMemberAttendance(string $memberId, $startDate = null, $endDate = null): Collection
    {
        return $this->attendanceRepository->getMemberAttendance($memberId, $startDate, $endDate);
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats($startDate = null, $endDate = null): array
    {
        return $this->attendanceRepository->getAttendanceStats($startDate, $endDate);
    }

    /**
     * Get weekly attendance data
     */
    public function getWeeklyAttendance(): array
    {
        return $this->attendanceRepository->getWeeklyAttendance();
    }

    /**
     * Get monthly attendance data
     */
    public function getMonthlyAttendance($year = null, $month = null): array
    {
        return $this->attendanceRepository->getMonthlyAttendance($year, $month);
    }

    /**
     * Check if member is currently checked in
     */
    public function isMemberCheckedIn(string $memberId): bool
    {
        return $this->attendanceRepository->isMemberCheckedIn($memberId);
    }

    /**
     * Get member's current check-in record
     */
    public function getMemberCurrentCheckIn(string $memberId): ?Attendance
    {
        return $this->attendanceRepository->getMemberCurrentCheckIn($memberId);
    }

    /**
     * Process QR code check-in
     */
    public function processQRCheckIn(string $qrCode, array $additionalData = []): array
    {
        try {
            // Decode QR code to get member ID
            $qrData = json_decode(base64_decode($qrCode), true);
            
            if (!$qrData || !isset($qrData['member_id'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid QR code format.',
                ];
            }

            $memberId = $qrData['member_id'];

            // Check if member is already checked in
            if ($this->isMemberCheckedIn($memberId)) {
                // If already checked in, check them out
                $this->checkOutByMemberId($memberId);
                return [
                    'success' => true,
                    'action' => 'check_out',
                    'message' => 'Member checked out successfully.',
                    'member_id' => $memberId,
                ];
            } else {
                // Check them in
                $additionalData['check_in_method'] = 'qr_code';
                $attendance = $this->checkIn($memberId, $additionalData);
                
                return [
                    'success' => true,
                    'action' => 'check_in',
                    'message' => 'Member checked in successfully.',
                    'member_id' => $memberId,
                    'attendance_id' => $attendance->id,
                ];
            }
        } catch (\Exception $e) {
            Log::error('QR check-in failed', [
                'qr_code' => $qrCode,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get top frequent members
     */
    public function getTopFrequentMembers($limit = 10, $startDate = null, $endDate = null): Collection
    {
        return $this->attendanceRepository->getTopFrequentMembers($limit, $startDate, $endDate);
    }

    /**
     * Get attendance by check-in method statistics
     */
    public function getAttendanceByMethod($startDate = null, $endDate = null): Collection
    {
        return $this->attendanceRepository->getAttendanceByMethod($startDate, $endDate);
    }

    /**
     * Bulk check-out members
     */
    public function bulkCheckOut(array $attendanceIds): int
    {
        return $this->attendanceRepository->bulkCheckOut($attendanceIds);
    }

    /**
     * Get attendance for export
     */
    public function getAttendanceForExport(Request $request): Collection
    {
        return $this->attendanceRepository->getAttendanceForExport($request);
    }

    /**
     * Validate member for check-in
     */
    public function validateMemberForCheckIn(string $memberId): array
    {
        $member = Member::find($memberId);

        if (!$member) {
            return [
                'success' => false,
                'message' => 'Member not found.',
            ];
        }

        if ($member->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Member account is not active.',
            ];
        }

        // Check if membership is expired
        if ($member->membership_end_date && Carbon::parse($member->membership_end_date)->isPast()) {
            return [
                'success' => false,
                'message' => 'Member membership has expired.',
            ];
        }

        // Check if member has any outstanding payments
        if ($member->payments()->where('status', 'pending')->exists()) {
            return [
                'success' => false,
                'message' => 'Member has outstanding payments.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Member is valid for check-in.',
            'member' => $member,
        ];
    }

    /**
     * Get attendance dashboard data
     */
    public function getDashboardData(): array
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'total_visits' => $this->attendanceRepository->getTodaysAttendance()->count(),
                'currently_checked_in' => $this->attendanceRepository->getCurrentlyCheckedIn()->count(),
                'unique_members' => $this->attendanceRepository->getTodaysAttendance()->unique('member_id')->count(),
            ],
            'this_week' => [
                'total_visits' => $this->attendanceRepository->getAttendanceStats($thisWeek)['total_visits'],
                'unique_members' => $this->attendanceRepository->getAttendanceStats($thisWeek)['unique_members'],
                'daily_data' => $this->getWeeklyAttendance(),
            ],
            'this_month' => [
                'total_visits' => $this->attendanceRepository->getAttendanceStats($thisMonth)['total_visits'],
                'unique_members' => $this->attendanceRepository->getAttendanceStats($thisMonth)['unique_members'],
                'average_duration' => $this->attendanceRepository->getAttendanceStats($thisMonth)['average_duration'],
            ],
            'top_members' => $this->getTopFrequentMembers(5, $thisMonth),
            'check_in_methods' => $this->getAttendanceByMethod($thisMonth),
            'peak_hours' => $this->attendanceRepository->getAttendanceStats($thisMonth),
        ];
    }
}

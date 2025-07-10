<?php

namespace App\Services\Interfaces;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceServiceInterface
{
    /**
     * Get paginated attendance records with filters
     */
    public function getAttendance(Request $request): LengthAwarePaginator;

    /**
     * Check in a member
     */
    public function checkIn(string $memberId, array $additionalData = []): Attendance;

    /**
     * Check out a member
     */
    public function checkOut(Attendance $attendance): bool;

    /**
     * Check out member by member ID
     */
    public function checkOutByMemberId(string $memberId): bool;

    /**
     * Get today's attendance
     */
    public function getTodaysAttendance(): Collection;

    /**
     * Get currently checked-in members
     */
    public function getCurrentlyCheckedIn(): Collection;

    /**
     * Get member's attendance history
     */
    public function getMemberAttendance(string $memberId, $startDate = null, $endDate = null): Collection;

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats($startDate = null, $endDate = null): array;

    /**
     * Get weekly attendance data
     */
    public function getWeeklyAttendance(): array;

    /**
     * Get monthly attendance data
     */
    public function getMonthlyAttendance($year = null, $month = null): array;

    /**
     * Check if member is currently checked in
     */
    public function isMemberCheckedIn(string $memberId): bool;

    /**
     * Get member's current check-in record
     */
    public function getMemberCurrentCheckIn(string $memberId): ?Attendance;

    /**
     * Process QR code check-in
     */
    public function processQRCheckIn(string $qrCode, array $additionalData = []): array;

    /**
     * Get top frequent members
     */
    public function getTopFrequentMembers($limit = 10, $startDate = null, $endDate = null): Collection;

    /**
     * Get attendance by check-in method statistics
     */
    public function getAttendanceByMethod($startDate = null, $endDate = null): Collection;

    /**
     * Bulk check-out members
     */
    public function bulkCheckOut(array $attendanceIds): int;

    /**
     * Get attendance for export
     */
    public function getAttendanceForExport(Request $request): Collection;

    /**
     * Validate member for check-in
     */
    public function validateMemberForCheckIn(string $memberId): array;

    /**
     * Get attendance dashboard data
     */
    public function getDashboardData(): array;
}

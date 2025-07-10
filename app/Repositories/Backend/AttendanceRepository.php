<?php

namespace App\Repositories\Backend;

use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceRepository
{
    protected $model;

    public function __construct(Attendance $model)
    {
        $this->model = $model;
    }

    /**
     * Get attendance with filters and pagination
     */
    public function getAttendanceEloquent(Request $request = null): LengthAwarePaginator
    {
        $query = $this->model->with(['member'])
            ->orderBy('check_in_time', 'desc');

        if ($request) {
            // Filter by member
            if ($request->filled('member_id')) {
                $query->where('member_id', $request->member_id);
            }

            // Filter by member name or ID
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('member', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('member_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by date range
            if ($request->filled('start_date')) {
                $query->whereDate('check_in_time', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('check_in_time', '<=', $request->end_date);
            }

            // Filter by check-in method
            if ($request->filled('check_in_method')) {
                $query->where('check_in_method', $request->check_in_method);
            }

            // Filter by status (checked out or still checked in)
            if ($request->filled('status')) {
                if ($request->status === 'checked_in') {
                    $query->whereNull('check_out_time');
                } elseif ($request->status === 'checked_out') {
                    $query->whereNotNull('check_out_time');
                }
            }

            // Filter by location
            if ($request->filled('location')) {
                $query->where('location', $request->location);
            }
        }

        return $query->paginate(15);
    }

    /**
     * Get today's attendance
     */
    public function getTodaysAttendance(): Collection
    {
        return $this->model->with(['member'])
            ->whereDate('check_in_time', Carbon::today())
            ->orderBy('check_in_time', 'desc')
            ->get();
    }

    /**
     * Get currently checked-in members
     */
    public function getCurrentlyCheckedIn(): Collection
    {
        return $this->model->with(['member'])
            ->whereNull('check_out_time')
            ->orderBy('check_in_time', 'desc')
            ->get();
    }

    /**
     * Get member's attendance history
     */
    public function getMemberAttendance(string $memberId, $startDate = null, $endDate = null): Collection
    {
        $query = $this->model->where('member_id', $memberId)
            ->orderBy('check_in_time', 'desc');

        if ($startDate) {
            $query->whereDate('check_in_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('check_in_time', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get attendance statistics for a date range
     */
    public function getAttendanceStats($startDate = null, $endDate = null): array
    {
        $query = $this->model->query();

        if ($startDate) {
            $query->whereDate('check_in_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('check_in_time', '<=', $endDate);
        }

        $totalVisits = $query->count();
        $uniqueMembers = $query->distinct('member_id')->count('member_id');
        $averageDuration = $query->whereNotNull('check_out_time')
            ->avg('duration_minutes');

        $peakHours = $this->model->selectRaw('HOUR(check_in_time) as hour, COUNT(*) as count')
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('check_in_time', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('check_in_time', '<=', $endDate);
            })
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->first();

        return [
            'total_visits' => $totalVisits,
            'unique_members' => $uniqueMembers,
            'average_duration' => round($averageDuration ?? 0, 2),
            'peak_hour' => $peakHours ? $peakHours->hour . ':00' : 'N/A',
            'peak_hour_visits' => $peakHours ? $peakHours->count : 0,
        ];
    }

    /**
     * Get weekly attendance data
     */
    public function getWeeklyAttendance(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $attendance = $this->model->selectRaw('DATE(check_in_time) as date, COUNT(*) as count')
            ->whereBetween('check_in_time', [$startOfWeek, $endOfWeek])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $weekData = [];
        for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $weekData[] = [
                'date' => $dateStr,
                'day' => $date->format('l'),
                'count' => $attendance->get($dateStr)->count ?? 0,
            ];
        }

        return $weekData;
    }

    /**
     * Get monthly attendance data
     */
    public function getMonthlyAttendance($year = null, $month = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth();

        return $this->model->selectRaw('DATE(check_in_time) as date, COUNT(*) as count')
            ->whereBetween('check_in_time', [$startOfMonth, $endOfMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                    'day' => Carbon::parse($item->date)->format('d'),
                ];
            })
            ->toArray();
    }

    /**
     * Check if member is currently checked in
     */
    public function isMemberCheckedIn(string $memberId): bool
    {
        return $this->model->where('member_id', $memberId)
            ->whereNull('check_out_time')
            ->exists();
    }

    /**
     * Get member's current check-in record
     */
    public function getMemberCurrentCheckIn(string $memberId): ?Attendance
    {
        return $this->model->where('member_id', $memberId)
            ->whereNull('check_out_time')
            ->first();
    }

    /**
     * Create new attendance record
     */
    public function create(array $data): Attendance
    {
        return $this->model->create($data);
    }

    /**
     * Update attendance record
     */
    public function update(Attendance $attendance, array $data): bool
    {
        // Calculate duration if checking out
        if (isset($data['check_out_time']) && !isset($data['duration_minutes'])) {
            $checkInTime = Carbon::parse($attendance->check_in_time);
            $checkOutTime = Carbon::parse($data['check_out_time']);
            $data['duration_minutes'] = $checkInTime->diffInMinutes($checkOutTime);
        }

        return $attendance->update($data);
    }

    /**
     * Delete attendance record
     */
    public function delete(Attendance $attendance): bool
    {
        return $attendance->delete();
    }

    /**
     * Get attendance by ID
     */
    public function findById(string $id): ?Attendance
    {
        return $this->model->with(['member'])->find($id);
    }

    /**
     * Get top frequent members
     */
    public function getTopFrequentMembers($limit = 10, $startDate = null, $endDate = null): Collection
    {
        $query = $this->model->with(['member'])
            ->selectRaw('member_id, COUNT(*) as visit_count')
            ->groupBy('member_id')
            ->orderBy('visit_count', 'desc')
            ->limit($limit);

        if ($startDate) {
            $query->whereDate('check_in_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('check_in_time', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Get attendance by check-in method
     */
    public function getAttendanceByMethod($startDate = null, $endDate = null): Collection
    {
        $query = $this->model->selectRaw('check_in_method, COUNT(*) as count')
            ->groupBy('check_in_method')
            ->orderBy('count', 'desc');

        if ($startDate) {
            $query->whereDate('check_in_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('check_in_time', '<=', $endDate);
        }

        return $query->get();
    }

    /**
     * Bulk check-out members
     */
    public function bulkCheckOut(array $attendanceIds): int
    {
        return $this->model->whereIn('id', $attendanceIds)
            ->whereNull('check_out_time')
            ->update([
                'check_out_time' => now(),
                'duration_minutes' => \DB::raw('TIMESTAMPDIFF(MINUTE, check_in_time, NOW())'),
            ]);
    }

    /**
     * Get attendance for export
     */
    public function getAttendanceForExport(Request $request): Collection
    {
        $query = $this->model->with(['member'])
            ->orderBy('check_in_time', 'desc');

        // Apply same filters as getAttendanceEloquent but return all records
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('member', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('check_in_time', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('check_in_time', '<=', $request->end_date);
        }

        if ($request->filled('check_in_method')) {
            $query->where('check_in_method', $request->check_in_method);
        }

        if ($request->filled('status')) {
            if ($request->status === 'checked_in') {
                $query->whereNull('check_out_time');
            } elseif ($request->status === 'checked_out') {
                $query->whereNotNull('check_out_time');
            }
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        return $query->get();
    }
}

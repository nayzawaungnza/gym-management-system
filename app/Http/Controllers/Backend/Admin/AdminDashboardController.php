<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Payment;
use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\Equipment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\MembershipType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AttendanceVerification;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $stats = $this->getBasicStats();
        $recentActivities = $this->getRecentActivities();
        $upcomingClasses = $this->getUpcomingClasses();
        $equipmentStatus = $this->getEquipmentStatus();
        $flaggedAttendances = $this->getFlaggedAttendances()['data'] ?? [];

        return view('backend.admin.dashboard', compact(
            'stats',
            'recentActivities',
            'upcomingClasses',
            'equipmentStatus',
            'flaggedAttendances'
        ));
    }

    public function getStats()
    {
        try {
            $stats = $this->getBasicStats();
            $memberGrowth = $this->getMemberGrowthData();
            $revenueData = $this->getRevenueData();
            $attendanceData = $this->getAttendanceData();
            $membershipTypeData = $this->getMembershipTypeDistribution()['data'];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'member_growth' => $memberGrowth,
                'revenue_data' => $revenueData,
                'attendance_data' => $attendanceData,
                'membership_type_data' => $membershipTypeData
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch dashboard stats: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch dashboard statistics. Please try again later.'
            ], 500);
        }
    }

    public function getRevenueChart(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            if (!in_array($period, ['week', 'month', 'year'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period specified. Must be week, month, or year.'
                ], 400);
            }

            $data = $this->getRevenueChartData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch revenue chart: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch revenue chart. Please try again later.'
            ], 500);
        }
    }

    public function getMemberGrowth(Request $request)
    {
        try {
            $period = $request->get('period', 'year');
            if (!in_array($period, ['month', 'year'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period specified. Must be month or year.'
                ], 400);
            }

            $data = $this->getMemberGrowthChartData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch member growth: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch member growth. Please try again later.'
            ], 500);
        }
    }

    public function getAttendanceOverview(Request $request)
    {
        try {
            $period = $request->get('period', 'week');
            if (!in_array($period, ['week', 'month'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period specified. Must be week or month.'
                ], 400);
            }

            $data = $this->getAttendanceOverviewData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch attendance overview: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch attendance overview. Please try again later.'
            ], 500);
        }
    }

    public function getFlaggedAttendances()
    {
        try {
            $flaggedAttendances = AttendanceVerification::flagged()
                ->with(['member', 'attendance', 'flaggedBy'])
                ->latest('flagged_at')
                ->limit(5)
                ->get()
                ->map(function ($verification) {
                    return [
                        'id' => $verification->id,
                        'member_name' => optional($verification->member)->full_name ?? 'Unknown',
                        'check_in_time' => optional($verification->attendance)->check_in_time?->toDateTimeString(),
                        'verification_method' => $verification->verification_method,
                        'flag_reason' => $verification->flag_reason,
                        'flagged_by' => optional($verification->flaggedBy)->name ?? 'System',
                        'flagged_at' => $verification->flagged_at->diffForHumans(),
                    ];
                });

            return [
                'success' => true,
                'data' => $flaggedAttendances
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to fetch flagged attendances: ' . $e->getMessage(), ['exception' => $e]);
            return [
                'success' => false,
                'message' => 'Unable to fetch flagged attendances. Please try again later.'
            ];
        }
    }

    public function getMembershipTypeDistribution()
    {
        try {
            $data = MembershipType::active()
                ->withCount(['members' => function ($query) {
                    $query->active()->where('membership_end_date', '>', Carbon::now());
                }])
                ->get()
                ->map(function ($type) {
                    return [
                        'name' => $type->type_name,
                        'count' => $type->members_count,
                    ];
                });

            return [
                'success' => true,
                'data' => [
                    'labels' => $data->pluck('name')->toArray(),
                    'data' => $data->pluck('count')->toArray(),
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to fetch membership type distribution: ' . $e->getMessage(), ['exception' => $e]);
            return [
                'success' => false,
                'message' => 'Unable to fetch membership type distribution. Please try again later.'
            ];
        }
    }

    private function getBasicStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $memberStats = Member::selectRaw('
            COUNT(*) as total_members,
            COUNT(CASE WHEN status = "active" AND membership_end_date > NOW() THEN 1 END) as active_members,
            COUNT(CASE WHEN created_at >= ? THEN 1 END) as new_members_this_month,
            COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as new_members_last_month
        ', [$thisMonth, $lastMonth, $thisMonth])
            ->first();

        $paymentStats = Payment::selectRaw('
            SUM(CASE WHEN DATE(payment_date) = ? AND status = "Completed" THEN amount ELSE 0 END) as today_revenue,
            SUM(CASE WHEN payment_date >= ? AND status = "Completed" THEN amount ELSE 0 END) as month_revenue
        ', [$today, $thisMonth])
            ->first();

        $attendanceStats = Attendance::selectRaw('
            COUNT(CASE WHEN DATE(check_in_time) = ? THEN 1 END) as today_checkins,
            COUNT(CASE WHEN DATE(check_in_time) = ? AND check_out_time IS NULL THEN 1 END) as active_members_now
        ', [$today, $today])
            ->first();

        $equipmentStats = Equipment::selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "available" THEN 1 END) as available,
            COUNT(CASE WHEN status = "maintenance" THEN 1 END) as maintenance
        ')->first();

        return [
            'total_members' => $memberStats->total_members,
            'active_members' => $memberStats->active_members,
            'total_trainers' => Trainer::count(),
            'active_trainers' => Trainer::where('is_active', true)->count(),
            'total_classes' => GymClass::count(),
            'today_classes' => GymClass::whereDate('schedule_day', $today)->count(),
            'total_equipment' => $equipmentStats->total,
            'equipment_available' => $equipmentStats->available,
            'maintenance_equipment' => $equipmentStats->maintenance,
            'today_revenue' => $paymentStats->today_revenue,
            'month_revenue' => $paymentStats->month_revenue,
            'today_checkins' => $attendanceStats->today_checkins,
            'active_members_now' => $attendanceStats->active_members_now,
            'new_members_this_month' => $memberStats->new_members_this_month,
            'member_growth_percentage' => $this->calculateGrowthPercentage(
                $memberStats->new_members_this_month,
                $memberStats->new_members_last_month
            )
        ];
    }

    private function getRecentActivities()
    {
        return Activity::with(['causer'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'subject_id' => $log->subject_id,
                    'causer_name' => $log->causer ? $log->causer->name : 'System',
                    'created_at' => $log->created_at->diffForHumans(),
                    'event' => $log->event ?? 'updated'
                ];
            });
    }

    private function getUpcomingClasses()
    {
        return GymClass::with(['trainer', 'classRegistrations'])
            ->where('schedule_day', '>=', Carbon::now())
            ->where('is_active', true)
            ->orderBy('schedule_day')
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->class_name,
                    'trainer_name' => optional($class->trainer)->full_name ?? 'No Trainer',
                    'start_date' => $class->schedule_day,
                    'start_time' => $class->start_time,
                    'capacity' => $class->max_capacity,
                    'registered' => $class->current_capacity,
                    'location' => $class->room
                ];
            });
    }

    private function getEquipmentStatus()
    {
        $stats = Equipment::selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN status = "available" THEN 1 END) as available,
            COUNT(CASE WHEN status = "in_use" THEN 1 END) as in_use,
            COUNT(CASE WHEN status = "maintenance" THEN 1 END) as maintenance,
            COUNT(CASE WHEN status = "out_of_order" THEN 1 END) as out_of_order
        ')->first();

        return [
            'total' => $stats->total,
            'available' => $stats->available,
            'in_use' => $stats->in_use,
            'maintenance' => $stats->maintenance,
            'out_of_order' => $stats->out_of_order
        ];
    }

    private function getDateAggregatedData($model, $column, $period, $aggregate = 'count', $conditions = [])
    {
        $labels = [];
        $data = [];

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $format = 'D';
                $groupBy = 'DATE(' . $column . ')';
                $interval = '1 DAY';
                break;
            case 'month':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $format = 'M d';
                $groupBy = 'DATE(' . $column . ')';
                $interval = '1 DAY';
                break;
            case 'year':
                $startDate = Carbon::now()->subMonths(11)->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $format = 'M Y';
                $groupBy = "DATE_FORMAT($column, '%Y-%m')";
                $interval = '1 MONTH';
                break;
            default:
                throw new \InvalidArgumentException('Invalid period specified');
        }

        $query = $model::whereBetween($column, [$startDate, $endDate]);
        foreach ($conditions as $key => $value) {
            $query->where($key, $value);
        }

        $aggregateColumn = $aggregate === 'sum' ? $column : '*';
        $results = $query->selectRaw("$groupBy as date, $aggregate($aggregateColumn) as total")
            ->groupByRaw($groupBy)
            ->orderBy('date')
            ->get();

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $labels[] = $currentDate->format($format);
            $dateKey = $period === 'year' ? $currentDate->format('Y-m') : $currentDate->toDateString();
            $data[] = $results->firstWhere('date', $dateKey)->total ?? 0;
            $currentDate->add($interval);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getRevenueChartData($period)
    {
        return $this->getDateAggregatedData(Payment::class, 'payment_date', $period, 'sum', ['status' => 'Completed']);
    }

    private function getRevenueData()
    {
        return $this->getDateAggregatedData(Payment::class, 'payment_date', 'week', 'sum', ['status' => 'Completed']);
    }

    private function getMemberGrowthChartData($period)
    {
        return $this->getDateAggregatedData(Member::class, 'created_at', $period);
    }

    private function getMemberGrowthData()
    {
        return $this->getDateAggregatedData(Member::class, 'created_at', 'year');
    }

    private function getAttendanceData()
    {
        return $this->getDateAggregatedData(Attendance::class, 'check_in_time', 'week');
    }

    private function getAttendanceOverviewData($period)
    {
        $labels = [];
        $checkins = [];
        $checkouts = [];

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $format = 'D';
                $groupBy = 'DATE(check_in_time)';
                $interval = '1 DAY';
                break;
            case 'month':
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $format = 'M d';
                $groupBy = 'DATE(check_in_time)';
                $interval = '1 DAY';
                break;
            default:
                throw new \InvalidArgumentException('Invalid period specified');
        }

        $checkinResults = Attendance::whereBetween('check_in_time', [$startDate, $endDate])
            ->selectRaw("$groupBy as date, COUNT(*) as total")
            ->groupByRaw($groupBy)
            ->orderBy('date')
            ->get();

        $checkoutResults = Attendance::whereBetween('check_out_time', [$startDate, $endDate])
            ->selectRaw("DATE(check_out_time) as date, COUNT(*) as total")
            ->groupByRaw('DATE(check_out_time)')
            ->orderBy('date')
            ->get();

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $labels[] = $currentDate->format($format);
            $dateKey = $currentDate->toDateString();
            $checkins[] = $checkinResults->firstWhere('date', $dateKey)->total ?? 0;
            $checkouts[] = $checkoutResults->firstWhere('date', $dateKey)->total ?? 0;
            $currentDate->add($interval);
        }

        return [
            'labels' => $labels,
            'checkins' => $checkins,
            'checkouts' => $checkouts
        ];
    }

    private function calculateGrowthPercentage($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
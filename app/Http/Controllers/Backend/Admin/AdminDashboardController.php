<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\Equipment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $stats = $this->getBasicStats();
        $recentActivities = $this->getRecentActivities();
        $upcomingClasses = $this->getUpcomingClasses();
        $equipmentStatus = $this->getEquipmentStatus();
        
        return view('backend.admin.dashboard', compact(
            'stats', 
            'recentActivities', 
            'upcomingClasses', 
            'equipmentStatus'
        ));
    }

    public function getStats()
    {
        try {
            $stats = $this->getBasicStats();
            $memberGrowth = $this->getMemberGrowthData();
            $revenueData = $this->getRevenueData();
            $attendanceData = $this->getAttendanceData();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'member_growth' => $memberGrowth,
                'revenue_data' => $revenueData,
                'attendance_data' => $attendanceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRevenueChart(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $data = $this->getRevenueChartData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching revenue chart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMemberGrowth(Request $request)
    {
        try {
            $period = $request->get('period', 'year');
            $data = $this->getMemberGrowthChartData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching member growth: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceOverview(Request $request)
    {
        try {
            $period = $request->get('period', 'week');
            $data = $this->getAttendanceOverviewData($period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance overview: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getBasicStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'total_members' => Member::count(),
            'active_members' => Member::where('status', 'active')->count(),
            'total_trainers' => Trainer::count(),
            'active_trainers' => Trainer::where('is_active', true)->count(),
            'total_classes' => GymClass::count(),
            'today_classes' => GymClass::whereDate('start_date', $today)->count(),
            'total_equipment' => Equipment::count(),
            'maintenance_equipment' => Equipment::where('status', 'maintenance')->count(),
            'today_revenue' => Payment::whereDate('payment_date', $today)
                ->where('status', 'completed')
                ->sum('amount'),
            'month_revenue' => Payment::where('payment_date', '>=', $thisMonth)
                ->where('status', 'completed')
                ->sum('amount'),
            'today_checkins' => Attendance::whereDate('check_in_time', $today)->count(),
            'active_members_now' => Attendance::whereDate('check_in_time', $today)
                ->whereNull('check_out_time')
                ->count(),
            'new_members_this_month' => Member::where('created_at', '>=', $thisMonth)->count(),
            'member_growth_percentage' => $this->calculateGrowthPercentage(
                Member::where('created_at', '>=', $thisMonth)->count(),
                Member::whereBetween('created_at', [$lastMonth, $thisMonth])->count()
            )
        ];
    }

    private function getRecentActivities()
    {
        return ActivityLog::with(['causer'])
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
        return GymClass::with(['trainer'])
            ->where('start_date', '>=', Carbon::now())
            ->where('status', 'active')
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'trainer_name' => $class->trainer ? $class->trainer->full_name : 'No Trainer',
                    'start_date' => $class->start_date,
                    'start_time' => $class->start_time,
                    'capacity' => $class->capacity,
                    'registered' => $class->classRegistrations()->count(),
                    'location' => $class->location
                ];
            });
    }

    private function getEquipmentStatus()
    {
        return [
            'total' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'in_use' => Equipment::where('status', 'in_use')->count(),
            'maintenance' => Equipment::where('status', 'maintenance')->count(),
            'out_of_order' => Equipment::where('status', 'out_of_order')->count()
        ];
    }

    private function getMemberGrowthData()
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $data[] = Member::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getRevenueData()
    {
        $days = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            $data[] = Payment::whereDate('payment_date', $date)
                ->where('status', 'completed')
                ->sum('amount');
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }

    private function getAttendanceData()
    {
        $days = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M d');
            $data[] = Attendance::whereDate('check_in_time', $date)->count();
        }

        return [
            'labels' => $days,
            'data' => $data
        ];
    }

    private function getRevenueChartData($period)
    {
        $labels = [];
        $data = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('D');
                    $data[] = Payment::whereDate('payment_date', $date)
                        ->where('status', 'completed')
                        ->sum('amount');
                }
                break;
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M d');
                    $data[] = Payment::whereDate('payment_date', $date)
                        ->where('status', 'completed')
                        ->sum('amount');
                }
                break;
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    $data[] = Payment::whereYear('payment_date', $date->year)
                        ->whereMonth('payment_date', $date->month)
                        ->where('status', 'completed')
                        ->sum('amount');
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMemberGrowthChartData($period)
    {
        $labels = [];
        $data = [];

        switch ($period) {
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M d');
                    $data[] = Member::whereDate('created_at', $date)->count();
                }
                break;
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $labels[] = $date->format('M Y');
                    $data[] = Member::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getAttendanceOverviewData($period)
    {
        $labels = [];
        $checkins = [];
        $checkouts = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('D');
                    $checkins[] = Attendance::whereDate('check_in_time', $date)->count();
                    $checkouts[] = Attendance::whereDate('check_out_time', $date)->count();
                }
                break;
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M d');
                    $checkins[] = Attendance::whereDate('check_in_time', $date)->count();
                    $checkouts[] = Attendance::whereDate('check_out_time', $date)->count();
                }
                break;
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
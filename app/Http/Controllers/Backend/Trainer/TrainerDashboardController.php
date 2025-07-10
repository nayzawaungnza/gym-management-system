<?php

namespace App\Http\Controllers\Backend\Trainer;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\ClassRegistration;
use App\Models\Attendance;
use App\Models\Member;
use App\Services\TrainerService;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainerDashboardController extends Controller
{
    protected $trainerService;

    public function __construct(TrainerService $trainerService)
    {
        $this->trainerService = $trainerService;
        $this->middleware(['auth', 'role:trainer']);
    }

    public function index()
    {
        $trainer = Auth::user()->trainer;
        
        if (!$trainer) {
            return redirect()->route('login')->with('error', 'Trainer profile not found.');
        }

        $stats = $this->getTrainerStats($trainer->id);
        $todayClasses = $this->getTodayClasses($trainer->id);
        $upcomingClasses = $this->getUpcomingClasses($trainer->id);
        $recentAttendance = $this->getRecentAttendance($trainer->id);
        
        return view('backend.trainer.dashboard', compact(
            'trainer',
            'stats',
            'todayClasses',
            'upcomingClasses',
            'recentAttendance'
        ));
    }

    public function getStats()
    {
        try {
            $trainer = Auth::user()->trainer;
            
            if (!$trainer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trainer profile not found'
                ], 404);
            }

            $stats = $this->getTrainerStats($trainer->id);
            $weeklyData = $this->getWeeklyAttendanceData($trainer->id);
            $monthlyData = $this->getMonthlyClassData($trainer->id);

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'weekly_data' => $weeklyData,
                'monthly_data' => $monthlyData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching stats: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSchedule(Request $request)
    {
        try {
            $trainer = Auth::user()->trainer;
            $startDate = $request->get('start_date', Carbon::now()->startOfWeek());
            $endDate = $request->get('end_date', Carbon::now()->endOfWeek());

            $schedule = $this->trainerService->getTrainerSchedule(
                $trainer->id,
                $startDate,
                $endDate
            );

            return response()->json([
                'success' => true,
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceChart(Request $request)
    {
        try {
            $trainer = Auth::user()->trainer;
            $period = $request->get('period', 'week');
            
            $data = $this->getAttendanceChartData($trainer->id, $period);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance chart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:gym_classes,id',
            'member_id' => 'required|exists:members,id',
            'status' => 'required|in:present,absent,late'
        ]);

        try {
            $trainer = Auth::user()->trainer;
            $class = GymClass::findOrFail($request->class_id);

            // Verify trainer owns this class
            if ($class->trainer_id !== $trainer->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to mark attendance for this class'
                ], 403);
            }

            $member = Member::findOrFail($request->member_id);
            
            // Check if member is registered for this class
            $registration = ClassRegistration::where('class_id', $class->id)
                ->where('member_id', $member->id)
                ->where('status', 'confirmed')
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member is not registered for this class'
                ], 400);
            }

            // Create or update attendance record
            $attendance = Attendance::updateOrCreate(
                [
                    'member_id' => $member->id,
                    'class_id' => $class->id,
                    'attendance_date' => Carbon::parse($class->start_date)->format('Y-m-d')
                ],
                [
                    'check_in_time' => $request->status === 'present' ? Carbon::now() : null,
                    'status' => $request->status,
                    'marked_by' => Auth::id(),
                    'notes' => $request->notes
                ]
            );

            ActivityLogHelper::log(
                'attendance',
                'marked',
                "Attendance marked for {$member->full_name} in class {$class->name} as {$request->status}",
                $attendance->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getTrainerStats($trainerId)
    {
        $today = Carbon::today();
        $thisWeek = Carbon::now()->startOfWeek();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'total_classes' => GymClass::where('trainer_id', $trainerId)->count(),
            'today_classes' => GymClass::where('trainer_id', $trainerId)
                ->whereDate('start_date', $today)
                ->count(),
            'week_classes' => GymClass::where('trainer_id', $trainerId)
                ->where('start_date', '>=', $thisWeek)
                ->count(),
            'month_classes' => GymClass::where('trainer_id', $trainerId)
                ->where('start_date', '>=', $thisMonth)
                ->count(),
            'total_students' => ClassRegistration::whereHas('gymClass', function($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })->distinct('member_id')->count(),
            'active_students' => ClassRegistration::whereHas('gymClass', function($query) use ($trainerId, $thisMonth) {
                $query->where('trainer_id', $trainerId)
                      ->where('start_date', '>=', $thisMonth);
            })->where('status', 'confirmed')->distinct('member_id')->count(),
            'completion_rate' => $this->calculateCompletionRate($trainerId),
            'average_attendance' => $this->calculateAverageAttendance($trainerId)
        ];
    }

    private function getTodayClasses($trainerId)
    {
        return GymClass::with(['classRegistrations.member'])
            ->where('trainer_id', $trainerId)
            ->whereDate('start_date', Carbon::today())
            ->orderBy('start_time')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'start_time' => $class->start_time,
                    'end_time' => $class->end_time,
                    'capacity' => $class->capacity,
                    'registered' => $class->classRegistrations->count(),
                    'location' => $class->location,
                    'status' => $class->status,
                    'members' => $class->classRegistrations->map(function ($registration) {
                        return [
                            'id' => $registration->member->id,
                            'name' => $registration->member->full_name,
                            'email' => $registration->member->email,
                            'status' => $registration->status
                        ];
                    })
                ];
            });
    }

    private function getUpcomingClasses($trainerId)
    {
        return GymClass::with(['classRegistrations'])
            ->where('trainer_id', $trainerId)
            ->where('start_date', '>', Carbon::today())
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'start_date' => $class->start_date,
                    'start_time' => $class->start_time,
                    'capacity' => $class->capacity,
                    'registered' => $class->classRegistrations->count(),
                    'location' => $class->location
                ];
            });
    }

    private function getRecentAttendance($trainerId)
    {
        return Attendance::with(['member', 'gymClass'])
            ->whereHas('gymClass', function($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'member_name' => $attendance->member->full_name,
                    'class_name' => $attendance->gymClass->name,
                    'check_in_time' => $attendance->check_in_time,
                    'check_out_time' => $attendance->check_out_time,
                    'status' => $attendance->status,
                    'duration' => $attendance->check_out_time 
                        ? Carbon::parse($attendance->check_in_time)->diffInMinutes($attendance->check_out_time) . ' mins'
                        : 'Still in'
                ];
            });
    }

    private function getWeeklyAttendanceData($trainerId)
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('D');
            
            $count = Attendance::whereHas('gymClass', function($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })->whereDate('check_in_time', $date)->count();
            
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getMonthlyClassData($trainerId)
    {
        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');
            
            $count = GymClass::where('trainer_id', $trainerId)
                ->whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->count();
            
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getAttendanceChartData($trainerId, $period)
    {
        $labels = [];
        $data = [];

        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('D');
                    $data[] = Attendance::whereHas('gymClass', function($query) use ($trainerId) {
                        $query->where('trainer_id', $trainerId);
                    })->whereDate('check_in_time', $date)->count();
                }
                break;
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $labels[] = $date->format('M d');
                    $data[] = Attendance::whereHas('gymClass', function($query) use ($trainerId) {
                        $query->where('trainer_id', $trainerId);
                    })->whereDate('check_in_time', $date)->count();
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function calculateCompletionRate($trainerId)
    {
        $totalClasses = GymClass::where('trainer_id', $trainerId)
            ->where('start_date', '<', Carbon::now())
            ->count();

        if ($totalClasses === 0) {
            return 0;
        }

        $completedClasses = GymClass::where('trainer_id', $trainerId)
            ->where('status', 'completed')
            ->count();

        return round(($completedClasses / $totalClasses) * 100, 1);
    }

    private function calculateAverageAttendance($trainerId)
    {
        $classes = GymClass::where('trainer_id', $trainerId)
            ->where('start_date', '<', Carbon::now())
            ->get();

        if ($classes->isEmpty()) {
            return 0;
        }

        $totalAttendance = 0;
        $classCount = 0;

        foreach ($classes as $class) {
            $attendanceCount = Attendance::where('class_id', $class->id)
                ->where('status', 'present')
                ->count();
            
            $totalAttendance += $attendanceCount;
            $classCount++;
        }

        return $classCount > 0 ? round($totalAttendance / $classCount, 1) : 0;
    }
}
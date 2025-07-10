<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\GymClass;
use App\Models\Payment;
use App\Models\Equipment;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('Admin')) {
            return $this->adminDashboard();
        } elseif ($user->hasRole('Trainer')) {
            return $this->trainerDashboard();
        } elseif ($user->hasRole('Member')) {
            return $this->memberDashboard();
        }
        
        return view('backend.dashboard.admin');
    }

    private function adminDashboard()
    {
        //dd('admin dashboard');
        $stats = [
            'total_members' => Member::count(),
            'active_members' => Member::active()->count(),
            'total_trainers' => Trainer::active()->count(),
            'total_classes' => GymClass::active()->count(),
            'monthly_revenue' => Payment::completed()
                ->whereMonth('payment_date', Carbon::now()->month)
                ->sum('amount'),
            'equipment_operational' => Equipment::operational()->count(),
            'equipment_maintenance' => Equipment::underMaintenance()->count(),
            'todays_attendance' => Attendance::whereDate('check_in_time', Carbon::today())->count()
        ];

        $recentMembers = Member::with('membershipType')
            ->latest()
            ->take(5)
            ->get();

        $upcomingClasses = GymClass::with('trainer')
            ->upcoming()
            ->orderBy('schedule_day')
            ->take(5)
            ->get();

        $monthlyRevenue = Payment::completed()
            ->selectRaw('MONTH(payment_date) as month, SUM(amount) as total')
            ->whereYear('payment_date', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('backend.dashboard.admin', compact(
            'stats', 
            'recentMembers', 
            'upcomingClasses', 
            'monthlyRevenue'
        ));
    }

    private function trainerDashboard()
    {
        $trainer = Trainer::where('email', auth()->user()->email)->first();
        
        if (!$trainer) {
            return redirect()->route('dashboard')->with('error', 'Trainer profile not found.');
        }

        $todaysClasses = GymClass::where('trainer_id', $trainer->id)
            ->whereDate('schedule_day', Carbon::today())
            ->with('classRegistrations.member')
            ->get();

        $upcomingClasses = GymClass::where('trainer_id', $trainer->id)
            ->upcoming()
            ->orderBy('schedule_day')
            ->take(10)
            ->get();

        $stats = [
            'total_classes' => GymClass::where('trainer_id', $trainer->id)->count(),
            'todays_classes' => $todaysClasses->count(),
            'upcoming_classes' => $upcomingClasses->count(),
            'total_students' => GymClass::where('trainer_id', $trainer->id)
                ->withCount('classRegistrations')
                ->get()
                ->sum('class_registrations_count')
        ];

        return view('backend.dashboard.trainer', compact(
            'trainer',
            'stats',
            'todaysClasses',
            'upcomingClasses'
        ));
    }

    private function memberDashboard()
    {
        $member = Member::where('email', auth()->user()->email)->first();
        
        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Member profile not found.');
        }

        $registeredClasses = $member->classRegistrations()
            ->with('gymClass.trainer')
            ->whereHas('gymClass', function($query) {
                $query->where('schedule_day', '>', Carbon::now());
            })
            ->get();

        $recentAttendance = $member->attendance()
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'membership_type' => $member->membershipType?->type_name ?? 'N/A',
            'join_date' => $member->join_date->format('M d, Y'),
            'status' => $member->status,
            'registered_classes' => $registeredClasses->count(),
            'total_visits' => $member->attendance()->count(),
            'this_month_visits' => $member->attendance()
                ->whereMonth('check_in_time', Carbon::now()->month)
                ->count()
        ];

        return view('backend.dashboard.member', compact(
            'member',
            'stats',
            'registeredClasses',
            'recentAttendance'
        ));
    }
}
<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Services\MemberService;
use App\Services\AttendanceService;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    protected $attendanceService;
    protected $memberService;

    public function __construct(AttendanceService $attendanceService, MemberService $memberService)
    {
        $this->middleware('permission:attendance-list', ['only' => ['index']]);
        $this->middleware('permission:attendance-create', ['only' => ['checkIn', 'checkOut']]);
        
        $this->attendanceService = $attendanceService;
        $this->memberService = $memberService;
    }

    public function index(Request $request)
{
    // Get the base query with filters applied from the service layer.
    $query = $this->attendanceService->getAttendanceEloquent($request);

    // Paginate the results, and ensure filter parameters are kept on pagination links.
    $attendances = $query->paginate(15)->withQueryString();

    return view('backend.attendance.index', [
        'attendances' => $attendances,
        'members' => Member::active()->get(), //
    ]);
}


    public function checkIn(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id'
        ]);

        // Check if member is already checked in
        $existingAttendance = Attendance::where('member_id', $request->member_id)
            ->whereNull('check_out_time')
            ->whereDate('check_in_time', Carbon::today())
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Member is already checked in.'
            ]);
        }

        $attendance = $this->attendanceService->checkIn($request->member_id);

        return response()->json([
            'success' => true,
            'message' => 'Member checked in successfully.',
            'data' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendance,id'
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);
        
        if ($attendance->check_out_time) {
            return response()->json([
                'success' => false,
                'message' => 'Member is already checked out.'
            ]);
        }

        $attendance = $this->attendanceService->checkOut($attendance);

        return response()->json([
            'success' => true,
            'message' => 'Member checked out successfully.',
            'data' => $attendance
        ]);
    }
}
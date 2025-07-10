<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Member;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->middleware('permission:attendance-list', ['only' => ['index']]);
        $this->middleware('permission:attendance-create', ['only' => ['checkIn', 'checkOut']]);
        
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $members = $this->memberService->getMembers($request);
            
            return DataTables::eloquent($members)
                ->addIndexColumn()
                ->addColumn('full_name', function ($member) {
                    return $member->full_name;
                })
                ->addColumn('membership_type', function ($member) {
                    return $member->membershipType?->type_name ?? 'N/A';
                })
                ->addColumn('status_badge', function ($member) {
                    $badgeClass = match($member->status) {
                        'Active' => 'success',
                        'Inactive' => 'secondary',
                        'Suspended' => 'danger',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-' . $badgeClass . '">' . $member->status . '</span>';
                })
                ->addColumn('created_at', function ($member) {
                    return $member->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('action', function ($member) {
                    $btn = '<div class="m-sm-n1">';
                    $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('members.edit', $member->id) . '" data-original-title="" title="Edit"><i class="fas fa-edit"></i><div class="ripple-container"></div></a></div>';
                    $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('members.show', $member->id) . '" data-original-title="" title="Show"><i class="fas fa-eye"></i><div class="ripple-container"></div></a></div>';
                    $btn .= '<div class="my-1 text-center"><form action="' . route('members.destroy', $member->id) . '" method="POST" id="del-member-' . $member->id . '" class="d-inline"><input type="hidden" name="_token" value="' . csrf_token() . '"><input type="hidden" name="_method" value="DELETE"><button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-member-' . $member->id . '" title="Delete"><i class="fas fa-trash"></i></button></form></div>';
                    if (auth()->user()->can('member-export')) {
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-info" href="' . route('members.export-attendance', $member->id) . '" data-original-title="" title="Export Attendance"><i class="fas fa-download"></i><div class="ripple-container"></div></a></div>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('backend.members.index');
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
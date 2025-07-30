<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\MembershipType; // Assuming this is still used directly for dropdowns
use App\Services\MemberService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CreateMemberRequest; // Import the new request
use App\Http\Requests\Member\UpdateMemberRequest; // Import the new request
use Maatwebsite\Excel\Facades\Excel; // Assuming Excel facade is available for export
use App\Exports\AttendanceExport; // Assuming this export exists

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService) // Use interface for DI
    {
        $this->middleware('permission:member-list', ['only' => ['index']]);
        $this->middleware('permission:member-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:member-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:member-delete', ['only' => ['destroy']]);
        
        $this->memberService = $memberService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $eloquent = $this->memberService->getMembersEloquent();

            return DataTables::eloquent($eloquent)
                ->addIndexColumn()
                ->addColumn('full_name', function($row) {
                    return $row->full_name;
                })
                ->addColumn('membership_status', function($row) {
                    $badgeClass = $row->status_color;
                    return '<span class="badge bg-'.$badgeClass.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('membership_type', function($row) {
                    return $row->membershipType->type_name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="m-sm-n1">';

                    // Assuming 'member-edit' permission check is handled by middleware or a gate
                    // if (auth()->user()->can('member-edit')) { // Uncomment if using Spatie Permissions
                        $btn .= '<div class="my-1 text-center">
                                    <a class="button-size btn btn-sm btn-success" href="' . route('members.edit', $row->id) . '" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                 </div>';
                    // }

                    $btn .= '<div class="my-1 text-center">
                                <a class="button-size btn btn-sm btn-primary" href="' . route('members.show', $row->id) . '" title="Show">
                                    <i class="fas fa-eye"></i>
                                </a>
                             </div>';

                    // if (auth()->user()->can('member-delete')) { // Uncomment if using Spatie Permissions
                        $btn .= '<div class="my-1 text-center">
                                    <form action="' . route('members.destroy', $row->id) . '" method="POST" id="del-member-' . $row->id . '" class="d-inline">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-origin="del-member-' . $row->id . '" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                 </div>';
                    // }

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['membership_status', 'action'])
                ->make(true);
        }

        return view('backend.members.index');
    }

    public function create()
    {
        $membershipTypes = MembershipType::active()->get(); // Direct usage as per your file
        return view('backend.members.create', compact('membershipTypes'));
    }

    public function store(CreateMemberRequest $request) // Use CreateMemberRequest
    {
        try {
            $this->memberService->createMember($request->validated());

            return redirect()->route('members.index')
                ->with('success', 'Member created successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function edit(Member $member)
    {
        $membershipTypes = MembershipType::active()->get(); // Direct usage as per your file
        return view('backend.members.edit', compact('member', 'membershipTypes'));
    }

    public function update(UpdateMemberRequest $request, Member $member) // Use UpdateMemberRequest
    {
        try {
            $this->memberService->updateMember($member, $request->validated());

            return redirect()->route('members.index')
                ->with('success', 'Member updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function destroy(Member $member)
    {
        try {
            $this->memberService->deleteMember($member);

            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    }

    public function show(Member $member)
    {
        $member->load(['membershipType', 'classRegistrations.gymClass.trainer', 'payments.paymentMethod']); // Eager load paymentMethod
        return view('backend.members.show', compact('member'));
    }

    public function exportAttendance(Request $request, Member $member)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|string'
        ]);

        $format = $request->input('format', 'xlsx');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');

        $query = $member->attendances()->with('member')
            ->when($dateFrom, function($query) use ($dateFrom) {
                return $query->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($dateTo, function($query) use ($dateTo) {
                return $query->whereDate('check_in_time', '<=', $dateTo);
            })
            ->when($status, function($query) use ($status) {
                if ($status === 'checked_in') {
                    return $query->whereNull('check_out_time');
                } elseif ($status === 'checked_out') {
                    return $query->whereNotNull('check_out_time');
                }
                return $query;
            })
            ->orderBy('check_in_time', 'desc');

        $fileName = 'attendance_' . $member->id . '_' . now()->format('YmdHis');

        if ($format === 'csv') {
            return Excel::download(new AttendanceExport($query->get()), $fileName . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new AttendanceExport($query->get()), $fileName . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipType;
use App\Services\MemberService;
use Illuminate\Http\Request;
use DataTables;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
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

                    if (auth()->user()->can('member-edit')) {
                        $btn .= '<div class="my-1 text-center">
                                    <a class="button-size btn btn-sm btn-success" href="' . route('members.edit', $row->id) . '" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                 </div>';
                    }

                    $btn .= '<div class="my-1 text-center">
                                <a class="button-size btn btn-sm btn-primary" href="' . route('members.show', $row->id) . '" title="Show">
                                    <i class="fas fa-eye"></i>
                                </a>
                             </div>';

                    if (auth()->user()->can('member-delete')) {
                        $btn .= '<div class="my-1 text-center">
                                    <form action="' . route('members.destroy', $row->id) . '" method="POST" id="del-member-' . $row->id . '" class="d-inline">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-origin="del-member-' . $row->id . '" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                 </div>';
                    }

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
        $membershipTypes = MembershipType::active()->get();
        return view('backend.members.create', compact('membershipTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:members,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:255',
            'membership_type_id' => 'required|exists:membership_types,id',
            'membership_start_date' => 'required|date',
            'membership_end_date' => 'required|date|after:membership_start_date',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended,expired'
        ]);

        $this->memberService->createMember($request->all());

        return redirect()->route('members.index')
            ->with('success', 'Member created successfully.');
    }

    public function edit(Member $member)
    {
        $membershipTypes = MembershipType::active()->get();
        return view('backend.members.edit', compact('member', 'membershipTypes'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:members,email,'.$member->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:255',
            'membership_type_id' => 'required|exists:membership_types,id',
            'membership_start_date' => 'required|date',
            'membership_end_date' => 'required|date|after:membership_start_date',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,suspended,expired'
        ]);

        $this->memberService->updateMember($member, $request->all());

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $this->memberService->deleteMember($member);

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully.'
        ]);
    }

    public function show(Member $member)
    {
        $member->load(['membershipType', 'classRegistrations.gymClass.trainer', 'payments']);
        return view('backend.members.show', compact('member'));
    }
}
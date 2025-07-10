<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\Attendance;
use App\Models\Payment;
use App\Services\MemberService;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MemberController extends Controller
{
    protected $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
        $this->middleware('permission:view_members')->only(['index', 'show']);
        $this->middleware('permission:create_members')->only(['create', 'store']);
        $this->middleware('permission:edit_members')->only(['edit', 'update']);
        $this->middleware('permission:delete_members')->only(['destroy']);
    }

    public function index(Request $request)
    {
        try {
            $members = $this->memberService->getPaginatedMembers($request);
            $membershipTypes = MembershipType::active()->get();
            
            return view('backend.members.index', compact('members', 'membershipTypes'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading members: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $membershipTypes = MembershipType::active()->get();
        return view('backend.members.create', compact('membershipTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'membership_type_id' => 'required|exists:membership_types,id',
            'membership_start_date' => 'required|date',
            'medical_conditions' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('members', 'public');
            }

            $member = $this->memberService->createMember($data);

            ActivityLogHelper::log(
                'member',
                'created',
                "Member {$member->full_name} created",
                $member->id
            );

            return redirect()->route('members.index')
                ->with('success', 'Member created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating member: ' . $e->getMessage());
        }
    }

    public function show(Member $member)
    {
        $member->load(['membershipType', 'payments', 'attendance' => function($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total_visits' => $member->attendance()->count(),
            'this_month_visits' => $member->attendance()
                ->whereMonth('check_in_time', Carbon::now()->month)
                ->count(),
            'total_payments' => $member->payments()->sum('amount'),
            'last_visit' => $member->attendance()->latest('check_in_time')->first()?->check_in_time,
            'membership_status' => $member->membership_end_date > Carbon::now() ? 'Active' : 'Expired'
        ];

        return view('backend.members.show', compact('member', 'stats'));
    }

    public function edit(Member $member)
    {
        $membershipTypes = MembershipType::active()->get();
        return view('backend.members.edit', compact('member', 'membershipTypes'));
    }

    public function update(Request $request, Member $member)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email,' . $member->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',
            'membership_type_id' => 'required|exists:membership_types,id',
            'membership_start_date' => 'required|date',
            'medical_conditions' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            $data = $request->all();
            
            // Handle file upload
            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('members', 'public');
            }

            $updatedMember = $this->memberService->updateMember($member->id, $data);

            ActivityLogHelper::log(
                'member',
                'updated',
                "Member {$updatedMember->full_name} updated",
                $updatedMember->id
            );

            return redirect()->route('members.index')
                ->with('success', 'Member updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating member: ' . $e->getMessage());
        }
    }

    public function destroy(Member $member)
    {
        try {
            $memberName = $member->full_name;
            $this->memberService->deleteMember($member->id);

            ActivityLogHelper::log(
                'member',
                'deleted',
                "Member {$memberName} deleted",
                $member->id
            );

            return redirect()->route('members.index')
                ->with('success', 'Member deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting member: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Member $member)
    {
        try {
            $newStatus = $member->status === 'active' ? 'inactive' : 'active';
            $member->update(['status' => $newStatus]);

            ActivityLogHelper::log(
                'member',
                'status_changed',
                "Member {$member->full_name} status changed to {$newStatus}",
                $member->id
            );

            return response()->json([
                'success' => true,
                'message' => "Member status updated to {$newStatus}",
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating member status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function attendance(Member $member)
    {
        $attendance = $member->attendance()
            ->with(['member'])
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);

        return view('backend.members.attendance', compact('member', 'attendance'));
    }

    public function payments(Member $member)
    {
        $payments = $member->payments()
            ->with(['membershipType'])
            ->orderBy('payment_date', 'desc')
            ->paginate(20);

        return view('backend.members.payments', compact('member', 'payments'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $members = Member::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('member_id', 'like', "%{$query}%")
            ->limit(10)
            ->get(['id', 'member_id', 'first_name', 'last_name', 'email']);

        return response()->json($members);
    }

    public function calculateEndDate(Request $request)
    {
        try {
            $membershipTypeId = $request->get('membership_type_id');
            $startDate = $request->get('start_date');

            if (!$membershipTypeId || !$startDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required parameters'
                ], 400);
            }

            $membershipType = MembershipType::find($membershipTypeId);
            if (!$membershipType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Membership type not found'
                ], 404);
            }

            $startDate = Carbon::parse($startDate);
            $endDate = $startDate->copy()->addDays($membershipType->duration_days);

            return response()->json([
                'success' => true,
                'end_date' => $endDate->format('Y-m-d'),
                'duration_days' => $membershipType->duration_days,
                'membership_type' => $membershipType->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating end date: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use Illuminate\Http\Request;
use App\Models\MembershipType;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\MembershipTypeService;
use App\Http\Requests\MembershipType\CreateMembershipTypeRequest;
use App\Http\Requests\MembershipType\UpdateMembershipTypeRequest;

class MembershipTypeController extends Controller
{
    protected $membershipTypeService;

    public function __construct(MembershipTypeService $membershipTypeService)
    {
        $this->middleware('permission:member-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:member-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:member-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:member-delete', ['only' => ['destroy']]);
        
        $this->membershipTypeService = $membershipTypeService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $membershipTypes = $this->membershipTypeService->getMembershipTypeEloquent($request->all());
                return DataTables::eloquent($membershipTypes)
                    ->addIndexColumn()
                    ->addColumn('price_formatted', function ($membershipType) {
                        return '$' . number_format($membershipType->price, 2);
                    })
                    ->addColumn('status_badge', function ($membershipType) {
                        $badgeClass = $membershipType->is_active ? 'success' : 'secondary';
                        $status = $membershipType->is_active ? 'Active' : 'Inactive';
                        return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
                    })
                    ->addColumn('action', function ($membershipType) {
                        $btn = '<div class=" m-sm-n1">';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('membershiptypes.edit', $membershipType->id) . '"
                                    data-original-title="" title="Edit">
                                    <i class="fas fa-edit"></i>
                                    <div class="ripple-container"></div>
                                    </a></div>';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('membershiptypes.show', $membershipType->id) . '"
                                    data-original-title="" title="Show">
                                    <i class="fas fa-eye"></i>
                                    <div class="ripple-container"></div>
                                </a></div>';
                        if (auth()->user()->can('member-delete')) {
                            $btn .= '<div class="my-1 text-center"><form action="' . route('membershiptypes.destroy', $membershipType->id) . '" method="POST" id="del-membershiptype-' . $membershipType->id . '" class="d-inline">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-membershiptype-' . $membershipType->id . '" title="Delete">
                                        <i class="fas fa-trash"></i>
                                        </button>                                                    
                                        </form></div>';
                        }
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('DataTables error in MembershipTypeController@index: ' . $e->getMessage(), ['exception' => $e]);
                // Return an error response that DataTables can understand
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'An error occurred while fetching data. Please check server logs.'
                ], 500);
            }
        }
        return view('backend.membershiptypes.index');
    }

    public function create()
    {
        return view('backend.membershiptypes.create');
    }

    public function store(CreateMembershipTypeRequest $request)
    {
        $this->membershipTypeService->createMembershipType($request->validated());

        return redirect()->route('membershiptypes.index')
            ->with('success', 'Membership Type created successfully.');
    }

    public function show(MembershipType $membershiptype)
    {
        return view('backend.membershiptypes.show', compact('membershiptype'));
    }

    public function edit(MembershipType $membershiptype)
    {
        return view('backend.membershiptypes.edit', compact('membershiptype'));
    }

    public function update(UpdateMembershipTypeRequest $request, MembershipType $membershiptype)
    {
        //dd($request->validated()); // Debugging line, remove in production
        $this->membershipTypeService->updateMembershipType($membershiptype, $request->validated());

        return redirect()->route('membershiptypes.index')
            ->with('success', 'Membership Type updated successfully.');
    }

    public function destroy(MembershipType $membershiptype)
    {
        // You might want to add logic here to prevent deletion if there are active members
        // associated with this membership type.
        if ($membershiptype->members()->count() > 0) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Cannot delete membership type with associated members.'
            // ]);
            return redirect()->route('membershiptypes.index')
                ->with('error', 'Cannot delete membership type with associated members.');
        }

        $this->membershipTypeService->deleteMembershipType($membershiptype);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Membership Type deleted successfully.'
        // ]);
        return redirect()->route('membershiptypes.index')
            ->with('success', 'Membership Type deleted successfully.');
    }

    public function changeStatus(Request $request, MembershipType $membershiptype)
    {
        $this->membershipTypeService->changeStatus($membershiptype);

        return response()->json([
            'success' => true,
            'message' => 'Membership Type status updated successfully.'
        ]);
    }
}
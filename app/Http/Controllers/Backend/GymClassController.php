<?php

namespace App\Http\Controllers\Backend;

use DataTables;
use App\Models\GymClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\GymClassService;
use App\Services\TrainerService; // Import TrainerServiceInterface
use App\Http\Requests\GymClass\CreateGymClassRequest;
use App\Http\Requests\GymClass\UpdateGymClassRequest;
use Illuminate\Support\Facades\Log;

class GymClassController extends Controller
{
    protected $gymClassService;
    protected $trainerService;

    public function __construct(GymClassService $gymClassService, TrainerService $trainerService)
    {
        $this->middleware('permission:class-list', ['only' => ['index', 'show']]);
        $this->middleware('permission:class-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:class-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:class-delete', ['only' => ['destroy']]);
        
        $this->gymClassService = $gymClassService;
        $this->trainerService = $trainerService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $gymClasses = $this->gymClassService->getGymClassEloquent($request->all());
                return DataTables::eloquent($gymClasses)
                    ->addIndexColumn()
                    ->addColumn('trainer_name', function ($gymClass) {
                        return $gymClass->trainer->full_name ?? 'N/A';
                    })
                    ->addColumn('schedule', function ($gymClass) {
                        return ucfirst($gymClass->schedule_day) . ' ' . \Carbon\Carbon::parse($gymClass->start_time)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($gymClass->end_time)->format('h:i A');
                    })
                    ->addColumn('capacity_display', function ($gymClass) {
                        return $gymClass->current_capacity . '/' . $gymClass->max_capacity;
                    })
                    ->addColumn('price_formatted', function ($gymClass) {
                        return '$' . number_format($gymClass->price, 2);
                    })
                    ->addColumn('status_badge', function ($gymClass) {
                        $badgeClass = $gymClass->is_active ? 'success' : 'secondary';
                        $status = $gymClass->is_active ? 'Active' : 'Inactive';
                        return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
                    })
                    ->addColumn('action', function ($gymClass) {
                        $btn = '<div class=" m-sm-n1">';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('gymclasses.edit', $gymClass->id) . '"
                                    data-original-title="" title="Edit">
                                    <i class="fas fa-edit"></i>
                                    <div class="ripple-container"></div>
                                    </a></div>';
                        $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('gymclasses.show', $gymClass->id) . '"
                                    data-original-title="" title="Show">
                                    <i class="fas fa-eye"></i>
                                    <div class="ripple-container"></div>
                                </a></div>';
                        if (auth()->user()->can('class-delete')) {
                            $btn .= '<div class="my-1 text-center"><form action="' . route('gymclasses.destroy', $gymClass->id) . '" method="POST" id="del-gymclass-' . $gymClass->id . '" class="d-inline">
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-gymclass-' . $gymClass->id . '" title="Delete">
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
                Log::error('DataTables error in GymClassController@index: ' . $e->getMessage(), ['exception' => $e]);
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'An error occurred while fetching data. Please check server logs.'
                ], 500);
            }
        }
        return view('backend.gym_classes.index');
    }

    public function create()
    {
        $trainers = $this->trainerService->getActiveTrainers();
        $scheduleDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];
        return view('backend.gym_classes.create', compact('trainers', 'scheduleDays', 'difficultyLevels'));
    }

    public function store(CreateGymClassRequest $request)
    {
        $this->gymClassService->createGymClass($request->validated());

        return redirect()->route('gymclasses.index')
            ->with('success', 'Gym Class created successfully.');
    }

    public function show(GymClass $gymclass)
    {
        $gymclass->load('trainer', 'classRegistrations.member'); // Eager load relationships for display
        return view('backend.gym_classes.show', compact('gymclass'));
    }

    public function edit(GymClass $gymclass)
    {
        $trainers = $this->trainerService->getActiveTrainers();
        $scheduleDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $difficultyLevels = ['beginner', 'intermediate', 'advanced'];
        return view('backend.gym_classes.edit', compact('gymclass', 'trainers', 'scheduleDays', 'difficultyLevels'));
    }

    public function update(UpdateGymClassRequest $request, GymClass $gymclass)
    {
        $this->gymClassService->updateGymClass($gymclass, $request->validated());

        return redirect()->route('gymclasses.index')
            ->with('success', 'Gym Class updated successfully.');
    }

    public function destroy(GymClass $gymclass)
    {
        try {
            $this->gymClassService->deleteGymClass($gymclass);
            return response()->json([
                'success' => true,
                'message' => 'Gym Class deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400); // Use 400 for client-side errors like "cannot delete with registrations"
        }
    }

    public function changeStatus(Request $request, GymClass $gymclass)
    {
        $this->gymClassService->changeStatus($gymclass);

        return response()->json([
            'success' => true,
            'message' => 'Gym Class status updated successfully.'
        ]);
    }
}
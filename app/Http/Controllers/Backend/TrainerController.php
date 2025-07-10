<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Services\TrainerService;
use Illuminate\Http\Request;
use DataTables;

class TrainerController extends Controller
{
    protected $trainerService;

    public function __construct(TrainerService $trainerService)
    {
        $this->middleware('permission:trainer-list', ['only' => ['index']]);
        $this->middleware('permission:trainer-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:trainer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:trainer-delete', ['only' => ['destroy']]);
        
        $this->trainerService = $trainerService;
    }

    public function index(Request $request)
{
    if ($request->ajax()) {
        $trainers = $this->trainerService->getTrainerElouent($request->all());
        return DataTables::eloquent($trainers)
            ->addIndexColumn()
            ->addColumn('full_name', function ($trainer) {
                return $trainer->full_name;
            })
            ->addColumn('hire_date_formatted', function ($trainer) {
                return $trainer->hire_date->format('Y-m-d H:i:s');
            })
            ->addColumn('status_badge', function ($trainer) {
                $badgeClass = $trainer->is_active ? 'success' : 'secondary';
                $status = $trainer->is_active ? 'Active' : 'Inactive';
                return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
            })
            ->addColumn('classes_count', function ($trainer) {
                return $trainer->classes()->count();
            })
            ->addColumn('action', function ($trainer) {
                $btn = '<div class=" m-sm-n1">';
                $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-success" href="' . route('trainers.edit', $trainer->id) . '"
                            data-original-title="" title="Edit">
                            <i class="fas fa-edit"></i>
                            <div class="ripple-container"></div>
                            </a></div>';
                $btn .= '<div class="my-1 text-center"><a rel="tooltip" class="button-size btn btn-sm btn-primary" href="' . route('trainers.show', $trainer->id) . '"
                            data-original-title="" title="Show">
                            <i class="fas fa-eye"></i>
                            <div class="ripple-container"></div>
                        </a></div>';
                if (auth()->user()->can('trainer-delete')) {
                    $btn .= '<div class="my-1 text-center"><form action="' . route('trainers.destroy', $trainer->id) . '" method="POST" id="del-trainer-' . $trainer->id . '" class="d-inline">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-original-title="" data-origin="del-trainer-' . $trainer->id . '" title="Delete">
                                <i class="fas fa-trash"></i>
                                </button>                                                    
                                </form></div>';
                }
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }
    return view('backend.trainers.index');
}

    public function create()
    {
        return view('backend.trainers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:trainers,email',
            'phone' => 'nullable|string|max:15',
            'specialization' => 'nullable|string|max:100',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'hire_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $this->trainerService->createTrainer($request->all());

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer created successfully.');
    }

    public function show(Trainer $trainer)
    {
        $trainer->load(['classes' => function($query) {
            $query->upcoming()->orderBy('schedule_day', 'asc');
        }]);
        
        $stats = [
            'total_classes' => $trainer->classes()->count(),
            'upcoming_classes' => $trainer->classes()->upcoming()->count(),
            'completed_classes' => $trainer->classes()->where('schedule_day', '<', now())->count(),
            'total_students' => $trainer->classes()->withCount('classRegistrations')->get()->sum('class_registrations_count')
        ];

        return view('backend.trainers.show', compact('trainer', 'stats'));
    }

    public function edit(Trainer $trainer)
    {
        return view('backend.trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|unique:trainers,email,' . $trainer->id,
            'phone' => 'nullable|string|max:15',
            'specialization' => 'nullable|string|max:100',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'hire_date' => 'required|date',
            'is_active' => 'boolean'
        ]);

        $this->trainerService->updateTrainer($trainer, $request->all());

        return redirect()->route('trainers.index')
            ->with('success', 'Trainer updated successfully.');
    }

    public function destroy(Trainer $trainer)
    {
        // Check if trainer has active classes
        $activeClasses = $trainer->classes()->upcoming()->count();
        
        if ($activeClasses > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete trainer with active classes. Please reassign or cancel classes first.'
            ]);
        }

        $this->trainerService->deleteTrainer($trainer);

        return response()->json([
            'success' => true,
            'message' => 'Trainer deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, Trainer $trainer)
    {
        $this->trainerService->changeStatus($trainer);

        return response()->json([
            'success' => true,
            'message' => 'Trainer status updated successfully.'
        ]);
    }
}
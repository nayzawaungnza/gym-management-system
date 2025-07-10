<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\GymClass;
use App\Models\Trainer;
use App\Services\ClassService;
use Illuminate\Http\Request;
use DataTables;

class ClassController extends Controller
{
    protected $classService;

    public function __construct(ClassService $classService)
    {
        $this->middleware('permission:class-list', ['only' => ['index']]);
        $this->middleware('permission:class-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:class-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:class-delete', ['only' => ['destroy']]);
        
        $this->classService = $classService;
    }

    public function index(Request $request)
{
    if ($request->ajax()) {
        $eloquent = $this->classService->getClassesEloquent();

        return DataTables::eloquent($eloquent)
            ->addIndexColumn()
            ->addColumn('trainer_name', function ($row) {
                return $row->trainer?->full_name ?? 'No Trainer';
            })
            ->addColumn('schedule_formatted', function ($row) {
                return $row->schedule_day->format('M d, Y H:i');
            })
            ->addColumn('capacity_info', function ($row) {
                return $row->current_capacity . '/' . $row->max_capacity;
            })
            ->addColumn('status_badge', function ($row) {
                $badgeClass = $row->is_active ? 'success' : 'secondary';
                $status = $row->is_active ? 'Active' : 'Inactive';
                return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="m-sm-n1">';

                if (auth()->user()->can('class-edit')) {
                    $btn .= '<div class="my-1 text-center">
                                <a class="button-size btn btn-sm btn-success" href="' . route('classes.edit', $row->id) . '" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                             </div>';
                }

                $btn .= '<div class="my-1 text-center">
                            <a class="button-size btn btn-sm btn-primary" href="' . route('classes.show', $row->id) . '" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                         </div>';

                if (auth()->user()->can('class-delete')) {
                    $btn .= '<div class="my-1 text-center">
                                <form action="' . route('classes.destroy', $row->id) . '" method="POST" id="del-class-' . $row->id . '" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="button" class="button-size btn btn-sm btn-danger destroy_btn" data-origin="del-class-' . $row->id . '" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                             </div>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    return view('backend.classes.index');
}


    public function create()
    {
        $trainers = Trainer::active()->get();
        return view('backend.classes.create', compact('trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:50',
            'trainer_id' => 'required|exists:trainers,id',
            'schedule' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'max_capacity' => 'required|integer|min:1|max:100'
        ]);

        $this->classService->createClass($request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(GymClass $class)
    {
        $trainers = Trainer::active()->get();
        return view('backend.classes.edit', compact('class', 'trainers'));
    }

    public function update(Request $request, GymClass $class)
    {
        $request->validate([
            'class_name' => 'required|string|max:50',
            'trainer_id' => 'required|exists:trainers,id',
            'schedule' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'max_capacity' => 'required|integer|min:1|max:100'
        ]);

        $this->classService->updateClass($class, $request->all());

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(GymClass $class)
    {
        $this->classService->deleteClass($class);

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully.'
        ]);
    }

    public function show(GymClass $class)
    {
        $class->load('trainer', 'classRegistrations.member');
        return view('backend.classes.show', compact('class'));
    }

    public function cancel(GymClass $class)
    {
        $this->classService->cancelClass($class);

        return response()->json([
            'success' => true,
            'message' => 'Class cancelled successfully.'
        ]);
    }
}
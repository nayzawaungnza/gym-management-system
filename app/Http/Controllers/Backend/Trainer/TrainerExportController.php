<?php

namespace App\Http\Controllers\Backend\Trainer;

use App\Http\Controllers\Controller;
use App\Exports\TrainerAttendanceExport;
use App\Exports\TrainerClassesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TrainerExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Trainer']);
    }

    public function index()
    {
        $trainer = Auth::user()->trainer;
        return view('backend.trainer.exports.index', compact('trainer'));
    }

    public function exportAttendance(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $trainer = Auth::user()->trainer;
        $filters = array_merge(
            $request->only(['date_from', 'date_to']),
            ['trainer_id' => $trainer->id]
        );

        $filename = 'trainer_attendance_' . $trainer->id . '_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new TrainerAttendanceExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportClasses(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:scheduled,completed,cancelled'
        ]);

        $trainer = Auth::user()->trainer;
        $filters = array_merge(
            $request->only(['date_from', 'date_to', 'status']),
            ['trainer_id' => $trainer->id]
        );

        $filename = 'trainer_classes_' . $trainer->id . '_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new TrainerClassesExport($filters),
            $filename . '.' . $request->format
        );
    }
}
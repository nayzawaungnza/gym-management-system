<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Exports\MembersExport;
use App\Exports\AttendanceExport;
use App\Exports\TrainersExport;
use App\Exports\PaymentsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        return view('backend.admin.exports.index');
    }

    public function exportMembers(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'status' => 'nullable|in:active,inactive,suspended',
            'membership_type' => 'nullable|exists:membership_types,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $filters = $request->only(['status', 'membership_type', 'date_from', 'date_to']);
        $filename = 'members_export_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new MembersExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportAttendance(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'member_id' => 'nullable|exists:members,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $filters = $request->only(['member_id', 'date_from', 'date_to']);
        $filename = 'attendance_export_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new AttendanceExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportTrainers(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'status' => 'nullable|in:active,inactive',
            'specialization' => 'nullable|string'
        ]);

        $filters = $request->only(['status', 'specialization']);
        $filename = 'trainers_export_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new TrainersExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportPayments(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv,pdf',
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $filters = $request->only(['status', 'payment_method', 'date_from', 'date_to']);
        $filename = 'payments_export_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new PaymentsExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function quickExport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:members,attendance,trainers,payments',
            'format' => 'required|in:xlsx,csv'
        ]);

        $filename = $request->type . '_quick_export_' . now()->format('Y_m_d_H_i_s');

        switch ($request->type) {
            case 'members':
                return Excel::download(new MembersExport([]), $filename . '.' . $request->format);
            case 'attendance':
                return Excel::download(new AttendanceExport([]), $filename . '.' . $request->format);
            case 'trainers':
                return Excel::download(new TrainersExport([]), $filename . '.' . $request->format);
            case 'payments':
                return Excel::download(new PaymentsExport([]), $filename . '.' . $request->format);
        }
    }
}
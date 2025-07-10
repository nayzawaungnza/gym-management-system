<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceExport;
use App\Exports\AttendanceSummaryExport;
use App\Exports\MembersExport;
use App\Models\Member;
use App\Models\MembershipType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:export-data');
    }

    public function index()
    {
        $membershipTypes = MembershipType::active()->get();
        $members = Member::select('id', 'first_name', 'last_name', 'email')->get();
        
        return view('backend.exports.index', compact('membershipTypes', 'members'));
    }

    public function exportAttendance(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'member_id' => 'nullable|exists:members,id',
            'status' => 'nullable|in:checked_out,still_inside'
        ]);

        $filters = $request->only(['date_from', 'date_to', 'member_id', 'status']);
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !is_null($value) && $value !== '';
        });

        $filename = 'attendance_export_' . now()->format('Y_m_d_H_i_s');
        
        if ($request->export_type === 'summary') {
            return Excel::download(
                new AttendanceSummaryExport($filters),
                $filename . '.' . $request->format
            );
        }

        return Excel::download(
            new AttendanceExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportMembers(Request $request)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'status' => 'nullable|in:Active,Inactive,Suspended',
            'join_date_from' => 'nullable|date',
            'join_date_to' => 'nullable|date|after_or_equal:join_date_from',
            'search' => 'nullable|string|max:255',
            'include_stats' => 'boolean'
        ]);

        $filters = $request->only([
            'membership_type_id', 
            'status', 
            'join_date_from', 
            'join_date_to', 
            'search'
        ]);
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !is_null($value) && $value !== '';
        });

        $includeStats = $request->boolean('include_stats', true);
        $filename = 'members_export_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new MembersExport($filters, $includeStats),
            $filename . '.' . $request->format
        );
    }

    public function exportMemberAttendance(Request $request, Member $member)
    {
        $request->validate([
            'format' => 'required|in:xlsx,csv',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $filters = array_merge(
            $request->only(['date_from', 'date_to']),
            ['member_id' => $member->id]
        );

        $filename = 'member_' . $member->id . '_attendance_' . now()->format('Y_m_d_H_i_s');

        return Excel::download(
            new AttendanceExport($filters),
            $filename . '.' . $request->format
        );
    }

    public function exportTemplate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:members,attendance',
            'format' => 'required|in:xlsx,csv'
        ]);

        $filename = $request->type . '_import_template.' . $request->format;

        if ($request->type === 'members') {
            return $this->generateMembersTemplate($filename);
        } else {
            return $this->generateAttendanceTemplate($filename);
        }
    }

    private function generateMembersTemplate(string $filename)
    {
        $headers = [
            'first_name',
            'last_name', 
            'email',
            'phone',
            'membership_type_id',
            'join_date',
            'status'
        ];

        $sampleData = [
            [
                'John',
                'Doe',
                'john.doe@example.com',
                '555-0123',
                '1',
                '2024-01-15',
                'Active'
            ],
            [
                'Jane',
                'Smith',
                'jane.smith@example.com',
                '555-0124',
                '2',
                '2024-01-20',
                'Active'
            ]
        ];

        return $this->generateTemplate($headers, $sampleData, $filename);
    }

    private function generateAttendanceTemplate(string $filename)
    {
        $headers = [
            'member_id',
            'check_in_time',
            'check_out_time'
        ];

        $sampleData = [
            [
                'member-uuid-here',
                '2024-01-15 09:00:00',
                '2024-01-15 10:30:00'
            ],
            [
                'member-uuid-here',
                '2024-01-15 18:00:00',
                '2024-01-15 19:45:00'
            ]
        ];

        return $this->generateTemplate($headers, $sampleData, $filename);
    }

    private function generateTemplate(array $headers, array $sampleData, string $filename)
    {
        $data = array_merge([$headers], $sampleData);
        
        return response()->streamDownload(function() use ($data) {
            $file = fopen('php://output', 'w');
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function quickExport(Request $request)
    {
        $request->validate([
            'type' => 'required|in:members_today,attendance_today,active_members,recent_members',
            'format' => 'required|in:xlsx,csv'
        ]);

        $filename = $request->type . '_' . now()->format('Y_m_d_H_i_s');

        switch ($request->type) {
            case 'members_today':
                $filters = ['join_date_from' => now()->format('Y-m-d')];
                return Excel::download(
                    new MembersExport($filters),
                    $filename . '.' . $request->format
                );

            case 'attendance_today':
                $filters = ['date_from' => now()->format('Y-m-d')];
                return Excel::download(
                    new AttendanceExport($filters),
                    $filename . '.' . $request->format
                );

            case 'active_members':
                $filters = ['status' => 'Active'];
                return Excel::download(
                    new MembersExport($filters),
                    $filename . '.' . $request->format
                );

            case 'recent_members':
                $filters = ['join_date_from' => now()->subDays(30)->format('Y-m-d')];
                return Excel::download(
                    new MembersExport($filters),
                    $filename . '.' . $request->format
                );
        }
    }
}

<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceSummaryExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new AttendanceDetailSheet($this->filters),
            new AttendanceSummarySheet($this->filters),
            new MemberAttendanceStatsSheet($this->filters),
        ];
    }
}

class AttendanceDetailSheet extends AttendanceExport
{
    public function title(): string
    {
        return 'Attendance Details';
    }
}

class AttendanceSummarySheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    use \Maatwebsite\Excel\Concerns\FromQuery;
    use \Maatwebsite\Excel\Concerns\WithHeadings;
    use \Maatwebsite\Excel\Concerns\WithMapping;
    use \Maatwebsite\Excel\Concerns\WithStyles;
    use \Maatwebsite\Excel\Concerns\WithColumnWidths;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Attendance::selectRaw('
                DATE(check_in_time) as date,
                COUNT(*) as total_checkins,
                COUNT(CASE WHEN check_out_time IS NOT NULL THEN 1 END) as completed_visits,
                COUNT(CASE WHEN check_out_time IS NULL THEN 1 END) as still_inside,
                AVG(CASE WHEN check_out_time IS NOT NULL THEN TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time) END) as avg_duration_minutes
            ')
            ->when($this->filters['date_from'] ?? null, function($q, $dateFrom) {
                return $q->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($this->filters['date_to'] ?? null, function($q, $dateTo) {
                return $q->whereDate('check_in_time', '<=', $dateTo);
            })
            ->groupBy('date')
            ->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Date',
            'Day of Week',
            'Total Check-ins',
            'Completed Visits',
            'Still Inside',
            'Average Duration (Minutes)',
            'Average Duration (Hours)',
            'Peak Day Indicator'
        ];
    }

    public function map($row): array
    {
        $date = \Carbon\Carbon::parse($row->date);
        $avgDurationHours = $row->avg_duration_minutes ? round($row->avg_duration_minutes / 60, 2) : 0;
        $isPeakDay = $row->total_checkins > 20 ? 'Peak Day' : 'Normal';

        return [
            $date->format('Y-m-d'),
            $date->format('l'),
            $row->total_checkins,
            $row->completed_visits,
            $row->still_inside,
            round($row->avg_duration_minutes ?? 0, 2),
            $avgDurationHours,
            $isPeakDay
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FF6B35']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, 'B' => 15, 'C' => 18, 'D' => 18, 
            'E' => 15, 'F' => 25, 'G' => 25, 'H' => 20
        ];
    }

    public function title(): string
    {
        return 'Daily Summary';
    }
}

class MemberAttendanceStatsSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    use \Maatwebsite\Excel\Concerns\FromQuery;
    use \Maatwebsite\Excel\Concerns\WithHeadings;
    use \Maatwebsite\Excel\Concerns\WithMapping;
    use \Maatwebsite\Excel\Concerns\WithStyles;
    use \Maatwebsite\Excel\Concerns\WithColumnWidths;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return Member::with(['membershipType'])
            ->withCount(['attendance as total_visits'])
            ->withCount(['attendance as visits_in_period' => function($query) {
                if (!empty($this->filters['date_from'])) {
                    $query->whereDate('check_in_time', '>=', $this->filters['date_from']);
                }
                if (!empty($this->filters['date_to'])) {
                    $query->whereDate('check_in_time', '<=', $this->filters['date_to']);
                }
            }])
            ->having('visits_in_period', '>', 0)
            ->orderBy('visits_in_period', 'desc');
    }

    public function headings(): array
    {
        return [
            'Member Name',
            'Email',
            'Membership Type',
            'Status',
            'Total Visits (All Time)',
            'Visits in Period',
            'Join Date',
            'Days Since Joined',
            'Visits per Month',
            'Activity Level'
        ];
    }

    public function map($member): array
    {
        $joinDate = \Carbon\Carbon::parse($member->join_date);
        $daysSinceJoined = $joinDate->diffInDays(now());
        $monthsSinceJoined = max(1, $joinDate->diffInMonths(now()));
        $visitsPerMonth = round($member->total_visits / $monthsSinceJoined, 2);
        
        $activityLevel = 'Low';
        if ($visitsPerMonth >= 12) $activityLevel = 'Very High';
        elseif ($visitsPerMonth >= 6) $activityLevel = 'High';
        elseif ($visitsPerMonth >= 3) $activityLevel = 'Medium';

        return [
            $member->full_name,
            $member->email,
            $member->membershipType?->type_name ?? 'N/A',
            $member->status,
            $member->total_visits,
            $member->visits_in_period,
            $member->join_date->format('Y-m-d'),
            $daysSinceJoined,
            $visitsPerMonth,
            $activityLevel
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '8E24AA']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, 'B' => 30, 'C' => 20, 'D' => 12, 'E' => 20,
            'F' => 18, 'G' => 15, 'H' => 18, 'I' => 18, 'J' => 18
        ];
    }

    public function title(): string
    {
        return 'Member Stats';
    }
}

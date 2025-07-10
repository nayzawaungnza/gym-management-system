<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $totalRecords;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Attendance::with(['member.membershipType'])
            ->when($this->filters['member_id'] ?? null, function($q, $memberId) {
                return $q->where('member_id', $memberId);
            })
            ->when($this->filters['date_from'] ?? null, function($q, $dateFrom) {
                return $q->whereDate('check_in_time', '>=', $dateFrom);
            })
            ->when($this->filters['date_to'] ?? null, function($q, $dateTo) {
                return $q->whereDate('check_in_time', '<=', $dateTo);
            })
            ->when($this->filters['status'] ?? null, function($q, $status) {
                if ($status === 'checked_out') {
                    return $q->whereNotNull('check_out_time');
                } elseif ($status === 'still_inside') {
                    return $q->whereNull('check_out_time');
                }
            })
            ->orderBy('check_in_time', 'desc');

        $this->totalRecords = $query->count();
        return $query;
    }

    public function headings(): array
    {
        return [
            'Member ID',
            'Member Name',
            'Email',
            'Membership Type',
            'Check-in Date',
            'Check-in Time',
            'Check-out Date',
            'Check-out Time',
            'Duration (Minutes)',
            'Duration (Hours)',
            'Status',
            'Day of Week',
            'Month',
            'Year'
        ];
    }

    public function map($attendance): array
    {
        $checkInTime = Carbon::parse($attendance->check_in_time);
        $checkOutTime = $attendance->check_out_time ? Carbon::parse($attendance->check_out_time) : null;
        
        $durationMinutes = null;
        $durationHours = null;
        $status = 'Still Inside';

        if ($checkOutTime) {
            $durationMinutes = $checkInTime->diffInMinutes($checkOutTime);
            $durationHours = round($durationMinutes / 60, 2);
            $status = 'Checked Out';
        }

        return [
            $attendance->member_id,
            $attendance->member?->full_name ?? 'Unknown Member',
            $attendance->member?->email ?? 'N/A',
            $attendance->member?->membershipType?->type_name ?? 'N/A',
            $checkInTime->format('Y-m-d'),
            $checkInTime->format('H:i:s'),
            $checkOutTime?->format('Y-m-d') ?? '',
            $checkOutTime?->format('H:i:s') ?? '',
            $durationMinutes ?? '',
            $durationHours ?? '',
            $status,
            $checkInTime->format('l'),
            $checkInTime->format('F'),
            $checkInTime->format('Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows styling
            'A:N' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Member ID
            'B' => 25, // Member Name
            'C' => 30, // Email
            'D' => 20, // Membership Type
            'E' => 15, // Check-in Date
            'F' => 15, // Check-in Time
            'G' => 15, // Check-out Date
            'H' => 15, // Check-out Time
            'I' => 18, // Duration (Minutes)
            'J' => 18, // Duration (Hours)
            'K' => 15, // Status
            'L' => 15, // Day of Week
            'M' => 15, // Month
            'N' => 10, // Year
        ];
    }

    public function title(): string
    {
        return 'Attendance Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Add report metadata
                $lastRow = $sheet->getHighestRow();
                $metadataRow = $lastRow + 2;
                
                $sheet->setCellValue("A{$metadataRow}", 'Report Generated:');
                $sheet->setCellValue("B{$metadataRow}", now()->format('Y-m-d H:i:s'));
                
                $sheet->setCellValue("A" . ($metadataRow + 1), 'Total Records:');
                $sheet->setCellValue("B" . ($metadataRow + 1), $this->totalRecords);
                
                $sheet->setCellValue("A" . ($metadataRow + 2), 'Filters Applied:');
                $sheet->setCellValue("B" . ($metadataRow + 2), $this->getFiltersDescription());
                
                // Style metadata
                $sheet->getStyle("A{$metadataRow}:A" . ($metadataRow + 2))->getFont()->setBold(true);
                
                // Freeze header row
                $sheet->freezePane('A2');
                
                // Auto-filter
                $sheet->setAutoFilter("A1:N{$lastRow}");
            }
        ];
    }

    private function getFiltersDescription(): string
    {
        $filters = [];
        
        if (!empty($this->filters['date_from'])) {
            $filters[] = "From: " . $this->filters['date_from'];
        }
        
        if (!empty($this->filters['date_to'])) {
            $filters[] = "To: " . $this->filters['date_to'];
        }
        
        if (!empty($this->filters['member_id'])) {
            $filters[] = "Member ID: " . $this->filters['member_id'];
        }
        
        if (!empty($this->filters['status'])) {
            $filters[] = "Status: " . ucfirst(str_replace('_', ' ', $this->filters['status']));
        }
        
        return empty($filters) ? 'None' : implode(', ', $filters);
    }
}

<?php

namespace App\Exports;

use App\Models\Member;
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
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class MembersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize, WithEvents
{
    protected $filters;
    protected $totalRecords;
    protected $includeStats;

    public function __construct(array $filters = [], bool $includeStats = true)
    {
        $this->filters = $filters;
        $this->includeStats = $includeStats;
    }

    public function query()
    {
        $query = Member::with(['membershipType', 'payments', 'attendance', 'classRegistrations'])
            ->when($this->filters['membership_type_id'] ?? null, function($q, $membershipTypeId) {
                return $q->where('membership_type_id', $membershipTypeId);
            })
            ->when($this->filters['status'] ?? null, function($q, $status) {
                return $q->where('status', $status);
            })
            ->when($this->filters['join_date_from'] ?? null, function($q, $dateFrom) {
                return $q->whereDate('join_date', '>=', $dateFrom);
            })
            ->when($this->filters['join_date_to'] ?? null, function($q, $dateTo) {
                return $q->whereDate('join_date', '<=', $dateTo);
            })
            ->when($this->filters['search'] ?? null, function($q, $search) {
                return $q->where(function($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('join_date', 'desc');

        $this->totalRecords = $query->count();
        return $query;
    }

    public function headings(): array
    {
        $headings = [
            'Member ID',
            'First Name',
            'Last Name',
            'Full Name',
            'Email',
            'Phone',
            'Membership Type',
            'Membership Price',
            'Join Date',
            'Status',
            'Days Since Joined',
            'Total Payments',
            'Total Amount Paid',
            'Last Payment Date',
            'Total Visits',
            'Last Visit Date',
            'Average Visits/Month',
            'Registered Classes',
            'Active Since (Days)',
            'Member Category'
        ];

        return $headings;
    }

    public function map($member): array
    {
        $joinDate = Carbon::parse($member->join_date);
        $daysSinceJoined = $joinDate->diffInDays(now());
        $monthsSinceJoined = max(1, $joinDate->diffInMonths(now()));
        
        // Payment statistics
        $totalPayments = $member->payments->count();
        $totalAmountPaid = $member->payments->where('status', 'Completed')->sum('amount');
        $lastPayment = $member->payments->sortByDesc('payment_date')->first();
        
        // Attendance statistics
        $totalVisits = $member->attendance->count();
        $lastVisit = $member->attendance->sortByDesc('check_in_time')->first();
        $averageVisitsPerMonth = $monthsSinceJoined > 0 ? round($totalVisits / $monthsSinceJoined, 2) : 0;
        
        // Class registrations
        $registeredClasses = $member->classRegistrations->where('status', 'Registered')->count();
        
        // Member category based on activity
        $memberCategory = $this->getMemberCategory($totalVisits, $monthsSinceJoined, $member->status);

        return [
            $member->id,
            $member->first_name,
            $member->last_name,
            $member->full_name,
            $member->email,
            $member->phone ?? 'N/A',
            $member->membershipType?->type_name ?? 'N/A',
            $member->membershipType?->price ?? 0,
            $member->join_date->format('Y-m-d'),
            $member->status,
            $daysSinceJoined,
            $totalPayments,
            number_format($totalAmountPaid, 2),
            $lastPayment?->payment_date->format('Y-m-d') ?? 'Never',
            $totalVisits,
            $lastVisit?->check_in_time->format('Y-m-d') ?? 'Never',
            $averageVisitsPerMonth,
            $registeredClasses,
            $member->status === 'Active' ? $daysSinceJoined : 0,
            $memberCategory
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
                    'startColor' => ['rgb' => '2E7D32']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            // Data rows styling
            'A:T' => [
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
            'B' => 15, // First Name
            'C' => 15, // Last Name
            'D' => 25, // Full Name
            'E' => 30, // Email
            'F' => 15, // Phone
            'G' => 20, // Membership Type
            'H' => 18, // Membership Price
            'I' => 15, // Join Date
            'J' => 12, // Status
            'K' => 18, // Days Since Joined
            'L' => 15, // Total Payments
            'M' => 18, // Total Amount Paid
            'N' => 18, // Last Payment Date
            'O' => 15, // Total Visits
            'P' => 18, // Last Visit Date
            'Q' => 20, // Average Visits/Month
            'R' => 18, // Registered Classes
            'S' => 18, // Active Since (Days)
            'T' => 18, // Member Category
        ];
    }

    public function title(): string
    {
        return 'Members Report';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Add conditional formatting for status column (J)
                $this->addStatusConditionalFormatting($sheet, $lastRow);
                
                // Add conditional formatting for member category column (T)
                $this->addCategoryConditionalFormatting($sheet, $lastRow);
                
                // Add summary statistics if enabled
                if ($this->includeStats) {
                    $this->addSummaryStatistics($sheet, $lastRow);
                }
                
                // Add report metadata
                $this->addReportMetadata($sheet, $lastRow);
                
                // Freeze header row
                $sheet->freezePane('A2');
                
                // Auto-filter
                $sheet->setAutoFilter("A1:T{$lastRow}");
            }
        ];
    }

    private function getMemberCategory($totalVisits, $monthsSinceJoined, $status): string
    {
        if ($status !== 'Active') {
            return 'Inactive';
        }

        $visitsPerMonth = $monthsSinceJoined > 0 ? $totalVisits / $monthsSinceJoined : 0;

        if ($visitsPerMonth >= 12) {
            return 'Very Active';
        } elseif ($visitsPerMonth >= 6) {
            return 'Active';
        } elseif ($visitsPerMonth >= 2) {
            return 'Moderate';
        } elseif ($visitsPerMonth > 0) {
            return 'Low Activity';
        } else {
            return 'No Activity';
        }
    }

    private function addStatusConditionalFormatting(Worksheet $sheet, int $lastRow): void
    {
        // Active status - Green
        $activeCondition = new Conditional();
        $activeCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $activeCondition->setText('Active');
        $activeCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('C8E6C9');

        // Inactive status - Red
        $inactiveCondition = new Conditional();
        $inactiveCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $inactiveCondition->setText('Inactive');
        $inactiveCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFCDD2');

        // Suspended status - Orange
        $suspendedCondition = new Conditional();
        $suspendedCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $suspendedCondition->setText('Suspended');
        $suspendedCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFE0B2');

        $sheet->getStyle("J2:J{$lastRow}")
            ->setConditionalStyles([$activeCondition, $inactiveCondition, $suspendedCondition]);
    }

    private function addCategoryConditionalFormatting(Worksheet $sheet, int $lastRow): void
    {
        // Very Active - Dark Green
        $veryActiveCondition = new Conditional();
        $veryActiveCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $veryActiveCondition->setText('Very Active');
        $veryActiveCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('A5D6A7');

        // Active - Light Green
        $activeCondition = new Conditional();
        $activeCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $activeCondition->setText('Active');
        $activeCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('C8E6C9');

        // Low Activity - Yellow
        $lowCondition = new Conditional();
        $lowCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $lowCondition->setText('Low Activity');
        $lowCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFF9C4');

        // No Activity - Red
        $noActivityCondition = new Conditional();
        $noActivityCondition->setConditionType(Conditional::CONDITION_CONTAINSTEXT);
        $noActivityCondition->setText('No Activity');
        $noActivityCondition->getStyle()->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFCDD2');

        $sheet->getStyle("T2:T{$lastRow}")
            ->setConditionalStyles([$veryActiveCondition, $activeCondition, $lowCondition, $noActivityCondition]);
    }

    private function addSummaryStatistics(Worksheet $sheet, int $lastRow): void
    {
        $statsRow = $lastRow + 3;
        
        // Summary title
        $sheet->setCellValue("A{$statsRow}", 'SUMMARY STATISTICS');
        $sheet->getStyle("A{$statsRow}")->getFont()->setBold(true)->setSize(14);
        $sheet->mergeCells("A{$statsRow}:D{$statsRow}");
        
        $statsRow += 2;
        
        // Calculate statistics
        $members = $this->query()->get();
        $totalMembers = $members->count();
        $activeMembers = $members->where('status', 'Active')->count();
        $inactiveMembers = $members->where('status', 'Inactive')->count();
        $suspendedMembers = $members->where('status', 'Suspended')->count();
        
        $totalRevenue = $members->sum(function($member) {
            return $member->payments->where('status', 'Completed')->sum('amount');
        });
        
        $averageVisits = $members->avg(function($member) {
            return $member->attendance->count();
        });

        // Add statistics
        $stats = [
            ['Total Members:', $totalMembers],
            ['Active Members:', $activeMembers],
            ['Inactive Members:', $inactiveMembers],
            ['Suspended Members:', $suspendedMembers],
            ['Total Revenue:', '$' . number_format($totalRevenue, 2)],
            ['Average Visits per Member:', round($averageVisits, 2)],
            ['Active Member Percentage:', round(($activeMembers / max($totalMembers, 1)) * 100, 2) . '%']
        ];

        foreach ($stats as $index => $stat) {
            $currentRow = $statsRow + $index;
            $sheet->setCellValue("A{$currentRow}", $stat[0]);
            $sheet->setCellValue("B{$currentRow}", $stat[1]);
            $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
        }
    }

    private function addReportMetadata(Worksheet $sheet, int $lastRow): void
    {
        $metadataRow = $lastRow + 12;
        
        $sheet->setCellValue("A{$metadataRow}", 'Report Generated:');
        $sheet->setCellValue("B{$metadataRow}", now()->format('Y-m-d H:i:s'));
        
        $sheet->setCellValue("A" . ($metadataRow + 1), 'Total Records:');
        $sheet->setCellValue("B" . ($metadataRow + 1), $this->totalRecords);
        
        $sheet->setCellValue("A" . ($metadataRow + 2), 'Filters Applied:');
        $sheet->setCellValue("B" . ($metadataRow + 2), $this->getFiltersDescription());
        
        // Style metadata
        $sheet->getStyle("A{$metadataRow}:A" . ($metadataRow + 2))->getFont()->setBold(true);
    }

    private function getFiltersDescription(): string
    {
        $filters = [];
        
        if (!empty($this->filters['membership_type_id'])) {
            $filters[] = "Membership Type ID: " . $this->filters['membership_type_id'];
        }
        
        if (!empty($this->filters['status'])) {
            $filters[] = "Status: " . $this->filters['status'];
        }
        
        if (!empty($this->filters['join_date_from'])) {
            $filters[] = "Join Date From: " . $this->filters['join_date_from'];
        }
        
        if (!empty($this->filters['join_date_to'])) {
            $filters[] = "Join Date To: " . $this->filters['join_date_to'];
        }
        
        if (!empty($this->filters['search'])) {
            $filters[] = "Search: " . $this->filters['search'];
        }
        
        return empty($filters) ? 'None' : implode(', ', $filters);
    }
}

<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TrainerAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with(['member', 'gymClass'])
            ->whereHas('member.classRegistrations.gymClass', function($q) {
                $q->where('trainer_id', $this->filters['trainer_id']);
            });

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('check_in_time', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('check_in_time', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('check_in_time', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Member Name',
            'Member Email',
            'Class Name',
            'Check In Time',
            'Check Out Time',
            'Duration (minutes)',
            'Check In Method',
            'Date'
        ];
    }

    public function map($attendance): array
    {
        $duration = null;
        if ($attendance->check_out_time) {
            $duration = $attendance->check_in_time->diffInMinutes($attendance->check_out_time);
        }

        return [
            $attendance->member->full_name,
            $attendance->member->email,
            $attendance->gymClass->class_name ?? 'General Access',
            $attendance->check_in_time->format('Y-m-d H:i:s'),
            $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : 'Still checked in',
            $duration,
            ucfirst(str_replace('_', ' ', $attendance->check_in_method)),
            $attendance->check_in_time->format('Y-m-d')
        ];
    }
}

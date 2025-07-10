<?php

namespace App\Exports;

use App\Models\GymClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TrainerClassesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = GymClass::with(['classRegistrations.member'])
            ->where('trainer_id', $this->filters['trainer_id']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('start_time', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('start_time', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('start_time', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Class Name',
            'Description',
            'Start Time',
            'End Time',
            'Max Participants',
            'Registered Participants',
            'Status',
            'Location',
            'Notes',
            'Created At'
        ];
    }

    public function map($class): array
    {
        return [
            $class->class_name,
            $class->description,
            $class->start_time->format('Y-m-d H:i:s'),
            $class->end_time->format('Y-m-d H:i:s'),
            $class->max_participants,
            $class->classRegistrations->where('status', 'registered')->count(),
            ucfirst($class->status),
            $class->location,
            $class->notes,
            $class->created_at->format('Y-m-d H:i:s')
        ];
    }
}

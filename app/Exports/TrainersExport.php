<?php

namespace App\Exports;

use App\Models\Trainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TrainersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Trainer::with(['user', 'gymClasses']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['specialization'])) {
            $query->where('specialization', 'like', '%' . $this->filters['specialization'] . '%');
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Specialization',
            'Experience Years',
            'Hourly Rate',
            'Status',
            'Total Classes',
            'Hire Date',
            'Created At'
        ];
    }

    public function map($trainer): array
    {
        return [
            $trainer->id,
            $trainer->full_name,
            $trainer->email,
            $trainer->phone,
            $trainer->specialization,
            $trainer->experience_years,
            '$' . number_format($trainer->hourly_rate, 2),
            ucfirst($trainer->status),
            $trainer->gymClasses->count(),
            $trainer->hire_date ? $trainer->hire_date->format('Y-m-d') : '',
            $trainer->created_at->format('Y-m-d H:i:s')
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Payment::with('member');

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }

        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Payment ID',
            'Member Name',
            'Member Email',
            'Amount',
            'Payment Method',
            'Status',
            'Description',
            'Transaction ID',
            'Payment Date',
            'Created At'
        ];
    }

    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->member->full_name ?? 'N/A',
            $payment->member->email ?? 'N/A',
            '$' . number_format($payment->amount, 2),
            ucfirst(str_replace('_', ' ', $payment->payment_method)),
            ucfirst($payment->status),
            $payment->description,
            $payment->transaction_id,
            $payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : '',
            $payment->created_at->format('Y-m-d H:i:s')
        ];
    }
}

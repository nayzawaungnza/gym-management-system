<?php
namespace App\Repositories\Backend;
use App\Models\Payment;
use App\Repositories\BaseRepository;
class PaymentRepository extends BaseRepository
{
    public function model()
    {
        return Payment::class;
    }

    public function getPaymentsEloquent()
    {
        return $this->model->query()
            ->with(['member', 'class'])
            ->orderBy('created_at', 'desc');
    }

    public function getById($id, $with = [])
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        $payment = $this->model->create([
            'member_id' => $data['member_id'],
            'membership_type_id' => $data['membership_type_id'] ?? null,
            'class_id' => $data['class_id'],
            'transaction_id' => $data['transaction_id'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'pending',
            'payment_date' => $data['payment_date'] ?? now(),
            'payment_method' => $data['payment_method'] ?? 'cash',
        ]);
        return $payment;
    }

    public function update(Payment $payment, array $data)
    {
        $payment->update($data);
        return $payment;
    }

    public function markCompleted(Payment $payment)
    {
        $payment->status = 'completed';
        $payment->save();
        return $payment;
    }

    public function getMemberPayments($memberId)
    {
        return $this->model->where('member_id', $memberId)->get();
    }

    public function getMonthlyRevenue(Payment $payment, $year = null, $month = null)
    {
        // Implement logic to calculate monthly revenue
        // This is a placeholder implementation
        return 0;
    }
}
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

    public function getPaymentsEloquent($request = null)
    {
        $query = $this->model->with(['member', 'membershipType', 'classRegistration', 'paymentMethod'])
            ->orderBy('created_at', 'desc');

        if ($request) {
            if ($request->has('member_id')) {
                $query->where('member_id', $request->input('member_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->has('payment_method_id')) {
                $query->where('payment_method_id', $request->input('payment_method_id'));
            }

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }

        return $query;
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
            'class_registration_id' => $data['class_registration_id'] ?? null,
            'payment_method_id' => $data['payment_method_id'] ?? null,
            'class_id' => $data['class_id'],
            'transaction_id' => $data['transaction_id'],
            'amount' => $data['amount'],
            'status' => $data['status'] ?? 'pending',
            'payment_date' => $data['payment_date'] ?? now(),
            //'payment_method' => $data['payment_method'] ?? 'cash',
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
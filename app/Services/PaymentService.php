<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\Backend\PaymentRepository;
use App\Services\Interfaces\PaymentServiceInterface;
use Illuminate\Http\Request;

class PaymentService implements PaymentServiceInterface
{
    protected $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getPaymentEloquent()
    {
        return $this->paymentRepository->getPaymentsEloquent();
    }

    public function getPayment(Payment $payment)
    {
        return $this->paymentRepository->getById($payment->id);
    }

    public function createPayment(array $data)
    {
        return $this->paymentRepository->create($data);
    }

    public function updatePayment(Payment $payment, array $data)
    {
        return $this->paymentRepository->update($payment, $data);
    }

    public function markCompleted(Payment $payment)
    {
        return $this->paymentRepository->markCompleted($payment);
    }

    public function getMemberPayments(Payment $payment,$memberId)
    {
        return $this->paymentRepository->getMemberPayments($memberId);
    }

    public function getMonthlyRevenue(Payment $payment, $year = null, $month = null)
    {
        return $this->paymentRepository->getMonthlyRevenue($year, $month);
    }
}
<?php

namespace App\Services\Interfaces;

use App\Models\Payment;

interface PaymentServiceInterface
{
    public function getPaymentEloquent();
public function getPayment(Payment $payment);
public function createPayment( array $data);
public function updatePayment(Payment $payment, array $data);
public function markCompleted(Payment $payment);
public function getMemberPayments(Payment $payment, string $memberId);
public function getMonthlyRevenue(Payment $payment,  $year = null,  $month = null);
}
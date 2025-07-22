<?php

namespace App\Services\Interfaces;

use App\Models\Payment;
use Illuminate\Http\Request;

interface PaymentServiceInterface
{
    public function getPaymentsEloquent(Request $request);
public function getPayment(Payment $payment);
public function createPayment( array $data);
public function updatePayment(Payment $payment, array $data);
public function markCompleted(Payment $payment);
public function getMemberPayments(Payment $payment, string $memberId);
public function getMonthlyRevenue(Payment $payment,  $year = null,  $month = null);
}
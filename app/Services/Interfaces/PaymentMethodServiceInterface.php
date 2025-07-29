<?php

namespace App\Services\Interfaces;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PaymentMethodServiceInterface
{
    /**
     * Get payment methods Eloquent query with filters for DataTables.
     */
    public function getPaymentMethodEloquent(array $filters = []);

    /**
     * Create a new payment method.
     */
    public function createPaymentMethod(array $data): PaymentMethod;

    /**
     * Update an existing payment method.
     */
    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): PaymentMethod;

    /**
     * Delete a payment method.
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool;

    /**
     * Change payment method status.
     */
    public function changeStatus(PaymentMethod $paymentMethod): PaymentMethod;

    /**
     * Get all payment methods with filters.
     */
    public function getAllPaymentMethods(array $filters = []): Collection;

    /**
     * Get payment method by ID.
     */
    public function getPaymentMethodById(string $id): ?PaymentMethod;

    /**
     * Get paginated payment methods with filters.
     */
    public function getPaginatedPaymentMethods(Request $request): LengthAwarePaginator;

    /**
     * Get active payment methods for dropdowns, etc.
     */
    public function getActivePaymentMethods(): Collection;
}

<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Repositories\Backend\PaymentMethodRepository;
use App\Services\Interfaces\PaymentMethodServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PaymentMethodService implements PaymentMethodServiceInterface
{
    protected $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function getPaymentMethodEloquent(array $filters = [])
    {
        return $this->paymentMethodRepository->getPaymentMethodEloquent($filters);
    }

    public function createPaymentMethod(array $data): PaymentMethod
    {
        try {
            return $this->paymentMethodRepository->createPaymentMethod($data);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService createPaymentMethod: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        try {
            return $this->paymentMethodRepository->updatePaymentMethod($paymentMethod, $data);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService updatePaymentMethod: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id, 'data' => $data]);
            throw $e;
        }
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        try {
            return $this->paymentMethodRepository->deletePaymentMethod($paymentMethod);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService deletePaymentMethod: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id]);
            throw $e;
        }
    }

    public function changeStatus(PaymentMethod $paymentMethod): PaymentMethod
    {
        try {
            return $this->paymentMethodRepository->changeStatus($paymentMethod);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService changeStatus: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id]);
            throw $e;
        }
    }

    public function getAllPaymentMethods(array $filters = []): Collection
    {
        try {
            return $this->paymentMethodRepository->getAllPaymentMethods($filters);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService getAllPaymentMethods: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPaymentMethodById(string $id): ?PaymentMethod
    {
        try {
            return $this->paymentMethodRepository->getPaymentMethodById($id);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService getPaymentMethodById: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function getPaginatedPaymentMethods(Request $request): LengthAwarePaginator
    {
        try {
            return $this->paymentMethodRepository->getPaginatedPaymentMethods($request);
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService getPaginatedPaymentMethods: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getActivePaymentMethods(): Collection
    {
        try {
            return $this->paymentMethodRepository->getActivePaymentMethods();
        } catch (\Exception $e) {
            Log::error('Error in PaymentMethodService getActivePaymentMethods: ' . $e->getMessage());
            throw $e;
        }
    }
}

<?php

namespace App\Repositories\Backend;

use App\Models\PaymentMethod;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class PaymentMethodRepository extends BaseRepository
{
    public function model()
    {
        return PaymentMethod::class;
    }

    public function getPaymentMethodEloquent(array $filters = [])
    {
        $query = PaymentMethod::query()
            ->orderBy('created_at', 'desc');

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('display_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('provider_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('method_name', 'like', '%' . $searchTerm . '%');
            });
        }
        return $query;
    }

    public function getPaymentMethodById(string $id): ?PaymentMethod
    {
        return $this->getById($id);
    }

    public function createPaymentMethod(array $data): PaymentMethod
    {
        DB::beginTransaction();
        try {
            $path = "payment_methods";
            $paymentLogoPath = null;

            if (isset($data['payment_logo']) && $data['payment_logo']) {
                $paymentLogoPath = $this->uploadFile($data['payment_logo'], $path);
            }

            $paymentMethod = PaymentMethod::create([
                'display_name' => $data['display_name'],
                'provider_name' => $data['provider_name'] ?? null,
                'method_name' => $data['method_name'] ?? null,
                'expire_minutes' => $data['expire_minutes'] ?? 0,
                'payment_logo' => $paymentLogoPath,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $activity_data['subject'] = $paymentMethod;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Payment Method (%s) was created.', $paymentMethod->display_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $paymentMethod;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create payment method: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function updatePaymentMethod(PaymentMethod $paymentMethod, array $data): PaymentMethod
    {
        DB::beginTransaction();
        try {
            $path = "payment_methods";
            $paymentLogoPath = $paymentMethod->payment_logo;

            // Handle payment logo update/removal
            if (isset($data['payment_logo']) && $data['payment_logo']) {
                if ($paymentMethod->payment_logo) {
                    $this->deleteFile($paymentMethod->payment_logo);
                }
                $paymentLogoPath = $this->uploadFile($data['payment_logo'], $path);
            } elseif (array_key_exists('payment_logo', $data) && $data['payment_logo'] === null) {
                if ($paymentMethod->payment_logo) {
                    $this->deleteFile($paymentMethod->payment_logo);
                }
                $paymentLogoPath = null;
            }

            $paymentMethod->update([
                'display_name' => $data['display_name'] ?? $paymentMethod->display_name,
                'provider_name' => $data['provider_name'] ?? $paymentMethod->provider_name,
                'method_name' => $data['method_name'] ?? $paymentMethod->method_name,
                'expire_minutes' => $data['expire_minutes'] ?? $paymentMethod->expire_minutes,
                'payment_logo' => $paymentLogoPath,
                'is_active' => $data['is_active'] ?? $paymentMethod->is_active,
            ]);

            $activity_data['subject'] = $paymentMethod->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Payment Method (%s) was updated.', $paymentMethod->display_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $paymentMethod;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update payment method: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id, 'data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        DB::beginTransaction();
        try {
            // Delete payment logo file from storage if it exists
            if ($paymentMethod->payment_logo) {
                $this->deleteFile($paymentMethod->payment_logo);
            }

            $deleted = $this->deleteById($paymentMethod->id);

            $activity_data['subject'] = $paymentMethod;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $activity_data['description'] = sprintf('Payment Method (%s) was deleted.', $paymentMethod->display_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete payment method: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function changeStatus(PaymentMethod $paymentMethod): PaymentMethod
    {
        DB::beginTransaction();
        try {
            $paymentMethod->is_active = !$paymentMethod->is_active;
            $paymentMethod->save();

            $activity_data['subject'] = $paymentMethod->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Payment Method (%s) status changed to %s.', $paymentMethod->display_name, $paymentMethod->is_active ? 'Active' : 'Inactive');
            saveActivityLog($activity_data);

            DB::commit();
            return $paymentMethod;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to change payment method status: ' . $e->getMessage(), ['payment_method_id' => $paymentMethod->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function getAllPaymentMethods(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where('display_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('provider_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('method_name', 'like', '%' . $searchTerm . '%');
        }

        return $query->get();
    }

    public function getPaginatedPaymentMethods(\Illuminate\Http\Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active') === 'active');
        }
        if ($request->has('search') && isset($request->input('search')['value']) && !empty($request->input('search')['value'])) {
            $searchTerm = $request->input('search')['value'];
            $query->where('display_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('provider_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('method_name', 'like', '%' . $searchTerm . '%');
        }

        return $query->paginate($request->input('per_page', 25));
    }

    public function getActivePaymentMethods(): \Illuminate\Database\Eloquent\Collection
    {
        return PaymentMethod::where('is_active', true)->get();
    }

    private function uploadFile($file, string $path): ?string
    {
        if ($file && $file->isValid()) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = Storage::disk('public')->putFileAs($path, $file, $filename);
            if ($filePath) {
                return $filePath;
            }
        }
        return null;
    }

    private function deleteFile(?string $filePath): bool
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        return false;
    }
}

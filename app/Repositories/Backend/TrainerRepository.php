<?php

namespace App\Repositories\Backend;

use App\Models\Trainer;
use App\Models\User; // Import User model (only for type hinting if needed, not for direct manipulation here)
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // For Str::random
use Exception;

class TrainerRepository extends BaseRepository
{
    public function model()
    {
        return Trainer::class;
    }

    public function getTrainerEloquent(array $filters = []) // Corrected typo and added filters
    {
        $query = Trainer::query()
            ->select('trainers.*')
            ->with(['classes', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters if any
        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }
        if (!empty($filters['specialization'])) {
            $query->where('specialization', 'like', "%{$filters['specialization']}%");
        }
        // Add more filters as needed for DataTables
        return $query;
    }

    public function getTrainerById(string $id): ?Trainer
    {
        return $this->getById($id);
    }

    public function createTrainer(array $data): Trainer
    {
        // This method is now called by TrainerService after User creation.
        // It expects 'user_id' to be present in $data.
        DB::beginTransaction();
        try {
            $path = "trainers";
            $profilePhotoPath = null;

            // If profile_photo is passed from the service (which got it from user creation)
            if (isset($data['profile_photo']) && $data['profile_photo']) {
                $profilePhotoPath = $data['profile_photo']; // Already uploaded by UserRepository
            }

            $trainer = Trainer::create([
                'user_id' => $data['user_id'], // Use the created user's ID
                'trainer_id' => $data['trainer_id'], // Generated in service
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'specialization' => $data['specialization'] ?? null,
                'certifications' => $data['certifications'] ?? [],
                'hourly_rate' => $data['hourly_rate'] ?? 0.00,
                'hire_date' => $data['hire_date'],
                'bio' => $data['bio'] ?? null,
                'profile_photo' => $profilePhotoPath,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Save activity log (assuming saveActivityLog is a global helper or trait)
            $activity_data['subject'] = $trainer;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Trainer (%s) was created.', $trainer->full_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $trainer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create trainer profile: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function updateTrainer(Trainer $trainer, array $data): Trainer
    {
        DB::beginTransaction();
        try {
            $path = "trainers";
            $profilePhotoPath = $trainer->profile_photo;

            // Handle profile photo update/removal
            if (isset($data['profile_photo']) && $data['profile_photo']) {
                if ($trainer->profile_photo) {
                    $this->deleteFile($trainer->profile_photo);
                }
                $profilePhotoPath = $this->uploadFile($data['profile_photo'], $path);
            } elseif (array_key_exists('profile_photo', $data) && $data['profile_photo'] === null) {
                if ($trainer->profile_photo) {
                    $this->deleteFile($trainer->profile_photo);
                }
                $profilePhotoPath = null;
            }

            $trainer->update([
                'first_name' => $data['first_name'] ?? $trainer->first_name,
                'last_name' => $data['last_name'] ?? $trainer->last_name,
                'email' => $data['email'] ?? $trainer->email,
                'phone' => $data['phone'] ?? $trainer->phone,
                'specialization' => $data['specialization'] ?? $trainer->specialization,
                'certifications' => $data['certifications'] ?? $trainer->certifications,
                'hourly_rate' => $data['hourly_rate'] ?? $trainer->hourly_rate,
                'hire_date' => $data['hire_date'] ?? $trainer->hire_date,
                'bio' => $data['bio'] ?? $trainer->bio,
                'profile_photo' => $profilePhotoPath,
                'is_active' => $data['is_active'] ?? $trainer->is_active,
            ]);

            // Save activity log
            $activity_data['subject'] = $trainer->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Trainer (%s) was updated.', $trainer->full_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $trainer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update trainer profile: ' . $e->getMessage(), ['trainer_id' => $trainer->id, 'data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function deleteTrainer(Trainer $trainer): bool
    {
        DB::beginTransaction();
        try {
            // Delete profile photo file from storage if it exists
            if ($trainer->profile_photo) {
                $this->deleteFile($trainer->profile_photo);
            }

            // Delete the trainer record
            $deleted = $this->deleteById($trainer->id);

            // Save activity log
            $activity_data['subject'] = $trainer;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $activity_data['description'] = sprintf('Trainer (%s) was deleted.', $trainer->full_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete trainer: ' . $e->getMessage(), ['trainer_id' => $trainer->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function changeStatus(Trainer $trainer): Trainer
    {
        DB::beginTransaction();
        try {
            $trainer->is_active = !$trainer->is_active;
            $trainer->save();

            // Save activity log
            $activity_data['subject'] = $trainer->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Trainer (%s) status changed to %s.', $trainer->full_name, $trainer->is_active ? 'Active' : 'Inactive');
            saveActivityLog($activity_data);

            DB::commit();
            return $trainer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to change trainer status: ' . $e->getMessage(), ['trainer_id' => $trainer->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function getAllTrainers(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        if (!empty($filters['specialization'])) {
            $query->where('specialization', 'like', "%{$filters['specialization']}%");
        }

        return $query->get();
    }

    public function getPaginatedTrainers(Request $request): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->has('specialization')) {
            $query->where('specialization', 'like', "%{$request->input('specialization')}%");
        }

        return $query->paginate($request->input('per_page', 25));
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
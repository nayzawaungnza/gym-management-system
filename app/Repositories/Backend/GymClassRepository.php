<?php

namespace App\Repositories\Backend;

use App\Models\GymClass;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class GymClassRepository extends BaseRepository
{
    public function model()
    {
        return GymClass::class;
    }

    public function getGymClassEloquent(array $filters = [])
    {
        $query = GymClass::query()
            ->with('trainer') // Eager load trainer to display trainer name
            ->orderBy('created_at', 'desc');

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }
        if (!empty($filters['class_type'])) {
            $query->where('class_type', $filters['class_type']);
        }
        if (!empty($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }
        if (!empty($filters['trainer_id'])) {
            $query->where('trainer_id', $filters['trainer_id']);
        }
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('class_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('room', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('trainer', function ($trainerQuery) use ($searchTerm) {
                      $trainerQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }
        return $query;
    }

    public function getGymClassById(string $id): ?GymClass
    {
        return $this->getById($id);
    }

    public function createGymClass(array $data): GymClass
    {
        DB::beginTransaction();
        try {
            $gymClass = GymClass::create([
                'trainer_id' => $data['trainer_id'] ?? null,
                'class_name' => $data['class_name'],
                'description' => $data['description'] ?? null,
                'class_type' => $data['class_type'] ?? null,
                'schedule_day' => $data['schedule_day'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration_minutes' => $data['duration_minutes'],
                'max_capacity' => $data['max_capacity'],
                'current_capacity' => 0, // Always start at 0 for new classes
                'price' => $data['price'] ?? 0.00,
                'room' => $data['room'] ?? null,
                'equipment_needed' => $data['equipment_needed'] ?? null,
                'difficulty_level' => $data['difficulty_level'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $activity_data['subject'] = $gymClass;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Gym Class (%s) was created.', $gymClass->class_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $gymClass;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create gym class: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function updateGymClass(GymClass $gymClass, array $data): GymClass
    {
        DB::beginTransaction();
        try {
            $gymClass->update([
                'trainer_id' => $data['trainer_id'] ?? $gymClass->trainer_id,
                'class_name' => $data['class_name'] ?? $gymClass->class_name,
                'description' => $data['description'] ?? $gymClass->description,
                'class_type' => $data['class_type'] ?? $gymClass->class_type,
                'schedule_day' => $data['schedule_day'] ?? $gymClass->schedule_day,
                'start_time' => $data['start_time'] ?? $gymClass->start_time,
                'end_time' => $data['end_time'] ?? $gymClass->end_time,
                'duration_minutes' => $data['duration_minutes'] ?? $gymClass->duration_minutes,
                'max_capacity' => $data['max_capacity'] ?? $gymClass->max_capacity,
                // current_capacity is not updated directly via form
                'price' => $data['price'] ?? $gymClass->price,
                'room' => $data['room'] ?? $gymClass->room,
                'equipment_needed' => $data['equipment_needed'] ?? $gymClass->equipment_needed,
                'difficulty_level' => $data['difficulty_level'] ?? $gymClass->difficulty_level,
                'is_active' => $data['is_active'] ?? $gymClass->is_active,
            ]);

            $activity_data['subject'] = $gymClass->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Gym Class (%s) was updated.', $gymClass->class_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $gymClass;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update gym class: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id, 'data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function deleteGymClass(GymClass $gymClass): bool
    {
        DB::beginTransaction();
        try {
            // Check for active registrations before deleting
            if ($gymClass->classRegistrations()->count() > 0) {
                throw new Exception('Cannot delete gym class with existing registrations. Please cancel or reassign members first.');
            }

            $deleted = $this->deleteById($gymClass->id);

            $activity_data['subject'] = $gymClass;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $activity_data['description'] = sprintf('Gym Class (%s) was deleted.', $gymClass->class_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete gym class: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function changeStatus(GymClass $gymClass): GymClass
    {
        DB::beginTransaction();
        try {
            $gymClass->is_active = !$gymClass->is_active;
            $gymClass->save();

            $activity_data['subject'] = $gymClass->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Gym Class (%s) status changed to %s.', $gymClass->class_name, $gymClass->is_active ? 'Active' : 'Inactive');
            saveActivityLog($activity_data);

            DB::commit();
            return $gymClass;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to change gym class status: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function getAllGymClasses(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active'] === 'active');
        }
        if (!empty($filters['class_type'])) {
            $query->where('class_type', $filters['class_type']);
        }
        if (!empty($filters['difficulty_level'])) {
            $query->where('difficulty_level', $filters['difficulty_level']);
        }
        if (!empty($filters['trainer_id'])) {
            $query->where('trainer_id', $filters['trainer_id']);
        }
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where('class_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('room', 'like', '%' . $searchTerm . '%');
        }

        return $query->get();
    }

    public function getPaginatedGymClasses(\Illuminate\Http\Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->input('is_active') === 'active');
        }
        if ($request->has('class_type')) {
            $query->where('class_type', $request->input('class_type'));
        }
        if ($request->has('difficulty_level')) {
            $query->where('difficulty_level', $request->input('difficulty_level'));
        }
        if ($request->has('trainer_id')) {
            $query->where('trainer_id', $request->input('trainer_id'));
        }
        if ($request->has('search') && isset($request->input('search')['value']) && !empty($request->input('search')['value'])) {
            $searchTerm = $request->input('search')['value'];
            $query->where('class_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('room', 'like', '%' . $searchTerm . '%');
        }

        return $query->paginate($request->input('per_page', 25));
    }

    public function getActiveGymClasses(): \Illuminate\Database\Eloquent\Collection
    {
        return GymClass::where('is_active', true)->get();
    }
}

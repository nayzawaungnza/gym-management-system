<?php

namespace App\Repositories\Backend;

use App\Models\Trainer;
use App\Repositories\BaseRepository;

class TrainerRepository extends BaseRepository
{
    public function model()
    {
        return Trainer::class;
    }

    public function getTrainersEloquent()
    {
        return Trainer::select('trainers.*');
    }

    public function getTrainerElouent()
    {
        return Trainer::query()
            ->select('trainers.*')
            ->with(['classes', 'user'])
            ->orderBy('created_at', 'desc');
    }

    public function getAllTrainers($filters = [])
    {
        $query = $this->model->newQuery();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['specialization'])) {
            $query->where('specializations', 'like', "%{$filters['specialization']}%");
        }

        return $query->get();
    }

    public function getTrainerById($id)
    {
        return $this->getById($id);
    }

    public function createTrainer(array $data)
    {
        $trainer = Trainer::create([
            'trainer_id' => $data['trainer_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'specializations' => $data['specializations'] ?? null,
            'certifications' => $data['certifications'] ?? null,
            'hourly_rate' => $data['hourly_rate'] ?? 50,
            'status' => $data['status'] ?? 'active',
            'hire_date' => $data['hire_date'],
        ]);

        // Save activity log
        $activity_data['subject'] = $trainer;
        $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Trainer (%s) was created.', $trainer->name);
        saveActivityLog($activity_data);

        return $trainer;
    }

    public function updateTrainer($id, array $data)
    {
        $trainer = $this->getById($id);

        $trainer->update([
            'name' => $data['name'] ?? $trainer->name,
            'email' => $data['email'] ?? $trainer->email,
            'phone' => $data['phone'] ?? $trainer->phone,
            'specializations' => $data['specializations'] ?? $trainer->specializations,
            'certifications' => $data['certifications'] ?? $trainer->certifications,
            'hourly_rate' => $data['hourly_rate'] ?? $trainer->hourly_rate,
            'status' => $data['status'] ?? $trainer->status,
            'hire_date' => $data['hire_date'] ?? $trainer->hire_date,
        ]);

        // Save activity log
        $activity_data['subject'] = $trainer->refresh();
        $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
        $activity_data['description'] = sprintf('Trainer (%s) was updated.', $trainer->name);
        saveActivityLog($activity_data);

        return $trainer;
    }

    public function deleteTrainer($id)
    {
        return $this->deleteById($id);
    }

    public function getPaginatedTrainers(Request $request): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('specialization')) {
            $query->where('specializations', 'like', "%{$request->input('specialization')}%");
        }

        return $query->paginate($request->input('per_page', 25));
    }
}
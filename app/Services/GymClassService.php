<?php

namespace App\Services;

use App\Models\GymClass;
use App\Repositories\Backend\GymClassRepository;
use App\Services\Interfaces\GymClassServiceInterface;
use App\Services\TrainerService; // Import TrainerServiceInterface
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GymClassService implements GymClassServiceInterface
{
    protected $gymClassRepository;
    protected $trainerService; // Declare TrainerService

    public function __construct(GymClassRepository $gymClassRepository, TrainerService $trainerService) // Inject TrainerService
    {
        $this->gymClassRepository = $gymClassRepository;
        $this->trainerService = $trainerService;
    }

    public function getGymClassEloquent(array $filters = [])
    {
        return $this->gymClassRepository->getGymClassEloquent($filters);
    }

    public function createGymClass(array $data): GymClass
    {
        try {
            return $this->gymClassRepository->createGymClass($data);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService createGymClass: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function updateGymClass(GymClass $gymClass, array $data): GymClass
    {
        try {
            return $this->gymClassRepository->updateGymClass($gymClass, $data);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService updateGymClass: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id, 'data' => $data]);
            throw $e;
        }
    }

    public function deleteGymClass(GymClass $gymClass): bool
    {
        try {
            return $this->gymClassRepository->deleteGymClass($gymClass);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService deleteGymClass: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id]);
            throw $e;
        }
    }

    public function changeStatus(GymClass $gymClass): GymClass
    {
        try {
            return $this->gymClassRepository->changeStatus($gymClass);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService changeStatus: ' . $e->getMessage(), ['gym_class_id' => $gymClass->id]);
            throw $e;
        }
    }

    public function getAllGymClasses(array $filters = []): Collection
    {
        try {
            return $this->gymClassRepository->getAllGymClasses($filters);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService getAllGymClasses: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getGymClassById(string $id): ?GymClass
    {
        try {
            return $this->gymClassRepository->getGymClassById($id);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService getGymClassById: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function getPaginatedGymClasses(Request $request): LengthAwarePaginator
    {
        try {
            return $this->gymClassRepository->getPaginatedGymClasses($request);
        } catch (\Exception $e) {
            Log::error('Error in GymClassService getPaginatedGymClasses: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getActiveGymClasses(): Collection
    {
        try {
            return $this->gymClassRepository->getActiveGymClasses();
        } catch (\Exception $e) {
            Log::error('Error in GymClassService getActiveGymClasses: ' . $e->getMessage());
            throw $e;
        }
    }
}
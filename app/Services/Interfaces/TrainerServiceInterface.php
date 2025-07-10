<?php

namespace App\Services\Interfaces;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface TrainerServiceInterface
{
    public function getTrainerElouent();
    /**
     * Get all trainers with filters
     */
    public function getAllTrainers($filters = []);

    /**
     * Get trainer by ID
     */
    public function getTrainerById($id);

    /**
     * Get paginated trainers with filters
     */
    public function getPaginatedTrainers(Request $request): LengthAwarePaginator;

    /**
     * Create a new trainer
     */
    public function createTrainer(array $data): Trainer;

    /**
     * Update an existing trainer
     */
    public function updateTrainer($id, array $data): Trainer;

    /**
     * Delete a trainer
     */
    public function deleteTrainer($id): bool;

    /**
     * Get trainer statistics
     */
    public function getTrainerStats($trainerId, $period = 'month'): array;

    /**
     * Get active trainers for dropdown
     */
    public function getActiveTrainers(): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get trainer's class schedule
     */
    public function getTrainerSchedule($trainerId, $startDate = null, $endDate = null): array;

    /**
     * Get trainer's performance metrics
     */
    public function getTrainerPerformance(Trainer $trainer): array;

    /**
     * Check trainer availability
     */
    public function checkTrainerAvailability($trainerId, $date, $startTime, $endTime): bool;

    /**
     * Get trainers available for specific time slot
     */
    public function getAvailableTrainers($date, $startTime, $endTime): \Illuminate\Database\Eloquent\Collection;

    /**
     * Assign trainer to class
     */
    public function assignTrainerToClass($trainerId, $classId): bool;

    /**
     * Remove trainer from class
     */
    public function removeTrainerFromClass($trainerId, $classId): bool;

    /**
     * Get trainer's monthly earnings
     */
    public function getTrainerEarnings($trainerId, $month = null, $year = null): array;

    /**
     * Update trainer status
     */
    public function updateTrainerStatus($id, $status): Trainer;

    /**
     * Get trainer dashboard data
     */
    public function getDashboardData($trainerId = null): array;

    /**
     * Get top performing trainers
     */
    public function getTopPerformingTrainers($limit = 5): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get trainers by specialization
     */
    public function getTrainersBySpecialization($specialization): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get trainer's classes by status
     */
    public function getTrainerClasses($trainerId, $status = 'active'): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get trainer's class history
     */
    public function getTrainerClassHistory($trainerId, $startDate = null, $endDate = null): \Illuminate\Database\Eloquent\Collection;
}
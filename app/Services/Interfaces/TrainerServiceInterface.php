<?php

namespace App\Services\Interfaces;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection; // Import Collection

interface TrainerServiceInterface
{
    /**
     * Get trainers Eloquent query with filters.
     */
    public function getTrainerEloquent(array $filters = []); // Corrected typo and added filters

    /**
     * Create a new trainer and associated user.
     */
    public function createTrainerAndUser(array $data);

    /**
     * Update an existing trainer and associated user.
     */
    public function updateTrainer(Trainer $trainer, array $data): Trainer; // Changed to Trainer object

    /**
     * Delete a trainer and handle associated user.
     */
    public function deleteTrainer(Trainer $trainer): bool; // Changed to Trainer object

    /**
     * Change trainer status and associated user status.
     */
    public function changeStatus(Trainer $trainer): Trainer; // Changed to Trainer object

    /**
     * Get all trainers with filters.
     */
    public function getAllTrainers(array $filters = []): Collection; // Added return type

    /**
     * Get trainer by ID.
     */
    public function getTrainerById(string $id): ?Trainer; // Added return type and nullable

    /**
     * Get paginated trainers with filters.
     */
    public function getPaginatedTrainers(Request $request): LengthAwarePaginator;

    /**
     * Get trainer statistics.
     */
    public function getTrainerStats(string $trainerId, string $period = 'month'): array;

    /**
     * Get active trainers for dropdown.
     */
    public function getActiveTrainers(): Collection;

    /**
     * Get trainer's class schedule.
     */
    public function getTrainerSchedule(string $trainerId, ?string $startDate = null, ?string $endDate = null): array;

    /**
     * Get trainer's performance metrics.
     */
    public function getTrainerPerformance(Trainer $trainer): array;

    /**
     * Check trainer availability.
     */
    public function checkTrainerAvailability(string $trainerId, string $date, string $startTime, string $endTime): bool;

    /**
     * Get trainers available for specific time slot.
     */
    public function getAvailableTrainers(string $date, string $startTime, string $endTime): Collection;

    /**
     * Assign trainer to class.
     */
    public function assignTrainerToClass(string $trainerId, string $classId): bool;

    /**
     * Remove trainer from class.
     */
    public function removeTrainerFromClass(string $trainerId, string $classId): bool;

    /**
     * Get trainer's monthly earnings.
     */
    public function getTrainerEarnings(string $trainerId, ?int $month = null, ?int $year = null): array;

    /**
     * Get trainer dashboard data.
     */
    public function getDashboardData(?string $trainerId = null): array;

    /**
     * Get top performing trainers.
     */
    public function getTopPerformingTrainers(int $limit = 5): Collection;

    /**
     * Get trainers by specialization.
     */
    public function getTrainersBySpecialization(string $specialization): Collection;

    /**
     * Get trainer's classes by status.
     */
    public function getTrainerClasses(string $trainerId, string $status = 'active'): Collection;

    /**
     * Get trainer's class history.
     */
    public function getTrainerClassHistory(string $trainerId, ?string $startDate = null, ?string $endDate = null): Collection;
}
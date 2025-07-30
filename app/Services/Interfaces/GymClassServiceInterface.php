<?php

namespace App\Services\Interfaces;

use App\Models\GymClass;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GymClassServiceInterface
{
    /**
     * Get gym classes Eloquent query with filters for DataTables.
     */
    public function getGymClassEloquent(array $filters = []);

    /**
     * Create a new gym class.
     */
    public function createGymClass(array $data): GymClass;

    /**
     * Update an existing gym class.
     */
    public function updateGymClass(GymClass $gymClass, array $data): GymClass;

    /**
     * Delete a gym class.
     */
    public function deleteGymClass(GymClass $gymClass): bool;

    /**
     * Change gym class status.
     */
    public function changeStatus(GymClass $gymClass): GymClass;

    /**
     * Get all gym classes with filters.
     */
    public function getAllGymClasses(array $filters = []): Collection;

    /**
     * Get gym class by ID.
     */
    public function getGymClassById(string $id): ?GymClass;

    /**
     * Get paginated gym classes with filters.
     */
    public function getPaginatedGymClasses(Request $request): LengthAwarePaginator;

    /**
     * Get active gym classes for dropdowns, etc.
     */
    public function getActiveGymClasses(): Collection;
}

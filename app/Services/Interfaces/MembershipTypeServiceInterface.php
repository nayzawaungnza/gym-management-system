<?php

namespace App\Services\Interfaces;

use App\Models\MembershipType;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface MembershipTypeServiceInterface
{
    /**
     * Get membership types Eloquent query with filters for DataTables.
     */
    public function getMembershipTypeEloquent(array $filters = []);

    /**
     * Create a new membership type.
     */
    public function createMembershipType(array $data): MembershipType;

    /**
     * Update an existing membership type.
     */
    public function updateMembershipType(MembershipType $membershipType, array $data): MembershipType;

    /**
     * Delete a membership type.
     */
    public function deleteMembershipType(MembershipType $membershipType): bool;

    /**
     * Change membership type status.
     */
    public function changeStatus(MembershipType $membershipType): MembershipType;

    /**
     * Get all membership types with filters.
     */
    public function getAllMembershipTypes(array $filters = []): Collection;

    /**
     * Get membership type by ID.
     */
    public function getMembershipTypeById(string $id): ?MembershipType;

    /**
     * Get paginated membership types with filters.
     */
    public function getPaginatedMembershipTypes(Request $request): LengthAwarePaginator;

    /**
     * Get active membership types for dropdowns, etc.
     */
    public function getActiveMembershipTypes(): Collection;
}

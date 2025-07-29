<?php

namespace App\Services;

use App\Models\MembershipType;
use App\Repositories\Backend\MembershipTypeRepository;
use App\Services\Interfaces\MembershipTypeServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class MembershipTypeService implements MembershipTypeServiceInterface
{
    protected $membershipTypeRepository;

    public function __construct(MembershipTypeRepository $membershipTypeRepository)
    {
        $this->membershipTypeRepository = $membershipTypeRepository;
    }

    public function getMembershipTypeEloquent(array $filters = [])
    {
        return $this->membershipTypeRepository->getMembershipTypeEloquent($filters);
    }

    public function createMembershipType(array $data): MembershipType
    {
        try {
            return $this->membershipTypeRepository->createMembershipType($data);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService createMembershipType: ' . $e->getMessage(), ['data' => $data]);
            throw $e;
        }
    }

    public function updateMembershipType(MembershipType $membershipType, array $data): MembershipType
    {
        try {
            return $this->membershipTypeRepository->updateMembershipType($membershipType, $data);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService updateMembershipType: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id, 'data' => $data]);
            throw $e;
        }
    }

    public function deleteMembershipType(MembershipType $membershipType): bool
    {
        try {
            return $this->membershipTypeRepository->deleteMembershipType($membershipType);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService deleteMembershipType: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id]);
            throw $e;
        }
    }

    public function changeStatus(MembershipType $membershipType): MembershipType
    {
        try {
            return $this->membershipTypeRepository->changeStatus($membershipType);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService changeStatus: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id]);
            throw $e;
        }
    }

    public function getAllMembershipTypes(array $filters = []): Collection
    {
        try {
            return $this->membershipTypeRepository->getAllMembershipTypes($filters);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService getAllMembershipTypes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getMembershipTypeById(string $id): ?MembershipType
    {
        try {
            return $this->membershipTypeRepository->getMembershipTypeById($id);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService getMembershipTypeById: ' . $e->getMessage(), ['id' => $id]);
            throw $e;
        }
    }

    public function getPaginatedMembershipTypes(Request $request): LengthAwarePaginator
    {
        try {
            return $this->membershipTypeRepository->getPaginatedMembershipTypes($request);
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService getPaginatedMembershipTypes: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getActiveMembershipTypes(): Collection
    {
        try {
            return $this->membershipTypeRepository->getActiveMembershipTypes();
        } catch (\Exception $e) {
            Log::error('Error in MembershipTypeService getActiveMembershipTypes: ' . $e->getMessage());
            throw $e;
        }
    }
}

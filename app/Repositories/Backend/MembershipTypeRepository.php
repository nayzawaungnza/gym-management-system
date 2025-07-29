<?php

namespace App\Repositories\Backend;

use App\Models\MembershipType;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class MembershipTypeRepository extends BaseRepository
{
    public function model()
    {
        return MembershipType::class;
    }

    public function getMembershipTypeEloquent(array $filters = [])
    {
        $query = MembershipType::query()
            ->orderBy('created_at', 'desc');

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }
        // Corrected: Access 'value' key from the 'search' array
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where(function($q) use ($searchTerm) {
                $q->where('type_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }
        return $query;
    }

    public function getMembershipTypeById(string $id): ?MembershipType
    {
        return $this->getById($id);
    }

    public function createMembershipType(array $data): MembershipType
    {
        DB::beginTransaction();
        try {
            $membershipType = MembershipType::create([
                'type_name' => $data['type_name'],
                'duration_months' => $data['duration_months'],
                'price' => $data['price'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $activity_data['subject'] = $membershipType;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Membership Type (%s) was created.', $membershipType->type_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $membershipType;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create membership type: ' . $e->getMessage(), ['data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function updateMembershipType(MembershipType $membershipType, array $data): MembershipType
    {
        //dd($data);
        DB::beginTransaction();
        try {
            $membershipType->update([
                'type_name' => $data['type_name'] ?? $membershipType->type_name,
                'duration_months' => $data['duration_months'] ?? $membershipType->duration_months,
                'price' => $data['price'] ?? $membershipType->price,
                'description' => $data['description'] ?? $membershipType->description,
                'is_active' => $data['is_active'] ?? $membershipType->is_active,
            ]);

            $activity_data['subject'] = $membershipType->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Membership Type (%s) was updated.', $membershipType->type_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $membershipType;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update membership type: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id, 'data' => $data, 'exception' => $e]);
            throw $e;
        }
    }

    public function deleteMembershipType(MembershipType $membershipType): bool
    {
        DB::beginTransaction();
        try {
            $deleted = $this->deleteById($membershipType->id);

            $activity_data['subject'] = $membershipType;
            $activity_data['event'] = config('constants.ACTIVITY_LOG.DELETED_EVENT_NAME');
            $activity_data['description'] = sprintf('Membership Type (%s) was deleted.', $membershipType->type_name);
            saveActivityLog($activity_data);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete membership type: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function changeStatus(MembershipType $membershipType): MembershipType
    {
        DB::beginTransaction();
        try {
            $membershipType->is_active = !$membershipType->is_active;
            $membershipType->save();

            $activity_data['subject'] = $membershipType->refresh();
            $activity_data['event'] = config('constants.ACTIVITY_LOG.UPDATED_EVENT_NAME');
            $activity_data['description'] = sprintf('Membership Type (%s) status changed to %s.', $membershipType->type_name, $membershipType->is_active ? 'Active' : 'Inactive');
            saveActivityLog($activity_data);

            DB::commit();
            return $membershipType;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to change membership type status: ' . $e->getMessage(), ['membership_type_id' => $membershipType->id, 'exception' => $e]);
            throw $e;
        }
    }

    public function getAllMembershipTypes(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }
        // Corrected: Access 'value' key from the 'search' array
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $searchTerm = $filters['search']['value'];
            $query->where('type_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
        }

        return $query->get();
    }

    public function getPaginatedMembershipTypes(\Illuminate\Http\Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }
        // Corrected: Access 'value' key from the 'search' array
        if ($request->has('search') && isset($request->input('search')['value']) && !empty($request->input('search')['value'])) {
            $searchTerm = $request->input('search')['value'];
            $query->where('type_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
        }

        return $query->paginate($request->input('per_page', 25));
    }

    public function getActiveMembershipTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return MembershipType::where('is_active', true)->get();
    }
}
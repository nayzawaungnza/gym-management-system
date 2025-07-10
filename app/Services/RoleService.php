<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Backend\RoleRepository;
use App\Services\Interfaces\RoleServiceInterface;

class RoleService implements RoleServiceInterface
{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getRoles()
    {
        return $this->roleRepository->orderBy('created_at', 'desc')->get();
    }

    public function getRolesPluckName()
    {
        return Role::pluck('name', 'name')->all();
    }

    public function getRole($id)
    {
        return $this->roleRepository->getRole($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            $result = $this->roleRepository->create($data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to create role');
        }
        DB::commit();

        return $result;
    }

    public function update(Role $role, array $data)
    {
        DB::beginTransaction();
        try {
            $result = $this->roleRepository->update($role, $data);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to update role');
        }
        DB::commit();

        return $result;
    }

    public function destroy(Role $role)
    {
        DB::beginTransaction();
        try {
            $result = $this->roleRepository->destroy($role);
        } catch (Exception $exc) {
            DB::rollBack();
            Log::error($exc->getMessage());
            throw new InvalidArgumentException('Unable to delete role');
        }
        DB::commit();

        return $result;
    }

    public function getRoleEloquent()
    {
        return $this->roleRepository->getRoleEloquent();
    }
}

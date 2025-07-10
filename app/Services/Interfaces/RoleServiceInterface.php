<?php

namespace App\Services\Interfaces;

use Spatie\Permission\Models\Role;

interface RoleServiceInterface
{
    public function getRole($id);
    public function create(array $data);
    public function update(Role $role,array $data);
    public function destroy(Role $role);
    public function getRoleEloquent();
}
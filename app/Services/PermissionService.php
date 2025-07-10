<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Services\Interfaces\PermissionServiceInterface;

class PermissionService implements PermissionServiceInterface
{
    public function getPermissions()
    {
        return Permission::all();
    }

    public function getRolePermission($id)
    {
        return Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
        ->where("role_has_permissions.role_id",$id)
        ->get(); 
    }

    public function getRolePermissions($id)
    {
        return DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
        ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
        ->all();
        
    }

    public function getPermissionsByGuard(string $guardName)
    {
        //return $this->permissionRepository->getByGuardName($guardName);
        return Permission::where('guard_name', $guardName)->get();
    }

}
<?php

namespace App\Services\Interfaces;

interface PermissionServiceInterface
{
    public function getPermissions();
    public function getRolePermission($id);
    public function getRolePermissions($id);
    public function getPermissionsByGuard(string $guardName);
    
}
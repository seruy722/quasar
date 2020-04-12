<?php


namespace App\Traits;


use App\UserPermissionRole;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait UserPermission
{
    public function hasPermission($permissionName)
    {
        $userRole = UserPermissionRole::find(auth()->user()->id);
        $permission = Permission::findByName($permissionName);
        $role = Role::findById($userRole->role_id);
        return $role->hasPermissionTo($permission);
    }

    public function userHasPermission()
    {

    }
}

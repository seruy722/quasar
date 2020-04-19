<?php


namespace App\Traits;

trait UserSetAccessData
{
    public function setAccessData()
    {
        $user = auth()->user();
        $user->getPermissionsViaRoles();
        $rolesWithPermissions = $user->roles->map(function ($role) {
            return $role->permissions->map(function ($permission) {
                return $permission->name;
            });
//            return ['role' => $role->name, 'permissions' => $rolePermissions];
        });
        $userPermissions = $user->permissions->map(function ($permission) {
            return $permission->name;
        });
        $userRoles = $user->roles->map(function ($role) {
            return $role->name;
        });
        unset($user->roles);
        unset($user->permissions);
        $mergeCollection = $userPermissions->merge($rolesWithPermissions);
        $user->access = ['roles' => $userRoles, 'permissions' => $mergeCollection->flatten()];
        return $user;
    }
}

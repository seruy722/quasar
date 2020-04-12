<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use function foo\func;

class PermissionController extends Controller
{
    public function createRole($name)
    {
        return Role::create(['name' => $name]);
    }

    public function createPermission($name)
    {
        return Permission::create(['name' => $name]);
    }

    public function assignRoleToUser($roleId, $userId)
    {
        $user = User::find($userId);
        $role = Role::findById($roleId);
        $user->assignRole($role);
        return $user;
    }

    public function getUsersWithRolesAndPermissions()
    {
        $users = User::with('roles')->get();
        $users->map(function ($user) {
            $user->permissions;
            return $user['roles']->map(function ($role) {
                $role->permissions;
                return $role;
            });
        });
        return $users;
    }

    public function index()
    {
        return response(['users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function getAllPermissions()
    {
        return Permission::orderBy('created_at', 'desc')->get();
    }

    public function addPermission(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions|max:255',
        ]);
        $permission = Permission::create(['name' => $request->name]);
        if (!empty($request->roles)) {
            foreach ($request->roles as $roleName) {
                $role = Role::findByName($roleName);
                $role->givePermissionTo($permission);
            }
        }

        if (!empty($request->users)) {
            foreach ($request->users as $userName) {
                $user = User::where('name', $userName)->first();
                $user->givePermissionTo($permission);
            }
        }
        return response(['addedPermission' => $permission, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function deletePermission($id)
    {
        $permission = Permission::findById($id);
        foreach ($permission->roles as $item) {
            $item->revokePermissionTo($permission);
        }
        $users = User::permission($permission)->get();
        $users->each(function ($item) use ($permission) {
            $item->revokePermissionTo($permission);
        });
        $permission->delete();
        return response(['status' => $permission, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function assignPermissionToRoleAndUser(Request $request)
    {
        $permission = Permission::findByName($request->name);
        if (!empty($request->roles)) {
            foreach ($request->roles as $roleName) {
                $role = Role::findByName($roleName);
                $role->givePermissionTo($permission);
            }
        }

        if (!empty($request->users)) {
            foreach ($request->users as $userName) {
                $user = User::where('name', $userName)->first();
                $user->givePermissionTo($permission);
            }
        }
        return response(['users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function deletePermissionFromRoleOrUser(Request $request)
    {
        $permission = Permission::findByName($request->permissionName);
        if ($request->key === 'user') {
            $user = User::where('name', $request->name)->first();
            $user->revokePermissionTo($permission);
        } else if ($request->key === 'role') {
            $role = Role::findByName($request->name);
            $role->revokePermissionTo($permission);
        }
        return response(['status' => true, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function getAllRoles()
    {
        return Role::orderBy('created_at', 'desc')->get();
    }

    public function addRole(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles|max:255',
        ]);
        $role = Role::create(['name' => $request->name]);
        if (!empty($request->users)) {
            foreach ($request->users as $userName) {
                $user = User::where('name', $userName)->first();
                $user->assignRole($role);
            }
        }
        return response(['addedRole' => $role, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function assignRoleToUsers(Request $request)
    {
        $role = Role::findByName($request->name);
        if (!empty($request->users)) {
            foreach ($request->users as $userName) {
                $user = User::where('name', $userName)->first();
                $user->assignRole($role);
            }
        }
        return response(['users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function deleteRole($id)
    {
        $role = Role::findById($id);
        $role->revokePermissionTo($role->permissions);
        $users = User::role($role)->get();
        $users->each(function ($item) use ($role) {
            $item->removeRole($role);
        });
        $role->delete();
        return response(['status' => true, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }

    public function deleteRoleFromUser(Request $request)
    {
        $role = Role::findById($request->roleId);
        $user = User::find($request->userId);
        $user->removeRole($role);
        return response(['status' => true, 'users' => $this->getUsersWithRolesAndPermissions()]);
    }
}

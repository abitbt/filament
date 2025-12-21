<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::RolesRead->value);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermission(Permission::RolesRead->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::RolesWrite->value);
    }

    public function update(User $user, Role $role): bool
    {
        // Cannot edit super-admin role unless you are super admin
        if ($role->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermission(Permission::RolesWrite->value);
    }

    public function delete(User $user, Role $role): bool
    {
        // Cannot delete super-admin role
        if ($role->isSuperAdmin()) {
            return false;
        }

        // Cannot delete role with users
        if ($role->users()->exists()) {
            return false;
        }

        return $user->hasPermission(Permission::RolesDelete->value);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::RolesDelete->value);
    }

    public function restore(User $user, Role $role): bool
    {
        return $user->hasPermission(Permission::RolesWrite->value);
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}

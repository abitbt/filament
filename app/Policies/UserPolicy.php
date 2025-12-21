<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::UsersRead->value);
    }

    public function view(User $user, User $model): bool
    {
        // Users can always view themselves
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermission(Permission::UsersRead->value);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(Permission::UsersWrite->value);
    }

    public function update(User $user, User $model): bool
    {
        // Users can update themselves
        if ($user->id === $model->id) {
            return true;
        }

        // Cannot edit super admin unless you are super admin
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermission(Permission::UsersWrite->value);
    }

    public function delete(User $user, User $model): bool
    {
        // Cannot delete yourself
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete super admin
        if ($model->isSuperAdmin()) {
            return false;
        }

        return $user->hasPermission(Permission::UsersDelete->value);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::UsersDelete->value);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->hasPermission(Permission::UsersWrite->value);
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}

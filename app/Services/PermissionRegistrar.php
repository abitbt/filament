<?php

namespace App\Services;

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PermissionRegistrar
{
    public function registerGates(): void
    {
        // Super admin bypass - implicitly grant all abilities
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        // Register a gate for each permission
        foreach (Permission::cases() as $permission) {
            Gate::define($permission->value, function (User $user) use ($permission): bool {
                return $user->hasPermission($permission->value);
            });
        }
    }
}

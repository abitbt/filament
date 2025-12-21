<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission(Permission::ActivityLogsRead->value);
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->hasPermission(Permission::ActivityLogsRead->value);
    }

    public function create(User $user): bool
    {
        // Activity logs are system-generated only
        return false;
    }

    public function update(User $user, ActivityLog $activityLog): bool
    {
        // Activity logs are immutable
        return false;
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return $user->hasPermission(Permission::ActivityLogsDelete->value);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermission(Permission::ActivityLogsDelete->value);
    }

    public function restore(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function forceDelete(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }
}

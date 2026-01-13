<?php

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\Role;
use App\Services\ActivityLogger;

class RoleObserver
{
    public function created(Role $role): void
    {
        ActivityLogger::log(ActivityEvent::Created, "Created role: {$role->name}", $role);
    }

    public function updated(Role $role): void
    {
        $changes = $role->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        ActivityLogger::log(
            ActivityEvent::Updated,
            "Updated role: {$role->name}",
            $role,
            [
                'old' => array_intersect_key($role->getOriginal(), $changes),
                'new' => $changes,
            ]
        );
    }

    public function deleted(Role $role): void
    {
        ActivityLogger::log(ActivityEvent::Deleted, "Deleted role: {$role->name}", $role);
    }
}

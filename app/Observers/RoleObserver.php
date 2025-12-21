<?php

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\Role;

class RoleObserver
{
    public function created(Role $role): void
    {
        $this->log(ActivityEvent::Created, $role, "Created role: {$role->name}");
    }

    public function updated(Role $role): void
    {
        $changes = $role->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $this->log(
            ActivityEvent::Updated,
            $role,
            "Updated role: {$role->name}",
            [
                'old' => array_intersect_key($role->getOriginal(), $changes),
                'new' => $changes,
            ]
        );
    }

    public function deleted(Role $role): void
    {
        $this->log(ActivityEvent::Deleted, $role, "Deleted role: {$role->name}");
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    protected function log(ActivityEvent $event, Role $role, string $description, ?array $properties = null): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}

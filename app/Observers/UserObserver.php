<?php

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        $this->log(ActivityEvent::Created, $user, "Created user: {$user->name}");
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        unset($changes['updated_at'], $changes['remember_token'], $changes['updated_by']);

        if (empty($changes)) {
            return;
        }

        // Don't log password in properties
        $safeChanges = $changes;
        if (isset($safeChanges['password'])) {
            $safeChanges['password'] = '[REDACTED]';
        }

        $this->log(
            ActivityEvent::Updated,
            $user,
            "Updated user: {$user->name}",
            [
                'old' => array_intersect_key($user->getOriginal(), $changes),
                'new' => $safeChanges,
            ]
        );
    }

    public function deleted(User $user): void
    {
        $this->log(ActivityEvent::Deleted, $user, "Deleted user: {$user->name}");
    }

    /**
     * @param  array<string, mixed>|null  $properties
     */
    protected function log(ActivityEvent $event, User $user, string $description, ?array $properties = null): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}

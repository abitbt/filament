<?php

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\User;
use App\Services\ActivityLogger;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLogger::log(ActivityEvent::Created, "Created user: {$user->name}", $user);
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        unset($changes['updated_at'], $changes['remember_token'], $changes['updated_by']);

        if (empty($changes)) {
            return;
        }

        // Redact password in both old and new values
        $safeOldValues = array_intersect_key($user->getOriginal(), $changes);
        $safeNewValues = $changes;

        if (isset($safeOldValues['password'])) {
            $safeOldValues['password'] = '[REDACTED]';
        }
        if (isset($safeNewValues['password'])) {
            $safeNewValues['password'] = '[REDACTED]';
        }

        ActivityLogger::log(
            ActivityEvent::Updated,
            "Updated user: {$user->name}",
            $user,
            [
                'old' => $safeOldValues,
                'new' => $safeNewValues,
            ]
        );
    }

    public function deleted(User $user): void
    {
        ActivityLogger::log(ActivityEvent::Deleted, "Deleted user: {$user->name}", $user);
    }
}

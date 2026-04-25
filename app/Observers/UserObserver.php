<?php

namespace App\Observers;

use App\Enums\ActivityEvent;
use App\Models\User;
use App\Services\ActivityLogger;

class UserObserver
{
    /**
     * Fields safe to capture in activity log diffs.
     * Allowlist (not denylist) so future sensitive fields don't leak by default.
     *
     * @var list<string>
     */
    private const LOGGABLE_FIELDS = [
        'name',
        'email',
        'status',
        'role_id',
        'avatar',
    ];

    public function created(User $user): void
    {
        ActivityLogger::log(ActivityEvent::Created, "Created user: {$user->name}", $user);
    }

    public function updated(User $user): void
    {
        $allowed = array_flip(self::LOGGABLE_FIELDS);
        $newValues = array_intersect_key($user->getChanges(), $allowed);

        if (empty($newValues)) {
            return;
        }

        $oldValues = array_intersect_key($user->getOriginal(), $newValues);

        ActivityLogger::log(
            ActivityEvent::Updated,
            "Updated user: {$user->name}",
            $user,
            [
                'old' => $oldValues,
                'new' => $newValues,
            ]
        );
    }

    public function deleted(User $user): void
    {
        ActivityLogger::log(ActivityEvent::Deleted, "Deleted user: {$user->name}", $user);
    }
}

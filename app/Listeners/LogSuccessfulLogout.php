<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        ActivityLog::create([
            'user_id' => $event->user->getAuthIdentifier(),
            'subject_type' => get_class($event->user),
            'subject_id' => $event->user->getAuthIdentifier(),
            'event' => ActivityEvent::Logout,
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}

<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        ActivityLog::create([
            'user_id' => $event->user->getAuthIdentifier(),
            'subject_type' => get_class($event->user),
            'subject_id' => $event->user->getAuthIdentifier(),
            'event' => ActivityEvent::Login,
            'description' => 'User logged in',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}

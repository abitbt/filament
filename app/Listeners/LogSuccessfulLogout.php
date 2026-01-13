<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        /** @var User $user */
        $user = $event->user;

        ActivityLogger::log(ActivityEvent::Logout, 'User logged out', $user);
    }
}

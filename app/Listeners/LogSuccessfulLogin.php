<?php

namespace App\Listeners;

use App\Enums\ActivityEvent;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        ActivityLogger::log(ActivityEvent::Login, 'User logged in', $user);
    }
}

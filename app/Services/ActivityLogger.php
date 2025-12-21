<?php

namespace App\Services;

use App\Enums\ActivityEvent;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $properties
     */
    public static function log(
        ActivityEvent $event,
        string $description,
        ?Model $subject = null,
        ?array $properties = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'event' => $event,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}

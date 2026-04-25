<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Adds a cached count()-driven navigation badge to a Filament resource.
 *
 * Navigation badges run on every panel page render — caching avoids hitting
 * the database for the same count on every request. The 1-minute TTL is short
 * enough that the badge feels live but long enough to absorb sidebar refreshes.
 *
 * Implementers define {@see navigationBadgeQuery()}; color and tooltip use
 * Filament's normal `getNavigationBadgeColor()` / `getNavigationBadgeTooltip()`
 * overrides on the resource itself.
 */
trait HasCountNavigationBadge
{
    abstract protected static function navigationBadgeQuery(): Builder;

    public static function getNavigationBadge(): ?string
    {
        $count = Cache::remember(
            'nav-badge:'.static::class,
            static::navigationBadgeCacheSeconds(),
            fn (): int => static::navigationBadgeQuery()->count(),
        );

        return $count > 0 ? (string) $count : null;
    }

    protected static function navigationBadgeCacheSeconds(): int
    {
        return 60;
    }
}

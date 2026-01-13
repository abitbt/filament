<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\Carbon;

/**
 * Provides date range filtering helpers for Filament widgets.
 *
 * Requires the widget to use Filament's InteractsWithPageFilters trait.
 *
 * @property array{startDate?: string, endDate?: string, status?: string}|null $pageFilters
 */
trait HasDateRangeFilter
{
    /**
     * Get the start date from page filters (nullable version).
     */
    protected function getFilterStartDate(): ?Carbon
    {
        $startDate = $this->pageFilters['startDate'] ?? null;

        return $startDate !== null ? Carbon::parse($startDate) : null;
    }

    /**
     * Get the start date from page filters with a default fallback.
     */
    protected function getFilterStartDateOrDefault(int $defaultDaysAgo = 30): Carbon
    {
        $startDate = $this->pageFilters['startDate'] ?? null;

        return $startDate !== null ? Carbon::parse($startDate) : now()->subDays($defaultDaysAgo);
    }

    /**
     * Get the end date from page filters.
     */
    protected function getFilterEndDate(): Carbon
    {
        $endDate = $this->pageFilters['endDate'] ?? null;

        return $endDate !== null ? Carbon::parse($endDate) : now();
    }

    /**
     * Get the status filter value.
     */
    protected function getFilterStatus(): string
    {
        return $this->pageFilters['status'] ?? 'all';
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

/**
 * Custom view widget showing system information.
 *
 * This widget demonstrates:
 * - Custom Blade view for complete UI control
 * - Passing data to views via getViewData()
 * - Reading Composer package versions
 * - Application configuration values
 *
 * The view file is at: resources/views/filament/widgets/system-info-widget.blade.php
 *
 * @see https://filamentphp.com/docs/4.x/widgets/overview#creating-a-widget
 */
class SystemInfoWidget extends Widget
{
    protected string $view = 'filament.widgets.system-info-widget';

    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 4;

    /**
     * Pass data to the Blade view.
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'filament_version' => \Composer\InstalledVersions::getVersion('filament/filament') ?? 'Unknown',
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'timezone' => config('app.timezone'),
        ];
    }
}

<x-filament-widgets::widget>
    <x-filament::section heading="System Info" icon="heroicon-o-server">
        <dl class="grid grid-cols-2 gap-2 text-sm">
            @foreach ([
        'PHP Version' => $php_version,
        'Laravel' => $laravel_version,
        'Filament' => $filament_version,
        'Environment' => $environment,
        'Debug' => $debug_mode,
        'Timezone' => $timezone,
    ] as $label => $value)
                <dt class="font-medium text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                <dd class="text-gray-900 dark:text-white">{{ $value }}</dd>
            @endforeach
        </dl>
    </x-filament::section>
</x-filament-widgets::widget>

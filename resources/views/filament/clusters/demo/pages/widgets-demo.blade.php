<x-filament::page>
    <x-filament::section>
        <x-slot name="heading">
            Widget Components
        </x-slot>
        <x-slot name="description">
            Widgets are reusable components that display data in various formats including stats, charts, and custom
            content.
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <h4>Widget Types Demonstrated:</h4>
            <ul>
                <li><strong>Stats Overview</strong> - Key metrics with sparkline charts and trend indicators</li>
                <li><strong>Line Chart</strong> - Time series data visualization with multiple datasets</li>
                <li><strong>Bar Chart</strong> - Categorical data comparison (horizontal orientation)</li>
                <li><strong>Doughnut Chart</strong> - Proportional data with percentage breakdown</li>
            </ul>

            <h4>Widget Features:</h4>
            <ul>
                <li>Responsive column spanning</li>
                <li>Customizable chart options via Chart.js</li>
                <li>Optional polling for real-time updates</li>
                <li>Filter integration with dashboard pages</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament::page>

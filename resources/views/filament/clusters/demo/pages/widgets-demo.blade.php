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

    <x-filament::section>
        <x-slot name="heading">
            Polling pattern
        </x-slot>
        <x-slot name="description">
            Live-updating widgets via Filament's <code>poll()</code> / <code>$pollingInterval</code>.
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <p>
                <code>app/Filament/Widgets/LatestActivityWidget.php</code> on the main dashboard demonstrates a
                <code>TableWidget</code> that refreshes every 30 seconds using <code>->poll('30s')</code> on the
                table. The dashboard's <code>StatsOverview</code> uses
                <code>protected ?string $pollingInterval = '30s'</code> for the same effect on a stats widget.
            </p>

            <pre><code class="language-php">// Inside table()
return $table
    ->query(ActivityLog::query()-&gt;with('user')-&gt;latest('created_at')-&gt;limit(10))
    -&gt;columns([ /* ... */ ])
    -&gt;poll('30s'); // auto-refresh

// Or, on any Widget subclass:
protected ?string $pollingInterval = '30s';</code></pre>

            <p>
                Pair polling with <code>databaseNotifications()</code> on the panel (see
                <code>AdminPanelProvider</code>) so users see new activity without reloading the page.
            </p>
        </div>
    </x-filament::section>
</x-filament::page>

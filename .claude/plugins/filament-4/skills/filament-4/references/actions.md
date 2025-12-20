# Filament 4 Actions Reference

Complete reference for actions, modals, wizards, notifications, and import/export.

## Basic Actions

```php
use Filament\Actions\Action;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\IconPosition;

Action::make('send')
    ->label('Send Email')
    ->icon('heroicon-o-envelope')
    ->iconPosition(IconPosition::After)
    ->color('primary')               // primary, danger, warning, success, gray
    ->size(Size::Large)              // Small, Medium, Large
    ->button()                       // or ->link(), ->iconButton(), ->badge()
    ->outlined()
    ->labeledFrom('md')              // Icon button on mobile, labeled on md+
    ->requiresConfirmation()
    ->action(fn () => $this->sendEmail())
    ->url('/posts')                  // Or use URL instead of action
    ->openUrlInNewTab()
```

## Action Styles

```php
// Button styles
Action::make('save')->button()
Action::make('edit')->link()
Action::make('delete')->iconButton()

// With badge
Action::make('notifications')
    ->iconButton()
    ->icon('heroicon-o-bell')
    ->badge(5)
    ->badgeColor('danger')

// Outlined
Action::make('cancel')
    ->button()
    ->outlined()
    ->color('gray')
```

## Action Groups

```php
use Filament\Actions\ActionGroup;

// Dropdown menu
ActionGroup::make([
    Action::make('view'),
    Action::make('edit'),
    Action::make('delete'),
])
    ->label('Actions')
    ->icon('heroicon-m-ellipsis-vertical')
    ->color('gray')
    ->button()
    ->dropdownPlacement('bottom-start')

// Button group (no dropdown)
ActionGroup::make([
    Action::make('edit')
        ->icon(Heroicon::PencilSquare)
        ->hiddenLabel(),
    Action::make('delete')
        ->icon(Heroicon::Trash)
        ->hiddenLabel(),
])
    ->buttonGroup()

// Nested groups
ActionGroup::make([
    Action::make('view'),
    ActionGroup::make([
        Action::make('export_csv'),
        Action::make('export_pdf'),
    ])->label('Export'),
])
```

## Modal Forms

```php
Action::make('create')
    ->schema([                           // Not form()
        TextInput::make('name')->required(),
        Select::make('category')->options([...]),
    ])
    ->data(fn ($record) => [            // Not fillForm()
        'name' => $record->name,
    ])
    ->action(function (array $data, $record) {
        $record->update($data);
    })
```

## Modal Configuration

```php
use Filament\Support\Enums\Width;
use Filament\Support\Enums\Alignment;

Action::make('edit')
    ->modal()                                    // Declare modal exists
    ->modalHeading('Edit Post')
    ->modalDescription('Update the post')        // Not modalSubheading()
    ->modalSubmitActionLabel('Save Changes')
    ->modalCancelActionLabel('Discard')
    ->modalIcon('heroicon-o-pencil')
    ->modalIconColor('warning')
    ->modalWidth(Width::FiveExtraLarge)          // ExtraSmall to SevenExtraLarge, Screen
    ->modalAlignment(Alignment::Center)
    ->slideOver()
    ->stickyModalHeader()
    ->stickyModalFooter()
    ->closeModalByClickingAway(false)
    ->closeModalByEscaping(false)
    ->modalCloseButton(false)
    ->modalAutofocus(false)
```

## Wizard in Modal

```php
use Filament\Schemas\Components\Wizard\Step;

Action::make('create')
    ->steps([
        Step::make('Details')
            ->description('Basic information')
            ->schema([
                TextInput::make('name')->required(),
            ])
            ->columns(2),
        Step::make('Settings')
            ->schema([
                Toggle::make('is_published'),
            ]),
    ])
```

## Extra Modal Footer Actions

```php
Action::make('create')
    ->schema([...])
    ->extraModalFooterActions(fn (Action $action) => [
        $action->makeModalSubmitAction('createAnother', arguments: ['another' => true]),
    ])
    ->action(function (array $data, array $arguments) {
        // Create record
        if ($arguments['another'] ?? false) {
            // Reset form, keep modal open
        }
    })
```

## Nested Actions

```php
Action::make('edit')
    ->extraModalFooterActions([
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->record->delete())
            ->cancelParentActions(),     // Close parent modal after
    ])
```

## Action Control Flow

```php
Action::make('submit')
    ->action(function (Action $action, array $data) {
        if (! $this->canSubmit()) {
            $action->halt();             // Not cancel()
            return;
        }

        // Continue with action...
    })
```

## Authorization

```php
Action::make('delete')
    ->visible(fn () => auth()->user()->can('delete', $this->post))
    ->hidden(fn () => ! $this->canDelete)
    ->authorize('delete')                    // Uses policy
    ->authorizationTooltip()                 // Show policy message as tooltip
    ->authorizationNotification()            // Show as notification instead
    ->disabled(fn () => $this->isLocked)
```

## Rate Limiting

```php
Action::make('submit')
    ->rateLimit(5)                           // 5 attempts per minute
    ->rateLimitedNotificationTitle('Slow down!')
```

## Keybindings

```php
Action::make('save')
    ->keyBindings(['command+s', 'ctrl+s'])
```

---

## Table Actions

### Record Actions

```php
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;

$table->recordActions([
    ViewAction::make(),
    EditAction::make(),
    DeleteAction::make(),

    Action::make('approve')
        ->action(fn (Post $record) => $record->approve())
        ->requiresConfirmation()
        ->icon('heroicon-o-check')
        ->color('success')
        ->hidden(fn (Post $record) => $record->is_approved),
])
```

### Bulk Actions

```php
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

$table->toolbarActions([
    BulkActionGroup::make([
        DeleteBulkAction::make(),

        BulkAction::make('export')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn (Collection $records) => $this->export($records)),
    ]),
])
```

### Header Actions

```php
$table->headerActions([
    CreateAction::make(),
    ImportAction::make()->importer(ProductImporter::class),
    ExportAction::make()->exporter(ProductExporter::class),
])
```

---

## Notifications

### Basic Notification

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->send();
```

### Full Options

```php
Notification::make()
    ->title('Post Published')
    ->body('Your post is now live.')
    ->success()                        // or ->danger(), ->warning(), ->info()
    ->icon('heroicon-o-check')
    ->iconColor('success')
    ->color('success')                 // Background color
    ->duration(5000)                   // milliseconds
    ->seconds(5)                       // alternative to duration
    ->persistent()                     // Don't auto-close
    ->actions([
        Action::make('view')
            ->button()
            ->url('/posts/1', shouldOpenInNewTab: true),
        Action::make('undo')
            ->color('gray')
            ->dispatch('undoAction', ['id' => 1])
            ->close(),
    ])
    ->send();
```

### Custom Notification ID

```php
// Send with custom ID
Notification::make('processing')
    ->title('Processing...')
    ->persistent()
    ->send();

// Close by ID
$this->dispatch('close-notification', id: 'processing');
```

### JavaScript Notifications

```js
new FilamentNotification()
    .title('Saved!')
    .success()
    .body('Your changes have been saved.')
    .actions([
        new FilamentNotificationAction('view')
            .button()
            .url('/view'),
    ])
    .send()
```

---

## Database Notifications

### Panel Configuration

```php
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        ->databaseNotifications()
        ->databaseNotificationsPolling('30s');
}
```

### Sending Database Notifications

```php
$recipient = auth()->user();

Notification::make()
    ->title('Order shipped')
    ->body('Your order #123 has been shipped.')
    ->success()
    ->actions([
        Action::make('view')
            ->button()
            ->url('/orders/123')
            ->markAsRead(),
    ])
    ->sendToDatabase($recipient);

// With websockets (immediate)
Notification::make()
    ->title('New message')
    ->sendToDatabase($recipient, isEventDispatched: true);
```

---

## Import Actions

### Setup

```bash
php artisan make:queue-batches-table
php artisan make:notifications-table
php artisan vendor:publish --tag=filament-actions-migrations
php artisan migrate

php artisan make:filament-importer Product
php artisan make:filament-importer Product --generate  # Auto-generate columns
```

### Using Import Action

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\ImportAction;

$table->headerActions([
    ImportAction::make()
        ->importer(ProductImporter::class)
        ->maxRows(100000)
        ->chunkSize(250)
        ->csvDelimiter(';')
])
```

### Importer Class

```php
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Product Name'),
            ImportColumn::make('sku')
                ->label('SKU')
                ->guess(['id', 'product_id']),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['numeric', 'min:0']),
            ImportColumn::make('category')
                ->relationship(resolveUsing: 'name'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Create new or update existing
        return Product::firstOrNew(['sku' => $this->data['sku']]);

        // Create only
        // return new Product();

        // Update only
        // return Product::where('sku', $this->data['sku'])->first();
    }
}
```

---

## Export Actions

### Setup

```bash
php artisan make:filament-exporter Product
php artisan make:filament-exporter Product --generate
```

### Using Export Action

```php
use App\Filament\Exports\ProductExporter;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\Exports\Enums\ExportFormat;

// Header action (export all)
$table->headerActions([
    ExportAction::make()
        ->exporter(ProductExporter::class)
        ->formats([ExportFormat::Csv, ExportFormat::Xlsx])
        ->maxRows(100000)
        ->fileName(fn (Export $export) => "products-{$export->getKey()}")
])

// Bulk action (export selected)
$table->toolbarActions([
    ExportBulkAction::make()
        ->exporter(ProductExporter::class)
])
```

### Exporter Class

```php
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
            ExportColumn::make('name'),
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('price')
                ->formatStateUsing(fn ($state) => number_format($state, 2)),
            ExportColumn::make('category.name')
                ->label('Category'),
            ExportColumn::make('created_at')
                ->enabledByDefault(false),
        ];
    }
}
```

---

## Import/Export Lifecycle Hooks

```php
// In Importer class
protected function beforeValidate(): void { }
protected function afterValidate(): void { }
protected function beforeFill(): void { }
protected function afterFill(): void { }
protected function beforeSave(): void { }
protected function afterSave(): void { }
protected function beforeCreate(): void { }
protected function afterCreate(): void { }
```

---

## Widgets

### Stats Widget

```php
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Analytics';
    protected ?string $description = 'Overview of key metrics';
    protected ?string $pollingInterval = '10s';     // Non-static

    protected function getStats(): array            // Not getCards()
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('12% increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('filterUsers')",
                ]),
        ];
    }
}
```

### Chart Widget

```php
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue';         // Non-static
    protected ?string $description = 'Monthly breakdown';
    protected ?string $pollingInterval = '10s';     // Non-static
    protected static ?string $maxHeight = '300px';

    protected function getType(): string
    {
        return 'line'; // line, bar, pie, doughnut, radar, polarArea
    }

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => [100, 200, 300, 400],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
```

### Chart Filters

```php
class RevenueChart extends ChartWidget
{
    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        // Use filter to modify query...
    }
}
```

### Widget Properties

```php
protected static ?int $sort = 2;                    // Widget order
protected int | string | array $columnSpan = 'full'; // or 1, 2, ['md' => 2]
protected static bool $isLazy = false;              // Disable lazy loading
protected ?string $pollingInterval = '10s';         // Non-static, or null

public static function canView(): bool
{
    return auth()->user()->isAdmin();
}
```

---

## Colors Reference

```php
'primary'   // Theme primary color
'secondary' // Theme secondary
'success'   // Green
'danger'    // Red
'warning'   // Yellow/Orange
'info'      // Blue
'gray'      // Gray/Neutral
```

## Heroicons

```php
->icon('heroicon-o-user')       // Outline (24x24)
->icon('heroicon-s-user')       // Solid (24x24)
->icon('heroicon-m-user')       // Mini (20x20)
```

## Common Icons

```php
// Navigation
'heroicon-o-home', 'heroicon-o-cog-6-tooth', 'heroicon-o-user', 'heroicon-o-users'

// Actions
'heroicon-o-plus', 'heroicon-o-pencil', 'heroicon-o-trash', 'heroicon-o-eye', 'heroicon-o-check', 'heroicon-o-x-mark'

// Status
'heroicon-o-check-circle', 'heroicon-o-x-circle', 'heroicon-o-exclamation-triangle', 'heroicon-o-information-circle'

// Misc
'heroicon-o-document', 'heroicon-o-folder', 'heroicon-o-arrow-down-tray', 'heroicon-o-arrow-up-tray', 'heroicon-o-magnifying-glass', 'heroicon-o-funnel'
```

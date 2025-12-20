---
name: Filament 4 Development
description: This skill should be used when the user asks to "create a Filament resource", "add a form field", "build a table", "create a widget", "add an action", "migrate to Filament 4", "fix deprecated Filament code", "upgrade from Filament 3", or works with any Filament admin panel development. Covers v3→v4 migration, components, actions, validation, and best practices.
version: 1.0.0
---

# Filament 4 Development

This skill provides comprehensive guidance for building Filament 4 admin panels in Laravel applications, including migration from v3 and building new features.

## Critical Namespace Changes (v3 → v4)

The major change in Filament 4 is renaming **Forms to Schemas**:

```php
// v3 → v4 namespace changes
use Filament\Forms\Form;                    // → Filament\Schemas\Schema
use Filament\Forms\Components\Section;      // → Filament\Schemas\Components\Section
use Filament\Forms\Get;                     // → Filament\Schemas\Components\Utilities\Get
use Filament\Forms\Set;                     // → Filament\Schemas\Components\Utilities\Set

// Table Actions unified
use Filament\Tables\Actions\*;              // → Filament\Actions\*
```

### Method Signature Changes

```php
// v3 signature
public function form(Form $form): Form
{
    return $form->schema([...]);
}

// v4 signature
public function form(Schema $schema): Schema
{
    return $schema->components([...]);
}
```

## Essential Deprecations Quick Reference

| v3 (Deprecated) | v4 (Replacement) |
|-----------------|------------------|
| `$label`, `getLabel()` | `$modelLabel`, `getModelLabel()` |
| `getCards()` | `getStats()` |
| `cancel()` | `halt()` |
| `modalSubheading()` | `modalDescription()` |
| `fillForm()` | `data()` |
| `form()` / `infolist()` on Action | `schema()` |
| `label()` on Repeater/Builder | `addActionLabel()` |
| `removable()` | `deletable()` |
| `sortable()` | `reorderable()` |
| `height()`, `size()` on ImageColumn | `imageHeight()`, `imageSize()` |
| `rounded()` | `circular()` |
| `options()` on IconColumn | `icons()` |
| `withoutDate()` / `withoutTime()` | `date(false)` / `time(false)` |
| `cacheForm()`, `getForm()` | `cacheSchema()`, `getSchema()` |

For complete deprecation list, see `references/deprecated.md`.

## Non-Static Properties (Changed in v4)

These properties are now **non-static**:

| Property | v4 Declaration |
|----------|----------------|
| `$view` (custom pages) | `protected string` |
| `$pollingInterval` (widgets) | `protected ?string` |
| `$heading` (ChartWidget) | `protected ?string` |

## Creating Resources

Generate resources with Artisan:

```bash
php artisan make:filament-resource Post
php artisan make:filament-resource Post --generate  # Auto-generate form/table
php artisan make:filament-resource Post --simple    # Modal-based CRUD
```

### Basic Resource Structure

```php
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $modelLabel = 'Post';           // Not $label
    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            RichEditor::make('content'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
```

### Advanced Resource Structure (Extracted Schemas)

For larger resources, extract form/table logic to separate classes:

```
Resources/Blog/Posts/
├── PostResource.php          # Main resource
├── Pages/
│   ├── ListPosts.php
│   ├── CreatePost.php
│   └── EditPost.php
├── Schemas/
│   └── PostForm.php          # Extracted form schema
└── Tables/
    └── PostsTable.php        # Extracted table config
```

```php
// PostResource.php - delegates to extracted classes
class PostResource extends Resource
{
    public static function form(Schema $schema): Schema
    {
        return PostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PostsTable::configure($table);
    }
}

// Schemas/PostForm.php
class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Group::make()->columnSpan(['lg' => 2])->schema([
                Section::make()->schema([
                    TextInput::make('title')->required()->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Set $set) =>
                            $operation === 'create' ? $set('slug', Str::slug($state)) : null
                        ),
                    RichEditor::make('content')->columnSpanFull(),
                ]),
            ]),
            Group::make()->columnSpan(['lg' => 1])->schema([
                Section::make('Status')->schema([
                    Toggle::make('is_published'),
                    DateTimePicker::make('published_at'),
                ]),
            ]),
        ])->columns(3);
    }
}
```

## Clusters (Grouping Resources)

Group related resources under a single navigation item:

```php
// Clusters/ProductsCluster.php
use Filament\Clusters\Cluster;

class ProductsCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?int $navigationSort = 0;
    protected static ?string $slug = 'shop/products';
}

// Resources in the cluster reference it
class ProductResource extends Resource
{
    protected static ?string $cluster = ProductsCluster::class;
}

class BrandResource extends Resource
{
    protected static ?string $cluster = ProductsCluster::class;
}
```

## Widget Traits

### InteractsWithPageTable

Access filtered table data in widgets on list pages:

```php
use Filament\Widgets\Concerns\InteractsWithPageTable;

class ProductStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();  // Respects table filters!
        return [
            Stat::make('Total', $query->count()),
            Stat::make('Published', $query->where('is_visible', true)->count()),
        ];
    }
}
```

### InteractsWithPageFilters

Access dashboard-level filters in widgets:

```php
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = Order::query()
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate));

        return [Stat::make('Orders', $query->count())];
    }
}

// Dashboard.php with filters
class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('startDate'),
            DatePicker::make('endDate'),
        ]);
    }
}
```

See `examples/widget.php.example` for complete widget patterns.

## Form Components

### Common Fields

```php
TextInput::make('name')->required()->maxLength(255)
Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])
Toggle::make('is_active')
DateTimePicker::make('published_at')
FileUpload::make('avatar')->image()
RichEditor::make('content')
Repeater::make('items')->relationship()->schema([...])
```

### Reactive Fields

```php
Select::make('country')
    ->options([...])
    ->live()                    // Re-render on change
    ->afterStateUpdated(fn (Set $set, ?string $state) =>
        $set('slug', Str::slug($state))
    )
```

### Visibility & Conditionals

```php
TextInput::make('company')
    ->hidden(fn (Get $get) => ! $get('is_company'))
    ->visible(fn (Get $get) => $get('show_company'))
    ->required(fn (string $operation) => $operation === 'create')
```

For complete component reference, see `references/components.md`.

## Table Columns

### Common Columns

```php
TextColumn::make('title')->searchable()->sortable()->copyable()
TextColumn::make('author.name')->label('Author')  // Relationship
IconColumn::make('is_featured')->boolean()
ImageColumn::make('avatar')->circular()           // Not rounded()
TextColumn::make('status')->badge()->color(fn ($state) => match($state) {
    'draft' => 'gray',
    'published' => 'success',
    default => 'warning',
})
```

### Filters

```php
$table->filters([
    SelectFilter::make('status')->options([...]),
    TernaryFilter::make('is_active'),
    Filter::make('created_at')
        ->form([DatePicker::make('from'), DatePicker::make('until')])
        ->query(fn (Builder $query, array $data) => $query
            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
        ),
])
```

## Actions

Actions are now unified under `Filament\Actions\*`:

```php
use Filament\Actions\Action;

Action::make('approve')
    ->label('Approve')
    ->icon('heroicon-o-check')
    ->color('success')
    ->requiresConfirmation()
    ->modalHeading('Approve Post')
    ->modalDescription('Are you sure?')    // Not modalSubheading()
    ->action(fn (Post $record) => $record->approve())
```

### Action with Modal Form

```php
Action::make('edit')
    ->schema([                              // Not form()
        TextInput::make('name')->required(),
        Select::make('status')->options([...]),
    ])
    ->data(fn ($record) => [               // Not fillForm()
        'name' => $record->name,
    ])
    ->action(function (array $data, $record) {
        $record->update($data);
    })
```

## Widgets

### Stats Widget

```php
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array    // Not getCards()
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('12% increase')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }
}
```

### Chart Widget

```php
use Filament\Widgets\ChartWidget;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Revenue';     // Non-static
    protected ?string $pollingInterval = '10s'; // Non-static

    protected function getType(): string
    {
        return 'line';  // line, bar, pie, doughnut, radar, polarArea
    }

    protected function getData(): array
    {
        return [
            'datasets' => [['label' => 'Revenue', 'data' => [100, 200, 300]]],
            'labels' => ['Jan', 'Feb', 'Mar'],
        ];
    }
}
```

## Layouts

### Section

```php
use Filament\Schemas\Components\Section;

Section::make('User Details')
    ->description('Basic information')
    ->icon('heroicon-o-user')
    ->collapsible()
    ->columns(2)
    ->schema([...])
```

### Tabs

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Settings')
    ->tabs([
        Tabs\Tab::make('General')->icon('heroicon-o-cog')->schema([...]),
        Tabs\Tab::make('Advanced')->schema([...]),
    ])
    ->persistTab()
```

## Notifications

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Saved successfully')
    ->success()
    ->send();

// With actions
Notification::make()
    ->title('Post Published')
    ->body('Your post is now live.')
    ->actions([
        Action::make('view')->button()->url('/posts/1'),
        Action::make('undo')->color('gray')->close(),
    ])
    ->send();
```

## Artisan Commands

```bash
php artisan make:filament-resource Post [--generate] [--simple]
php artisan make:filament-page Settings
php artisan make:filament-widget StatsOverview --stats-overview
php artisan make:filament-widget RevenueChart --chart
php artisan make:filament-widget LatestOrders --table
php artisan make:filament-importer Product [--generate]
php artisan make:filament-exporter Product [--generate]
```

## Additional Resources

### Reference Files

For detailed patterns and complete lists:
- **`references/deprecated.md`** - Complete deprecation list with all replacements
- **`references/components.md`** - Full component reference (forms, tables, infolists)
- **`references/actions.md`** - Actions, modals, wizards, import/export

### Example Files

Working code patterns in `examples/`:
- **`examples/resource.php.example`** - Complete resource with extracted schemas
- **`examples/widget.php.example`** - Stats, charts, and dashboard filter patterns

## Common Migration Patterns

### Fixing Deprecated Widget Properties

```php
// v3 (deprecated)
class MyWidget extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?string $pollingInterval = '10s';
}

// v4 (correct)
class MyWidget extends ChartWidget
{
    protected ?string $heading = 'Chart';          // Non-static
    protected ?string $pollingInterval = '10s';    // Non-static
}
```

### Fixing Deprecated Action Methods

```php
// v3 (deprecated)
Action::make('edit')
    ->form([...])
    ->fillForm(fn ($record) => [...])
    ->modalSubheading('Edit details')

// v4 (correct)
Action::make('edit')
    ->schema([...])                    // Not form()
    ->data(fn ($record) => [...])      // Not fillForm()
    ->modalDescription('Edit details') // Not modalSubheading()
```

### Fixing Deprecated Resource Properties

```php
// v3 (deprecated)
protected static ?string $label = 'Post';
protected static ?string $pluralLabel = 'Posts';

// v4 (correct)
protected static ?string $modelLabel = 'Post';
protected static ?string $pluralModelLabel = 'Posts';
```

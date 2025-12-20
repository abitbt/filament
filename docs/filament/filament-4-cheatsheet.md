# Filament 4 Cheatsheet

A comprehensive reference guide for Filament 4 based on the official documentation.

---

## Table of Contents

- [Namespace Changes (v3 → v4)](#namespace-changes-v3--v4)
- [Forms / Schema](#forms--schema)
- [Validation](#validation)
- [Tables](#tables)
- [Table Summaries](#table-summaries)
- [Table Grouping](#table-grouping)
- [Actions & Modals](#actions--modals)
- [Import & Export Actions](#import--export-actions)
- [Infolists](#infolists-read-only-display)
- [Prime Components](#prime-components)
- [Notifications](#notifications)
- [Database Notifications](#database-notifications)
- [Widgets](#widgets)
- [Layouts](#layouts)
- [Utility Injection](#utility-injection)
- [Global Configuration](#global-configuration)
- [Relationships](#relationships)
- [Colors & Icons](#colors--icons)
- [Quick Reference](#quick-reference)

---

## Namespace Changes (v3 → v4)

```php
// Forms → Schemas
use Filament\Forms\Form;                    // → Filament\Schemas\Schema
use Filament\Forms\Components\Section;      // → Filament\Schemas\Components\Section
use Filament\Forms\Get;                     // → Filament\Schemas\Components\Utilities\Get
use Filament\Forms\Set;                     // → Filament\Schemas\Components\Utilities\Set

// Table Actions moved
use Filament\Tables\Actions\*;              // → Filament\Actions\*
```

### Deprecated Methods → Replacements

| Component | Deprecated | Replacement |
|-----------|------------|-------------|
| Resource | `$label`, `getLabel()` | `$modelLabel`, `getModelLabel()` |
| StatsWidget | `getCards()` | `getStats()` |
| Action | `cancel()` | `halt()` |
| Action | `modalSubheading()` | `modalDescription()` |
| Action | `fillForm()` | `data()` |
| Action | `dispatchEvent()` | `dispatch()` |
| Repeater/Builder | `label()` | `addActionLabel()` |
| Repeater/Builder | `removable()` | `deletable()` |
| Repeater/Builder | `sortable()` | `reorderable()` |
| ImageColumn | `height()`, `size()` | `imageHeight()`, `imageSize()` |
| ImageColumn | `rounded()` | `circular()` |
| IconColumn | `options()` | `icons()` |
| DateTimePicker | `withoutDate()` | `date(false)` |
| DateTimePicker | `withoutTime()` | `time(false)` |
| Forms | `cacheForm()`, `getForm()` | `cacheSchema()`, `getSchema()` |

### Non-Static Properties (v3 → v4)

| Property | v4 Declaration |
|----------|----------------|
| `$view` (custom pages) | `protected string` (non-static) |
| `$pollingInterval` (widgets) | `protected ?string` (non-static) |
| `$heading` (ChartWidget) | `protected ?string` (non-static) |

---

## Forms / Schema

### Basic Field Structure

```php
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

public function form(Schema $schema): Schema
{
    return $schema->components([
        TextInput::make('name')
            ->required()
            ->maxLength(255)
            ->label('Full Name')
            ->placeholder('John Doe')
            ->default('Default Value')
            ->autofocus(),
    ]);
}
```

### Available Form Fields

```php
TextInput::make('name')
Select::make('status')
Checkbox::make('is_active')
Toggle::make('published')
CheckboxList::make('roles')
Radio::make('type')
DateTimePicker::make('published_at')
FileUpload::make('avatar')
RichEditor::make('content')
MarkdownEditor::make('bio')
Repeater::make('items')
Builder::make('blocks')
TagsInput::make('tags')
Textarea::make('description')
KeyValue::make('meta')
ColorPicker::make('color')
ToggleButtons::make('status')
Slider::make('rating')
CodeEditor::make('code')
Hidden::make('user_id')
```

### Field Validation

```php
TextInput::make('email')
    ->required()
    ->email()
    ->unique('users', 'email')
    ->maxLength(255)
    ->minLength(3)
    ->rules(['regex:/^[a-z]+$/'])
```

### Visibility & State

```php
// Hide/Show fields
TextInput::make('company')
    ->hidden(fn (Get $get) => ! $get('is_company'))
    ->visible(fn (Get $get) => $get('show_company'))
    ->hiddenOn('edit')
    ->visibleOn(['create', 'edit'])
    ->disabledOn('edit')

// JavaScript-based visibility (no server request)
TextInput::make('field')
    ->hiddenJs(<<<'JS'
        $get('type') !== 'custom'
    JS)
```

### Reactive Fields

```php
Select::make('country')
    ->options([...])
    ->live()                    // Re-render on change
    ->live(onBlur: true)        // Re-render on blur
    ->live(debounce: 500)       // Debounce 500ms
    ->afterStateUpdated(fn (Set $set, ?string $state) =>
        $set('slug', Str::slug($state))
    )

// JavaScript-based (no server request)
TextInput::make('name')
    ->afterStateUpdatedJs(<<<'JS'
        $set('slug', ($state ?? '').toLowerCase().replaceAll(' ', '-'))
    JS)
```

### Field Lifecycle

```php
TextInput::make('name')
    // Hydration (when form loads)
    ->afterStateHydrated(fn ($component, $state) =>
        $component->state(ucwords($state))
    )
    ->formatStateUsing(fn ($state) => ucwords($state))

    // Dehydration (when form submits)
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
    ->dehydrated(fn (?string $state) => filled($state))
```

### Partial Rendering (Performance)

```php
// Only re-render specific components
TextInput::make('name')
    ->live()
    ->partiallyRenderComponentsAfterStateUpdated(['email', 'slug'])

// Only re-render self
TextInput::make('name')
    ->live()
    ->partiallyRenderAfterStateUpdated()

// Skip render entirely
TextInput::make('name')
    ->live()
    ->skipRenderAfterStateUpdated()
```

### Affixes & Content Slots

```php
TextInput::make('price')
    ->prefix('$')
    ->suffix('.00')
    ->prefixIcon('heroicon-o-currency-dollar')

TextInput::make('name')
    ->belowContent('Helper text here')
    ->aboveContent('Note: This field is required')
    ->beforeLabel(Icon::make(Heroicon::Star))
    ->afterLabel(Action::make('help'))
```

### Fused Groups

```php
use Filament\Schemas\Components\FusedGroup;

FusedGroup::make([
    TextInput::make('city')->placeholder('City'),
    Select::make('country')->options([...]),
])
    ->label('Location')
    ->columns(2)
```

---

## Validation

### Built-in Validation Methods

```php
// String validations
Field::make('name')
    ->required()
    ->string()
    ->alpha()
    ->alphaDash()
    ->alphaNum()
    ->ascii()
    ->startsWith(['Mr', 'Mrs'])
    ->endsWith(['Jr', 'Sr'])
    ->doesntStartWith(['admin'])
    ->doesntEndWith(['test'])

// Length validations
Field::make('bio')
    ->minLength(10)
    ->maxLength(500)

// Numeric validations
Field::make('age')
    ->numeric()
    ->integer()
    ->gt('min_age')              // Greater than field
    ->gte('min_age')             // Greater than or equal
    ->lt('max_age')              // Less than
    ->lte('max_age')             // Less than or equal
    ->multipleOf(5)

// Date validations
Field::make('start_date')
    ->after('today')
    ->afterOrEqual('today')
    ->before('end_date')
    ->beforeOrEqual('end_date')

// Format validations
Field::make('email')->email()
Field::make('url')->activeUrl()
Field::make('ip')->ip()->ipv4()->ipv6()
Field::make('mac')->macAddress()
Field::make('color')->hexColor()
Field::make('data')->json()
Field::make('id')->uuid()->ulid()

// Database validations
Field::make('email')
    ->unique('users', 'email')
    ->unique(table: User::class, column: 'email', ignoreRecord: true)
    ->scopedUnique()             // Uses Eloquent model (respects soft deletes)
    ->exists('users', 'email')
    ->scopedExists()             // Uses Eloquent model

// Enum validation
Field::make('status')->enum(StatusEnum::class)

// Conditional validations
Field::make('company')
    ->requiredIf('is_business', true)
    ->requiredUnless('type', 'personal')
    ->requiredWith('business_name')
    ->requiredWithAll('field1,field2')
    ->requiredWithout('personal_name')
    ->requiredWithoutAll('field1,field2')
    ->requiredIfAccepted('terms')
    ->prohibitedIf('type', 'guest')
    ->prohibitedUnless('role', 'admin')
    ->prohibits(['other_field'])

// Comparison validations
Field::make('password')->confirmed()
Field::make('email')->different('backup_email')
Field::make('confirm')->same('password')
Field::make('status')->in(['pending', 'active'])
Field::make('status')->notIn(['banned', 'deleted'])

// Regex validations
Field::make('code')->regex('/^[A-Z]{3}[0-9]{3}$/')
Field::make('name')->notRegex('/[0-9]/')
```

### Custom Validation Rules

```php
use Closure;

// Laravel rule objects
TextInput::make('slug')->rules([new Uppercase()])

// Closure rules
TextInput::make('slug')->rules([
    fn (): Closure => function (string $attribute, $value, Closure $fail) {
        if ($value === 'admin') {
            $fail('The :attribute cannot be "admin".');
        }
    },
])

// With utility injection
TextInput::make('slug')->rules([
    fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
        if ($get('type') === 'page' && $value === 'home') {
            $fail('Pages cannot use the slug "home".');
        }
    },
])
```

### Validation Messages

```php
TextInput::make('email')
    ->unique()
    ->validationMessages([
        'unique' => 'This :attribute is already registered.',
    ])
    ->validationAttribute('email address')  // Custom attribute name in messages
```

---

## Tables

### Basic Table

```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

public function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')
                ->searchable()
                ->sortable()
                ->copyable(),
            TextColumn::make('author.name')  // Relationship
                ->label('Author'),
            IconColumn::make('is_featured')
                ->boolean(),
        ])
        ->defaultSort('created_at', 'desc');
}
```

### Column Types

```php
TextColumn::make('name')
IconColumn::make('status')->boolean()
ImageColumn::make('avatar')->circular()
ColorColumn::make('color')
SelectColumn::make('status')
ToggleColumn::make('is_active')
TextInputColumn::make('name')
CheckboxColumn::make('is_featured')
```

### Column Formatting

```php
TextColumn::make('price')
    ->money('USD')
    ->numeric(2)
    ->prefix('$')
    ->suffix(' USD')
    ->badge()
    ->color(fn ($state) => match($state) {
        'draft' => 'gray',
        'published' => 'success',
        default => 'warning',
    })

TextColumn::make('created_at')
    ->dateTime('M j, Y')
    ->since()              // "2 hours ago"
    ->date()
    ->time()

TextColumn::make('content')
    ->limit(50)
    ->words(10)
    ->wrap()
    ->html()
    ->markdown()
```

### Toggleable Columns

```php
TextColumn::make('created_at')
    ->toggleable()
    ->toggleable(isToggledHiddenByDefault: true)
```

### Filters

```php
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

$table->filters([
    Filter::make('is_featured')
        ->query(fn (Builder $query) => $query->where('is_featured', true)),

    SelectFilter::make('status')
        ->options([
            'draft' => 'Draft',
            'published' => 'Published',
        ]),

    TernaryFilter::make('email_verified')
        ->nullable(),

    Filter::make('created_at')
        ->form([
            DatePicker::make('from'),
            DatePicker::make('until'),
        ])
        ->query(fn (Builder $query, array $data) => $query
            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date))
        ),
])
```

### Table Actions

```php
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

$table
    ->recordActions([
        Action::make('approve')
            ->action(fn (Post $record) => $record->approve())
            ->requiresConfirmation()
            ->icon('heroicon-o-check')
            ->color('success')
            ->hidden(fn (Post $record) => $record->is_approved),
    ])
    ->toolbarActions([
        BulkActionGroup::make([
            DeleteBulkAction::make(),
        ]),
    ])
```

### Table Options

```php
$table
    ->paginated([10, 25, 50, 100, 'all'])
    ->defaultPaginationPageOption(25)
    ->paginationMode(PaginationMode::Simple)  // or Cursor
    ->striped()
    ->reorderable('sort')
    ->poll('10s')
    ->deferLoading()
    ->searchable()
    ->heading('Posts')
    ->description('Manage your posts')
    ->recordUrl(fn (Model $record) => route('posts.show', $record))
    ->openRecordUrlInNewTab()
    ->recordClasses(fn (Model $record) => match($record->status) {
        'draft' => 'opacity-50',
        default => '',
    })
    ->queryStringIdentifier('posts')  // For multiple tables
```

### Push Columns (Global)

```php
// In AppServiceProvider boot()
Table::configureUsing(function (Table $table) {
    $table->pushColumns([
        TextColumn::make('created_at')
            ->toggleable(isToggledHiddenByDefault: true),
    ]);
});
```

---

## Table Summaries

### Available Summarizers

```php
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Range;
use Filament\Tables\Columns\Summarizers\Sum;

TextColumn::make('rating')
    ->summarize(Average::make())

TextColumn::make('price')
    ->summarize(Sum::make()->money('USD'))

TextColumn::make('quantity')
    ->summarize([
        Average::make(),
        Range::make(),
        Sum::make(),
    ])

IconColumn::make('is_published')
    ->boolean()
    ->summarize(Count::make()->icons())  // Visual icon count
```

### Summarizer Options

```php
Sum::make()
    ->label('Total Revenue')
    ->money('USD')
    ->numeric(decimalPlaces: 2)
    ->prefix('$')
    ->suffix(' USD')
    ->query(fn (Builder $query) => $query->where('is_active', true))

Range::make()
    ->minimalDateTimeDifference()  // For date ranges
    ->minimalTextualDifference()   // For text ranges
    ->excludeNull(false)           // Include null values

// Custom summarizer
Summarizer::make()
    ->label('First Name')
    ->using(fn (Builder $query): string => $query->min('last_name'))
```

---

## Table Grouping

### Basic Grouping

```php
use Filament\Tables\Grouping\Group;

$table
    ->defaultGroup('status')
    ->groups([
        'status',
        'category',
        Group::make('author.name')
            ->label('Author'),
    ])
```

### Group Options

```php
Group::make('status')
    ->label('Status')
    ->collapsible()
    ->getTitleFromRecordUsing(fn (Post $record) => ucfirst($record->status->value))
    ->getDescriptionFromRecordUsing(fn (Post $record) => $record->status->getDescription())
    ->getKeyFromRecordUsing(fn (Post $record) => $record->status->value)

Group::make('created_at')
    ->date()  // Group by date only, ignore time
    ->collapsible()
```

### Table Grouping Options

```php
$table
    ->groups([...])
    ->defaultGroup('status')
    ->collapsedGroupsByDefault()        // All groups collapsed on load
    ->groupsOnly()                      // Hide rows, show only summaries
    ->groupingSettingsHidden()          // Hide grouping UI
    ->groupingDirectionSettingHidden()  // Hide direction control
    ->groupingSettingsInDropdownOnDesktop()
```

---

## Actions & Modals

### Basic Action

```php
use Filament\Actions\Action;
use Filament\Support\Enums\Size;

Action::make('send')
    ->label('Send Email')
    ->icon('heroicon-o-envelope')
    ->iconPosition(IconPosition::After)
    ->color('primary')           // primary, danger, warning, success, gray
    ->size(Size::Large)          // Small, Medium, Large
    ->button()                   // or ->link(), ->iconButton(), ->badge()
    ->outlined()
    ->labeledFrom('md')          // Icon button on mobile, labeled on md+
    ->requiresConfirmation()
    ->action(fn () => $this->sendEmail())
    ->url('/posts')              // Or use URL instead of action
    ->openUrlInNewTab()
```

### Action with Badge

```php
Action::make('notifications')
    ->iconButton()
    ->icon('heroicon-o-bell')
    ->badge(5)
    ->badgeColor('danger')
```

### Action Groups

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

### Modal Forms

```php
Action::make('create')
    ->schema([
        TextInput::make('name')->required(),
        Select::make('category')->options([...]),
    ])
    ->fillForm(fn ($record) => [
        'name' => $record->name,
    ])
    ->action(function (array $data, $record) {
        $record->update($data);
    })
```

### Modal Configuration

```php
Action::make('edit')
    ->modal()                                    // Declare modal exists
    ->modalHeading('Edit Post')
    ->modalDescription('Update the post details')
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
    ->modalHidden(fn () => ! $this->canEdit)
```

### Wizard in Modal

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

### Extra Modal Footer Actions

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

### Nested Actions

```php
Action::make('edit')
    ->extraModalFooterActions([
        Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->record->delete())
            ->cancelParentActions(),  // Close parent modal after
    ])
```

### Action Authorization

```php
Action::make('delete')
    ->visible(fn () => auth()->user()->can('delete', $this->post))
    ->hidden(fn () => ! $this->canDelete)
    ->authorize('delete')                    // Uses policy
    ->authorizationTooltip()                 // Show policy message as tooltip
    ->authorizationNotification()            // Show as notification instead
    ->disabled(fn () => $this->isLocked)
```

### Rate Limiting

```php
Action::make('submit')
    ->rateLimit(5)                           // 5 attempts per minute
    ->rateLimitedNotificationTitle('Slow down!')
```

### Keybindings

```php
Action::make('save')
    ->keyBindings(['command+s', 'ctrl+s'])
```

---

## Import & Export Actions

### Import Action Setup

```bash
# Required migrations
php artisan make:queue-batches-table
php artisan make:notifications-table
php artisan vendor:publish --tag=filament-actions-migrations
php artisan migrate

# Create importer
php artisan make:filament-importer Product
php artisan make:filament-importer Product --generate  # Auto-generate columns
```

### Using Import Action

```php
use App\Filament\Imports\ProductImporter;
use Filament\Actions\ImportAction;

// In table header
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

### Export Action Setup

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

### Import/Export Lifecycle Hooks

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

## Infolists (Read-Only Display)

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Schema;

public function infolist(Schema $schema): Schema
{
    return $schema->components([
        TextEntry::make('name')
            ->label('Full Name')
            ->copyable()
            ->url(fn ($record) => route('users.show', $record)),

        TextEntry::make('email')
            ->url(fn ($state) => "mailto:{$state}")
            ->openUrlInNewTab(),

        IconEntry::make('is_active')
            ->boolean(),

        ImageEntry::make('avatar')
            ->circular(),

        TextEntry::make('created_at')
            ->dateTime()
            ->since(),

        TextEntry::make('bio')
            ->markdown()
            ->columnSpanFull(),

        TextEntry::make('status')
            ->badge()
            ->color(fn ($state) => match($state) {
                'active' => 'success',
                'pending' => 'warning',
                default => 'gray',
            }),
    ]);
}
```

### Entry Types

```php
TextEntry::make('name')
IconEntry::make('status')->boolean()
ImageEntry::make('photo')
ColorEntry::make('brand_color')
CodeEntry::make('json_data')
KeyValueEntry::make('metadata')
RepeatableEntry::make('comments')
```

### Entry Options

```php
TextEntry::make('description')
    ->label('Description')
    ->hiddenLabel()
    ->default('N/A')
    ->placeholder('No description')
    ->tooltip('Additional info')
    ->alignStart()           // or alignCenter(), alignEnd()
    ->inlineLabel()
    ->hidden(fn () => ! $this->showDescription)
    ->hiddenOn('create')
```

### Content Slots

```php
TextEntry::make('name')
    ->aboveLabel([...])
    ->beforeLabel(Icon::make(Heroicon::Star))
    ->afterLabel([...])
    ->belowLabel([...])
    ->aboveContent([...])
    ->beforeContent([...])
    ->afterContent(Action::make('edit'))
    ->belowContent('Helper text')
```

---

## Prime Components

Prime components are basic building blocks for arbitrary content in schemas.

### Text Component

```php
use Filament\Schemas\Components\Text;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\TextSize;

Text::make('Warning: This action cannot be undone.')
    ->color('danger')              // primary, success, danger, warning, info, gray, neutral
    ->size(TextSize::Large)        // ExtraSmall, Small, Medium, Large
    ->weight(FontWeight::Bold)
    ->fontFamily(FontFamily::Mono)
    ->badge()                      // Render as badge
    ->icon(Heroicon::ExclamationTriangle)
    ->tooltip('Important message')
    ->copyable()

// HTML content
Text::make(new HtmlString('<strong>Bold</strong> text'))

// Markdown
Text::make(str('**Bold** text')->inlineMarkdown()->toHtmlString())

// JavaScript-based dynamic content
Text::make(<<<'JS'
    $get('name') ? `Hello, ${$get('name')}` : 'Enter your name'
JS)->js()
```

### Icon Component

```php
use Filament\Schemas\Components\Icon;
use Filament\Support\Icons\Heroicon;

Icon::make(Heroicon::Star)
    ->color('warning')
    ->tooltip('Featured item')
```

### Image Component

```php
use Filament\Schemas\Components\Image;

Image::make(
    url: asset('images/qr.jpg'),
    alt: 'QR code',
)
    ->imageWidth('12rem')
    ->imageHeight('12rem')
    ->imageSize('12rem')          // Both width and height
    ->alignCenter()               // or alignStart(), alignEnd()
    ->tooltip('Scan this code')
```

### Unordered List Component

```php
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Components\Text;

UnorderedList::make([
    'Item one',
    'Item two',
    Text::make('Styled item')->weight(FontWeight::Bold),
])
    ->size(TextSize::Small)
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
    ->success()                    // or ->danger(), ->warning(), ->info()
    ->icon('heroicon-o-check')
    ->iconColor('success')
    ->color('success')             // Background color
    ->duration(5000)               // milliseconds
    ->seconds(5)                   // alternative to duration
    ->persistent()                 // Don't auto-close
    ->actions([
        Action::make('view')
            ->button()
            ->url('/posts/1', shouldOpenInNewTab: true),
        Action::make('undo')
            ->color('gray')
            ->dispatch('undoAction', ['id' => 1])
            ->dispatchSelf('undoAction')
            ->dispatchTo('component', 'undoAction')
            ->close(),
    ])
    ->send();
```

### Custom Notification ID

```php
// Send with custom ID
Notification::make('my-notification')
    ->title('Processing...')
    ->persistent()
    ->send();

// Close by ID
$this->dispatch('close-notification', id: 'my-notification');
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

### Positioning

```php
// In a service provider
use Filament\Notifications\Livewire\Notifications;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;

Notifications::alignment(Alignment::Start);
Notifications::verticalAlignment(VerticalAlignment::End);
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
        ->databaseNotificationsPolling('30s')  // or null to disable
        // ->databaseNotifications(position: DatabaseNotificationsPosition::Sidebar)
}
```

### Sending Database Notifications

```php
use Filament\Notifications\Notification;

$recipient = auth()->user();

// Fluent API
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

// Using Laravel notification
$recipient->notify(
    Notification::make()
        ->title('Saved')
        ->toDatabase()
);
```

### Mark as Read/Unread

```php
Notification::make()
    ->title('New comment')
    ->actions([
        Action::make('view')
            ->markAsRead(),
        Action::make('markAsUnread')
            ->markAsUnread(),
    ])
    ->sendToDatabase($recipient);
```

### Open Notifications Modal

```blade
<button
    x-data="{}"
    x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
>
    View Notifications
</button>
```

---

## Widgets

### Stats Overview

```php
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Analytics';
    protected ?string $description = 'Overview of key metrics';
    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
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
    protected ?string $heading = 'Revenue';
    protected ?string $description = 'Monthly revenue breakdown';
    protected ?string $pollingInterval = '10s';
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

### Chart Filters (Basic)

```php
class RevenueChart extends ChartWidget
{
    public ?string $filter = 'month';  // Default filter

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

### Chart Filters (Custom Schema)

```php
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class RevenueChart extends ChartWidget
{
    use HasFiltersSchema;

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('startDate')->default(now()->subDays(30)),
            DatePicker::make('endDate')->default(now()),
        ]);
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
        // Use filters...
    }
}
```

### Table Widget

```php
// Create with: php artisan make:filament-widget LatestOrders --table

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest()->limit(5))
            ->columns([...]);
    }
}
```

### Widget Properties

```php
protected static ?int $sort = 2;                    // Widget order
protected int | string | array $columnSpan = 'full'; // or 1, 2, ['md' => 2]
protected static bool $isLazy = false;              // Disable lazy loading
protected ?string $pollingInterval = '10s';         // or null to disable

// Conditional visibility
public static function canView(): bool
{
    return auth()->user()->isAdmin();
}
```

### Dashboard Filters

```php
// In Dashboard.php
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                DatePicker::make('startDate'),
                DatePicker::make('endDate'),
            ])->columns(2),
        ]);
    }
}

// In widget
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        // Use filters...
    }
}
```

---

## Layouts

### Grid System

```php
use Filament\Schemas\Components\Grid;

// Simple grid
Grid::make(2)->schema([...])

// Responsive grid
Grid::make([
    'default' => 1,
    'sm' => 2,
    'md' => 3,
    'lg' => 4,
    'xl' => 6,
    '2xl' => 8,
])->schema([...])
```

### Column Span & Start

```php
TextInput::make('name')
    ->columnSpan(2)
    ->columnSpan('full')
    ->columnSpanFull()
    ->columnSpan(['md' => 2, 'xl' => 3])
    ->columnStart(2)
    ->columnStart(['md' => 2, 'xl' => 3])
    ->columnOrder(1)
    ->columnOrder(['default' => 2, 'xl' => 1])
```

### Section

```php
use Filament\Schemas\Components\Section;

Section::make('User Details')
    ->description('Basic user information')
    ->icon('heroicon-o-user')
    ->iconColor('primary')
    ->collapsible()
    ->collapsed()
    ->persistCollapsed()
    ->compact()
    ->columns(2)
    ->aside()                    // Render as sidebar
    ->schema([...])
```

### Tabs

```php
use Filament\Schemas\Components\Tabs;

Tabs::make('Settings')
    ->tabs([
        Tabs\Tab::make('General')
            ->icon('heroicon-o-cog')
            ->badge(5)
            ->badgeColor('success')
            ->schema([...]),
        Tabs\Tab::make('Advanced')
            ->schema([...]),
    ])
    ->persistTab()               // Remember selected tab
    ->contained(false)           // Remove container styling
```

### Wizard (Multi-Step)

```php
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;

Wizard::make([
    Step::make('Account')
        ->description('Create your account')
        ->icon('heroicon-o-user')
        ->completedIcon('heroicon-o-check')
        ->schema([...])
        ->columns(2)
        ->afterValidation(fn () => $this->validateStep1()),
    Step::make('Profile')
        ->schema([...]),
])
    ->skippable()
    ->persistStepInQueryString()
    ->startOnStep(2)
    ->submitAction(view('submit-button'))
```

### Fieldset

```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Address')
    ->columns(2)
    ->contained(false)           // Remove border
    ->schema([...])
```

### Flex Layout

```php
use Filament\Schemas\Components\Flex;

Flex::make([
    Section::make([...])->grow(),        // Takes available space
    Section::make([...])->grow(false),   // Fixed width sidebar
])
    ->from('md')                         // Stack on smaller screens
```

### Spacing

```php
Grid::make()
    ->dense()        // 50% reduced spacing
    ->gap(false)     // No gap between items
```

### Container Queries

```php
Grid::make()
    ->gridContainer()
    ->columns([
        '@md' => 3,      // Container breakpoint
        '@xl' => 4,
        '!@md' => 2,     // Fallback for older browsers
    ])
```

---

## Utility Injection

### Available Utilities

```php
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

function (
    Get $get,                    // Get other field values
    Set $set,                    // Set other field values
    mixed $state,                // Current field state
    mixed $rawState,             // Raw state before casting
    ?Model $record,              // Current Eloquent record
    string $operation,           // 'create', 'edit', 'view'
    Component $livewire,         // Livewire component instance
    Field $component,            // Current field instance
    ?string $old,                // Previous state (in afterStateUpdated)
) {
    // Your logic
}
```

### Type-Safe Get

```php
$get->string('email');
$get->string('email', isNullable: true);  // ?string
$get->integer('age');
$get->float('price');
$get->boolean('is_admin');
$get->array('tags');
$get->date('published_at');
$get->enum('status', StatusEnum::class);
$get->filled('email');  // bool - checks if filled
$get->blank('email');   // bool - checks if blank
```

### Setting State

```php
function (Set $set) {
    $set('title', 'New Title');
    $set('slug', 'new-title', shouldCallUpdatedHooks: true);
}
```

### Action-Specific Utilities

```php
// In actions
function (
    array $data,                 // Modal form data
    array $arguments,            // Action arguments
    Model $record,               // Associated record
    array $mountedActions,       // Parent actions stack
    $schemaGet,                  // Schema data getter
    $schemaSet,                  // Schema data setter
    string $schemaOperation,     // Schema operation
) {
    // Your logic
}
```

---

## Global Configuration

```php
// In AppServiceProvider boot()
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Support\View\Components\ModalComponent;

// Configure all TextInputs
TextInput::configureUsing(function (TextInput $input) {
    $input->maxLength(255);
});

// Configure all Checkboxes
Checkbox::configureUsing(function (Checkbox $checkbox) {
    $checkbox->inline(false);
});

// Configure all Sections
Section::configureUsing(function (Section $section) {
    $section->columns(2);
});

// Configure all Tables
Table::configureUsing(function (Table $table) {
    $table
        ->striped()
        ->paginated([10, 25, 50])
        ->pushColumns([
            TextColumn::make('created_at')
                ->toggleable(isToggledHiddenByDefault: true),
        ]);
});

// Configure all Modals
ModalComponent::closedByClickingAway(false);
ModalComponent::closedByEscaping(false);
ModalComponent::closeButton(false);
ModalComponent::autofocus(false);
```

---

## Relationships

### In Form Layouts

```php
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Fieldset;

// HasOne / BelongsTo / MorphOne
Group::make()
    ->relationship('address')
    ->schema([
        TextInput::make('street'),
        TextInput::make('city'),
    ])

// Conditional relationship saving
Group::make()
    ->relationship(
        'customer',
        condition: fn (?array $state) => filled($state['name']),
    )
    ->schema([...])

// MorphTo with specific model
Group::make()
    ->relationship('commentable', relatedModel: Post::class)
    ->schema([...])
```

### Repeater Relationships

```php
use Filament\Forms\Components\Repeater;

Repeater::make('addresses')
    ->relationship()
    ->schema([
        TextInput::make('street'),
        TextInput::make('city'),
    ])
    ->defaultItems(1)
    ->deletable()
    ->reorderable()
    ->collapsible()
```

### Builder Component

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;

Builder::make('content')
    ->blocks([
        Block::make('heading')
            ->icon('heroicon-o-bookmark')
            ->label(fn (?array $state) => $state['content'] ?? 'Heading')
            ->schema([
                TextInput::make('content')
                    ->label('Heading')
                    ->required(),
                Select::make('level')
                    ->options(['h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3'])
                    ->required(),
            ])
            ->columns(2),
        Block::make('paragraph')
            ->schema([
                RichEditor::make('content')->required(),
            ]),
        Block::make('image')
            ->schema([
                FileUpload::make('url')->image()->required(),
                TextInput::make('alt')->required(),
            ]),
    ])
    ->blockNumbers(false)
    ->blockPickerColumns(3)
    ->collapsible()
    ->cloneable()
    ->reorderable()
    ->minItems(1)
    ->maxItems(10)
```

### In Tables

```php
TextColumn::make('author.name')           // BelongsTo
TextColumn::make('tags.name')             // HasMany/BelongsToMany
    ->badge()
    ->separator(', ')
TextColumn::make('comments_count')        // Count
    ->counts('comments')
```

---

## Colors & Icons

### Available Colors

```php
'primary'   // Theme primary color
'secondary' // Theme secondary
'success'   // Green
'danger'    // Red
'warning'   // Yellow/Orange
'info'      // Blue
'gray'      // Gray/Neutral
```

### Heroicons

```php
->icon('heroicon-o-user')       // Outline (24x24)
->icon('heroicon-s-user')       // Solid (24x24)
->icon('heroicon-m-user')       // Mini (20x20)
```

### Common Icons

```php
// Navigation
'heroicon-o-home'
'heroicon-o-cog-6-tooth'
'heroicon-o-user'
'heroicon-o-users'

// Actions
'heroicon-o-plus'
'heroicon-o-pencil'
'heroicon-o-trash'
'heroicon-o-eye'
'heroicon-o-check'
'heroicon-o-x-mark'

// Status
'heroicon-o-check-circle'
'heroicon-o-x-circle'
'heroicon-o-exclamation-triangle'
'heroicon-o-information-circle'

// Misc
'heroicon-o-document'
'heroicon-o-folder'
'heroicon-o-arrow-down-tray'
'heroicon-o-arrow-up-tray'
'heroicon-o-magnifying-glass'
'heroicon-o-funnel'
```

---

## Quick Reference

### Artisan Commands

```bash
# Resources
php artisan make:filament-resource Post
php artisan make:filament-resource Post --generate  # With form/table
php artisan make:filament-resource Post --simple    # Simple resource

# Pages
php artisan make:filament-page Settings

# Widgets
php artisan make:filament-widget StatsOverview --stats-overview
php artisan make:filament-widget RevenueChart --chart
php artisan make:filament-widget LatestOrders --table

# Other
php artisan make:livewire MyComponent
```

### Common Patterns

```php
// Password field with hashing
TextInput::make('password')
    ->password()
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
    ->dehydrated(fn (?string $state) => filled($state))
    ->required(fn (string $operation) => $operation === 'create')

// Slug generation from title
TextInput::make('title')
    ->live(onBlur: true)
    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
        if (($get('slug') ?? '') !== Str::slug($old)) {
            return;
        }
        $set('slug', Str::slug($state));
    })

// Dependent select
Select::make('country')
    ->options(Country::pluck('name', 'id'))
    ->live()

Select::make('state')
    ->options(fn (Get $get) =>
        State::where('country_id', $get('country'))->pluck('name', 'id')
    )
```

---

## Resources

- [Official Documentation](https://filamentphp.com/docs)
- [GitHub Repository](https://github.com/filamentphp/filament)
- [Discord Community](https://filamentphp.com/discord)

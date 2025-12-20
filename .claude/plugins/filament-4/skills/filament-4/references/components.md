# Filament 4 Components Reference

Complete reference for form fields, table columns, infolist entries, and layout components.

## Form Fields

### Text Inputs

```php
use Filament\Forms\Components\TextInput;

TextInput::make('name')
    ->required()
    ->maxLength(255)
    ->minLength(3)
    ->label('Full Name')
    ->placeholder('John Doe')
    ->default('Default Value')
    ->autofocus()
    ->autocomplete('name')
    ->prefix('$')
    ->suffix('.00')
    ->prefixIcon('heroicon-o-user')

// Password
TextInput::make('password')
    ->password()
    ->revealable()
    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
    ->dehydrated(fn (?string $state) => filled($state))
    ->required(fn (string $operation) => $operation === 'create')

// Email
TextInput::make('email')
    ->email()
    ->unique('users', 'email', ignoreRecord: true)

// Numeric
TextInput::make('price')
    ->numeric()
    ->inputMode('decimal')
    ->step(0.01)

// URL
TextInput::make('website')
    ->url()
    ->suffixIcon('heroicon-o-globe-alt')

// Tel
TextInput::make('phone')
    ->tel()
    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
```

### Textarea

```php
use Filament\Forms\Components\Textarea;

Textarea::make('description')
    ->rows(5)
    ->cols(20)
    ->minLength(10)
    ->maxLength(500)
    ->autosize()
```

### Select

```php
use Filament\Forms\Components\Select;

Select::make('status')
    ->options([
        'draft' => 'Draft',
        'reviewing' => 'Reviewing',
        'published' => 'Published',
    ])
    ->default('draft')
    ->required()
    ->native(false)          // Custom styled select
    ->searchable()
    ->preload()

// Multiple selection
Select::make('categories')
    ->multiple()
    ->options(Category::pluck('name', 'id'))
    ->maxItems(5)

// Relationship
Select::make('author_id')
    ->relationship('author', 'name')
    ->searchable()
    ->preload()
    ->createOptionForm([
        TextInput::make('name')->required(),
        TextInput::make('email')->email()->required(),
    ])
```

### Toggle & Checkbox

```php
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;

Toggle::make('is_active')
    ->default(true)
    ->onColor('success')
    ->offColor('danger')
    ->inline(false)

Checkbox::make('is_admin')
    ->label('Administrator')

CheckboxList::make('roles')
    ->options([
        'admin' => 'Administrator',
        'editor' => 'Editor',
        'viewer' => 'Viewer',
    ])
    ->columns(2)
    ->searchable()
```

### Radio & Toggle Buttons

```php
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\ToggleButtons;

Radio::make('status')
    ->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ])
    ->inline()
    ->descriptions([
        'draft' => 'Not visible to public',
        'published' => 'Visible to everyone',
    ])

ToggleButtons::make('status')
    ->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ])
    ->icons([
        'draft' => 'heroicon-o-pencil',
        'published' => 'heroicon-o-check',
    ])
    ->colors([
        'draft' => 'warning',
        'published' => 'success',
    ])
    ->inline()
```

### DateTimePicker

```php
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;

DatePicker::make('date_of_birth')
    ->native(false)
    ->displayFormat('d/m/Y')
    ->format('Y-m-d')
    ->minValue(now()->subYears(100))      // Not minDate()
    ->maxValue(now())                      // Not maxDate()
    ->closeOnDateSelection()

DateTimePicker::make('published_at')
    ->native(false)
    ->seconds(false)                       // Not withoutSeconds()
    ->timezone('America/New_York')
    ->displayFormat('M j, Y H:i')

TimePicker::make('starts_at')
    ->seconds(false)
    ->minutesStep(15)
```

### File Upload

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('avatar')
    ->image()
    ->avatar()
    ->directory('avatars')
    ->disk('public')
    ->visibility('public')
    ->maxSize(1024)                        // KB
    ->imageEditor()
    ->imageEditorAspectRatios(['1:1', '4:3', '16:9'])
    ->circleCropper()

FileUpload::make('attachments')
    ->multiple()
    ->maxFiles(5)
    ->acceptedFileTypes(['application/pdf', 'image/*'])
    ->downloadable()                       // Not enableDownload()
    ->openable()                           // Not enableOpen()
    ->reorderable()                        // Not enableReordering()
    ->appendFiles()
```

### Rich Text Editors

```php
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\MarkdownEditor;

RichEditor::make('content')
    ->toolbarButtons([
        'bold', 'italic', 'underline', 'strike',
        'h2', 'h3',
        'bulletList', 'orderedList',
        'link', 'blockquote', 'codeBlock',
        'attachFiles',
    ])
    ->fileAttachmentsDirectory('attachments')  // Not attachmentDirectory()
    ->fileAttachmentsVisibility('public')      // Not attachmentVisibility()

MarkdownEditor::make('bio')
    ->toolbarButtons([
        'bold', 'italic', 'link',
        'bulletList', 'orderedList',
        'codeBlock',
    ])
```

### Repeater

```php
use Filament\Forms\Components\Repeater;

Repeater::make('items')
    ->schema([
        TextInput::make('name')->required(),
        TextInput::make('quantity')->numeric()->required(),
        TextInput::make('price')->numeric()->prefix('$'),
    ])
    ->columns(3)
    ->defaultItems(1)
    ->minItems(1)
    ->maxItems(10)
    ->addActionLabel('Add Item')           // Not label()
    ->deletable()                          // Not removable()
    ->reorderable()                        // Not sortable()
    ->collapsible()
    ->cloneable()
    ->itemLabel(fn (array $state) => $state['name'] ?? 'New Item')

// With relationship
Repeater::make('addresses')
    ->relationship()
    ->schema([...])
```

### Builder (Block Editor)

```php
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;

Builder::make('content')
    ->blocks([
        Block::make('heading')
            ->icon('heroicon-o-bookmark')
            ->label(fn (?array $state) => $state['content'] ?? 'Heading')
            ->schema([
                TextInput::make('content')->required(),
                Select::make('level')
                    ->options(['h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4'])
                    ->default('h2'),
            ]),
        Block::make('paragraph')
            ->icon('heroicon-o-bars-3-bottom-left')
            ->schema([
                RichEditor::make('content')->required(),
            ]),
        Block::make('image')
            ->icon('heroicon-o-photo')
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
    ->maxItems(20)
```

### Other Fields

```php
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;

TagsInput::make('tags')
    ->separator(',')
    ->suggestions(['Laravel', 'PHP', 'Filament'])
    ->reorderable()

KeyValue::make('meta')
    ->keyLabel('Property')
    ->valueLabel('Value')
    ->addActionLabel('Add Property')       // Not addButtonLabel()
    ->deletable()                          // Not disableDeletingRows()
    ->reorderable()

ColorPicker::make('color')
    ->rgba()
    ->hexColor()

Hidden::make('user_id')
    ->default(fn () => auth()->id())
```

---

## Table Columns

### Text Column

```php
use Filament\Tables\Columns\TextColumn;

TextColumn::make('title')
    ->searchable()
    ->sortable()
    ->copyable()
    ->limit(50)
    ->words(10)
    ->wrap()
    ->description(fn ($record) => $record->subtitle)
    ->tooltip(fn ($record) => $record->full_title)

// Formatting
TextColumn::make('price')
    ->money('USD')
    ->numeric(decimalPlaces: 2)
    ->prefix('$')
    ->suffix(' USD')

TextColumn::make('created_at')
    ->dateTime('M j, Y')
    ->since()                              // "2 hours ago"
    ->date()
    ->time()
    ->timezone('America/New_York')

// Badge
TextColumn::make('status')
    ->badge()
    ->color(fn (string $state) => match ($state) {
        'draft' => 'gray',
        'reviewing' => 'warning',
        'published' => 'success',
        default => 'primary',
    })
    ->icon(fn (string $state) => match ($state) {
        'draft' => 'heroicon-o-pencil',
        'published' => 'heroicon-o-check',
        default => null,
    })

// HTML/Markdown
TextColumn::make('content')
    ->html()
    ->markdown()
```

### Icon Column

```php
use Filament\Tables\Columns\IconColumn;

IconColumn::make('is_featured')
    ->boolean()

IconColumn::make('status')
    ->icons([                              // Not options()
        'heroicon-o-x-circle' => 'draft',
        'heroicon-o-clock' => 'reviewing',
        'heroicon-o-check-circle' => 'published',
    ])
    ->colors([
        'danger' => 'draft',
        'warning' => 'reviewing',
        'success' => 'published',
    ])
```

### Image Column

```php
use Filament\Tables\Columns\ImageColumn;

ImageColumn::make('avatar')
    ->circular()                           // Not rounded()
    ->imageSize(50)                        // Not size()
    ->imageHeight(60)                      // Not height()
    ->imageWidth(60)
    ->defaultImageUrl(url('/images/placeholder.png'))
    ->stacked()                            // For multiple images
    ->overlap(3)
    ->limit(3)
    ->limitedRemainingText()
```

### Other Columns

```php
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\CheckboxColumn;

ColorColumn::make('color')
    ->copyable()

SelectColumn::make('status')
    ->options([
        'draft' => 'Draft',
        'published' => 'Published',
    ])

ToggleColumn::make('is_active')

TextInputColumn::make('order')
    ->rules(['required', 'integer'])

CheckboxColumn::make('is_featured')
```

### Toggleable Columns

```php
TextColumn::make('created_at')
    ->toggleable()
    ->toggleable(isToggledHiddenByDefault: true)
```

### Relationships

```php
// BelongsTo
TextColumn::make('author.name')
    ->label('Author')

// HasMany / BelongsToMany
TextColumn::make('tags.name')
    ->badge()
    ->separator(', ')
    ->limitList(3)                         // Not limit()
    ->expandableLimitedList()

// Count
TextColumn::make('comments_count')
    ->counts('comments')
    ->sortable()
```

---

## Infolist Entries

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;

TextEntry::make('name')
    ->label('Full Name')
    ->copyable()
    ->url(fn ($record) => route('users.show', $record))
    ->badge()
    ->color('success')

TextEntry::make('email')
    ->url(fn ($state) => "mailto:{$state}")
    ->openUrlInNewTab()

IconEntry::make('is_active')
    ->boolean()

ImageEntry::make('avatar')
    ->circular()                           // Not rounded()
    ->imageSize(100)                       // Not size()

TextEntry::make('created_at')
    ->dateTime()
    ->since()

TextEntry::make('bio')
    ->markdown()
    ->columnSpanFull()

ColorEntry::make('brand_color')

KeyValueEntry::make('metadata')
    ->placeholder('No metadata')           // Not emptyLabel()
```

---

## Layout Components

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
    ->aside()                              // Render as sidebar
    ->schema([...])
```

### Grid

```php
use Filament\Schemas\Components\Grid;

Grid::make(2)->schema([...])

// Responsive
Grid::make([
    'default' => 1,
    'sm' => 2,
    'md' => 3,
    'lg' => 4,
])->schema([...])

// Column span
TextInput::make('name')
    ->columnSpan(2)
    ->columnSpanFull()
    ->columnSpan(['md' => 2, 'xl' => 3])
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
    ->persistTab()
    ->contained(false)
```

### Wizard

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
```

### Fieldset

```php
use Filament\Schemas\Components\Fieldset;

Fieldset::make('Address')
    ->columns(2)
    ->contained(false)
    ->schema([...])
```

### Group (Relationships)

```php
use Filament\Schemas\Components\Group;

// HasOne / BelongsTo / MorphOne
Group::make()
    ->relationship('address')
    ->schema([
        TextInput::make('street'),
        TextInput::make('city'),
    ])

// Conditional saving
Group::make()
    ->relationship(
        'customer',
        condition: fn (?array $state) => filled($state['name']),
    )
    ->schema([...])
```

### Flex Layout

```php
use Filament\Schemas\Components\Flex;

Flex::make([
    Section::make([...])->grow(),          // Takes available space
    Section::make([...])->grow(false),     // Fixed width
])
    ->from('md')                           // Stack on smaller screens
```

### FusedGroup

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

### Built-in Rules

```php
// String
Field::make('name')
    ->required()
    ->string()
    ->alpha()
    ->alphaDash()
    ->alphaNum()
    ->startsWith(['Mr', 'Mrs'])
    ->endsWith(['Jr', 'Sr'])

// Length
Field::make('bio')
    ->minLength(10)
    ->maxLength(500)

// Numeric
Field::make('age')
    ->numeric()
    ->integer()
    ->gt('min_age')
    ->gte('min_age')
    ->lt('max_age')
    ->lte('max_age')

// Date
Field::make('start_date')
    ->after('today')
    ->before('end_date')

// Format
Field::make('email')->email()
Field::make('url')->activeUrl()
Field::make('ip')->ip()->ipv4()->ipv6()
Field::make('color')->hexColor()
Field::make('data')->json()
Field::make('id')->uuid()->ulid()

// Database
Field::make('email')
    ->unique('users', 'email')
    ->unique(table: User::class, column: 'email', ignoreRecord: true)
    ->scopedUnique()
    ->exists('users', 'email')
    ->scopedExists()

// Enum
Field::make('status')->enum(StatusEnum::class)

// Conditional
Field::make('company')
    ->requiredIf('is_business', true)
    ->requiredUnless('type', 'personal')
    ->requiredWith('business_name')
    ->prohibitedIf('type', 'guest')
```

### Custom Validation

```php
use Closure;

// Closure rule
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

// Custom messages
TextInput::make('email')
    ->unique()
    ->validationMessages([
        'unique' => 'This :attribute is already registered.',
    ])
    ->validationAttribute('email address')
```

---

## Utility Injection

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
) {
    // Logic here
}

// Type-safe Get
$get->string('email');
$get->integer('age');
$get->boolean('is_admin');
$get->array('tags');
$get->date('published_at');
$get->enum('status', StatusEnum::class);
$get->filled('email');          // bool
$get->blank('email');           // bool
```

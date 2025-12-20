# Coding Standards

## General Principles

1. **Follow Laravel Conventions** - Use Laravel's built-in features and patterns
2. **Keep It Simple** - Avoid over-engineering
3. **Be Consistent** - Match existing code patterns in the project
4. **Document Complex Logic** - Add PHPDoc for non-obvious code

---

## PHP Standards

### Formatting

This project uses **Laravel Pint** for code formatting. Run before committing:

```bash
./vendor/bin/pint
```

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Classes | PascalCase | `InvoiceService` |
| Methods | camelCase | `calculateTotal()` |
| Properties | camelCase | `$invoiceItems` |
| Constants | SCREAMING_SNAKE | `MAX_RETRY_COUNT` |
| Database tables | snake_case (plural) | `invoice_items` |
| Database columns | snake_case | `created_at` |

### Type Declarations

Always use explicit return types and parameter types:

```php
// Good
public function calculateTotal(Invoice $invoice): float
{
    return $invoice->items->sum('line_total');
}

// Bad
public function calculateTotal($invoice)
{
    return $invoice->items->sum('line_total');
}
```

### Curly Braces

Always use curly braces, even for single-line statements:

```php
// Good
if ($condition) {
    return true;
}

// Bad
if ($condition) return true;
```

---

## Laravel Standards

### Models

```php
<?php

namespace App\Models\Sales;

use App\Enums\InvoiceStatus;
use App\Traits\HasDocumentNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasDocumentNumber;
    use SoftDeletes;

    // 1. Properties
    protected static string $documentType = 'invoice';

    protected $fillable = [
        'number',
        'customer_id',
        // ...
    ];

    // 2. Casts (use method in Laravel 12)
    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'total' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    // 3. Relationships (with return types)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    // 4. Accessors/Mutators
    public function getBalanceDueAttribute(): float
    {
        return $this->total - $this->amount_paid;
    }

    // 5. Scopes
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', [InvoiceStatus::Paid, InvoiceStatus::Void]);
    }

    // 6. Business Logic Methods
    public function isOverdue(): bool
    {
        return $this->due_date->isPast() && $this->status !== InvoiceStatus::Paid;
    }

    public function applyPayment(float $amount): void
    {
        $this->increment('amount_paid', $amount);
        $this->updateStatus();
    }
}
```

### Controllers

Use Form Requests for validation:

```php
// Good - Use Form Request
public function store(StoreInvoiceRequest $request): RedirectResponse
{
    $invoice = Invoice::create($request->validated());
    return redirect()->route('invoices.show', $invoice);
}

// Bad - Inline validation
public function store(Request $request)
{
    $request->validate(['customer_id' => 'required']);
    // ...
}
```

### Services

For complex business logic, use service classes:

```php
<?php

namespace App\Services\Sales;

use App\Models\Sales\Invoice;
use App\Models\Sales\Payment;

class InvoiceService
{
    public function createFromSalesOrder(SalesOrder $salesOrder): Invoice
    {
        return DB::transaction(function () use ($salesOrder) {
            $invoice = Invoice::create([
                'customer_id' => $salesOrder->customer_id,
                'sales_order_id' => $salesOrder->id,
                // ...
            ]);

            foreach ($salesOrder->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->remaining_invoice_quantity,
                    // ...
                ]);
            }

            return $invoice;
        });
    }
}
```

---

## Filament Standards

### Resources

```php
<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\InvoiceResource\Pages;
use App\Models\Sales\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class InvoiceResource extends Resource
{
    // 1. Model binding
    protected static ?string $model = Invoice::class;

    // 2. Navigation (use proper types for Filament 4)
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document';
    protected static string|UnitEnum|null $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 4;
    protected static ?string $recordTitleAttribute = 'number';

    // 3. Form definition
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Form fields...
            ]);
    }

    // 4. Table definition
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Table columns...
            ])
            ->filters([
                // Filters...
            ])
            ->actions([
                // Row actions...
            ])
            ->bulkActions([
                // Bulk actions...
            ])
            ->defaultSort('created_at', 'desc');
    }

    // 5. Relations
    public static function getRelations(): array
    {
        return [
            // Relation managers...
        ];
    }

    // 6. Pages
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
```

### Form Components

```php
// Group related fields in sections
Section::make('Customer Details')
    ->schema([
        Select::make('customer_id')
            ->relationship('customer', 'name')
            ->required()
            ->searchable()
            ->preload()
            ->live()
            ->afterStateUpdated(function (Set $set, $state) {
                // React to changes
            }),

        Select::make('customer_contact_id')
            ->relationship('customerContact', 'name')
            ->visible(fn (Get $get) => filled($get('customer_id'))),
    ])
    ->columns(2);

// Use Repeater for line items
Repeater::make('items')
    ->relationship()
    ->schema([
        // Item fields...
    ])
    ->columns(12)
    ->defaultItems(1)
    ->reorderable()
    ->collapsible();
```

### Table Columns

```php
// Use appropriate column types
TextColumn::make('number')
    ->searchable()
    ->sortable()
    ->weight('bold'),

TextColumn::make('total')
    ->money('BTN')
    ->sortable(),

TextColumn::make('status')
    ->badge(),  // Uses enum's getColor()

TextColumn::make('due_date')
    ->date()
    ->sortable()
    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
```

---

## Enums

### Structure

```php
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel, HasColor, HasIcon
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Paid = 'paid';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Sent => 'Sent',
            self::Paid => 'Paid',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'info',
            self::Paid => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Sent => 'heroicon-o-paper-airplane',
            self::Paid => 'heroicon-o-check-circle',
        };
    }
}
```

---

## Database

### Migrations

```php
// Use descriptive names
// 2024_01_15_create_invoices_table.php

public function up(): void
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();

        // Foreign keys with explicit names
        $table->foreignId('customer_id')->constrained();
        $table->foreignId('sales_order_id')
            ->nullable()
            ->constrained()
            ->nullOnDelete();

        // Use appropriate types
        $table->string('number')->unique();
        $table->date('invoice_date');
        $table->decimal('total', 15, 2)->default(0);
        $table->enum('status', ['draft', 'sent', 'paid']);

        // Virtual columns for computed values
        $table->decimal('balance_due', 15, 2)
            ->virtualAs('total - amount_paid');

        $table->timestamps();
        $table->softDeletes();

        // Indexes for frequently filtered columns
        $table->index('status');
        $table->index('due_date');
    });
}
```

### Relationships

Always define both sides of relationships:

```php
// Invoice model
public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}

// Customer model
public function invoices(): HasMany
{
    return $this->hasMany(Invoice::class);
}
```

---

## Testing

### Feature Tests

```php
<?php

namespace Tests\Feature\Sales;

use App\Models\Sales\Customer;
use App\Models\Sales\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_invoice(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($user)
            ->post(route('invoices.store'), [
                'customer_id' => $customer->id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'customer_id' => $customer->id,
        ]);
    }

    public function test_invoice_calculates_balance_due(): void
    {
        $invoice = Invoice::factory()->create([
            'total' => 1000,
            'amount_paid' => 300,
        ]);

        $this->assertEquals(700, $invoice->balance_due);
    }
}
```

### Unit Tests

```php
<?php

namespace Tests\Unit\Services;

use App\Models\Sales\Invoice;
use App\Services\Sales\InvoiceService;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    public function test_apply_payment_updates_amount_paid(): void
    {
        $invoice = Invoice::factory()->create([
            'total' => 1000,
            'amount_paid' => 0,
        ]);

        $invoice->applyPayment(500);

        $this->assertEquals(500, $invoice->fresh()->amount_paid);
    }
}
```

---

## Git Conventions

### Commit Messages

```
type(scope): description

[optional body]
```

Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

Examples:
```
feat(sales): add invoice PDF generation
fix(inventory): correct stock level calculation
docs: update API documentation
refactor(models): extract HasDocumentNumber trait
```

### Branch Naming

```
feature/invoice-pdf-export
bugfix/stock-calculation-error
hotfix/payment-processing
```

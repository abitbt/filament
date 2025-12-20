# Filament v4 Testing Guide

## Overview

Filament testing relies on **Livewire testing helpers** since all Filament components are mounted to Livewire components.

**Livewire components** (testable with `Livewire::test()`):
- Panel pages and resource page classes
- Relation managers
- Widgets

**Non-Livewire classes** (tested differently):
- Resource classes
- Schema components
- Actions

---

## Authentication Setup

```php
protected function setUp(): void
{
    parent::setUp();
    $this->actingAs(User::factory()->create());
}
```

---

## Testing Resources

### List Pages

```php
use function Livewire\livewire;

livewire(ListUsers::class)
    ->assertOk()
    ->assertCanSeeTableRecords($users)
    ->assertCanNotSeeTableRecords($otherUsers);
```

**Search:**
```php
livewire(ListUsers::class)
    ->searchTable('john')
    ->assertCanSeeTableRecords($matchingUsers)
    ->assertCanNotSeeTableRecords($nonMatchingUsers);
```

**Sorting:**
```php
livewire(ListUsers::class)
    ->sortTable('name')
    ->assertCanSeeTableRecords($sortedUsers, inOrder: true);

livewire(ListUsers::class)
    ->sortTable('name', 'desc')
    ->assertCanSeeTableRecords($sortedUsersDesc, inOrder: true);
```

**Filtering:**
```php
livewire(ListUsers::class)
    ->filterTable('status', 'active')
    ->assertCanSeeTableRecords($activeUsers)
    ->assertCanNotSeeTableRecords($inactiveUsers);
```

**Bulk Actions:**
```php
livewire(ListUsers::class)
    ->selectTableRecords($users->pluck('id')->toArray())
    ->callAction(TestAction::make('delete')->table()->bulk())
    ->assertDatabaseMissing('users', ['id' => $users->first()->id]);
```

### Create Pages

```php
livewire(CreateUser::class)
    ->fillForm([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ])
    ->call('create')
    ->assertHasNoFormErrors()
    ->assertNotified();

$this->assertDatabaseHas('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);
```

**Validation Testing:**
```php
livewire(CreateUser::class)
    ->fillForm([
        'name' => '',
        'email' => 'invalid-email',
    ])
    ->call('create')
    ->assertHasFormErrors([
        'name' => 'required',
        'email' => 'email',
    ]);
```

### Edit Pages

```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertOk()
    ->assertSchemaStateSet([
        'name' => $user->name,
        'email' => $user->email,
    ]);
```

**Updating:**
```php
livewire(EditUser::class, ['record' => $user->id])
    ->fillForm([
        'name' => 'Updated Name',
    ])
    ->call('save')
    ->assertHasNoFormErrors();

$this->assertDatabaseHas('users', [
    'id' => $user->id,
    'name' => 'Updated Name',
]);
```

**Deleting:**
```php
livewire(EditUser::class, ['record' => $user->id])
    ->callAction(DeleteAction::class)
    ->assertDatabaseMissing('users', ['id' => $user->id]);
```

### View Pages

```php
livewire(ViewUser::class, ['record' => $user->id])
    ->assertOk()
    ->assertSchemaStateSet([
        'name' => $user->name,
        'email' => $user->email,
    ]);
```

### Relation Managers

```php
// Verify relation manager renders on parent page
$this->get(UserResource::getUrl('edit', ['record' => $user]))
    ->assertSeeLivewire(PostsRelationManager::class);

// Test relation manager independently
livewire(PostsRelationManager::class, [
    'ownerRecord' => $user,
    'pageClass' => EditUser::class,
])
    ->assertCanSeeTableRecords($user->posts);
```

---

## Testing Tables

### Column Assertions

```php
livewire(ListUsers::class)
    ->assertCanRenderTableColumn('name')
    ->assertCanNotRenderTableColumn('secret_field')
    ->assertTableColumnVisible('email')
    ->assertTableColumnHidden('internal_notes');
```

**Column State:**
```php
livewire(ListUsers::class)
    ->assertTableColumnStateSet('name', 'John Doe', $user)
    ->assertTableColumnFormattedStateSet('created_at', 'Jan 1, 2024', $user);
```

**Column Existence with Callback:**
```php
livewire(ListUsers::class)
    ->assertTableColumnExists('status', function ($column) {
        return $column->isSortable();
    });
```

### Searching

**Global Search:**
```php
livewire(ListUsers::class)
    ->searchTable('john')
    ->assertCanSeeTableRecords($results);
```

**Column-Specific Search:**
```php
livewire(ListUsers::class)
    ->searchTableColumns(['email' => '@example.com'])
    ->assertCanSeeTableRecords($results);
```

### Filters

```php
livewire(ListUsers::class)
    ->filterTable('is_admin', true)
    ->assertCanSeeTableRecords($admins)
    ->resetTableFilters()
    ->assertCanSeeTableRecords($allUsers);
```

**Filter Visibility:**
```php
livewire(ListUsers::class)
    ->assertTableFilterVisible('status')
    ->assertTableFilterHidden('internal_filter');
```

### Summaries

```php
livewire(ListOrders::class)
    ->assertTableColumnSummarySet('total', 'sum', 1500.00);
```

### Toggleable Columns

```php
livewire(ListUsers::class)
    ->toggleAllTableColumns()      // enable all
    ->toggleAllTableColumns(false); // disable all
```

---

## Testing Schemas/Forms

### Filling Forms

```php
livewire(CreateUser::class)
    ->fillForm([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);
```

**Multi-Schema Components:**
```php
livewire(EditUser::class, ['record' => $user->id])
    ->fillForm(['field' => 'value'], 'schemaName');
```

### State Assertions

```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertSchemaStateSet([
        'name' => $user->name,
        'email' => $user->email,
    ]);
```

**With Callback:**
```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertSchemaStateSet(function (array $state) {
        $this->assertEquals('expected', $state['field']);
        return true;
    });
```

### Validation

```php
livewire(CreateUser::class)
    ->fillForm(['email' => 'invalid'])
    ->call('create')
    ->assertHasFormErrors(['email' => 'email']);

livewire(CreateUser::class)
    ->fillForm(['email' => 'valid@example.com'])
    ->call('create')
    ->assertHasNoFormErrors();
```

### Field Assertions

```php
livewire(CreateUser::class)
    ->assertFormFieldExists('name')
    ->assertFormFieldDoesNotExist('secret_field')
    ->assertFormFieldVisible('email')
    ->assertFormFieldHidden('internal_field')
    ->assertFormFieldEnabled('name')
    ->assertFormFieldDisabled('readonly_field');
```

**Field Configuration:**
```php
livewire(CreateUser::class)
    ->assertFormFieldExists('status', function ($field) {
        return $field->isDisabled();
    });
```

### Repeaters and Builders

Always call `fake()` at test start for predictable keys:

```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;

Repeater::fake();
Builder::fake();

livewire(EditPost::class, ['record' => $post->id])
    ->assertSchemaStateSet([
        'items' => [
            0 => ['name' => 'First'],
            1 => ['name' => 'Second'],
        ],
    ]);
```

### Wizards

```php
livewire(CreateOrder::class)
    ->assertWizardCurrentStep(1)
    ->fillForm(['customer_id' => $customer->id])
    ->goToNextWizardStep()
    ->assertWizardCurrentStep(2)
    ->goToPreviousWizardStep()
    ->assertWizardCurrentStep(1)
    ->goToWizardStep(3)
    ->assertWizardCurrentStep(3);
```

---

## Testing Actions

### Basic Invocation

```php
livewire(EditUser::class, ['record' => $user->id])
    ->callAction('send')
    ->assertNotified();
```

### Table Actions

**Row Actions:**
```php
livewire(ListUsers::class)
    ->callAction(TestAction::make('delete')->table($user));
```

**Header Actions:**
```php
livewire(ListUsers::class)
    ->callAction(TestAction::make('create')->table());
```

**Bulk Actions:**
```php
livewire(ListUsers::class)
    ->selectTableRecords($users->pluck('id')->toArray())
    ->callAction(TestAction::make('delete')->table()->bulk());
```

### Schema Component Actions

```php
livewire(EditUser::class, ['record' => $user->id])
    ->callAction(TestAction::make('lookup')->schemaComponent('address_id'));
```

### Action with Form Data

```php
livewire(EditInvoice::class, ['record' => $invoice->id])
    ->callAction('send', data: [
        'email' => 'recipient@example.com',
        'subject' => 'Your Invoice',
    ])
    ->assertHasNoFormErrors()
    ->assertNotified();
```

### Visibility and State

```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertActionExists('delete')
    ->assertActionDoesNotExist('secret_action')
    ->assertActionVisible('edit')
    ->assertActionHidden('admin_only')
    ->assertActionEnabled('save')
    ->assertActionDisabled('locked_action');
```

**Action Order:**
```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertActionsExistInOrder(['save', 'cancel', 'delete']);
```

### Action Properties

```php
livewire(EditUser::class, ['record' => $user->id])
    ->assertActionHasLabel('delete', 'Delete User')
    ->assertActionHasIcon('delete', 'heroicon-o-trash')
    ->assertActionHasColor('delete', 'danger')
    ->assertActionHasUrl('website', 'https://example.com')
    ->assertActionShouldOpenUrlInNewTab('website');
```

### Modal Testing

```php
livewire(EditInvoice::class, ['record' => $invoice->id])
    ->mountAction('send')
    ->assertMountedActionModalSee($invoice->customer->email)
    ->assertMountedActionModalDontSee('secret info');
```

### Action Halting

```php
livewire(EditUser::class, ['record' => $user->id])
    ->callAction('validate')
    ->assertActionHalted('validate');
```

### Action Arguments

```php
livewire(ListUsers::class)
    ->callAction(
        TestAction::make('process')
            ->table($user)
            ->arguments(['mode' => 'fast'])
    );
```

---

## Testing Notifications

### Basic Assertions

```php
livewire(CreateUser::class)
    ->fillForm(['name' => 'John'])
    ->call('create')
    ->assertNotified();
```

### With Title

```php
livewire(CreateUser::class)
    ->call('create')
    ->assertNotified('User created successfully');
```

### Exact Match

```php
use Filament\Notifications\Notification;

livewire(CreateUser::class)
    ->call('create')
    ->assertNotified(
        Notification::make()
            ->success()
            ->title('User created')
            ->body('The user has been created successfully.')
    );
```

### Negative Assertions

```php
livewire(CreateUser::class)
    ->assertNotNotified();

livewire(CreateUser::class)
    ->assertNotNotified('Error message');
```

---

## Multi-Panel Testing

When testing non-default panels:

```php
use Filament\Facades\Filament;

protected function setUp(): void
{
    parent::setUp();

    Filament::setCurrentPanel('admin');
    $this->actingAs(User::factory()->create());
}
```

---

## Required Imports

```php
use Livewire\Livewire;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;
use Filament\Notifications\Notification;
use Filament\Testing\TestAction;
```

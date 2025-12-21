<?php

use App\Enums\UserStatus;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    $this->superAdminRole = Role::where('slug', 'super-admin')->first();
    $this->adminRole = Role::where('slug', 'admin')->first();
    $this->viewerRole = Role::where('slug', 'viewer')->first();
});

describe('list users page', function () {
    it('can render the list page', function () {
        $user = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($user);

        Livewire::test(ListUsers::class)
            ->assertOk();
    });

    it('can see table records', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $users = User::factory()->count(3)->create();

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($users);
    });

    it('can search users by name', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $john = User::factory()->create(['name' => 'John Doe']);
        $jane = User::factory()->create(['name' => 'Jane Smith']);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$john])
            ->assertCanNotSeeTableRecords([$jane]);
    });

    it('can search users by email', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user1 = User::factory()->create(['email' => 'test@company.com']);
        $user2 = User::factory()->create(['email' => 'other@example.org']);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->searchTable('company.com')
            ->assertCanSeeTableRecords([$user1])
            ->assertCanNotSeeTableRecords([$user2]);
    });

    it('can filter by status', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $activeUser = User::factory()->create(['status' => UserStatus::Active]);
        $inactiveUser = User::factory()->inactive()->create();

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$activeUser])
            ->assertCanNotSeeTableRecords([$inactiveUser]);
    });

    it('can sort by name', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id, 'name' => 'Zoe']);
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->sortTable('name')
            ->assertCanSeeTableRecords([$alice, $bob, $admin], inOrder: true);
    });
});

describe('create user page', function () {
    it('can render the create page', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->assertOk();
    });

    it('can create a user', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'status' => 'active',
                'role_id' => $this->viewerRole->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'status' => 'active',
            'role_id' => $this->viewerRole->id,
        ]);
    });

    it('validates required fields', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'email' => '',
                'password' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'email', 'password']);
    });

    it('validates email format', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'invalid-email',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'email']);
    });

    it('validates unique email', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New User',
                'email' => 'taken@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->call('create')
            ->assertHasFormErrors(['email']);
    });

    it('validates password confirmation', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
            ])
            ->call('create')
            ->assertHasFormErrors(['password']);
    });
});

describe('edit user page', function () {
    it('can render the edit page', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create();

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertOk();
    });

    it('populates form with existing data', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'status' => UserStatus::Active,
            'role_id' => $this->viewerRole->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->assertSchemaStateSet([
                'name' => 'Existing User',
                'email' => 'existing@example.com',
                'status' => UserStatus::Active,
                'role_id' => $this->viewerRole->id,
            ]);
    });

    it('can update a user', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create(['role_id' => $this->viewerRole->id]);

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm([
                'name' => 'Updated Name',
                'role_id' => $this->viewerRole->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    });

    it('can update password', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create(['role_id' => $this->viewerRole->id]);
        $originalPassword = $user->password;

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->fillForm([
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'role_id' => $this->viewerRole->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user->refresh();
        expect($user->password)->not->toBe($originalPassword);
    });

    it('can delete a user', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create();

        $this->actingAs($admin);

        Livewire::test(EditUser::class, ['record' => $user->id])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    });
});

describe('authorization', function () {
    it('denies access to viewers for create', function () {
        $viewer = User::factory()->create(['role_id' => $this->viewerRole->id]);

        $this->actingAs($viewer);

        $this->get(UserResource::getUrl('create'))
            ->assertForbidden();
    });

    it('allows admin to access create page', function () {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $this->actingAs($admin);

        $this->get(UserResource::getUrl('create'))
            ->assertSuccessful();
    });
});

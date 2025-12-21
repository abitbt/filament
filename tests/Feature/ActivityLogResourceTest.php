<?php

use App\Enums\ActivityEvent;
use App\Filament\Resources\ActivityLogResource;
use App\Filament\Resources\ActivityLogResource\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogResource\Pages\ViewActivityLog;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
    $this->superAdminRole = Role::where('slug', 'super-admin')->first();
    $this->viewerRole = Role::where('slug', 'viewer')->first();
});

describe('list activity logs page', function () {
    it('can render the list page', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        Livewire::test(ListActivityLogs::class)
            ->assertOk();
    });

    it('displays activity logs', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $logs = collect();
        for ($i = 0; $i < 3; $i++) {
            $logs->push(ActivityLog::create([
                'user_id' => $admin->id,
                'subject_type' => User::class,
                'subject_id' => $admin->id,
                'event' => ActivityEvent::Login,
                'description' => 'User logged in',
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ]));
        }

        $this->actingAs($admin);

        Livewire::test(ListActivityLogs::class)
            ->assertCanSeeTableRecords($logs);
    });

    it('can filter by event type', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $loginLog = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => ActivityEvent::Login,
            'description' => 'User logged in',
            'created_at' => now(),
        ]);

        $createLog = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => ActivityEvent::Created,
            'description' => 'User created',
            'created_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(ListActivityLogs::class)
            ->filterTable('event', 'login')
            ->assertCanSeeTableRecords([$loginLog])
            ->assertCanNotSeeTableRecords([$createLog]);
    });

    it('can search by description', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $log1 = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => ActivityEvent::Login,
            'description' => 'User logged in successfully',
            'created_at' => now(),
        ]);

        $log2 = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => Role::class,
            'subject_id' => 1,
            'event' => ActivityEvent::Created,
            'description' => 'Created new role',
            'created_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(ListActivityLogs::class)
            ->searchTable('logged in')
            ->assertCanSeeTableRecords([$log1])
            ->assertCanNotSeeTableRecords([$log2]);
    });
});

describe('view activity log page', function () {
    it('can render the view page', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $log = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => ActivityEvent::Login,
            'description' => 'User logged in',
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Mozilla/5.0',
            'created_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(ViewActivityLog::class, ['record' => $log->id])
            ->assertOk();
    });

    it('displays activity log details', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $log = ActivityLog::create([
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $admin->id,
            'event' => ActivityEvent::Updated,
            'description' => 'Updated user profile',
            'properties' => [
                'old' => ['name' => 'Old Name'],
                'new' => ['name' => 'New Name'],
            ],
            'ip_address' => '10.0.0.1',
            'created_at' => now(),
        ]);

        $this->actingAs($admin);

        Livewire::test(ViewActivityLog::class, ['record' => $log->id])
            ->assertSchemaStateSet([
                'description' => 'Updated user profile',
                'ip_address' => '10.0.0.1',
            ]);
    });
});

describe('activity log creation via observers', function () {
    it('logs user creation', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        $newUser = User::factory()->create(['name' => 'New Test User']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $newUser->id,
            'event' => ActivityEvent::Created->value,
        ]);
    });

    it('logs user update', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);
        $user = User::factory()->create(['name' => 'Original Name']);

        $this->actingAs($admin);

        $user->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'event' => ActivityEvent::Updated->value,
        ]);
    });

    it('logs role creation', function () {
        $admin = User::factory()->create(['role_id' => $this->superAdminRole->id]);

        $this->actingAs($admin);

        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'subject_type' => Role::class,
            'subject_id' => $role->id,
            'event' => ActivityEvent::Created->value,
        ]);
    });
});

describe('authorization', function () {
    it('cannot create activity logs directly', function () {
        expect(ActivityLogResource::canCreate())->toBeFalse();
    });

    it('allows viewers to view activity logs', function () {
        $viewer = User::factory()->create(['role_id' => $this->viewerRole->id]);

        $this->actingAs($viewer);

        $this->get(ActivityLogResource::getUrl('index'))
            ->assertSuccessful();
    });
});

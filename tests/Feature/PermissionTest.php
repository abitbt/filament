<?php

use App\Enums\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class]);
});

it('groups permissions correctly', function () {
    $grouped = Permission::grouped();

    expect($grouped)->toHaveKeys(['Users', 'Roles', 'Activity Logs']);
    expect($grouped['Users'])->toHaveCount(3);
    expect($grouped['Roles'])->toHaveCount(3);
    expect($grouped['Activity Logs'])->toHaveCount(3);
});

it('returns permission labels correctly', function () {
    expect(Permission::UsersRead->getLabel())->toBe('Read');
    expect(Permission::UsersWrite->getLabel())->toBe('Write');
    expect(Permission::RolesDelete->getLabel())->toBe('Delete');
});

it('returns permission groups correctly', function () {
    expect(Permission::UsersRead->getGroup())->toBe('Users');
    expect(Permission::RolesWrite->getGroup())->toBe('Roles');
    expect(Permission::ActivityLogsDelete->getGroup())->toBe('Activity Logs');
});

it('returns access levels correctly', function () {
    expect(Permission::UsersRead->getAccessLevel())->toBe(1);
    expect(Permission::UsersWrite->getAccessLevel())->toBe(2);
    expect(Permission::UsersDelete->getAccessLevel())->toBe(3);
});

it('gets permissions for resource at level', function () {
    // Level 0 = no permissions
    expect(Permission::forResourceAtLevel('Users', 0))->toBeEmpty();

    // Level 1 = read only
    $level1 = Permission::forResourceAtLevel('Users', 1);
    expect($level1)->toHaveCount(1);
    expect($level1[0])->toBe(Permission::UsersRead);

    // Level 2 = read + write
    $level2 = Permission::forResourceAtLevel('Users', 2);
    expect($level2)->toHaveCount(2);

    // Level 3 = read + write + delete
    $level3 = Permission::forResourceAtLevel('Users', 3);
    expect($level3)->toHaveCount(3);
});

it('provides options for select fields', function () {
    $options = Permission::options();

    expect($options)->toBeArray();
    expect($options)->toHaveCount(9);
    expect($options['users.read'])->toBe('Read');
});

describe('super admin bypasses all permissions', function () {
    it('bypasses all permission checks', function () {
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $user = User::factory()->create(['role_id' => $superAdminRole->id]);

        expect($user->isSuperAdmin())->toBeTrue();
        expect($user->can('users.read'))->toBeTrue();
        expect($user->can('roles.delete'))->toBeTrue();
        expect($user->can('some.random.ability'))->toBeTrue();
    });
});

describe('regular users check permissions', function () {
    it('allows users with correct permissions', function () {
        $adminRole = Role::where('slug', 'admin')->first();
        $user = User::factory()->create(['role_id' => $adminRole->id]);

        expect($user->isSuperAdmin())->toBeFalse();
        expect($user->can('users.read'))->toBeTrue();
        expect($user->can('users.write'))->toBeTrue();
        expect($user->can('roles.delete'))->toBeTrue();
    });

    it('denies users without correct permissions', function () {
        $viewerRole = Role::where('slug', 'viewer')->first();
        $user = User::factory()->create(['role_id' => $viewerRole->id]);

        // Viewer can read
        expect($user->can('users.read'))->toBeTrue();
        expect($user->can('roles.read'))->toBeTrue();

        // Viewer cannot write or delete
        expect($user->can('users.write'))->toBeFalse();
        expect($user->can('users.delete'))->toBeFalse();
        expect($user->can('roles.write'))->toBeFalse();
    });

    it('editor has read and write but not delete', function () {
        $editorRole = Role::where('slug', 'editor')->first();
        $user = User::factory()->create(['role_id' => $editorRole->id]);

        // Can read and write
        expect($user->can('users.read'))->toBeTrue();
        expect($user->can('users.write'))->toBeTrue();

        // Cannot delete
        expect($user->can('users.delete'))->toBeFalse();

        // Can read and write roles
        expect($user->can('roles.read'))->toBeTrue();
        expect($user->can('roles.write'))->toBeTrue();
        expect($user->can('roles.delete'))->toBeFalse();
    });
});

describe('user without role', function () {
    it('denies all permissions', function () {
        $user = User::factory()->create(['role_id' => null]);

        expect($user->can('users.read'))->toBeFalse();
        expect($user->can('roles.read'))->toBeFalse();
    });
});

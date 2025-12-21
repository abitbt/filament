<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin - bypasses all permissions via Gate::before()
        Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Full access to all system features',
                'is_default' => false,
            ]
        );

        // Admin - all permissions (read, write, delete for all resources)
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Administrative access with all permissions',
                'is_default' => false,
            ]
        );
        $admin->permissions()->sync(Permission::all());

        // Editor - read + write (no delete)
        $editor = Role::firstOrCreate(
            ['slug' => 'editor'],
            [
                'name' => 'Editor',
                'description' => 'Can view and edit content',
                'is_default' => false,
            ]
        );
        $editorPermissions = Permission::whereIn('name', [
            PermissionEnum::UsersRead->value,
            PermissionEnum::UsersWrite->value,
            PermissionEnum::RolesRead->value,
            PermissionEnum::RolesWrite->value,
            PermissionEnum::ActivityLogsRead->value,
            PermissionEnum::ActivityLogsWrite->value,
        ])->get();
        $editor->permissions()->sync($editorPermissions);

        // Viewer - read only (default role)
        $viewer = Role::firstOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access to view content',
                'is_default' => true,
            ]
        );
        $viewerPermissions = Permission::whereIn('name', [
            PermissionEnum::UsersRead->value,
            PermissionEnum::RolesRead->value,
            PermissionEnum::ActivityLogsRead->value,
        ])->get();
        $viewer->permissions()->sync($viewerPermissions);
    }
}

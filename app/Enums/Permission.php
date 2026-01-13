<?php

namespace App\Enums;

enum Permission: string
{
    // Users
    case UsersRead = 'users.read';
    case UsersWrite = 'users.write';
    case UsersDelete = 'users.delete';

    // Roles
    case RolesRead = 'roles.read';
    case RolesWrite = 'roles.write';
    case RolesDelete = 'roles.delete';

    // Activity Logs (read-only - logs are system-generated and immutable)
    case ActivityLogsRead = 'activity_logs.read';
    case ActivityLogsDelete = 'activity_logs.delete';

    public function getGroup(): string
    {
        return match (true) {
            str_starts_with($this->value, 'users.') => 'Users',
            str_starts_with($this->value, 'roles.') => 'Roles',
            str_starts_with($this->value, 'activity_logs.') => 'Activity Logs',
            default => 'Other',
        };
    }

    public function getAction(): string
    {
        return str($this->value)->afterLast('.')->toString();
    }

    public function getLabel(): string
    {
        return str($this->value)->afterLast('.')->headline()->toString();
    }

    /**
     * Get the access level for this permission (used for radio UI).
     * Higher level implies all lower permissions.
     */
    public function getAccessLevel(): int
    {
        return match ($this->getAction()) {
            'read' => 1,
            'write' => 2,
            'delete' => 3,
            default => 0,
        };
    }

    /**
     * Get permissions for a resource at a given access level.
     * Level 3 (delete) includes read + write + delete.
     *
     * @return array<self>
     */
    public static function forResourceAtLevel(string $group, int $level): array
    {
        if ($level === 0) {
            return [];
        }

        return collect(self::cases())
            ->filter(fn (self $p) => $p->getGroup() === $group && $p->getAccessLevel() <= $level)
            ->values()
            ->all();
    }

    /**
     * Get all unique resource groups.
     *
     * @return array<string>
     */
    public static function groups(): array
    {
        return collect(self::cases())
            ->map(fn (self $p) => $p->getGroup())
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Get permissions grouped by resource.
     *
     * @return array<string, array<string, self>>
     */
    public static function grouped(): array
    {
        $grouped = [];
        foreach (self::cases() as $permission) {
            $grouped[$permission->getGroup()][$permission->value] = $permission;
        }

        return $grouped;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $permission) => [$permission->value => $permission->getLabel()])
            ->all();
    }

    /**
     * Get icon for a resource group.
     */
    public static function getGroupIcon(string $group): string
    {
        return match ($group) {
            'Users' => 'heroicon-o-users',
            'Roles' => 'heroicon-o-shield-check',
            'Activity Logs' => 'heroicon-o-clipboard-document-list',
            default => 'heroicon-o-squares-2x2',
        };
    }
}

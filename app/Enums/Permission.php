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
     * Get the access levels available for a resource group.
     * Always includes 0 (None). Other levels reflect which permissions actually exist.
     * Example: 'Activity Logs' has only Read + Delete, so this returns [0, 1, 3].
     *
     * @return array<int>
     */
    public static function levelsForGroup(string $group): array
    {
        $levels = collect(self::cases())
            ->filter(fn (self $p) => $p->getGroup() === $group)
            ->map(fn (self $p) => $p->getAccessLevel())
            ->unique()
            ->sort()
            ->values()
            ->all();

        return [0, ...$levels];
    }

    /**
     * Get level → label lookup for a group. Relabels level 3 to "View & Delete"
     * when the group skips level 2 (e.g. Activity Logs has Read + Delete but no Write).
     *
     * @return array<int, string>
     */
    public static function labelsForGroup(string $group): array
    {
        $defaults = [
            0 => 'None',
            1 => 'View',
            2 => 'View & Edit',
            3 => 'View, Edit & Delete',
        ];

        $levels = self::levelsForGroup($group);

        $labels = [];
        foreach ($levels as $level) {
            $labels[$level] = $defaults[$level] ?? "Level {$level}";
        }

        if (in_array(3, $levels, true) && ! in_array(2, $levels, true)) {
            $labels[3] = 'View & Delete';
        }

        return $labels;
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

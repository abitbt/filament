<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'slug' => fn (array $attributes) => str($attributes['name'])->slug()->toString(),
            'description' => fake()->sentence(),
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full access to all system features',
            'is_default' => false,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrative access with most permissions',
            'is_default' => false,
        ]);
    }

    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Can view and edit content',
            'is_default' => false,
        ]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Viewer',
            'slug' => 'viewer',
            'description' => 'Read-only access to view content',
            'is_default' => true,
        ]);
    }
}

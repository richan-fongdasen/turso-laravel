<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use RichanFongdasen\Turso\Tests\Fixtures\Models\Role;

class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->text(rand(5, 10)),
        ];
    }

    public function modelName(): string
    {
        return Role::class;
    }
}

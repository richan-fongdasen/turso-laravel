<?php

declare(strict_types=1);

namespace RichanFongdasen\Turso\Tests\Fixtures\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use RichanFongdasen\Turso\Tests\Fixtures\Models\User;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    public function modelName(): string
    {
        return User::class;
    }
}

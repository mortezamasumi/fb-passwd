<?php

namespace Mortezamasumi\FbPasswd\Tests\Services;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'force_change_password' => false,
        ];
    }

    public function forceChangePassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'force_change_password' => true,
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('123456789'),
            'remember_token' => Str::random(10),
            'role_id' => Role::factory()->author()->create()->id,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function roleAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->admin()->create()->id
        ]);
    }

    public function roleEditor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->editor()->create()->id
        ]);
    }

    public function roleUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::factory()->user()->create()->id
        ]);
    }
}

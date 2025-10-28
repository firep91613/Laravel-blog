<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [

        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
        ]);
    }

    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'editor',
        ]);
    }

    public function author(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'author',
        ]);
    }

    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'user',
        ]);
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => substr($this->faker->word(), 0, 50),
            'slug' => substr($this->faker->slug(), 0, 50)
        ];
    }
}

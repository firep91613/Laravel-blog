<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        return [
            'title' => substr($this->faker->sentence, 0, 500),
            'excerpt' => substr($this->faker->paragraph, 0, 1000),
            'content' => $this->faker->text,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'slug' => substr($this->faker->slug, 0, 50)
        ];
    }

    public function withUserRoleAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->roleAdmin()->create()->id
        ]);
    }

    public function withUserRoleEditor(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->roleEditor()->create()->id
        ]);
    }

    public function withUserRoleUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->roleUser()->create()->id
        ]);
    }
}

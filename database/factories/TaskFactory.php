<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'due_date' => fake()->dateTimeBetween('-1 week', '+1 month'),
            'status' => fake()->randomElement(['pending', 'completed']),
            'category' => fake()->randomElement(['school', 'project', 'study', 'personal']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
        ];
    }
}

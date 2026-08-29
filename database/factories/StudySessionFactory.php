<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudySessionFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = fake()->dateTimeBetween('-1 day', 'now');
        $duration = rand(300, 7200);

        return [
            'user_id' => User::factory(),
            'task_id' => null,
            'title' => fake()->sentence(3),
            'duration_seconds' => $duration,
            'started_at' => $startedAt,
            'ended_at' => (clone $startedAt)->modify("+{$duration} seconds"),
            'status' => 'completed',
        ];
    }

    public function running(): static
    {
        return $this->state(fn () => [
            'duration_seconds' => 0,
            'started_at' => now(),
            'ended_at' => null,
            'status' => 'running',
        ]);
    }
}

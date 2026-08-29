<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 hours', '+6 hours');
        $end = (clone $start)->modify('+'.rand(30, 120).' minutes');

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'subject' => fake()->optional()->words(2, true),
            'date' => fake()->dateTimeBetween('-1 week', '+1 month'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'status' => fake()->randomElement(['pending', 'active', 'completed']),
            'color' => fake()->randomElement(['#5B1744', '#2563EB', '#059669', '#D97706']),
        ];
    }
}

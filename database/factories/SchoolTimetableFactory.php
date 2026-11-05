<?php

namespace Database\Factories;

use App\Models\SchoolTimetable;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolTimetable>
 */
class SchoolTimetableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'day_of_week' => fake()->numberBetween(1, 5),
            'subject' => fake()->randomElement(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia']),
            'start_time' => '07:30',
            'end_time' => '09:00',
        ];
    }
}

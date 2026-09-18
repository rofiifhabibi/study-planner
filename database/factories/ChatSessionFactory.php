<?php

namespace Database\Factories;

use App\Models\ChatSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ChatSession>
 */
class ChatSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'user_id' => UserFactory::new(),
            'title' => fake()->sentence(3),
            'session_key' => Str::random(32),
            'is_project' => false,
            'parent_id' => null,
        ];
    }

    /**
     * Indicate that the session is a project container.
     */
    public function project(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => fake()->words(2, true),
            'is_project' => true,
        ]);
    }

    /**
     * Attach the session to a parent project session.
     */
    public function forProject(ChatSession $project): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $project->id,
        ]);
    }
}

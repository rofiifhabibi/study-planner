<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_session_id' => ChatSessionFactory::new(),
            'role' => fake()->randomElement(['user', 'assistant']),
            'content' => fake()->paragraph(),
            'file_url' => null,
            'is_canceled' => false,
        ];
    }
}

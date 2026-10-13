<?php

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use App\Services\GroqAiService;
use Illuminate\Support\Facades\Http;

it('returns AI reply from Groq API', function () {
    config()->set('services.groq.api_key', 'test-groq-key');

    Http::fake([
        'api.groq.com/*' => Http::response([
            'choices' => [
                ['message' => ['content' => 'Halo! Ada yang bisa saya bantu?']],
            ],
        ], 200),
    ]);

    $service = new GroqAiService;
    $reply = $service->chat('Hai');

    expect($reply)->toBe('Halo! Ada yang bisa saya bantu?');
});

it('throws RuntimeException when Groq API fails', function () {
    config()->set('services.groq.api_key', 'test-groq-key');

    Http::fake([
        'api.groq.com/*' => Http::response([], 500),
    ]);

    $service = new GroqAiService;

    expect(fn () => $service->chat('Hai'))->toThrow(RuntimeException::class);
});

it('builds history from session correctly', function () {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    ChatMessage::factory()->create([
        'chat_session_id' => $session->id,
        'role' => 'user',
        'content' => 'Halo',
        'is_canceled' => false,
    ]);
    ChatMessage::factory()->create([
        'chat_session_id' => $session->id,
        'role' => 'assistant',
        'content' => 'Halo juga!',
        'is_canceled' => false,
    ]);

    $service = new GroqAiService;
    $history = $service->buildHistoryFromSession($session);

    expect($history)->toHaveCount(2)
        ->and($history[0]['role'])->toBe('user')
        ->and($history[1]['role'])->toBe('assistant');
});

it('excludes canceled messages from history', function () {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    ChatMessage::factory()->create([
        'chat_session_id' => $session->id,
        'role' => 'user',
        'content' => 'Pesan yang dibatalkan',
        'is_canceled' => true,
    ]);

    $service = new GroqAiService;
    $history = $service->buildHistoryFromSession($session);

    expect($history)->toHaveCount(0);
});

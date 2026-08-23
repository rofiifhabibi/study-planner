<?php

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('halaman chat mengalihkan sesi yang bukan milik pengguna', function () {
    $user = User::factory()->create();
    $otherSession = ChatSession::factory()->create();

    $this->actingAs($user)
        ->get('/chat?session='.$otherSession->id)
        ->assertRedirect(route('chat'));
});

test('halaman chat dapat membuka sesi milik pengguna', function () {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/chat?session='.$session->id)
        ->assertOk()
        ->assertSee('AI Study Companion');
});

test('membuat sesi bertipe project', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/chat/session', [
            'title' => 'New Project Session',
            'is_project' => true,
        ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('session.is_project', true);

    $this->assertDatabaseHas('chat_sessions', [
        'title' => 'New Project Session',
        'is_project' => true,
    ]);
});

test('membuat sesi anak di dalam project', function () {
    $user = User::factory()->create();
    $project = ChatSession::factory()->project()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/api/chat/session', [
            'title' => 'Sesi dalam proyek',
            'parent_id' => $project->id,
        ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('session.parent_id', $project->id)
        ->assertJsonPath('session.is_project', false);
});

test('membuat sesi anak dengan project milik orang lain ditolak', function () {
    $user = User::factory()->create();
    $otherProject = ChatSession::factory()->project()->create();

    $this->actingAs($user)
        ->postJson('/api/chat/session', [
            'title' => 'Sesi ilegal',
            'parent_id' => $otherProject->id,
        ])
        ->assertNotFound();
});

test('sesi biasa tidak dapat menjadi induk sesi anak', function () {
    $user = User::factory()->create();
    $plainSession = ChatSession::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson('/api/chat/session', [
            'title' => 'Sesi anak',
            'parent_id' => $plainSession->id,
        ])
        ->assertNotFound();
});

test('pengguna dapat membatalkan pesannya sendiri', function () {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);
    $message = ChatMessage::factory()->for($session, 'session')->create([
        'role' => 'user',
        'content' => 'Halo AI',
    ]);

    $response = $this->actingAs($user)
        ->postJson("/api/chat/messages/{$message->id}/cancel");

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message.is_canceled', true);

    $this->assertDatabaseHas('chat_messages', [
        'id' => $message->id,
        'is_canceled' => true,
    ]);
});

test('pengguna tidak dapat membatalkan pesan milik orang lain', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $message = ChatMessage::factory()->for(ChatSession::factory()->create(['user_id' => $otherUser->id]), 'session')->create();

    $this->actingAs($user)
        ->postJson("/api/chat/messages/{$message->id}/cancel")
        ->assertForbidden();

    $this->assertDatabaseHas('chat_messages', [
        'id' => $message->id,
        'is_canceled' => false,
    ]);
});

test('mengirim pesan menyimpan pesan dan mengembalikan id', function () {
    Http::fake([
        'n8n.test/*' => Http::response(['output' => 'Jawaban AI']),
    ]);

    config([
        'services.n8n.webhook_url' => 'https://n8n.test/webhook',
        'services.n8n.secret_key' => 'rahasia',
    ]);

    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->postJson('/api/chat/send', [
            'chat_session_id' => $session->id,
            'message' => 'Halo',
        ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('reply', 'Jawaban AI')
        ->assertJsonStructure(['message_id']);

    $this->assertDatabaseHas('chat_messages', [
        'chat_session_id' => $session->id,
        'role' => 'user',
        'content' => 'Halo',
    ]);

    $this->assertDatabaseHas('chat_messages', [
        'chat_session_id' => $session->id,
        'role' => 'assistant',
        'content' => 'Jawaban AI',
    ]);
});

test('mengirim pesan di sesi project mengirim history lintas sesi', function () {
    Http::fake([
        'n8n.test/*' => Http::response(['output' => 'Jawaban AI']),
    ]);

    config([
        'services.n8n.webhook_url' => 'https://n8n.test/webhook',
        'services.n8n.secret_key' => 'rahasia',
    ]);

    $user = User::factory()->create();
    $project = ChatSession::factory()->project()->create(['user_id' => $user->id]);
    $sibling = ChatSession::factory()->forProject($project)->create(['user_id' => $user->id]);
    $active = ChatSession::factory()->forProject($project)->create(['user_id' => $user->id]);

    ChatMessage::factory()->for($sibling, 'session')->create([
        'role' => 'user',
        'content' => 'Bagaimana cara install n8n?',
        'created_at' => now()->subMinutes(10),
    ]);
    ChatMessage::factory()->for($sibling, 'session')->create([
        'role' => 'assistant',
        'content' => 'Install dengan npm atau docker.',
        'created_at' => now()->subMinutes(9),
    ]);
    ChatMessage::factory()->for($active, 'session')->create([
        'role' => 'user',
        'content' => 'Pesan di sesi aktif tidak boleh dobel',
        'created_at' => now()->subMinutes(5),
    ]);

    $this->actingAs($user)
        ->postJson('/api/chat/send', [
            'chat_session_id' => $active->id,
            'message' => 'Gimana cara bikin workflow?',
        ])
        ->assertOk()
        ->assertJsonPath('status', 'success');

    Http::assertSent(function ($request) use ($active) {
        return $request['session_id'] === 'user_'.auth()->id().'_session_'.$active->session_key
            && $request['message'] === 'Gimana cara bikin workflow?'
            && $request['history'] === [
                ['role' => 'user', 'content' => 'Bagaimana cara install n8n?', 'is_canceled' => false],
                ['role' => 'assistant', 'content' => 'Install dengan npm atau docker.', 'is_canceled' => false],
            ];
    });
});

test('mengirim pesan di sesi biasa tidak mengirim history', function () {
    Http::fake([
        'n8n.test/*' => Http::response(['output' => 'Jawaban AI']),
    ]);

    config([
        'services.n8n.webhook_url' => 'https://n8n.test/webhook',
        'services.n8n.secret_key' => 'rahasia',
    ]);

    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson('/api/chat/send', [
            'chat_session_id' => $session->id,
            'message' => 'Halo',
        ])
        ->assertOk();

    Http::assertSent(fn ($request) => ! array_key_exists('history', $request->data()));
});

test('menghapus project menghapus sesi anaknya', function () {
    $user = User::factory()->create();
    $project = ChatSession::factory()->project()->create(['user_id' => $user->id]);
    $child = ChatSession::factory()->forProject($project)->create(['user_id' => $user->id]);
    $otherProject = ChatSession::factory()->project()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/chat/sessions/{$project->id}")
        ->assertOk()
        ->assertJsonPath('status', 'success');

    $this->assertDatabaseMissing('chat_sessions', ['id' => $project->id]);
    $this->assertDatabaseMissing('chat_sessions', ['id' => $child->id]);
    $this->assertDatabaseHas('chat_sessions', ['id' => $otherProject->id]);
});

test('daftar sesi menyertakan parent_id', function () {
    $user = User::factory()->create();
    $project = ChatSession::factory()->project()->create(['user_id' => $user->id]);
    ChatSession::factory()->forProject($project)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson('/api/chat/sessions')
        ->assertOk()
        ->assertJsonStructure([
            'status',
            'sessions' => [['id', 'title', 'is_project', 'parent_id']],
        ]);
});

test('project dapat diganti namanya', function () {
    $user = User::factory()->create();
    $project = ChatSession::factory()->project()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->putJson("/api/chat/sessions/{$project->id}", [
            'title' => 'Belajar n8n',
        ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('session.title', 'Belajar n8n');

    $this->assertDatabaseHas('chat_sessions', [
        'id' => $project->id,
        'title' => 'Belajar n8n',
    ]);
});

test('mengganti nama sesi milik orang lain ditolak', function () {
    $user = User::factory()->create();
    $otherSession = ChatSession::factory()->create();

    $this->actingAs($user)
        ->putJson("/api/chat/sessions/{$otherSession->id}", [
            'title' => 'Diambil',
        ])
        ->assertNotFound();

    $this->assertDatabaseHas('chat_sessions', [
        'id' => $otherSession->id,
        'title' => $otherSession->title,
    ]);
});

test('ganti nama tanpa title divalidasi', function () {
    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->putJson("/api/chat/sessions/{$session->id}", [])
        ->assertUnprocessable();
});

test('pesan dapat diedit dan balasan baru dibuat', function () {
    Http::fake([
        'n8n.test/*' => Http::response(['output' => ' Balasan dari AI ']),
    ]);

    config([
        'services.n8n.webhook_url' => 'https://n8n.test/webhook',
        'services.n8n.secret_key' => 'rahasia',
    ]);

    $user = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $user->id]);
    $message = ChatMessage::factory()->for($session, 'session')->create([
        'role' => 'user',
        'content' => 'Pertanyaan lama',
    ]);

    $response = $this->actingAs($user)
        ->putJson("/api/chat/messages/{$message->id}", [
            'message' => 'Pertanyaan revisi',
        ]);

    $response->assertOk()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('reply', 'Balasan dari AI');

    $this->assertDatabaseHas('chat_messages', [
        'id' => $message->id,
        'content' => 'Pertanyaan revisi',
    ]);

    $this->assertDatabaseHas('chat_messages', [
        'chat_session_id' => $session->id,
        'role' => 'assistant',
        'content' => 'Balasan dari AI',
    ]);
});

test('edit pesan milik orang lain ditolak', function () {
    Http::fake();

    config([
        'services.n8n.webhook_url' => 'https://n8n.test/webhook',
        'services.n8n.secret_key' => 'rahasia',
    ]);

    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $session = ChatSession::factory()->create(['user_id' => $otherUser->id]);
    $message = ChatMessage::factory()->for($session, 'session')->create([
        'role' => 'user',
        'content' => 'Pesan asli',
    ]);

    $this->actingAs($user)
        ->putJson("/api/chat/messages/{$message->id}", ['message' => 'Diubah'])
        ->assertForbidden();

    $this->assertDatabaseHas('chat_messages', [
        'id' => $message->id,
        'content' => 'Pesan asli',
    ]);
});

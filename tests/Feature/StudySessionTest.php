<?php

use App\Models\StudySession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can start a study session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/study-sessions', ['title' => 'Belajar PHP'])
        ->assertCreated()
        ->assertJsonFragment(['title' => 'Belajar PHP', 'status' => 'running']);

    $this->assertDatabaseHas('study_sessions', [
        'user_id' => $user->id,
        'status' => 'running',
    ]);
});

it('cannot start two concurrent sessions', function () {
    $user = User::factory()->create();
    StudySession::factory()->running()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson('/api/study-sessions', ['title' => 'Sesi kedua'])
        ->assertStatus(409);
});

it('can stop a running session', function () {
    $user = User::factory()->create();
    $session = StudySession::factory()->running()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->postJson("/api/study-sessions/{$session->id}/stop")
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed']);

    $this->assertDatabaseHas('study_sessions', [
        'id' => $session->id,
        'status' => 'completed',
    ]);
});

it('can get active session', function () {
    $user = User::factory()->create();
    $session = StudySession::factory()->running()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson('/api/study-sessions/active')
        ->assertOk()
        ->assertJsonPath('session.id', $session->id);
});

it('returns null when no active session', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/study-sessions/active')
        ->assertOk()
        ->assertJsonPath('session', null);
});

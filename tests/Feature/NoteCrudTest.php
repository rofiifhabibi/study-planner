<?php

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list notes for authenticated user', function () {
    $user = User::factory()->create();
    Note::factory()->count(3)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson('/api/notes')
        ->assertOk()
        ->assertJsonCount(3, 'notes');
});

it('can create a note', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/notes', [
            'title' => 'Ringkasan jaringan',
            'content' => 'OSI layer dan TCP/IP.',
        ])
        ->assertCreated()
        ->assertJsonFragment(['title' => 'Ringkasan jaringan']);

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'title' => 'Ringkasan jaringan',
    ]);
});

it('can create a note without a title', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/notes', ['content' => 'Hanya isi saja.'])
        ->assertCreated();

    $this->assertDatabaseHas('notes', [
        'user_id' => $user->id,
        'title' => null,
        'content' => 'Hanya isi saja.',
    ]);
});

it('can update a note', function () {
    $user = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->putJson("/api/notes/{$note->id}", ['content' => 'Isi yang diperbarui.'])
        ->assertOk()
        ->assertJsonFragment(['content' => 'Isi yang diperbarui.']);

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'content' => 'Isi yang diperbarui.',
    ]);
});

it('can delete a note', function () {
    $user = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/notes/{$note->id}")
        ->assertOk();

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});

it('cannot update another users note', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $note = Note::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->putJson("/api/notes/{$note->id}", ['content' => 'jangan berubah'])
        ->assertForbidden();
});

it('validates required content field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/notes', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

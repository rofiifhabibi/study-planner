<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list tasks for authenticated user', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->getJson('/api/tasks')
        ->assertOk()
        ->assertJsonCount(3, 'tasks');
});

it('can create a task', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/tasks', [
            'title' => 'Finish assignment',
            'due_date' => now()->addDay()->format('Y-m-d'),
            'category' => 'school',
            'priority' => 'high',
        ])
        ->assertCreated()
        ->assertJsonFragment(['title' => 'Finish assignment']);

    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'Finish assignment',
    ]);
});

it('can update a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

    $this->actingAs($user)
        ->putJson("/api/tasks/{$task->id}", ['status' => 'completed'])
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed']);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => 'completed',
    ]);
});

it('can delete a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/tasks/{$task->id}")
        ->assertOk();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('cannot update another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)
        ->putJson("/api/tasks/{$task->id}", ['status' => 'completed'])
        ->assertForbidden();
});

it('validates required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/tasks', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'due_date', 'category', 'priority']);
});

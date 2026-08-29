<?php

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can list schedules for a date', function () {
    $user = User::factory()->create();
    Schedule::factory()->count(3)->create(['user_id' => $user->id, 'date' => now()]);

    $this->actingAs($user)
        ->getJson('/api/schedules?date='.now()->format('Y-m-d'))
        ->assertOk()
        ->assertJsonCount(3, 'schedules');
});

it('can create a schedule', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/schedules', [
            'title' => 'Review Database',
            'subject' => 'Database Systems',
            'date' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ])
        ->assertCreated()
        ->assertJsonFragment(['title' => 'Review Database']);

    $this->assertDatabaseHas('schedules', [
        'user_id' => $user->id,
        'title' => 'Review Database',
    ]);
});

it('can update a schedule', function () {
    $user = User::factory()->create();
    $schedule = Schedule::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

    $this->actingAs($user)
        ->putJson("/api/schedules/{$schedule->id}", ['status' => 'completed'])
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed']);
});

it('can delete a schedule', function () {
    $user = User::factory()->create();
    $schedule = Schedule::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->deleteJson("/api/schedules/{$schedule->id}")
        ->assertOk();

    $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
});

it('validates required fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson('/api/schedules', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'date', 'start_time', 'end_time']);
});

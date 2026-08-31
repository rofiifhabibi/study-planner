<?php

use App\Models\Schedule;
use App\Models\StudySession;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the summary dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Overview');
});

it('renders the tasks page with user tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(2)->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get(route('tasks'))
        ->assertOk()
        ->assertSee('My Tasks');
});

it('renders the schedule page with today schedules', function () {
    $user = User::factory()->create();
    Schedule::factory()->create(['user_id' => $user->id, 'date' => today()]);

    $this->actingAs($user)
        ->get(route('schedule'))
        ->assertOk()
        ->assertSee('Your schedule');
});

it('renders the progress page with recent study sessions', function () {
    $user = User::factory()->create();
    StudySession::factory()->create(['user_id' => $user->id, 'status' => 'completed']);

    $this->actingAs($user)
        ->get(route('progress'))
        ->assertOk()
        ->assertSee('Your progress');
});

it('renders the integrations page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('integrations'))
        ->assertOk()
        ->assertSee('Integrations');
});

it('renders the logout and profile forms on the shared layout', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee(route('logout'), false)
        ->assertSee(route('profile.destroy'), false)
        ->assertSee('name="_token"', false);
});

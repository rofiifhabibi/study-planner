<?php

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('imports tasks from google tasks via the pull route', function () {
    $user = User::factory()->create();
    $service = Mockery::mock(GoogleCalendarService::class);
    $service->shouldReceive('pullTasksFromGoogle')
        ->once()
        ->andReturn(['imported' => 3, 'errors' => []]);
    app()->bind(GoogleCalendarService::class, fn () => $service);

    $this->actingAs($user)
        ->post('/google/tasks/pull')
        ->assertRedirect(route('integrations'))
        ->assertSessionHas('success', 'Berhasil import 3 tugas dari Google Tasks.');
});

it('reports errors when google tasks pull fails', function () {
    $user = User::factory()->create();
    $service = Mockery::mock(GoogleCalendarService::class);
    $service->shouldReceive('pullTasksFromGoogle')
        ->once()
        ->andReturn(['imported' => 0, 'errors' => ['Something went wrong']]);
    app()->bind(GoogleCalendarService::class, fn () => $service);

    $this->actingAs($user)
        ->post('/google/tasks/pull')
        ->assertRedirect(route('integrations'))
        ->assertSessionHas('success', 'Berhasil import 0 tugas dari Google Tasks. Ada 1 error.');
});

it('requires authentication for the pull route', function () {
    $this->post('/google/tasks/pull')
        ->assertRedirect(route('login'));
});

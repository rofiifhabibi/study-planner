<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('dashboard renders the logout form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertOk();
    $response->assertSee(route('logout'), false);
    $response->assertSee(route('profile.destroy'), false);
    $response->assertSee('name="_token"', false);
});

test('chat page renders the logout form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/chat');

    $response->assertOk();
    $response->assertSee(route('logout'), false);
});

test('account can be deleted with the correct password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect('/');
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('profile information can be updated from the dashboard modal', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from(route('dashboard'))
        ->put('/profile', [
            'name' => 'Nama Baru',
            'email' => $user->email,
        ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('status', 'profile-updated');

    expect($user->fresh()->name)->toBe('Nama Baru')
        ->and($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('changing the email requires re-verification', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->put('/profile', [
            'name' => $user->name,
            'email' => 'baru@example.com',
        ]);

    $response->assertRedirect(route('dashboard'));

    $fresh = $user->fresh();

    expect($fresh->email)->toBe('baru@example.com')
        ->and($fresh->hasVerifiedEmail())->toBeFalse();
});

test('account cannot be deleted with the wrong password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('userDeletion', 'password');
    $this->assertModelExists($user);
    $this->assertAuthenticatedAs($user);
});

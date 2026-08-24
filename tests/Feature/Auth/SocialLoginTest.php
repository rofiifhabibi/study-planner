<?php

use App\Models\User;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('unverified users are redirected to verification notice when accessing dashboard', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertRedirect(route('verification.notice'));
});

test('google callback creates a verified user and logs in', function () {
    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-123',
        'name' => 'Andi Wijaya',
        'email' => 'andi@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('google_id', 'google-123')->first();

    expect($user)->not->toBeNull()
        ->and($user->email)->toBe('andi@example.com')
        ->and($user->hasVerifiedEmail())->toBeTrue();
});

test('google callback links provider id to an existing account with the same email', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);

    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-456',
        'name' => 'Siti Rahma',
        'email' => 'siti@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->google_id)->toBe('google-456')
        ->and(User::count())->toBe(1);
});

test('google callback verifies an existing unverified account linked by email', function () {
    $user = User::factory()->unverified()->create(['email' => 'budi@example.com']);

    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-789',
        'name' => 'Budi',
        'email' => 'budi@example.com',
    ]));

    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh())
        ->google_id->toBe('google-789')
        ->hasVerifiedEmail()->toBeTrue();
});

test('telegram callback creates a verified user with a placeholder email', function () {
    Socialite::fake('telegram', SocialiteUser::fake([
        'id' => 987654,
        'name' => 'Budi Santoso',
        'nickname' => 'buditelegram',
        'email' => null,
    ]));

    $response = $this->get('/auth/telegram/callback');

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('telegram_id', '987654')->first();

    expect($user)->not->toBeNull()
        ->and($user->email)->toBe('987654@telegram.user')
        ->and($user->hasVerifiedEmail())->toBeTrue();
});

test('telegram callback logs into an existing linked account', function () {
    $user = User::factory()->create(['telegram_id' => '111222']);

    Socialite::fake('telegram', SocialiteUser::fake([
        'id' => 111222,
        'name' => 'Existing Telegram User',
    ]));

    $response = $this->get('/auth/telegram/callback');

    $response->assertRedirect(route('dashboard', absolute: false));
    expect(User::count())->toBe(1);
});

test('social callbacks without valid state redirect back to login', function () {
    $response = $this->get('/auth/google/callback');

    $response->assertRedirect(route('login'));

    $response = $this->get('/auth/telegram/callback');

    $response->assertRedirect(route('login'));

    expect(User::count())->toBe(0);
});

test('unconfigured providers redirect back to login with a friendly message', function () {
    config()->set('services.google.client_id', null);
    config()->set('services.telegram.bot', null);

    $googleResponse = $this->get(route('auth.google.redirect'));
    $telegramResponse = $this->from(route('login'))->get(route('auth.telegram.redirect'));

    $googleResponse->assertRedirect(route('login'));
    $telegramResponse->assertRedirect(route('login'));
    $googleResponse->assertSessionHasErrors('email');
});

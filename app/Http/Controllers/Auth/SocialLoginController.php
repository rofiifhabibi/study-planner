<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as ProviderUser;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return $this->unconfiguredRedirect('Google');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the Google authentication callback.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        $providerUser = $this->resolveProviderUser('google');

        if ($providerUser === null) {
            return $this->failedRedirect();
        }

        return $this->authenticateWith($providerUser, 'google_id');
    }

    /**
     * Serve the Telegram Login Widget page.
     */
    public function redirectToTelegram(): Response|RedirectResponse
    {
        if (empty(config('services.telegram.client_secret')) || empty(config('services.telegram.bot'))) {
            return $this->unconfiguredRedirect('Telegram');
        }

        return response(Socialite::driver('telegram')->redirect());
    }

    /**
     * Handle the Telegram authentication callback.
     */
    public function handleTelegramCallback(): RedirectResponse
    {
        $providerUser = $this->resolveProviderUser('telegram');

        if ($providerUser === null) {
            return $this->failedRedirect();
        }

        return $this->authenticateWith($providerUser, 'telegram_id');
    }

    /**
     * Resolve the provider user, returning null when the OAuth state or signature is invalid.
     */
    private function resolveProviderUser(string $driver): ?ProviderUser
    {
        try {
            return Socialite::driver($driver)->user();
        } catch (InvalidStateException|InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Log the user in via their linked provider account.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function authenticateWith(ProviderUser $providerUser, string $idColumn): RedirectResponse
    {
        $user = User::where($idColumn, $providerUser->getId())->first()
            ?? $this->linkByEmailOrCreate($providerUser, $idColumn);

        Auth::login($user);

        session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Link the provider id to an existing account by email, otherwise create a verified account.
     */
    private function linkByEmailOrCreate(ProviderUser $providerUser, string $idColumn): User
    {
        $email = $providerUser->getEmail();

        if (! empty($email)) {
            $existing = User::where('email', Str::lower($email))->first();

            if ($existing !== null) {
                $existing->forceFill([$idColumn => $providerUser->getId()])->save();

                if (! $existing->hasVerifiedEmail()) {
                    $existing->markEmailAsVerified();
                }

                return $existing;
            }
        }

        $user = new User;
        $user->forceFill([
            'name' => ($providerUser->getName() ?: $providerUser->getNickname()) ?: 'User',
            'email' => $email ? Str::lower($email) : $this->placeholderEmail($idColumn, $providerUser->getId()),
            'password' => Hash::make(Str::random(40)),
            'email_verified_at' => now(),
            $idColumn => $providerUser->getId(),
        ])->save();

        return $user;
    }

    /**
     * Build a unique placeholder email for providers that do not share an address (Telegram).
     */
    private function placeholderEmail(string $idColumn, int|string $providerId): string
    {
        $provider = str_replace('_id', '', $idColumn);

        return $providerId.'@'.$provider.'.user';
    }

    /**
     * Redirect back to the login screen with a friendly error message.
     */
    private function failedRedirect(): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Sesi login sosial tidak valid atau sudah kedaluwarsa. Silakan coba lagi.']);
    }

    /**
     * Redirect back to the login screen when a provider has not been configured yet.
     */
    private function unconfiguredRedirect(string $provider): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors(['email' => "Login {$provider} belum dikonfigurasi. Silakan hubungi administrator."]);
    }
}

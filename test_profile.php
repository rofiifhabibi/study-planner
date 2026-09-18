<?php
$file = 'app/Http/Controllers/ProfileController.php';
$content = file_get_contents($file);

$oldDestroy = <<<PHP
    public function destroy(Request \$request): RedirectResponse
    {
        \$request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        \$user = \$request->user();

        Auth::logout();

        \$user->delete();

        \$request->session()->invalidate();
        \$request->session()->regenerateToken();

        return Redirect::to('/');
    }
PHP;

$newDestroy = <<<PHP
    public function destroy(Request \$request): RedirectResponse
    {
        \$user = \$request->user();
        
        // If the user has a social login, we might not want to force password validation
        // since they might have a randomly generated password.
        // We'll only require the password if they don't have a social login,
        // or we can just bypass it if they leave it empty and have a social login.
        
        \$hasSocial = !empty(\$user->google_id) || !empty(\$user->telegram_id);
        
        if (!\$hasSocial) {
            \$request->validateWithBag('userDeletion', [
                'password' => ['required', 'current_password'],
            ]);
        } else {
            // For social users, we can just optionally check password if provided.
            if (\$request->filled('password')) {
                \$request->validateWithBag('userDeletion', [
                    'password' => ['current_password'],
                ]);
            }
        }

        Auth::logout();

        \$user->delete();

        \$request->session()->invalidate();
        \$request->session()->regenerateToken();

        return Redirect::to('/');
    }
PHP;

$content = str_replace($oldDestroy, $newDestroy, $content);
file_put_contents($file, $content);
echo "Patched ProfileController.\n";

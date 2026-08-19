<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/chat', function () {
    $user = auth()->user();
    return view('chat', [
        'isGuest' => !auth()->check(),
        'userName' => $user?->name ?? '',
        'userInitial' => $user ? strtoupper(substr($user->name, 0, 1)) : '',
    ]);
})->name('chat');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'dashboard'])->name('dashboard');
});

require __DIR__.'/auth.php';

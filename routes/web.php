<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', function () {
        $user = auth()->user();
        return view('chat', [
            'userName' => $user->name,
            'userInitial' => strtoupper(substr($user->name, 0, 1)),
        ]);
    })->name('chat');
});

require __DIR__.'/auth.php';

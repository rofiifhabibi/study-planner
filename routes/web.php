<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
});

require __DIR__.'/auth.php';

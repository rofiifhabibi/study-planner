<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])->group(function () {
    Route::get('/chat/sessions', [ChatController::class, 'getSessions']);
    Route::get('/chat/sessions/{sessionId}/messages', [ChatController::class, 'getMessages']);
    Route::delete('/chat/sessions/{sessionId}', [ChatController::class, 'deleteSession']);
    Route::put('/chat/sessions/{sessionId}', [ChatController::class, 'renameSession']);
    Route::post('/chat/session', [ChatController::class, 'createSession']);
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::put('/chat/messages/{message}', [ChatController::class, 'updateMessage']);
    Route::post('/chat/messages/{message}/cancel', [ChatController::class, 'cancelMessage']);
    Route::get('/chat/user', [ChatController::class, 'user']);
});

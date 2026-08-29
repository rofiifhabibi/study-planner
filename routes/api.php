<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudySessionController;
use App\Http\Controllers\TaskController;
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

    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    Route::get('/schedules', [ScheduleController::class, 'index']);
    Route::post('/schedules', [ScheduleController::class, 'store']);
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);

    Route::get('/study-sessions', [StudySessionController::class, 'index']);
    Route::get('/study-sessions/active', [StudySessionController::class, 'active']);
    Route::post('/study-sessions', [StudySessionController::class, 'store']);
    Route::post('/study-sessions/{session}/stop', [StudySessionController::class, 'stop']);
    Route::post('/study-sessions/{session}/pause', [StudySessionController::class, 'pause']);
    Route::post('/study-sessions/{session}/resume', [StudySessionController::class, 'resume']);
    Route::delete('/study-sessions/{session}', [StudySessionController::class, 'destroy']);
});

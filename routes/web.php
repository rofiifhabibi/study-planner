<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ChatController::class, 'dashboard'])->name('dashboard');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/google/calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
    Route::get('/google/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
    Route::post('/google/calendar/sync', [GoogleCalendarController::class, 'syncCalendar'])->name('google.calendar.sync');
    Route::post('/google/calendar/pull', [GoogleCalendarController::class, 'pullCalendar'])->name('google.calendar.pull');
    Route::post('/google/tasks/sync', [GoogleCalendarController::class, 'syncTasks'])->name('google.tasks.sync');
    Route::get('/google/status', [GoogleCalendarController::class, 'status'])->name('google.status');
});

require __DIR__.'/auth.php';

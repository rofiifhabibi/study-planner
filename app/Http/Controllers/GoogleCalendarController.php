<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $service = new GoogleCalendarService(auth()->user());

        return redirect($service->getAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('dashboard')
                ->with('error', 'Google authorization ditolak.');
        }

        $service = new GoogleCalendarService(auth()->user());
        $success = $service->handleCallback($request->input('code'));

        if ($success) {
            return redirect()->route('dashboard')
                ->with('success', 'Berhasil terhubung ke Google Calendar & Tasks!');
        }

        return redirect()->route('dashboard')
            ->with('error', 'Gagal terhubung ke Google. Silakan coba lagi.');
    }

    public function syncCalendar(): RedirectResponse
    {
        $service = new GoogleCalendarService(auth()->user());
        $result = $service->syncSchedulesToCalendar();

        $message = "Berhasil sync {$result['synced']} jadwal ke Google Calendar.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function pullCalendar(): RedirectResponse
    {
        $service = new GoogleCalendarService(auth()->user());
        $result = $service->pullEventsFromCalendar();

        $message = "Berhasil import {$result['imported']} event dari Google Calendar.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function syncTasks(): RedirectResponse
    {
        $service = new GoogleCalendarService(auth()->user());
        $result = $service->syncTasksToList();

        $message = "Berhasil sync {$result['synced']} tugas ke Google Tasks.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function status(): JsonResponse
    {
        $service = new GoogleCalendarService(auth()->user());

        return response()->json([
            'connected' => $service->isConnected(),
            'task_lists' => $service->isConnected() ? $service->getTaskLists() : [],
        ]);
    }
}

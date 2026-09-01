<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    private function service(): GoogleCalendarService
    {
        return app(GoogleCalendarService::class, ['user' => auth()->user()]);
    }

    public function redirect(): RedirectResponse
    {
        $service = $this->service();

        return redirect($service->getAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('integrations')
                ->with('error', 'Google authorization ditolak.');
        }

        $service = $this->service();
        $success = $service->handleCallback($request->input('code'));

        if ($success) {
            return redirect()->route('integrations')
                ->with('success', 'Berhasil terhubung ke Google Calendar & Tasks!');
        }

        return redirect()->route('integrations')
            ->with('error', 'Gagal terhubung ke Google. Silakan coba lagi.');
    }

    public function syncCalendar(): RedirectResponse
    {
        $service = $this->service();
        $result = $service->syncSchedulesToCalendar();

        $message = "Berhasil sync {$result['synced']} jadwal ke Google Calendar.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('integrations')->with('success', $message);
    }

    public function pullCalendar(): RedirectResponse
    {
        $service = $this->service();
        $result = $service->pullEventsFromCalendar();

        $message = "Berhasil import {$result['imported']} event dari Google Calendar.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('integrations')->with('success', $message);
    }

    public function syncTasks(): RedirectResponse
    {
        $service = $this->service();
        $result = $service->syncTasksToList();

        $message = "Berhasil sync {$result['synced']} tugas ke Google Tasks.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('integrations')->with('success', $message);
    }

    public function pullTasks(): RedirectResponse
    {
        $service = $this->service();
        $result = $service->pullTasksFromGoogle();

        $message = "Berhasil import {$result['imported']} tugas dari Google Tasks.";
        if (! empty($result['errors'])) {
            $message .= ' Ada '.count($result['errors']).' error.';
        }

        return redirect()->route('integrations')->with('success', $message);
    }

    public function status(): JsonResponse
    {
        $service = $this->service();

        return response()->json([
            'connected' => $service->isConnected(),
            'task_lists' => $service->isConnected() ? $service->getTaskLists() : [],
        ]);
    }
}

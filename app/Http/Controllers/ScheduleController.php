<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    private function calendarService(): GoogleCalendarService
    {
        return app(GoogleCalendarService::class, ['user' => auth()->user()]);
    }

    public function index(Request $request): JsonResponse
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $date = $request->query('date');

        $query = Schedule::where('user_id', auth()->id());

        if ($start && $end) {
            $query->whereBetween('date', [
                \Carbon\Carbon::parse($start)->format('Y-m-d'),
                \Carbon\Carbon::parse($end)->format('Y-m-d')
            ]);
        } elseif ($date) {
            $query->whereDate('date', $date);
        } else {
            $query->whereDate('date', now()->format('Y-m-d'));
        }

        $schedules = $query->orderBy('start_time')->get();

        if ($start && $end) {
            $events = $schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'start' => $schedule->date->format('Y-m-d') . 'T' . $schedule->start_time->format('H:i:s'),
                    'end' => $schedule->date->format('Y-m-d') . 'T' . $schedule->end_time->format('H:i:s'),
                    'extendedProps' => [
                        'status' => $schedule->status,
                        'subject' => $schedule->subject,
                    ]
                ];
            });
            return response()->json($events);
        }

        return response()->json([
            'status' => 'success',
            'schedules' => $schedules,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'color' => 'nullable|string|max:7',
            'is_lesson' => 'nullable|boolean',
            'recurrence_frequency' => 'nullable|string|in:daily,weekly,monthly',
            'recurrence_interval' => 'nullable|integer|min:1|max:52',
            'recurrence_days' => 'nullable|string|max:50',
            'recurrence_until' => 'nullable|date|after_or_equal:date',
            'recurrence_count' => 'nullable|integer|min:1|max:365',
        ]);

        $schedule = Schedule::create([
            'user_id' => auth()->id(),
            ...$validated,
            'recurrence_interval' => $validated['recurrence_interval'] ?? 1,
        ]);

        $this->calendarService()->syncSchedule($schedule);

        return response()->json([
            'status' => 'success',
            'schedule' => $schedule,
        ], 201);
    }

    public function update(Request $request, Schedule $schedule): JsonResponse
    {
        if ($schedule->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required|date_format:H:i',
            'end_time' => 'sometimes|required|date_format:H:i|after:start_time',
            'status' => 'sometimes|required|in:pending,active,completed',
            'color' => 'nullable|string|max:7',
            'is_lesson' => 'nullable|boolean',
            'recurrence_frequency' => 'nullable|string|in:daily,weekly,monthly',
            'recurrence_interval' => 'nullable|integer|min:1|max:52',
            'recurrence_days' => 'nullable|string|max:50',
            'recurrence_until' => 'nullable|date|after_or_equal:date',
            'recurrence_count' => 'nullable|integer|min:1|max:365',
        ]);

        if (array_key_exists('recurrence_interval', $validated) && $validated['recurrence_interval'] === null) {
            $validated['recurrence_interval'] = 1;
        }

        $schedule->update($validated);

        $this->calendarService()->syncSchedule($schedule);

        return response()->json([
            'status' => 'success',
            'schedule' => $schedule->fresh(),
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        if ($schedule->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $this->calendarService()->deleteEvent($schedule);

        $schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil dihapus.',
        ]);
    }
}

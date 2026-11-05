<?php

namespace App\Http\Controllers;

use App\Models\SchoolTimetable;
use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SchoolTimetableController extends Controller
{
    public function index(): View
    {
        $timetables = SchoolTimetable::where('user_id', auth()->id())
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Group by day of week for easy display
        $grouped = $timetables->groupBy('day_of_week');

        return view('timetable', [
            'grouped' => $grouped,
        ]);
    }

    public function store(Request $request)
    {
        if ($request->has('start_time') && is_string($request->start_time)) {
            $request->merge(['start_time' => substr(trim($request->start_time), 0, 5)]);
        }
        if ($request->filled('end_time') && is_string($request->end_time)) {
            $request->merge(['end_time' => substr(trim($request->end_time), 0, 5)]);
        }

        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $timetable = SchoolTimetable::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        try {
            $service = app(GoogleCalendarService::class, ['user' => auth()->user()]);
            $service->syncSchoolTimetable($timetable);
        } catch (\Throwable $e) {
            Log::info('Google Calendar timetable sync skipped: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function destroy(SchoolTimetable $timetable)
    {
        if ($timetable->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $service = app(GoogleCalendarService::class, ['user' => auth()->user()]);
            $service->deleteSchoolTimetableEvent($timetable);
        } catch (\Throwable $e) {
            Log::info('Google Calendar timetable delete event skipped: '.$e->getMessage());
        }

        $timetable->delete();

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}

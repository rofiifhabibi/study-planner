<?php

namespace App\Http\Controllers;

use App\Models\SchoolTimetable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\GoogleCalendarService;

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
            'grouped' => $grouped
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'subject' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $timetable = SchoolTimetable::create([
            'user_id' => auth()->id(),
            ...$validated
        ]);

        $service = app(GoogleCalendarService::class, ['user' => auth()->user()]);
        $service->syncSchoolTimetable($timetable);

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function destroy(SchoolTimetable $timetable)
    {
        if ($timetable->user_id !== auth()->id()) {
            abort(403);
        }
        
        $service = app(GoogleCalendarService::class, ['user' => auth()->user()]);
        $service->deleteSchoolTimetableEvent($timetable);
        
        $timetable->delete();
        
        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}

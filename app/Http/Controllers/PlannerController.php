<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\StudySession;
use App\Models\Task;
use App\Services\GoogleCalendarService;
use App\Services\ProgressService;
use Illuminate\View\View;

class PlannerController extends Controller
{
    public function tasks(): View
    {
        $user = auth()->user();

        $tasks = Task::where('user_id', $user->id)
            ->orderBy('status', 'asc')
            ->orderBy('due_date', 'asc')
            ->orderByRaw("CASE priority WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END")
            ->get();

        $totalTasks = $tasks->count();
        $totalCompleted = $tasks->where('status', 'completed')->count();
        $completionPercentage = $totalTasks > 0 ? round(($totalCompleted / $totalTasks) * 100) : 0;

        return view('tasks', [
            'tasks' => $tasks,
            'totalTasks' => $totalTasks,
            'totalCompleted' => $totalCompleted,
            'completionPercentage' => $completionPercentage,
        ]);
    }

    public function schedule(): View
    {
        $user = auth()->user();

        $todaySchedules = Schedule::where('user_id', $user->id)
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get();

        $upcomingSchedules = Schedule::where('user_id', $user->id)
            ->whereDate('date', '>=', today()->addDay())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(30)
            ->get();

        return view('schedule', [
            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,
        ]);
    }

    public function progress(): View
    {
        $user = auth()->user();

        $progress = (new ProgressService($user->id))->getDashboardStats();

        $recentSessions = StudySession::where('user_id', $user->id)
            ->latest('started_at')
            ->limit(10)
            ->get();

        return view('progress', [
            ...$progress,
            'recentSessions' => $recentSessions,
        ]);
    }

    public function integrations(): View
    {
        $service = new GoogleCalendarService(auth()->user());

        return view('integrations', [
            'isConnected' => $service->isConnected(),
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\StudySession;
use App\Models\Task;
use Carbon\Carbon;

class ProgressService
{
    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getDashboardStats(): array
    {
        // Get active tasks (pending or in_progress) ordered by due date
        $dashboardTasks = Task::where('user_id', $this->userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        $totalTasks = Task::where('user_id', $this->userId)->count();
        $totalCompleted = Task::where('user_id', $this->userId)->where('status', 'completed')->count();

        // Calculate today's stats explicitly
        $todayTotal = Task::where('user_id', $this->userId)->whereDate('due_date', today())->count();
        $todayCompleted = Task::where('user_id', $this->userId)->whereDate('due_date', today())->where('status', 'completed')->count();

        $totalStudySeconds = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->sum('duration_seconds');

        $todayStudySeconds = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereDate('ended_at', today())
            ->sum('duration_seconds');

        $streak = $this->calculateStreak();

        return [
            'todayTasks' => $dashboardTasks,
            'todayCompleted' => $todayCompleted,
            'todayTotal' => $todayTotal,
            'totalTasks' => $totalTasks,
            'totalCompleted' => $totalCompleted,
            'completionPercentage' => $totalTasks > 0 ? round(($totalCompleted / $totalTasks) * 100) : 0,
            'totalStudyHours' => round($totalStudySeconds / 3600, 1),
            'todayStudyMinutes' => round($todayStudySeconds / 60),
            'streak' => $streak,
        ];
    }

    private function calculateStreak(): int
    {
        // Get dates from study sessions
        $studyDates = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereNotNull('ended_at')
            ->pluck('ended_at')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay()->format('Y-m-d'));

        // Get dates from completed tasks
        $taskDates = Task::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->pluck('updated_at')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay()->format('Y-m-d'));

        $dates = $studyDates->merge($taskDates)->unique()->sortDesc()->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentDate = Carbon::today();
        
        $firstDate = Carbon::parse($dates->first());
        
        // If the first activity is from yesterday, the streak is still active
        if ($firstDate->isSameDay(Carbon::yesterday())) {
            $currentDate = Carbon::yesterday();
        } elseif ($firstDate->isBefore(Carbon::yesterday())) {
            // No activity today or yesterday -> streak broken
            return 0;
        }

        foreach ($dates as $dateString) {
            $date = Carbon::parse($dateString);
            if ($date->isSameDay($currentDate)) {
                $streak++;
                $currentDate->subDay();
            } elseif ($date->isAfter($currentDate)) {
                // skip future dates if any
                continue;
            } else {
                break;
            }
        }

        return $streak;
    }
}

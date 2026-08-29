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
        $todayTasks = Task::where('user_id', $this->userId)
            ->whereDate('due_date', today())
            ->get();

        $totalTasks = Task::where('user_id', $this->userId)->count();
        $totalCompleted = Task::where('user_id', $this->userId)->where('status', 'completed')->count();

        $todayCompleted = $todayTasks->where('status', 'completed')->count();
        $todayTotal = $todayTasks->count();

        $totalStudySeconds = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->sum('duration_seconds');

        $todayStudySeconds = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereDate('ended_at', today())
            ->sum('duration_seconds');

        $streak = $this->calculateStreak();

        return [
            'todayTasks' => $todayTasks->sortBy('priority')->values(),
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
        $dates = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereNotNull('ended_at')
            ->selectRaw('DATE(ended_at) as study_date')
            ->groupBy('study_date')
            ->orderBy('study_date', 'desc')
            ->pluck('study_date')
            ->map(fn ($d) => Carbon::parse($d));

        if ($dates->isEmpty()) {
            $dates = Task::where('user_id', $this->userId)
                ->where('status', 'completed')
                ->selectRaw('DATE(updated_at) as completed_date')
                ->groupBy('completed_date')
                ->orderBy('completed_date', 'desc')
                ->pluck('completed_date')
                ->map(fn ($d) => Carbon::parse($d));
        }

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $current = Carbon::today();

        foreach ($dates as $date) {
            if ($date->isSameDay($current)) {
                $streak++;
                $current->subDay();
            } elseif ($date->isSameDay($current)) {
                $streak++;
                $current->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}

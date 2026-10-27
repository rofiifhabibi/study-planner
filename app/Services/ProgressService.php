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

        $totalHours = intdiv((int) $totalStudySeconds, 3600);
        $totalMinutes = intdiv(((int) $totalStudySeconds) % 3600, 60);

        if ($totalHours > 0 && $totalMinutes > 0) {
            $formattedStudyTime = "{$totalHours}h {$totalMinutes}m";
        } elseif ($totalHours > 0) {
            $formattedStudyTime = "{$totalHours}h";
        } else {
            $formattedStudyTime = "{$totalMinutes}m";
        }

        return [
            'todayTasks' => $dashboardTasks,
            'todayCompleted' => $todayCompleted,
            'todayTotal' => $todayTotal,
            'totalTasks' => $totalTasks,
            'totalCompleted' => $totalCompleted,
            'completionPercentage' => $totalTasks > 0 ? round(($totalCompleted / $totalTasks) * 100) : 0,
            'totalStudyHours' => round($totalStudySeconds / 3600, 1),
            'totalStudySeconds' => (int) $totalStudySeconds,
            'totalStudyTimeFormatted' => $formattedStudyTime,
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

    /**
     * Generate GitHub-like activity contribution heatmap for the user for the last year (53 weeks).
     *
     * @return array{weeks: array<int, array{month_label: ?string, days: array<int, array<string, mixed>>}>, totalActivities: int, startDate: string, endDate: string}
     */
    public function getContributionHeatmap(int $weeks = 53): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subWeeks($weeks - 1)->startOfWeek(Carbon::MONDAY);

        $sessions = StudySession::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereNotNull('ended_at')
            ->whereDate('ended_at', '>=', $startDate)
            ->whereDate('ended_at', '<=', $endDate)
            ->get()
            ->groupBy(fn ($s) => Carbon::parse($s->ended_at)->format('Y-m-d'));

        $tasks = Task::where('user_id', $this->userId)
            ->where('status', 'completed')
            ->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->get()
            ->groupBy(fn ($t) => Carbon::parse($t->updated_at)->format('Y-m-d'));

        $grid = [];
        $current = $startDate->copy();
        $totalActivities = 0;

        while ($current->lte($endDate) || $current->dayOfWeekIso !== 1) {
            $dateStr = $current->format('Y-m-d');
            $sessionCount = isset($sessions[$dateStr]) ? $sessions[$dateStr]->count() : 0;
            $taskCount = isset($tasks[$dateStr]) ? $tasks[$dateStr]->count() : 0;
            $count = $sessionCount + $taskCount;
            $isFuture = $current->isAfter($endDate);

            if (! $isFuture) {
                $totalActivities += $count;
            }

            $level = 0;
            if (! $isFuture) {
                if ($count >= 4) {
                    $level = 4;
                } elseif ($count >= 3) {
                    $level = 3;
                } elseif ($count >= 2) {
                    $level = 2;
                } elseif ($count >= 1) {
                    $level = 1;
                }
            }

            $dateFormatted = $current->format('F jS');
            $tooltip = $isFuture
                ? $dateFormatted.' (Upcoming)'
                : ($count === 0
                    ? 'No activities on '.$dateFormatted.'.'
                    : "{$count} ".($count === 1 ? 'activity' : 'activities').' on '.$dateFormatted.'.');

            $grid[] = [
                'date' => $dateStr,
                'formatted_date' => $dateFormatted,
                'month' => $current->format('M'),
                'day' => $current->day,
                'day_of_week' => $current->dayOfWeekIso,
                'count' => $count,
                'session_count' => $sessionCount,
                'task_count' => $taskCount,
                'level' => $level,
                'tooltip' => $tooltip,
                'is_future' => $isFuture,
                'is_today' => $current->isToday(),
            ];

            $current->addDay();
            if ($current->isAfter($endDate) && $current->dayOfWeekIso === 1) {
                break;
            }
        }

        $rawWeeks = array_chunk($grid, 7);
        $weeksWithMonths = [];
        $lastMonth = null;
        $lastMonthIndex = -4;

        foreach ($rawWeeks as $idx => $days) {
            $monthLabel = null;
            if (! empty($days)) {
                $month = $days[0]['month'];
                if ($month !== $lastMonth && ($idx - $lastMonthIndex) >= 3) {
                    $monthLabel = $month;
                    $lastMonth = $month;
                    $lastMonthIndex = $idx;
                }
            }

            $weeksWithMonths[] = [
                'month_label' => $monthLabel,
                'days' => $days,
            ];
        }

        return [
            'weeks' => $weeksWithMonths,
            'totalActivities' => $totalActivities,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Tasks as GoogleTasks;
use Google\Service\Tasks\TaskList;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private Client $client;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->client = new Client;
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(route('google.calendar.callback'));
        $this->client->setScopes([
            GoogleCalendar::CALENDAR,
            GoogleCalendar::CALENDAR_EVENTS,
            GoogleTasks::TASKS,
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');

        if ($user->google_access_token) {
            $this->client->setAccessToken($user->google_access_token);

            if ($this->client->isAccessTokenExpired() && $user->google_refresh_token) {
                $this->client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $user->update([
                    'google_access_token' => $this->client->getAccessToken(),
                ]);
            }
        }
    }

    public function getAuthUrl(): string
    {
        return $this->client->createAuthUrl();
    }

    public function handleCallback(string $code): bool
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                Log::error('Google OAuth error', $token);

                return false;
            }

            $this->user->update([
                'google_access_token' => $token,
                'google_refresh_token' => $token['refresh_token'] ?? $this->user->google_refresh_token,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google OAuth callback error', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function isConnected(): bool
    {
        return ! empty($this->user->google_access_token);
    }

    public function syncSchedulesToCalendar(): array
    {
        if (! $this->isConnected()) {
            return ['synced' => 0, 'errors' => ['Not connected to Google Calendar']];
        }

        $schedules = Schedule::where('user_id', $this->user->id)
            ->whereDate('date', '>=', now()->subWeek())
            ->get();

        $synced = 0;
        $errors = [];

        foreach ($schedules as $schedule) {
            try {
                $this->upsertEvent($schedule);
                $synced++;
            } catch (\Exception $e) {
                $errors[] = "Schedule '{$schedule->title}': ".$e->getMessage();
                Log::error('Google Calendar sync error', [
                    'schedule_id' => $schedule->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    public function syncSchedule(Schedule $schedule): bool
    {
        if (! $this->isConnected()) {
            return false;
        }

        try {
            $this->upsertEvent($schedule);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar auto-sync error', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function deleteEvent(Schedule $schedule): bool
    {
        if (! $this->isConnected() || empty($schedule->google_event_id)) {
            return false;
        }

        try {
            $calendar = new GoogleCalendar($this->client);
            $calendar->events->delete('primary', $schedule->google_event_id);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar event delete error', [
                'schedule_id' => $schedule->id,
                'event_id' => $schedule->google_event_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function upsertEvent(Schedule $schedule): void
    {
        $calendar = new GoogleCalendar($this->client);

        $event = new Event([
            'summary' => $schedule->title,
            'description' => $schedule->subject,
            'start' => [
                'dateTime' => $schedule->date->format('Y-m-d').'T'.$schedule->start_time->format('H:i').':00',
                'timeZone' => $this->calendarTimezone(),
            ],
            'end' => [
                'dateTime' => $schedule->date->format('Y-m-d').'T'.$schedule->end_time->format('H:i').':00',
                'timeZone' => $this->calendarTimezone(),
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 60],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ],
        ]);

        $rule = $this->recurrenceRule($schedule);

        if ($rule !== null) {
            $event->setRecurrence([$rule]);
        }

        if (! empty($schedule->google_event_id)) {
            $calendar->events->update('primary', $schedule->google_event_id, $event);

            return;
        }

        $createdEvent = $calendar->events->insert('primary', $event);
        $schedule->update(['google_event_id' => $createdEvent->getId()]);
    }

    private function recurrenceRule(Schedule $schedule): ?string
    {
        $frequency = strtolower((string) $schedule->recurrence_frequency);

        if (! in_array($frequency, ['daily', 'weekly', 'monthly'], true)) {
            return null;
        }

        $freqMap = [
            'daily' => 'DAILY',
            'weekly' => 'WEEKLY',
            'monthly' => 'MONTHLY',
        ];

        $interval = max(1, (int) $schedule->recurrence_interval);
        $rule = 'RRULE:FREQ='.$freqMap[$frequency].';INTERVAL='.$interval;

        if ($frequency === 'weekly' && ! empty($schedule->recurrence_days)) {
            $days = collect(explode(',', $schedule->recurrence_days))
                ->map(fn ($day) => strtoupper(trim($day)))
                ->filter()
                ->implode(',');

            if ($days !== '') {
                $rule .= ';BYDAY='.$days;
            }
        }

        if (! empty($schedule->recurrence_count)) {
            $rule .= ';COUNT='.(int) $schedule->recurrence_count;
        } elseif (! empty($schedule->recurrence_until)) {
            $rule .= ';UNTIL='.$schedule->recurrence_until->format('Ymd').'T235959Z';
        }

        return $rule;
    }

    private function calendarTimezone(): string
    {
        return config('services.google.calendar_timezone', 'Asia/Jakarta');
    }

    public function pullEventsFromCalendar(int $daysAhead = 30): array
    {
        if (! $this->isConnected()) {
            return ['imported' => 0, 'errors' => ['Not connected to Google Calendar']];
        }

        $calendar = new GoogleCalendar($this->client);
        $timeMin = now()->subDay()->startOfDay()->toRfc3339String();
        $timeMax = now()->addDays($daysAhead)->endOfDay()->toRfc3339String();

        try {
            $events = $calendar->events->listEvents('primary', [
                'timeMin' => $timeMin,
                'timeMax' => $timeMax,
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'maxResults' => 100,
            ]);

            $imported = 0;
            $errors = [];

            foreach ($events->getItems() as $event) {
                $start = $event->getStart();
                $end = $event->getEnd();

                if (! $start || ! $end) {
                    continue;
                }

                $startDateTime = $start->getDateTime() ?? $start->getDate();
                $endDateTime = $end->getDateTime() ?? $end->getDate();

                if (! $startDateTime || ! $endDateTime) {
                    continue;
                }

                $existing = Schedule::where('user_id', $this->user->id)
                    ->where('google_event_id', $event->getId())
                    ->first();

                if ($existing) {
                    continue;
                }

                $startDate = Carbon::parse($startDateTime);
                $endDate = Carbon::parse($endDateTime);

                Schedule::create([
                    'user_id' => $this->user->id,
                    'title' => $event->getSummary() ?? 'Untitled Event',
                    'subject' => $event->getDescription(),
                    'date' => $startDate->format('Y-m-d'),
                    'start_time' => $startDate->format('H:i'),
                    'end_time' => $endDate->format('H:i'),
                    'status' => 'pending',
                    'google_event_id' => $event->getId(),
                ]);
                $imported++;
            }

            return ['imported' => $imported, 'errors' => $errors];
        } catch (\Exception $e) {
            Log::error('Google Calendar pull error', ['error' => $e->getMessage()]);

            return ['imported' => 0, 'errors' => [$e->getMessage()]];
        }
    }

    // === Google Tasks ===

    public function getTaskLists(): array
    {
        if (! $this->isConnected()) {
            return [];
        }

        try {
            $tasksService = new GoogleTasks($this->client);
            $taskLists = $tasksService->tasklists->listTasklists(['maxResults' => 100]);

            return array_map(fn (TaskList $list) => [
                'id' => $list->getId(),
                'title' => $list->getTitle(),
            ], $taskLists->getItems());
        } catch (\Exception $e) {
            Log::error('Google Tasks list error', ['error' => $e->getMessage()]);

            return [];
        }
    }

    public function pullTasksFromGoogle(?string $taskListId = null): array
    {
        if (! $this->isConnected()) {
            return ['imported' => 0, 'errors' => ['Not connected to Google Tasks']];
        }

        $taskListId = $taskListId ?? $this->taskListId();

        try {
            $tasksService = new GoogleTasks($this->client);
            $googleTasks = $tasksService->tasks->listTasks($taskListId, ['maxResults' => 100]);

            $imported = 0;
            $errors = [];

            foreach ($googleTasks->getItems() as $googleTask) {
                $existing = Task::where('user_id', $this->user->id)
                    ->where('google_task_id', $googleTask->getId())
                    ->first();

                if ($existing || ! $googleTask->getTitle()) {
                    continue;
                }

                $dueDate = null;
                $due = $googleTask->getDue();
                if ($due) {
                    $dueDate = Carbon::parse($due)->setTimezone($this->calendarTimezone())->format('Y-m-d');
                }

                Task::create([
                    'user_id' => $this->user->id,
                    'title' => $googleTask->getTitle(),
                    'description' => $googleTask->getNotes(),
                    'due_date' => $dueDate,
                    'status' => $googleTask->getStatus() === 'completed' ? 'completed' : 'pending',
                    'category' => 'personal',
                    'priority' => 'medium',
                    'google_task_id' => $googleTask->getId(),
                ]);
                $imported++;
            }

            return ['imported' => $imported, 'errors' => $errors];
        } catch (\Exception $e) {
            Log::error('Google Tasks pull error', ['error' => $e->getMessage()]);

            return ['imported' => 0, 'errors' => [$e->getMessage()]];
        }
    }

    public function syncTasksToList(?string $taskListId = null): array
    {
        if (! $this->isConnected()) {
            return ['synced' => 0, 'errors' => ['Not connected to Google Tasks']];
        }

        $taskListId = $taskListId ?? $this->taskListId();

        $tasks = Task::where('user_id', $this->user->id)
            ->where('status', 'pending')
            ->get();

        $synced = 0;
        $errors = [];

        foreach ($tasks as $task) {
            try {
                if ($this->upsertTask($task, $taskListId)) {
                    $synced++;
                }
            } catch (\Exception $e) {
                $errors[] = "Task '{$task->title}': ".$e->getMessage();
                Log::error('Google Tasks sync error', [
                    'task_id' => $task->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    private function taskListId(): string
    {
        return (string) config('services.google.tasks_task_list', 'default');
    }

    public function syncTask(Task $task): bool
    {
        if (! $this->isConnected()) {
            return false;
        }

        try {
            $this->upsertTask($task);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Tasks auto-sync error', [
                'task_id' => $task->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function deleteTaskInGoogle(Task $task): bool
    {
        if (! $this->isConnected() || empty($task->google_task_id)) {
            return false;
        }

        try {
            $tasksService = new GoogleTasks($this->client);
            $tasksService->tasks->delete($this->taskListId(), $task->google_task_id);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Tasks delete error', [
                'task_id' => $task->id,
                'google_task_id' => $task->google_task_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function upsertTask(Task $task, ?string $taskListId = null): void
    {
        $tasksService = new GoogleTasks($this->client);
        $taskListId = $taskListId ?? $this->taskListId();

        $googleTask = new GoogleTasks\Task([
            'title' => $task->title,
            'notes' => $task->description,
            'due' => $task->due_date
                ->setTimezone($this->calendarTimezone())
                ->endOfDay()
                ->toRfc3339String(),
            'status' => $task->status === 'completed' ? 'completed' : 'needsAction',
        ]);

        if (! empty($task->google_task_id)) {
            $tasksService->tasks->update($taskListId, $task->google_task_id, $googleTask);

            return;
        }

        $created = $tasksService->tasks->insert($taskListId, $googleTask);
        $task->update(['google_task_id' => $created->getId()]);
    }
}

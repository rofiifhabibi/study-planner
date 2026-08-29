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
        $this->client->setRedirectUri(config('services.google.redirect'));
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

        $calendar = new GoogleCalendar($this->client);
        $schedules = Schedule::where('user_id', $this->user->id)
            ->whereNull('google_event_id')
            ->whereDate('date', '>=', now()->subWeek())
            ->get();

        $synced = 0;
        $errors = [];

        foreach ($schedules as $schedule) {
            try {
                $event = new Event([
                    'summary' => $schedule->title,
                    'description' => $schedule->subject,
                    'start' => [
                        'dateTime' => $schedule->date->format('Y-m-d').'T'.$schedule->start_time.':00',
                        'timeZone' => config('app.timezone', 'Asia/Jakarta'),
                    ],
                    'end' => [
                        'dateTime' => $schedule->date->format('Y-m-d').'T'.$schedule->end_time.':00',
                        'timeZone' => config('app.timezone', 'Asia/Jakarta'),
                    ],
                ]);

                $createdEvent = $calendar->events->insert('primary', $event);
                $schedule->update(['google_event_id' => $createdEvent->getId()]);
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

    public function syncTasksToList(string $taskListId = 'default'): array
    {
        if (! $this->isConnected()) {
            return ['synced' => 0, 'errors' => ['Not connected to Google Tasks']];
        }

        try {
            $tasksService = new GoogleTasks($this->client);
            $tasks = Task::where('user_id', $this->user->id)
                ->where('status', 'pending')
                ->get();

            $synced = 0;
            $errors = [];

            foreach ($tasks as $task) {
                try {
                    $googleTask = new GoogleTasks\Task([
                        'title' => $task->title,
                        'notes' => $task->description,
                        'due' => $task->due_date->endOfDay()->toRfc3339String(),
                    ]);

                    $tasksService->tasks->insert($taskListId, $googleTask);
                    $synced++;
                } catch (\Exception $e) {
                    $errors[] = "Task '{$task->title}': ".$e->getMessage();
                }
            }

            return ['synced' => $synced, 'errors' => $errors];
        } catch (\Exception $e) {
            Log::error('Google Tasks sync error', ['error' => $e->getMessage()]);

            return ['synced' => 0, 'errors' => [$e->getMessage()]];
        }
    }
}

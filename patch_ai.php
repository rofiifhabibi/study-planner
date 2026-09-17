<?php
$content = file_get_contents('app/Http/Controllers/AiIntegrationController.php');

// Add GoogleCalendarService usage
$content = str_replace(
    "use App\Models\Task;",
    "use App\Models\Task;\nuse App\Services\GoogleCalendarService;\nuse Illuminate\Support\Facades\Log;",
    $content
);

// Add sync to createCalendarEvent
$replacement1 = <<<'HTML'
        $schedule = Schedule::create([
            'user_id' => $user->id,
            ...$validated,
            'status' => 'pending'
        ]);
        
        try {
            $service = app(GoogleCalendarService::class, ['user' => $user]);
            $service->syncSchedule($schedule);
        } catch (\Exception $e) {
            Log::error('AI Failed to sync Schedule to Google', ['schedule_id' => $schedule->id, 'error' => $e->getMessage()]);
        }
HTML;
$content = preg_replace('/\$schedule = Schedule::create\(\[\s*\'user_id\' => \$user->id,\s*\.\.\.\$validated,\s*\'status\' => \'pending\'\s*\]\);/s', $replacement1, $content);

// Add sync to createTask
$replacement2 = <<<'HTML'
        $task = Task::create([
            'user_id' => $user->id,
            ...$validated,
            'status' => 'pending'
        ]);
        
        try {
            $service = app(GoogleCalendarService::class, ['user' => $user]);
            $service->syncTask($task);
        } catch (\Exception $e) {
            Log::error('AI Failed to sync Task to Google', ['task_id' => $task->id, 'error' => $e->getMessage()]);
        }
HTML;
$content = preg_replace('/\$task = Task::create\(\[\s*\'user_id\' => \$user->id,\s*\.\.\.\$validated,\s*\'status\' => \'pending\'\s*\]\);/s', $replacement2, $content);

file_put_contents('app/Http/Controllers/AiIntegrationController.php', $content);

<?php

namespace App\Observers;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

class TaskObserver
{
    /**
     * Handle the Task "deleting" event.
     */
    public function deleting(Task $task): void
    {
        try {
            if ($task->user && $task->google_task_id) {
                $service = app(GoogleCalendarService::class, ['user' => $task->user]);
                $service->deleteTaskInGoogle($task);
            }
        } catch (\Exception $e) {
            Log::error('Observer Failed to delete Google Task', ['task_id' => $task->id, 'error' => $e->getMessage()]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskStepController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => 'success',
            'steps' => $task->steps,
        ]);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $step = $task->steps()->create([
            'title' => $validated['title'],
            'is_completed' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'step' => $step,
        ]);
    }

    public function update(Request $request, Task $task, TaskStep $step): JsonResponse
    {
        if ($task->user_id !== auth()->id() || $step->task_id !== $task->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $step->update([
            'is_completed' => $validated['is_completed'],
        ]);
        
        // Auto-complete task if all steps are completed
        $totalSteps = $task->steps()->count();
        $completedSteps = $task->steps()->where('is_completed', true)->count();
        
        $taskCompleted = false;
        if ($totalSteps > 0 && $totalSteps === $completedSteps && $task->status !== 'completed') {
            $task->update(['status' => 'completed']);
            $taskCompleted = true;
            
            // Sync to Google Tasks
            try {
                $service = app(\App\Services\GoogleCalendarService::class, ['user' => auth()->user()]);
                $service->syncTask($task);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-sync task failed', ['error' => $e->getMessage()]);
            }
        } elseif ($task->status === 'completed' && $completedSteps < $totalSteps) {
            // Un-complete the task if a step is unchecked
            $task->update(['status' => 'pending']);
            $taskCompleted = false;
            
            // Sync to Google Tasks
            try {
                $service = app(\App\Services\GoogleCalendarService::class, ['user' => auth()->user()]);
                $service->syncTask($task);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Auto-sync task failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'status' => 'success',
            'step' => $step,
            'task_status' => $task->status,
        ]);
    }

    public function destroy(Task $task, TaskStep $step): JsonResponse
    {
        if ($task->user_id !== auth()->id() || $step->task_id !== $task->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $step->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }
}

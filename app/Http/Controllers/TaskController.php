<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function calendarService(): GoogleCalendarService
    {
        return app(GoogleCalendarService::class, ['user' => auth()->user()]);
    }

    public function index(Request $request): JsonResponse
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('due_date', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
            'category' => 'required|in:school,project,study,personal',
            'priority' => 'required|in:low,medium,high',
        ]);

        $task = Task::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        $this->calendarService()->syncTask($task);

        return response()->json([
            'status' => 'success',
            'task' => $task,
        ], 201);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:pending,completed',
            'category' => 'sometimes|required|in:school,project,study,personal',
            'priority' => 'sometimes|required|in:low,medium,high',
        ]);

        $task->update($validated);

        $this->calendarService()->syncTask($task);

        return response()->json([
            'status' => 'success',
            'task' => $task->fresh(),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $this->calendarService()->deleteTaskInGoogle($task);

        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task berhasil dihapus.',
        ]);
    }
}

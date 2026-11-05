<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudySessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sessions = StudySession::where('user_id', auth()->id())
            ->latest('started_at')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'sessions' => $sessions,
        ]);
    }

    public function active(): JsonResponse
    {
        $session = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->latest('started_at')
            ->first();

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'task_id' => 'nullable|exists:tasks,id',
            'duration_seconds' => 'nullable|integer|min:0',
            'status' => 'nullable|in:running,completed,paused',
        ]);

        if (! empty($validated['task_id'])) {
            $task = Task::where('id', $validated['task_id'])
                ->where('user_id', auth()->id())
                ->first();

            if (! $task) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tugas tidak ditemukan.',
                ], 422);
            }
        }

        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            $active->update([
                'ended_at' => now(),
                'duration_seconds' => $active->duration_seconds + $active->started_at->diffInSeconds(now()),
                'status' => 'completed',
            ]);
        }

        $status = $validated['status'] ?? 'running';
        $durationSeconds = isset($validated['duration_seconds']) ? (int) $validated['duration_seconds'] : 0;

        $session = StudySession::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'task_id' => $validated['task_id'] ?? null,
            'duration_seconds' => $durationSeconds,
            'started_at' => $durationSeconds > 0 ? now()->subSeconds($durationSeconds) : now(),
            'ended_at' => $status === 'completed' ? now() : null,
            'status' => $status,
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ], 201);
    }

    public function stop(Request $request, StudySession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($session->status !== 'running' && $session->status !== 'paused') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi ini sudah tidak aktif.',
            ], 400);
        }

        $requestedDuration = $request->input('duration_seconds');
        if ($requestedDuration !== null && (int) $requestedDuration > 0) {
            $durationSeconds = (int) $requestedDuration;
        } else {
            $durationSeconds = $session->duration_seconds + $session->started_at->diffInSeconds(now());
        }

        $session->update([
            'ended_at' => now(),
            'duration_seconds' => max($durationSeconds, 1),
            'status' => 'completed',
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
        ]);
    }

    public function pause(StudySession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id() || $session->status !== 'running') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized atau sesi tidak aktif.',
            ], 403);
        }

        $elapsed = $session->started_at->diffInSeconds(now());

        $session->update([
            'duration_seconds' => $session->duration_seconds + $elapsed,
            'status' => 'paused',
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
        ]);
    }

    public function resume(StudySession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id() || ($session->status !== 'paused' && $session->status !== 'completed')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized atau sesi tidak bisa dilanjutkan.',
            ], 403);
        }

        // Auto-stop currently running session if there is one
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            $active->update([
                'ended_at' => now(),
                'duration_seconds' => $active->duration_seconds + $active->started_at->diffInSeconds(now()),
                'status' => 'completed',
            ]);
        }

        $session->update([
            'started_at' => now(),
            'status' => 'running',
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
        ]);
    }

    public function destroy(StudySession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $session->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesi belajar dihapus.',
        ]);
    }
}

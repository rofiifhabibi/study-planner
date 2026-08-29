<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
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
        $active = StudySession::where('user_id', auth()->id())
            ->where('status', 'running')
            ->first();

        if ($active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ada sesi belajar yang masih berjalan. Selesaikan atau hentikan terlebih dahulu.',
                'active_session' => $active,
            ], 409);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'task_id' => 'nullable|exists:tasks,id',
        ]);

        $session = StudySession::create([
            'user_id' => auth()->id(),
            ...$validated,
            'started_at' => now(),
            'status' => 'running',
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ], 201);
    }

    public function stop(StudySession $session): JsonResponse
    {
        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($session->status !== 'running') {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi ini sudah tidak aktif.',
            ], 400);
        }

        $session->update([
            'ended_at' => now(),
            'duration_seconds' => $session->started_at->diffInSeconds(now()),
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
        if ($session->user_id !== auth()->id() || $session->status !== 'paused') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized atau sesi tidak di-pause.',
            ], 403);
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

<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        $notes = Note::where('user_id', auth()->id())
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'notes' => $notes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:10000',
        ]);

        $note = Note::create([
            'user_id' => auth()->id(),
            ...$validated,
        ]);

        return response()->json([
            'status' => 'success',
            'note' => $note,
        ], 201);
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'sometimes|required|string|max:10000',
        ]);

        $note->update($validated);

        return response()->json([
            'status' => 'success',
            'note' => $note->fresh(),
        ]);
    }

    public function destroy(Note $note): JsonResponse
    {
        if ($note->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $note->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan berhasil dihapus.',
        ]);
    }
}

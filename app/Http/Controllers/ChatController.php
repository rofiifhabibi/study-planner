<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Schedule;
use App\Models\SchoolTimetable;
use App\Services\GroqAiService;
use App\Services\ProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $sessionId = $request->query('session');
        if ($sessionId) {
            $session = ChatSession::where('id', $sessionId)
                ->where('user_id', auth()->id())
                ->first();

            if (! $session) {
                return redirect()->route('chat');
            }
        }

        $user = auth()->user();

        return view('chat', [
            'userName' => $user->name,
            'userInitial' => strtoupper(substr($user->name, 0, 1)),
        ]);
    }

    public function dashboard()
    {
        $user = auth()->user();

        $sessions = ChatSession::where('user_id', $user->id)
            ->withCount('messages')
            ->latest()
            ->get();

        $sessionIds = $sessions->pluck('id');
        $totalMessages = $sessionIds->isNotEmpty()
            ? ChatMessage::whereIn('chat_session_id', $sessionIds)->count()
            : 0;

        $progress = (new ProgressService($user->id))->getDashboardStats();

        $todaySchedules = Schedule::where('user_id', $user->id)
            ->whereDate('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $todayDayOfWeek = now()->dayOfWeek;
        $todayTimetables = SchoolTimetable::where('user_id', $user->id)
            ->where('day_of_week', $todayDayOfWeek)
            ->orderBy('start_time')
            ->get();

        $tomorrowDayOfWeek = now()->addDay()->dayOfWeek;
        $tomorrowTimetables = SchoolTimetable::where('user_id', $user->id)
            ->where('day_of_week', $tomorrowDayOfWeek)
            ->orderBy('start_time')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'sessions' => $sessions,
            'totalSessions' => $sessions->count(),
            'totalMessages' => $totalMessages,
            'todaySchedules' => $todaySchedules,
            'todayTimetables' => $todayTimetables,
            'tomorrowTimetables' => $tomorrowTimetables,
            ...$progress,
        ]);
    }

    public function getSessions(Request $request)
    {
        $sessions = ChatSession::where('user_id', auth()->id())
            ->withCount('messages')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'sessions' => $sessions,
        ]);
    }

    public function getMessages(Request $request, string $sessionId)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $messages = ChatMessage::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'session' => $session,
            'messages' => $messages,
        ]);
    }

    public function renameSession(Request $request, string $sessionId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $session->update(['title' => $request->input('title')]);

        return response()->json([
            'status' => 'success',
            'session' => $session->fresh(),
        ]);
    }

    public function deleteSession(Request $request, string $sessionId)
    {
        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($session->is_project) {
            ChatSession::where('parent_id', $session->id)->delete();
        }

        $session->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Session berhasil dihapus',
        ]);
    }

    public function createSession(Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|string|exists:chat_sessions,id',
        ]);

        $parentId = null;

        if ($request->filled('parent_id')) {
            $project = ChatSession::where('id', $request->input('parent_id'))
                ->where('user_id', auth()->id())
                ->where('is_project', true)
                ->first();

            if (! $project) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Proyek tidak ditemukan.',
                ], 404);
            }

            $parentId = $project->id;
        }

        $title = $request->input('title', 'Chat '.now()->format('d M Y H:i'));

        $session = ChatSession::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'title' => $title,
            'session_key' => Str::random(32),
            'is_project' => $request->boolean('is_project'),
            'parent_id' => $parentId,
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_session_id' => 'required|string',
            'message' => 'required|string',
            'file_url' => 'nullable|url',
        ]);

        $sessionId = $request->chat_session_id;
        $userMessage = $request->message;
        $fileUrl = $request->file_url;

        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $savedUserMessage = ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => 'user',
            'content' => $userMessage,
            'file_url' => $fileUrl,
        ]);

        /** @var GroqAiService $groq */
        $groq = app(GroqAiService::class);
        $history = $groq->buildHistoryFromSession($session);

        // Jika sesi adalah bagian dari project, sertakan history dari sesi saudara
        if ($session->parent_id) {
            $siblingIds = ChatSession::where('parent_id', $session->parent_id)
                ->where('user_id', auth()->id())
                ->whereKeyNot($session->id)
                ->pluck('id');

            $siblingHistory = ChatMessage::whereIn('chat_session_id', $siblingIds)
                ->where('is_canceled', false)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->reverse()
                ->map(fn (ChatMessage $m) => [
                    'role' => $m->role === 'assistant' ? 'assistant' : 'user',
                    'content' => $m->content,
                ])
                ->values()
                ->all();

            $history = array_merge($siblingHistory, $history);
        }

        try {
            $aiReply = $groq->chat($userMessage, $history);
        } catch (\Throwable $e) {
            Log::error('Groq chat error pada sendMessage', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke AI: '.$e->getMessage(),
            ], 502);
        }

        if (empty($aiReply)) {
            Log::warning('Groq memberikan respons kosong pada sendMessage');

            return response()->json([
                'status' => 'error',
                'message' => 'AI memberikan respons kosong.',
            ], 502);
        }

        ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $aiReply,
        ]);

        return response()->json([
            'status' => 'success',
            'reply' => $aiReply,
            'message_id' => $savedUserMessage->id,
        ]);
    }

    public function updateMessage(Request $request, ChatMessage $message)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $session = $message->session;

        if ($session->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $message->update([
            'content' => $request->message,
            'is_canceled' => false,
        ]);

        /** @var GroqAiService $groq */
        $groq = app(GroqAiService::class);
        $history = $groq->buildHistoryFromSession($session);

        try {
            $aiReply = $groq->chat($request->message, $history);
        } catch (\Throwable $e) {
            Log::error('Groq chat error pada updateMessage', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi AI: '.$e->getMessage(),
            ], 502);
        }

        if ($aiReply !== '') {
            $replyMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $aiReply,
            ]);

            return response()->json([
                'status' => 'success',
                'reply' => $aiReply,
                'message' => $message->fresh(),
                'reply_message' => $replyMessage,
            ]);
        }

        Log::warning('Groq memberikan respons kosong pada updateMessage');

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan respon dari AI',
        ], 502);
    }

    public function cancelMessage(ChatMessage $message)
    {
        if ($message->session->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.',
            ], 403);
        }

        $message->update(['is_canceled' => true]);

        return response()->json([
            'status' => 'success',
            'message' => $message->fresh(),
        ]);
    }

    public function user()
    {
        return response()->json([
            'user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ]);
    }
}

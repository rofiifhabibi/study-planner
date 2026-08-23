<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        return view('dashboard', [
            'user' => $user,
            'sessions' => $sessions,
            'totalSessions' => $sessions->count(),
            'totalMessages' => $totalMessages,
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

        $webhookUrl = config('services.n8n.webhook_url');
        $secretKey = config('services.n8n.secret_key');
        $timeout = config('services.n8n.timeout', 90);

        if (empty($webhookUrl)) {
            Log::error('n8n webhook URL tidak dikonfigurasi');

            return response()->json([
                'status' => 'error',
                'message' => 'Konfigurasi n8n webhook belum diatur.',
            ], 500);
        }

        $session = ChatSession::where('id', $sessionId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $savedUserMessage = ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => 'user',
            'content' => $userMessage,
            'file_url' => $fileUrl,
        ]);

        $memoryKey = 'user_'.auth()->id().'_session_'.$session->session_key;

        $payload = [
            'user_id' => auth()->id(),
            'session_id' => $memoryKey,
            'message' => $userMessage,
            'file_url' => $fileUrl,
        ];

        if ($session->parent_id) {
            $siblingIds = ChatSession::where('parent_id', $session->parent_id)
                ->where('user_id', auth()->id())
                ->whereKeyNot($session->id)
                ->pluck('id');

            $history = ChatMessage::whereIn('chat_session_id', $siblingIds)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
                ->reverse()
                ->map(fn (ChatMessage $m) => [
                    'role' => $m->role,
                    'content' => $m->content,
                    'is_canceled' => $m->is_canceled,
                ])
                ->values()
                ->all();

            if ($history !== []) {
                $payload['history'] = $history;
            }
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $secretKey,
            ])->timeout($timeout)->post($webhookUrl, $payload);
        } catch (ConnectionException $e) {
            Log::error('n8n webhook timeout', [
                'url' => $webhookUrl,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke n8n AI. Webhook tidak dapat dijangkau.',
            ], 504);
        } catch (\Exception $e) {
            Log::error('n8n webhook error', [
                'url' => $webhookUrl,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi n8n AI.',
            ], 500);
        }

        if ($response->successful()) {
            $aiReply = $response->json('output')
                    ?? $response->json('reply')
                    ?? $response->json('message')
                    ?? $response->json('text')
                    ?? '';

            if (is_string($aiReply)) {
                $aiReply = trim($aiReply);
            }

            if (empty($aiReply)) {
                Log::warning('n8n respons kosong', [
                    'url' => $webhookUrl,
                    'raw_response' => $response->body(),
                ]);

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

        Log::warning('n8n webhook error', [
            'url' => $webhookUrl,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan respon dari n8n AI',
        ], 502);
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

        $webhookUrl = config('services.n8n.webhook_url');
        $secretKey = config('services.n8n.secret_key');
        $timeout = config('services.n8n.timeout', 90);

        if (empty($webhookUrl)) {
            Log::error('n8n webhook URL tidak dikonfigurasi');

            return response()->json([
                'status' => 'error',
                'message' => 'Konfigurasi n8n webhook belum diatur.',
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $secretKey,
            ])->timeout($timeout)->post($webhookUrl, [
                'user_id' => auth()->id(),
                'session_id' => 'user_'.auth()->id().'_session_'.$session->session_key,
                'message' => $request->message,
            ]);
        } catch (\Exception $e) {
            Log::error('n8n webhook error saat edit pesan', [
                'url' => $webhookUrl,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi n8n AI.',
            ], 502);
        }

        if ($response->successful()) {
            $aiReply = trim((string) ($response->json('output')
                ?? $response->json('reply')
                ?? $response->json('message')
                ?? $response->json('text')
                ?? ''));

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
        }

        Log::warning('n8n webhook error saat edit pesan', [
            'url' => $webhookUrl,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mendapatkan respon dari n8n AI',
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

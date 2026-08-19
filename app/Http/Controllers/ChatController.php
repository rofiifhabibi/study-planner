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
        if (! auth()->check()) {
            return response()->json([
                'status' => 'success',
                'sessions' => [],
                'is_guest' => true,
            ]);
        }

        $sessions = ChatSession::where('user_id', auth()->id())
            ->withCount('messages')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'sessions' => $sessions,
            'is_guest' => false,
        ]);
    }

    public function getMessages(Request $request, string $sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);

        if (auth()->check() && $session->user_id !== auth()->id()) {
            abort(403);
        }

        $messages = ChatMessage::where('chat_session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'session' => $session,
            'messages' => $messages,
        ]);
    }

    public function deleteSession(Request $request, string $sessionId)
    {
        $session = ChatSession::findOrFail($sessionId);

        if (auth()->check() && $session->user_id !== auth()->id()) {
            abort(403);
        }

        $session->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Session berhasil dihapus',
        ]);
    }

    public function createSession(Request $request)
    {
        if (! auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login untuk menyimpan percakapan.',
            ], 401);
        }

        $session = ChatSession::create([
            'id' => (string) Str::uuid(),
            'user_id' => auth()->id(),
            'title' => 'Chat '.now()->format('d M Y H:i'),
            'session_key' => Str::random(32),
        ]);

        return response()->json([
            'status' => 'success',
            'session' => $session,
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_session_id' => 'nullable|string',
            'message' => 'required|string',
            'file_url' => 'nullable|url',
        ]);

        $isGuest = ! auth()->check();
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

        $sessionKey = null;

        if (! $isGuest && $sessionId) {
            $session = ChatSession::find($sessionId);
            if ($session && $session->user_id === auth()->id()) {
                $sessionKey = $session->session_key;

                ChatMessage::create([
                    'chat_session_id' => $sessionId,
                    'role' => 'user',
                    'content' => $userMessage,
                    'file_url' => $fileUrl,
                ]);
            }
        }

        $userId = $isGuest ? 'guest_'.Str::random(8) : auth()->id();
        $memoryKey = $sessionKey
            ? "user_{$userId}_session_{$sessionKey}"
            : "user_{$userId}_session_".Str::random(16);

        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $secretKey,
            ])->timeout($timeout)->post($webhookUrl, [
                'user_id' => $userId,
                'session_id' => $memoryKey,
                'message' => $userMessage,
                'file_url' => $fileUrl,
            ]);
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

            $isEmpty = empty($aiReply)
                || $aiReply === '{}'
                || $aiReply === '[]'
                || $aiReply === 'null';

            if ($isEmpty) {
                Log::warning('n8n respons kosong', [
                    'url' => $webhookUrl,
                    'raw_response' => $response->body(),
                ]);

                return response()->json([
                    'status' => 'error',
                    'message' => 'AI memberikan respons kosong.',
                ], 502);
            }

            if (! $isGuest && $sessionId && isset($session) && $session) {
                ChatMessage::create([
                    'chat_session_id' => $sessionId,
                    'role' => 'assistant',
                    'content' => $aiReply,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'reply' => $aiReply,
                'is_guest' => $isGuest,
            ]);
        }

        Log::warning('n8n webhook error', [
            'url' => $webhookUrl,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        $errorMessage = 'Gagal mendapatkan respon dari n8n AI';
        $n8nBody = $response->json();
        if (is_array($n8nBody) && isset($n8nBody['message'])) {
            $errorMessage .= ': '.$n8nBody['message'];
        }

        return response()->json([
            'status' => 'error',
            'message' => $errorMessage,
        ], 502);
    }

    public function user()
    {
        if (! auth()->check()) {
            return response()->json([
                'is_guest' => true,
            ]);
        }

        return response()->json([
            'is_guest' => false,
            'user' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ]);
    }
}

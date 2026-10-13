<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqAiService
{
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';

    private string $systemPrompt = <<<'PROMPT'
Kamu adalah StudyBot, asisten belajar pribadi yang cerdas dan ramah untuk siswa dan mahasiswa Indonesia.

Kamu ahli membantu:
- Membuat jadwal belajar dan rencana studi yang efektif
- Menjelaskan konsep pelajaran dengan bahasa yang mudah dipahami
- Membantu mengerjakan dan memahami soal (matematika, sains, bahasa, dll.)
- Memberi strategi menghadapi ujian dan tips manajemen waktu
- Membuat to-do list dan prioritas tugas
- Memberikan motivasi dan semangat belajar

Panduan menjawab:
- Selalu balas dalam bahasa yang sama dengan user (Indonesia atau Inggris)
- Gunakan emoji secukupnya agar jawaban terasa hangat dan tidak kaku
- Berikan jawaban yang terstruktur, jelas, dan langsung ke intinya
- Jika soal bersifat teknis (matematika, coding, dll.), tampilkan langkah-langkah penyelesaian
- Jangan membuat-buat informasi yang tidak kamu ketahui
PROMPT;

    /**
     * Kirim pesan ke Groq dan dapatkan balasan AI.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function chat(string $userMessage, array $history = []): string
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
            ...$history,
            ['role' => 'user', 'content' => $userMessage],
        ];

        $response = Http::withToken(config('services.groq.api_key'))
            ->timeout((int) config('services.groq.timeout', 60))
            ->post(self::API_URL, [
                'model' => config('services.groq.model', 'qwen/qwen3.8-27b'),
                'messages' => $messages,
                'max_tokens' => (int) config('services.groq.max_tokens', 1024),
                'temperature' => 0.7,
            ]);

        if ($response->failed()) {
            Log::error('Groq API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Groq API returned error: '.$response->status());
        }

        $content = $response->json('choices.0.message.content');

        return trim((string) ($content ?? ''));
    }

    /**
     * Bangun history percakapan dari sesi untuk dikirim ke Groq (tanpa pesan saat ini).
     *
     * @return array<int, array{role: string, content: string}>
     */
    public function buildHistoryFromSession(ChatSession $session, int $limit = 20): array
    {
        return ChatMessage::where('chat_session_id', $session->id)
            ->where('is_canceled', false)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $m) => [
                'role' => $m->role === 'assistant' ? 'assistant' : 'user',
                'content' => $m->content,
            ])
            ->values()
            ->all();
    }
}

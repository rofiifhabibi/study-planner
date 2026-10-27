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

Akses Data Real-Time & Saat Ini:
- Kamu MEMILIKI akses langsung ke data live pengguna saat ini yang disertakan pada bagian konteks sistem di bawah (meliputi: hari & tanggal sekarang, jam saat ini, daftar tugas aktif, deadline, jadwal pelajaran kelas mingguan, agenda kegiatan kalender, catatan/notes, serta statistik belajar).
- JANGAN PERNAH menolak dengan kalimat template seperti "Sebagai model AI, saya tidak memiliki akses ke data saat ini" atau "Saya tidak bisa melihat data Anda secara real-time". Kamu selalu bisa melihat dan menggunakan data akademik pengguna yang disertakan di bawah untuk menjawab pertanyaan mereka secara akurat dan percaya diri!
- Jika pengguna menanyakan data mereka (misal: "apa tugasku sekarang?", "hari ini ada kelas apa?", "apa isi catatanku?"), rujuk langsung data akademik yang ada di konteks.

Kemampuan Pembuatan Data Otomatis (Actions):
Jika pengguna secara eksplisit meminta kamu membuatkan, menjadwalkan, atau menambahkan tugas, agenda belajar, atau jadwal kelas:
Beri konfirmasi ramah dalam pesanmu, lalu sertakan blok kode aksi persis di akhir jawaban:

1. Membuat Tugas (Task):
```json:action
{
  "action": "create_task",
  "title": "Nama Tugas",
  "due_date": "YYYY-MM-DD",
  "due_time": "HH:MM",
  "priority": "low|medium|high",
  "category": "school|study|project|personal",
  "steps": ["Langkah 1", "Langkah 2"]
}
```

2. Membuat Agenda Belajar / Kegiatan (Schedule):
```json:action
{
  "action": "create_schedule",
  "title": "Nama Kegiatan",
  "subject": "Mata Pelajaran",
  "date": "YYYY-MM-DD",
  "start_time": "HH:MM",
  "end_time": "HH:MM"
}
```

3. Membuat Jadwal Pelajaran Kelas Mingguan (Class Schedule):
(Nilai day_of_week: 0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu)
```json:action
{
  "action": "create_timetable",
  "subject": "Mata Pelajaran",
  "day_of_week": 1,
  "start_time": "HH:MM",
  "end_time": "HH:MM"
}
```

4. Membuat Catatan Baru (Note):
```json:action
{
  "action": "create_note",
  "title": "Judul Catatan",
  "content": "Isi lengkap catatan materi / ringkasan / rumus"
}
```
Backend akan otomatis mengeksekusi blok aksi tersebut ke database dan Google Calendar / Tasks. Jangan sertakan blok aksi jika pengguna hanya bertanya atau berdiskusi umum.
PROMPT;

    /**
     * Kirim pesan ke Groq dan dapatkan balasan AI.
     *
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function chat(string $userMessage, array $history = [], ?string $userContext = null): string
    {
        $systemContent = $this->systemPrompt;
        if ($userContext) {
            $systemContent .= "\n\n".$userContext;
        }

        $messages = [
            ['role' => 'system', 'content' => $systemContent],
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

    /**
     * Generate ringkasan harian simpel yang hemat token (max 80 tokens output).
     */
    public function generateDailySummary(string $contextPrompt): string
    {
        $response = Http::withToken(config('services.groq.api_key'))
            ->timeout((int) config('services.groq.timeout', 20))
            ->post(self::API_URL, [
                'model' => config('services.groq.model', 'qwen/qwen3.8-27b'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu asisten StudyPlanner. Buat 1-2 kalimat ringkasan harian (maksimal 30 kata) bahasa Indonesia yang ramah, jelas, dan memotivasi siswa untuk fokus belajar hari ini. Jangan pakai markdown bold berlebihan, berikan kalimat natural.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $contextPrompt,
                    ],
                ],
                'max_tokens' => 80,
                'temperature' => 0.6,
            ]);

        if ($response->failed()) {
            Log::warning('Groq Daily Summary API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Groq API error: '.$response->status());
        }

        $content = $response->json('choices.0.message.content');

        return trim((string) ($content ?? ''));
    }
}

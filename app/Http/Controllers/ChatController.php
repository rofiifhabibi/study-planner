<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\Note;
use App\Models\Schedule;
use App\Models\SchoolTimetable;
use App\Models\Task;
use App\Models\TaskStep;
use App\Models\User;
use App\Services\GoogleCalendarService;
use App\Services\GroqAiService;
use App\Services\ProgressService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $cacheKey = "ai_daily_summary_{$user->id}_".today()->toDateString();
        $aiDailySummary = Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            return $this->buildAiDailySummary($user, app(GroqAiService::class));
        });

        return view('dashboard', [
            'user' => $user,
            'sessions' => $sessions,
            'totalSessions' => $sessions->count(),
            'totalMessages' => $totalMessages,
            'todaySchedules' => $todaySchedules,
            'todayTimetables' => $todayTimetables,
            'tomorrowTimetables' => $tomorrowTimetables,
            'aiDailySummary' => $aiDailySummary,
            ...$progress,
        ]);
    }

    public function getDailySummary(Request $request, GroqAiService $groqService)
    {
        $user = auth()->user();
        $cacheKey = "ai_daily_summary_{$user->id}_".today()->toDateString();

        if ($request->boolean('refresh')) {
            Cache::forget($cacheKey);
        }

        $summary = Cache::remember($cacheKey, now()->addHours(6), function () use ($user, $groqService) {
            return $this->buildAiDailySummary($user, $groqService);
        });

        return response()->json([
            'status' => 'success',
            'summary' => $summary,
        ]);
    }

    /**
     * Bangun ringkasan harian berbasis AI yang ringkas & hemat token (maks 1-2 kalimat).
     */
    protected function buildAiDailySummary(User $user, GroqAiService $groqService): string
    {
        $today = today();
        $dayName = now()->locale('id')->translatedFormat('l');

        // 1. Tugas hari ini
        $todayTasks = Task::where('user_id', $user->id)
            ->whereDate('due_date', $today)
            ->get();
        $totalTasksToday = $todayTasks->count();
        $completedTasksToday = $todayTasks->where('status', 'completed')->count();
        $pendingTasks = $todayTasks->where('status', '!=', 'completed')->pluck('title')->take(2)->implode(', ');

        // 2. Jadwal kelas hari ini
        $dayOfWeek = now()->dayOfWeek;
        $timetablesToday = SchoolTimetable::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->pluck('subject')
            ->take(3)
            ->implode(', ');

        // 3. Agenda acara hari ini
        $schedulesToday = Schedule::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->pluck('title')
            ->take(2)
            ->implode(', ');

        // Context ringkas (sangat hemat token)
        $parts = ["Nama: {$user->name}", "Hari: {$dayName}"];
        if ($timetablesToday) {
            $parts[] = "Jadwal kelas: {$timetablesToday}";
        }
        if ($totalTasksToday > 0) {
            $parts[] = "Tugas hari ini: {$completedTasksToday}/{$totalTasksToday} selesai".($pendingTasks ? " (belum: {$pendingTasks})" : '');
        } else {
            $parts[] = 'Tugas: Tidak ada tugas hari ini';
        }
        if ($schedulesToday) {
            $parts[] = "Agenda: {$schedulesToday}";
        }

        $compactPrompt = implode(' | ', $parts);

        try {
            $aiText = $groqService->generateDailySummary($compactPrompt);
            if (! empty($aiText)) {
                return $aiText;
            }
        } catch (\Throwable $e) {
            Log::warning('AI Daily Summary fallback: '.$e->getMessage());
        }

        // Fallback lokal jika API Groq gagal/offline
        if ($totalTasksToday > 0) {
            $remaining = $totalTasksToday - $completedTasksToday;
            if ($remaining > 0) {
                return "Kamu punya {$remaining} tugas yang perlu diselesaikan hari ini. Tetap fokus dan selesaikan satu per satu!";
            }

            return 'Luar biasa! Semua tugas hari ini sudah berhasil kamu selesaikan. Manfaatkan waktu luang untuk istirahat atau mengulang materi.';
        }

        if ($timetablesToday) {
            return "Hari ini ada jadwal kelas {$timetablesToday}. Siapkan catatan dan tetap semangat mengikuti pelajaran!";
        }

        return 'Belum ada agenda padat hari ini. Waktu yang tepat untuk mencicil materi atau merencanakan target belajarmu!';
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
        $history = $this->resolveChatHistory($session, $groq);
        $userContext = $this->buildUserAcademicContext(auth()->user());

        try {
            $aiReply = $groq->chat($userMessage, $history, $userContext);
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

        $processedReply = $this->processAiActions($aiReply, auth()->user());

        ChatMessage::create([
            'chat_session_id' => $sessionId,
            'role' => 'assistant',
            'content' => $processedReply,
        ]);

        return response()->json([
            'status' => 'success',
            'reply' => $processedReply,
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
        $history = $this->resolveChatHistory($session, $groq);
        $userContext = $this->buildUserAcademicContext(auth()->user());

        try {
            $aiReply = $groq->chat($request->message, $history, $userContext);
        } catch (\Throwable $e) {
            Log::error('Groq chat error pada updateMessage', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghubungi AI: '.$e->getMessage(),
            ], 502);
        }

        if ($aiReply !== '') {
            $processedReply = $this->processAiActions($aiReply, auth()->user());

            $replyMessage = ChatMessage::create([
                'chat_session_id' => $session->id,
                'role' => 'assistant',
                'content' => $processedReply,
            ]);

            return response()->json([
                'status' => 'success',
                'reply' => $processedReply,
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

    /**
     * Selesaikan history chat:
     * - Sesi Chat Biasa: HANYA mengambil pesan dari sesi itu sendiri (tidak bisa melihat riwayat sesi lain).
     * - Sesi Proyek: Dapat melihat riwayat percakapan dari sesi lain dalam proyek yang sama.
     */
    protected function resolveChatHistory(ChatSession $session, GroqAiService $groq): array
    {
        $currentHistory = $groq->buildHistoryFromSession($session);

        $isProjectRelated = (bool) ($session->is_project || $session->parent_id);
        if (! $isProjectRelated) {
            return $currentHistory;
        }

        $projectId = $session->is_project ? $session->id : $session->parent_id;

        $projectSessionIds = ChatSession::where('user_id', auth()->id())
            ->where(function ($query) use ($projectId) {
                $query->where('id', $projectId)
                    ->orWhere('parent_id', $projectId);
            })
            ->whereKeyNot($session->id)
            ->pluck('id');

        if ($projectSessionIds->isEmpty()) {
            return $currentHistory;
        }

        $projectHistory = ChatMessage::whereIn('chat_session_id', $projectSessionIds)
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

        return array_merge($projectHistory, $currentHistory);
    }

    /**
     * Bangun konteks akademik pengguna (Tugas aktif, Jadwal agenda, Jadwal pelajaran kelas)
     * agar AI dapat membaca dan memahami jadwal serta tugas pengguna secara real-time.
     */
    protected function buildUserAcademicContext(User $user): string
    {
        $now = now()->locale('id');
        $currentDateStr = $now->translatedFormat('l, d F Y H:i').' WIB';

        // 1. Pending Tasks
        $pendingTasks = Task::where('user_id', $user->id)
            ->where('status', '!=', 'completed')
            ->with('steps')
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->orderByRaw('due_time IS NULL, due_time ASC')
            ->take(15)
            ->get();

        $tasksSummary = [];
        foreach ($pendingTasks as $t) {
            $due = $t->due_date ? $t->due_date->format('d M Y') : 'Tanpa deadline';
            if ($t->due_time) {
                $due .= ' '.$t->due_time;
            }
            $prio = ucfirst($t->priority ?? 'medium');
            $cat = $t->category ? "[{$t->category}] " : '';

            $stepsText = '';
            if ($t->steps->isNotEmpty()) {
                $doneSteps = $t->steps->where('is_completed', true)->count();
                $totalSteps = $t->steps->count();
                $stepsText = " (Sub-langkah checklist: {$doneSteps}/{$totalSteps} selesai)";
            }

            $tasksSummary[] = "- {$t->title} {$cat}| Prioritas: {$prio} | Deadline: {$due}{$stepsText}";
        }

        $tasksStr = empty($tasksSummary) ? 'Tidak ada tugas yang tertunda.' : implode("\n", $tasksSummary);

        // 2. Upcoming Schedules / Events
        $upcomingSchedules = Schedule::where('user_id', $user->id)
            ->whereDate('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(10)
            ->get();

        $schedSummary = [];
        foreach ($upcomingSchedules as $s) {
            $dateStr = $s->date ? $s->date->format('d M Y') : '';
            $timeStr = '';
            if ($s->start_time && $s->end_time) {
                $timeStr = $s->start_time->format('H:i').' - '.$s->end_time->format('H:i');
            } elseif ($s->start_time) {
                $timeStr = $s->start_time->format('H:i');
            }
            $sub = $s->subject ? " ({$s->subject})" : '';
            $schedSummary[] = "- {$dateStr} [{$timeStr}]: {$s->title}{$sub}";
        }

        $schedStr = empty($schedSummary) ? 'Tidak ada agenda khusus mendatang dalam waktu dekat.' : implode("\n", $schedSummary);

        // 3. School Timetable / Class Schedule
        $timetables = SchoolTimetable::where('user_id', $user->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $dayNames = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $timetableSummary = [];
        if ($timetables->isNotEmpty()) {
            $grouped = $timetables->groupBy('day_of_week');
            foreach ($grouped as $dayNum => $items) {
                $dayName = $dayNames[$dayNum] ?? "Hari ke-{$dayNum}";
                $classList = [];
                foreach ($items as $item) {
                    $time = $item->start_time ? substr($item->start_time, 0, 5) : '';
                    if ($item->end_time) {
                        $time .= ' - '.substr($item->end_time, 0, 5);
                    }
                    $classList[] = "{$item->subject} ({$time})";
                }
                $timetableSummary[] = "- {$dayName}: ".implode(', ', $classList);
            }
        }

        $timetableStr = empty($timetableSummary) ? 'Belum ada jadwal kelas yang diatur.' : implode("\n", $timetableSummary);

        // 4. Catatan Pengguna (Notes)
        $userNotes = Note::where('user_id', $user->id)
            ->latest('updated_at')
            ->take(12)
            ->get();

        $notesSummary = [];
        foreach ($userNotes as $n) {
            $noteTitle = $n->title ?: 'Tanpa Judul';
            $contentExcerpt = Str::limit(trim(strip_tags($n->content ?? '')), 140);
            $dateStr = $n->updated_at ? $n->updated_at->format('d M Y') : '';
            $notesSummary[] = "- [{$noteTitle}] ({$dateStr}): {$contentExcerpt}";
        }

        $notesStr = empty($notesSummary) ? 'Belum ada catatan yang disimpan.' : implode("\n", $notesSummary);

        // 5. Statistik & Progres Belajar (Pomodoro & Streak)
        $progressService = new ProgressService($user->id);
        $stats = $progressService->getDashboardStats();
        $formattedStudyTime = $stats['totalStudyTimeFormatted'] ?? '0m';
        $streak = $stats['streak'] ?? 0;
        $todayTasksCompleted = $stats['todayCompleted'] ?? 0;
        $todayTasksTotal = $stats['todayTotal'] ?? 0;
        $progressStr = "- Total Waktu Belajar (Pomodoro): {$formattedStudyTime}\n- Streak Belajar: {$streak} hari berturut-turut\n- Target Tugas Hari Ini: {$todayTasksCompleted}/{$todayTasksTotal} selesai";

        return <<<CONTEXT
=== DATA AKADEMIK PENGGUNA SAAT INI (REAL-TIME CONTEXT) ===
Waktu Sekarang: {$currentDateStr}
Nama Pengguna: {$user->name}

[DAFTAR TUGAS AKTIF PENGGUNA (MY TASKS)]
{$tasksStr}

[JADWAL KEGIATAN & AGENDA MENDATANG (SCHEDULE)]
{$schedStr}

[JADWAL PELAJARAN / KELAS MINGGUAN (CLASS SCHEDULE)]
{$timetableStr}

[CATATAN PENGGUNA (NOTES)]
{$notesStr}

[STATISTIK & PROGRES BELAJAR (STUDY PROGRESS)]
{$progressStr}

=== PANDUAN PENGGUNAAN INFORMASI DI ATAS ===
- Kamu memiliki akses penuh ke data tugas aktif, agenda kegiatan, jadwal pelajaran kelas, catatan materi (notes), dan statistik progres belajar pengguna di atas.
- Jika pengguna menanyakan jadwal kelas (misal: "hari ini ada kelas apa?", "besok jadwal apa?", "jadwal hari Kamis apa?"), lihat hari dan jam saat ini, lalu jawab dengan tepat dan ramah berdasarkan jadwal di atas.
- Jika pengguna menanyakan tugas (misal: "tugas apa yang belum selesai?", "ada tugas matematika nggak?"), gunakan data tugas di atas beserta deadline dan sub-langkahnya.
- Jika pengguna menanyakan catatan atau rangkuman (misal: "catatan apa saja yang ada?", "ada rumus fisika di catatan nggak?"), gunakan data catatan di atas.
- Jika pengguna menanyakan progres belajar atau streak (misal: "berapa jam saya sudah belajar?", "bagaimana progres belajarku?"), gunakan data statistik belajar di atas.
- Berikan saran perencanaan belajar atau strategi pengerjaan tugas yang realistis dengan memanfaatkan waktu luang di antara jadwal kelas mereka.
CONTEXT;
    }

    /**
     * Eksekusi aksi pembuatan data yang diminta oleh AI:
     * - create_task: Membuat Task baru (beserta sub-langkah) & sinkron ke Google Tasks
     * - create_schedule: Membuat Schedule agenda belajar baru & sinkron ke Google Calendar
     * - create_timetable: Membuat jadwal pelajaran kelas mingguan baru
     * - create_note: Membuat catatan belajar baru ke My Notes
     */
    protected function processAiActions(string $reply, User $user): string
    {
        if (! preg_match('/```(?:json:action|json)?\s*(\{[^`]*"action"\s*:[^`]*\})\s*```/is', $reply, $matches)) {
            return $reply;
        }

        $jsonStr = trim($matches[1]);
        $actionData = json_decode($jsonStr, true);

        if (! is_array($actionData) || empty($actionData['action'])) {
            return $reply;
        }

        $actionType = $actionData['action'];
        $badge = '';

        try {
            if ($actionType === 'create_task') {
                $title = trim($actionData['title'] ?? 'Tugas Baru');
                $dueDate = ! empty($actionData['due_date']) ? Carbon::parse($actionData['due_date'])->format('Y-m-d') : null;
                $dueTime = ! empty($actionData['due_time']) ? Carbon::parse($actionData['due_time'])->format('H:i') : null;
                $priority = in_array($actionData['priority'] ?? '', ['low', 'medium', 'high']) ? $actionData['priority'] : 'medium';
                $category = in_array($actionData['category'] ?? '', ['school', 'project', 'study', 'personal']) ? $actionData['category'] : 'school';

                $task = Task::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'description' => $actionData['description'] ?? null,
                    'due_date' => $dueDate,
                    'due_time' => $dueTime,
                    'priority' => $priority,
                    'category' => $category,
                    'status' => 'pending',
                ]);

                if (! empty($actionData['steps']) && is_array($actionData['steps'])) {
                    foreach ($actionData['steps'] as $stepTitle) {
                        if (is_string($stepTitle) && trim($stepTitle) !== '') {
                            TaskStep::create([
                                'task_id' => $task->id,
                                'title' => trim($stepTitle),
                                'is_completed' => false,
                            ]);
                        }
                    }
                }

                // Sinkron ke Google Tasks jika terhubung
                try {
                    $googleService = app(GoogleCalendarService::class, ['user' => $user]);
                    $googleService->syncTask($task);
                } catch (\Throwable $ge) {
                    Log::info('Google Tasks sync skipped: '.$ge->getMessage());
                }

                $badge = "\n\n> 🎯 **Berhasil Dibuat**: Tugas **{$task->title}** sudah otomatis disimpan ke [My Tasks](/tasks) dan disinkronkan ke Google Tasks!";
            } elseif ($actionType === 'create_schedule') {
                $title = trim($actionData['title'] ?? 'Jadwal Belajar');
                $date = ! empty($actionData['date']) ? Carbon::parse($actionData['date'])->format('Y-m-d') : today()->format('Y-m-d');
                $startTime = ! empty($actionData['start_time']) ? Carbon::parse($actionData['start_time'])->format('H:i') : '08:00';
                $endTime = ! empty($actionData['end_time']) ? Carbon::parse($actionData['end_time'])->format('H:i') : '09:00';

                $schedule = Schedule::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'subject' => $actionData['subject'] ?? null,
                    'date' => $date,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 'pending',
                ]);

                // Sinkron ke Google Calendar jika terhubung
                try {
                    $googleService = app(GoogleCalendarService::class, ['user' => $user]);
                    $googleService->syncSchedule($schedule);
                } catch (\Throwable $ge) {
                    Log::info('Google Calendar sync skipped: '.$ge->getMessage());
                }

                $badge = "\n\n> 📅 **Berhasil Dibuat**: Agenda **{$schedule->title}** ({$date}, {$startTime} - {$endTime}) sudah otomatis tersimpan ke [Schedule](/schedule) dan Google Calendar!";
            } elseif ($actionType === 'create_timetable') {
                $subject = trim($actionData['subject'] ?? 'Pelajaran');
                $dayOfWeek = isset($actionData['day_of_week']) ? (int) $actionData['day_of_week'] : 1;
                $startTime = ! empty($actionData['start_time']) ? Carbon::parse($actionData['start_time'])->format('H:i') : '07:30';
                $endTime = ! empty($actionData['end_time']) ? Carbon::parse($actionData['end_time'])->format('H:i') : '09:00';

                $days = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                $dayName = $days[$dayOfWeek] ?? 'Hari terpilih';

                $timetable = SchoolTimetable::create([
                    'user_id' => $user->id,
                    'day_of_week' => $dayOfWeek,
                    'subject' => $subject,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);

                // Sinkron ke Google Calendar jika terhubung
                try {
                    $googleService = app(GoogleCalendarService::class, ['user' => $user]);
                    $googleService->syncSchoolTimetable($timetable);
                } catch (\Throwable $ge) {
                    Log::info('Google Calendar timetable sync skipped: '.$ge->getMessage());
                }

                $badge = "\n\n> 🎓 **Berhasil Dibuat**: Jadwal kelas **{$timetable->subject}** (Setiap {$dayName}, {$startTime} - {$endTime}) sudah otomatis tersimpan ke [Class Schedule](/timetable)!";
            } elseif ($actionType === 'create_note') {
                $title = trim($actionData['title'] ?? 'Catatan Baru');
                $content = trim($actionData['content'] ?? '');

                if ($content !== '') {
                    $note = Note::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'content' => $content,
                    ]);

                    $badge = "\n\n> 📝 **Berhasil Dibuat**: Catatan **{$note->title}** sudah otomatis tersimpan ke [Notes](/notes)!";
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error executing AI action: '.$e->getMessage(), ['action' => $actionData]);
            $badge = "\n\n> ⚠️ *Catatan: Gagal menyimpan data otomatis: {$e->getMessage()}*";
        }

        $cleanReply = preg_replace('/```(?:json:action|json)?\s*\{[^`]*"action"\s*:[^`]*\}\s*```/is', '', $reply);

        return trim($cleanReply).$badge;
    }
}

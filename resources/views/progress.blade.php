@extends('layouts.study')

@section('title', 'Progress — Study Planner')
@section('page-label', 'MY PROGRESS')

@php
    $activeNav = 'progress';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Your progress</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau pencapaian dan kebiasaan belajarmu.</p>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        @php
            $stats = [
                ['label' => "Today's Tasks", 'value' => "$todayCompleted/$todayTotal", 'icon' => 'fa-regular fa-circle-check', 'description' => "$todayCompleted completed"],
                ['label' => 'Study Hours', 'value' => $totalStudyHours . 'h', 'icon' => 'fa-regular fa-clock', 'description' => "$todayStudyMinutes min today"],
                ['label' => 'Completed', 'value' => (string) $totalCompleted, 'icon' => 'fa-solid fa-chart-line', 'description' => 'All time'],
                ['label' => 'Study Streak', 'value' => (string) $streak, 'icon' => 'fa-solid fa-fire text-amber-500', 'description' => 'days in a row'],
            ];
        @endphp
        @foreach ($stats as $stat)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 card-hover shadow-xs">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium">{{ $stat['label'] }}</p>
                        <p class="text-2xl sm:text-3xl font-bold mt-1 text-[#5B1744]">{{ $stat['value'] }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                        <i class="{{ $stat['icon'] }}"></i>
                    </div>
                </div>
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-2">{{ $stat['description'] }}</p>
            </div>
        @endforeach
    </section>

    {{-- Study Session Timer + Progress Ring --}}
    <section class="grid md:grid-cols-12 gap-5">

        <div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div id="study-timer-icon" class="w-14 h-14 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">STUDY SESSION</p>
                        <div id="study-timer-display" class="text-3xl font-bold text-[#5B1744] font-mono tabular-nums mt-1">00:00:00</div>
                        <p id="study-timer-status" class="text-xs text-gray-400 mt-0.5">Ready to study</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div id="study-session-controls">
                        <button onclick="startStudySession()" class="flex items-center gap-2 px-5 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 transition">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Start Session</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">YOUR PROGRESS</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">This week</h2>
            </div>

            @php
                $dashOffset = 402 - (402 * $completionPercentage / 100);
            @endphp

            <div class="relative w-36 h-36 mx-auto my-4">
                <svg class="w-full h-full progress-ring" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="64" fill="none" stroke="#F4E7EF" stroke-width="12"></circle>
                    <circle cx="80" cy="80" r="64" fill="none" stroke="#5B1744" stroke-width="12" stroke-linecap="round" stroke-dasharray="402" stroke-dashoffset="{{ $dashOffset }}"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-[#5B1744]">{{ $completionPercentage }}%</span>
                    <span class="text-[9px] text-gray-400 uppercase font-semibold">completed</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                    <p class="text-[10px] text-gray-400 font-medium">Tasks</p>
                    <p class="font-bold text-xs mt-0.5 text-gray-800">{{ $totalCompleted }} / {{ $totalTasks }}</p>
                </div>
                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                    <p class="text-[10px] text-gray-400 font-medium">Hours</p>
                    <p class="font-bold text-xs mt-0.5 text-gray-800">{{ $totalStudyHours }}h</p>
                </div>
            </div>
        </div>

    </section>

    {{-- Study history --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="mb-5">
            <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">HISTORY</p>
            <h2 class="text-lg font-bold text-gray-900 mt-0.5">Sesi belajar terbaru</h2>
        </div>

        <div class="space-y-2">
            @forelse ($recentSessions as $s)
                @php
                    $dur = $s->duration_seconds ?? 0;
                    $hours = floor($dur / 3600);
                    $mins = floor(($dur % 3600) / 60);
                    $durLabel = $hours > 0 ? "{$hours}j {$mins}m" : "{$mins}m";
                    $statusLabel = ucfirst($s->status);
                    $statusClass = $s->status === 'completed' ? 'bg-green-50 text-green-600' : ($s->status === 'running' ? 'bg-amber-50 text-amber-600 font-bold' : 'bg-gray-50 text-gray-500');
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition">
                    <div class="w-9 h-9 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-[12px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 truncate">{{ $s->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $s->started_at ? $s->started_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="text-xs font-bold text-gray-700 shrink-0 w-14 text-right">{{ $durLabel }}</span>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-solid fa-book-open text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada sesi belajar.</p>
                    <button onclick="startStudySession()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Mulai sesi sekarang</button>
                </div>
            @endforelse
        </div>
    </section>

@endsection

@push('scripts')

    // === STUDY SESSION TIMER ===

    let studyTimerInterval = null;
    let activeStudySession = null;

    function formatStudyTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function startStudyTimer(session) {
        activeStudySession = session;
        const baseSeconds = session.duration_seconds || 0;
        const startTs = new Date(session.started_at).getTime();

        function tick() {
            const now = Date.now();
            const elapsed = baseSeconds + Math.floor((now - startTs) / 1000);
            document.getElementById('study-timer-display').textContent = formatStudyTime(elapsed);
        }

        tick();
        studyTimerInterval = setInterval(tick, 1000);

        document.getElementById('study-session-controls').innerHTML = `
            <div class="flex items-center gap-2">
                <button onclick="pauseStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-pause text-[10px]"></i>
                    <span>Pause</span>
                </button>
                <button onclick="stopStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-stop text-[10px]"></i>
                    <span>Stop</span>
                </button>
            </div>
        `;
        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
        document.getElementById('study-timer-icon').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    }

    async function startStudySession() {
        const title = prompt('Judul sesi belajar:', 'Belajar hari ini');
        if (!title) return;

        try {
            const res = await apiFetch(`${API_BASE}/study-sessions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            } else {
                alert(data.message || 'Gagal memulai sesi.');
            }
        } catch (err) {
            alert('Gagal memulai sesi belajar.');
        }
    }

    async function pauseStudySession() {
        if (!activeStudySession) return;
        try {
            clearInterval(studyTimerInterval);
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/pause`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                activeStudySession = data.session;
                document.getElementById('study-timer-status').textContent = 'Paused';
                document.getElementById('study-timer-icon').innerHTML = '<i class="fa-solid fa-pause"></i>';
                document.getElementById('study-session-controls').innerHTML = `
                    <div class="flex items-center gap-2">
                        <button onclick="resumeStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md transition">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Resume</span>
                        </button>
                        <button onclick="stopStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition">
                            <i class="fa-solid fa-stop text-[10px]"></i>
                            <span>Stop</span>
                        </button>
                    </div>
                `;
            }
        } catch (err) {
            alert('Gagal pause sesi.');
        }
    }

    async function resumeStudySession() {
        if (!activeStudySession) return;
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/resume`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            }
        } catch (err) {
            alert('Gagal resume sesi.');
        }
    }

    async function stopStudySession() {
        if (!activeStudySession) return;
        if (!confirm('Akhiri sesi belajar ini?')) return;
        try {
            clearInterval(studyTimerInterval);
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/stop`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                const dur = data.session.formatted_duration || formatStudyTime(data.session.duration_seconds);
                alert('Sesi belajar selesai! Durasi: ' + dur);
                window.location.reload();
            }
        } catch (err) {
            alert('Gagal menghentikan sesi.');
        }
    }

    async function checkActiveStudySession() {
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/active`);
            const data = await res.json();
            if (data.session) {
                startStudyTimer(data.session);
            }
        } catch (err) {}
    }

    checkActiveStudySession();

@endpush

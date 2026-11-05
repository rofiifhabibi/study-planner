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
                ['label' => 'Study Time', 'value' => $totalStudyTimeFormatted ?? '0d', 'icon' => 'fa-regular fa-clock', 'description' => ($todayStudyTimeFormatted ?? '0d').' hari ini'],
                ['label' => 'Completed', 'value' => (string) $totalCompleted, 'icon' => 'fa-solid fa-chart-line', 'description' => 'All time'],
                ['label' => 'Study Streak', 'value' => (string) $streak, 'icon' => 'fa-solid fa-fire text-amber-500', 'description' => 'days in a row'],
            ];
        @endphp
        @foreach ($stats as $stat)
            @if ($stat['label'] === 'Study Streak')
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 card-hover shadow-xs">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-gray-400 font-medium">{{ $stat['label'] }}</p>
                            <p class="text-2xl sm:text-3xl font-bold mt-1 text-[#5B1744]">{{ $stat['value'] }}</p>
                        </div>

                        {{-- Flame streak vector contour from reference without emoji --}}
                        <div class="relative w-10 h-12 sm:w-12 sm:h-14 flex items-center justify-center shrink-0">
                            <img src="{{ asset('images/streak-flame.png') }}" alt="Streak Flame" class="w-full h-full object-contain filter drop-shadow-xs">
                            <span class="absolute inset-0 flex items-center justify-center font-black text-gray-900 text-xs sm:text-sm pt-2 select-none leading-none">
                                {{ $streak }}
                            </span>
                        </div>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-2">
                        {{ $streak > 0 ? $streak . ' hari berturut-turut' : 'Mulai belajar hari ini' }}
                    </p>
                </div>
            @elseif ($stat['label'] === 'Study Time')
                <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 card-hover shadow-xs"
                    data-tour-step="1" data-tour-title="Study Time & Statistik Otomatis" data-tour-desc="Total waktu belajar efektifmu otomatis tercatat saat sesi fokus selesai pada kartu Study Time hari ini.">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[11px] text-gray-400 font-medium">{{ $stat['label'] }}</p>
                            <p id="stat-study-time-val" class="text-2xl sm:text-3xl font-bold mt-1 text-[#5B1744]">{{ $stat['value'] }}</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-gray-400 mt-2">
                        <span id="stat-study-time-today">{{ $todayStudyTimeFormatted ?? '0d' }}</span> hari ini
                    </p>
                </div>
            @else
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
            @endif
        @endforeach
    </section>

    {{-- Pomodoro Timer + Progress Ring --}}
    <section class="grid md:grid-cols-12 gap-5">

        {{-- Integrated Pomodoro & Step-by-Step Study Session Card --}}
        <div class="md:col-span-7 bg-white rounded-3xl border-2 border-[#E7C8DB] p-6 sm:p-8 soft-shadow flex flex-col justify-between relative overflow-hidden"
            data-tour-step="2" data-tour-title="Fokus & Hitung Mundur" data-tour-desc="Pilih tugas spesifik, waktu akan otomatis disinkronkan, lalu klik tombol play segitiga untuk hitung mundur.">
            
            <div>
                {{-- Card Header --}}
                <div class="flex items-center justify-between mb-5 pb-3.5 border-b border-[#F4E7EF]">
                    <div>
                        <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">FOCUS TIMER</p>
                        <h2 class="text-base sm:text-lg font-bold text-gray-900 mt-0.5">Fokus & Langkah Belajar</h2>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-400">
                        <i class="fa-regular fa-clock text-[#5B1744]"></i>
                        <span>Hitung Mundur</span>
                    </div>
                </div>

                {{-- Pomodoro Mode Indicator (Automatic Break Transition) --}}
                <div class="flex items-center justify-center mb-3">
                    <div id="pomo-mode-badge" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition bg-[#5B1744] text-white shadow-2xs">
                        <i id="pomo-mode-icon" class="fa-solid fa-bullseye text-[11px]"></i>
                        <span id="pomo-mode-title">Sesi Fokus</span>
                        <span class="text-white/60">·</span>
                        <span id="pomo-focus-tab-min">25m</span>
                    </div>
                </div>

                {{-- Dedicated Task Picker Button & Duration Presets (Clean, No Emojis) --}}
                <div class="flex flex-wrap items-center justify-center gap-2.5 mb-2">
                    {{-- Dedicated Button for Selecting Task --}}
                    <button type="button" onclick="openPomoTaskPicker()" 
                        data-tour-step="3" data-tour-title="Pilih Tugas & Langkah Belajar" data-tour-desc="Klik di sini untuk memilih tugas belajar spesifik. Checklist langkah belajar step-by-step akan otomatis muncul khusus untuk tugas yang kamu pilih."
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-[#FAF6F0] hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-bold text-[#5B1744] transition shadow-2xs">
                        <i class="fa-solid fa-list-check text-[11px]"></i>
                        <span id="pomo-active-task-label">Pilih Tugas</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-gray-400 ml-0.5"></i>
                    </button>

                    {{-- Duration Preset Pills --}}
                    <div class="inline-flex items-center p-1 bg-[#FAF6F0] rounded-xl border border-[#E7C8DB]">
                        <button type="button" onclick="applyPomoPreset(15, 3)" id="preset-btn-15" class="pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5B1744] transition">15m</button>
                        <button type="button" onclick="applyPomoPreset(25, 5)" id="preset-btn-25" class="pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-[#5B1744] text-white transition shadow-2xs">25m</button>
                        <button type="button" onclick="applyPomoPreset(50, 10)" id="preset-btn-50" class="pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5B1744] transition">50m</button>
                        <button type="button" onclick="toggleCustomPomoDuration()" id="preset-btn-custom" class="pomo-preset-btn px-2 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5B1744] transition" title="Kustom Durasi">
                            <i class="fa-solid fa-sliders text-[11px]"></i>
                        </button>
                    </div>

                    {{-- Test Alarm Button --}}
                    <button type="button" onclick="testAlarmChime()" title="Tes Nada Dering Alarm"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#FAF6F0] hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-bold text-[#5B1744] transition shadow-2xs">
                        <i class="fa-solid fa-bell text-[11px]"></i>
                        <span class="hidden sm:inline text-[11px]">Tes Nada Dering</span>
                    </button>
                </div>

                {{-- Custom duration inputs (hidden unless custom selected) --}}
                <div id="pomo-custom-duration-box" class="hidden max-w-xs mx-auto mb-3 grid grid-cols-2 gap-2 p-2.5 bg-[#FAF6F0] rounded-2xl border border-[#E7C8DB] text-center">
                    <div>
                        <label class="text-[10px] text-gray-600 font-bold block">Fokus (menit)</label>
                        <input type="number" id="pomo-custom-focus" min="1" max="180" value="25" onchange="onPomoCustomApply()" class="w-full mt-1 text-center px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-[#5B1744] focus:border-[#5B1744] outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] text-gray-600 font-bold block">Istirahat (menit)</label>
                        <input type="number" id="pomo-custom-break" min="1" max="60" value="5" onchange="onPomoCustomApply()" class="w-full mt-1 text-center px-2 py-1 bg-white border border-gray-200 rounded-lg text-xs font-bold text-[#5B1744] focus:border-[#5B1744] outline-none">
                    </div>
                </div>

                {{-- Big Countdown Display --}}
                <div class="text-center my-6 select-none">
                    <div id="pomo-countdown-display" class="text-6xl sm:text-7xl font-black text-[#5B1744] tracking-wider font-mono">
                        25 : 00
                    </div>
                </div>

                {{-- Three Controls --}}
                <div class="flex items-center justify-center gap-4 mb-4">
                    {{-- Reset --}}
                    <button type="button" onclick="resetPomodoro()" title="Reset Waktu" 
                        class="w-12 h-12 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] text-[#5B1744] hover:bg-[#E7C8DB]/60 flex items-center justify-center text-base transition active:scale-95 shadow-2xs">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>

                    {{-- Play / Pause 3D button (Color palette: #5B1744 with #330D26 3D shadow) --}}
                    <button id="pomo-play-btn" type="button" onclick="togglePomodoroPlay()" title="Mulai / Jeda" 
                        class="w-14 h-14 rounded-2xl bg-[#5B1744] border-2 border-[#330D26] text-white flex items-center justify-center text-xl shadow-[0_4px_0_0_#330D26] hover:shadow-[0_2px_0_0_#330D26] hover:translate-y-0.5 active:translate-y-1 active:shadow-none transition">
                        <i id="pomo-play-icon" class="fa-solid fa-play"></i>
                    </button>

                    {{-- Skip / Switch Mode --}}
                    <button type="button" onclick="skipPomodoroMode()" title="Ganti Mode Fokus/Istirahat" 
                        class="w-12 h-12 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] text-[#5B1744] hover:bg-[#E7C8DB]/60 flex items-center justify-center text-base transition active:scale-95 shadow-2xs">
                        <i class="fa-solid fa-forward-step"></i>
                    </button>
                </div>

                {{-- Finish Session Button --}}
                <div class="max-w-md mx-auto">
                    <button type="button" onclick="finishPomodoroSession()" 
                        class="w-full py-3.5 px-6 rounded-2xl bg-[#5B1744] hover:bg-[#481236] text-white font-bold text-xs sm:text-sm flex items-center justify-center gap-2 transition shadow-md shadow-[#5B1744]/20">
                        <i class="fa-regular fa-circle-check text-base"></i>
                        <span>Selesaikan Sesi</span>
                    </button>
                </div>
            </div>

            {{-- Interactive Step-by-Step Task Checklist --}}
            <div id="active-checklist-container" class="mt-6 pt-5 border-t border-[#F4E7EF]"
                data-tour-step="3" data-tour-title="Langkah Belajar Step-by-Step" data-tour-desc="Pecah tugas belajarmu menjadi langkah-langkah kecil. Setiap kali satu langkah dicentang, progres belajarmu otomatis tersimpan dan terhitung ke statistik harian!">
                <div class="flex items-start sm:items-center justify-between gap-3 mb-3">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-[10px] uppercase tracking-wider text-[#5B1744] font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-list-check"></i>
                                <span>LANGKAH BELAJAR STEP-BY-STEP</span>
                            </p>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-[#F4E7EF] text-[#5B1744] font-bold border border-[#E7C8DB]">Fokus Terarah</span>
                        </div>
                        <p id="active-task-subtitle" class="text-xs text-gray-500 mt-1 font-medium">
                            Pecah tugas besar menjadi bagian-bagian kecil agar belajar terasa ringan dan terarah.
                        </p>
                    </div>
                </div>

                <div id="active-steps-list" class="space-y-2">
                    <!-- Rendered by renderActiveChecklist() -->
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
                    <p class="font-bold text-xs mt-0.5 text-gray-800">{{ $totalStudyTimeFormatted ?? ($totalStudyHours . 'h') }}</p>
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
                    $durLabel = \App\Services\ProgressService::formatDuration($dur);
                    $statusLabel = ucfirst($s->status);
                    $statusClass = $s->status === 'completed' ? 'bg-green-50 text-green-600 font-semibold' : ($s->status === 'running' ? 'bg-amber-50 text-amber-600 font-bold' : 'bg-gray-50 text-gray-500');
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition cursor-pointer" onclick="loadPastSession({{ json_encode($s) }})">
                    <div class="w-9 h-9 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-[12px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 truncate">{{ $s->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $s->started_at ? $s->started_at->format('d M Y, H:i') : ($s->ended_at ? $s->ended_at->format('d M Y, H:i') : '-') }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="text-xs font-bold text-gray-700 shrink-0 text-right">{{ $durLabel }}</span>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-solid fa-book-open text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada sesi belajar.</p>
                    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'}); openPomoTaskPicker();" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Mulai fokus sekarang</button>
                </div>
            @endforelse
        </div>
    </section>

@endsection

@push('modals')
    {{-- Dedicated Task Picker Modal for Pomodoro --}}
    <div id="pomoTaskPickerModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closePomoTaskPicker()"></div>
        <div class="relative w-full max-w-md bg-white rounded-[2rem] p-6 sm:p-7 shadow-2xl border-2 border-[#E7C8DB] transform scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-y-auto" id="pomoTaskPickerModalBox">
            
            <div class="flex items-center justify-between pb-3.5 border-b border-[#F4E7EF] mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Pilih Tugas Belajar</h3>
                        <p class="text-xs text-gray-500">Pilih tugas yang ingin kamu fokuskan</p>
                    </div>
                </div>
                <button type="button" onclick="closePomoTaskPicker()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-[#F4E7EF] text-gray-500 hover:text-[#5B1744] flex items-center justify-center transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                {{-- Clear / Free Focus Option --}}
                <div onclick="selectPomoTask(null, 'Fokus Umum', 25, 5)" class="p-3 rounded-2xl border border-dashed border-gray-200 hover:border-[#5B1744] hover:bg-[#FAF6F0] cursor-pointer transition flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Fokus Umum (Tanpa Tugas)</p>
                            <p class="text-[11px] text-gray-400">Belajar bebas tanpa mengaitkan tugas tertentu</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-arrow-right text-xs text-gray-400"></i>
                </div>

                {{-- Task Items --}}
                @forelse($activeTasks ?? [] as $t)
                    <div onclick="selectPomoTask({{ $t->id }}, '{{ addslashes($t->title) }}', {{ $t->pomodoro_duration ?? 25 }}, {{ $t->break_duration ?? 5 }})" 
                        class="p-3.5 rounded-2xl border border-[#E7C8DB]/60 hover:border-[#5B1744] hover:bg-[#FAF6F0] cursor-pointer transition flex items-center justify-between group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs shrink-0 group-hover:bg-[#5B1744] group-hover:text-white transition">
                                <i class="fa-solid fa-book-open"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 truncate group-hover:text-[#5B1744]">{{ $t->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[10px] text-gray-500 font-medium">{{ $t->pomodoro_duration ?? 25 }} menit fokus</span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-[10px] uppercase font-bold text-[#5B1744]">{{ $t->category ?? 'Tugas' }}</span>
                                </div>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-[#5B1744] transition"></i>
                    </div>
                @empty
                    <div class="text-center py-6 text-gray-400 text-xs">
                        Belum ada tugas aktif. Buat tugas di menu Tugas terlebih dahulu.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endpush

@push('scripts')

    // === POMODORO COUNTDOWN TIMER ===
    const activeTaskIds = new Set([{{ $activeTasks->pluck('id')->implode(',') }}]);
    let pomoMode = 'focus'; // 'focus' | 'break'
    let pomoFocusMinutes = 25;
    let pomoBreakMinutes = 5;
    let pomoTimeLeft = 25 * 60; // seconds, decreases every tick ('ngurang')
    let pomoTimerInterval = null;
    let pomoIsRunning = false;
    let pomoSelectedTaskId = null;
    let pomoTotalElapsedFocusSeconds = 0;
    let currentTodayStudySec = {{ (int)($todayStudySeconds ?? 0) }};
    let currentTotalStudySec = {{ (int)($totalStudySeconds ?? 0) }};

    function formatStudyDuration(sec) {
        if (!sec || sec <= 0) return '0d';
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        const parts = [];
        if (h > 0) parts.push(`${h}j`);
        if (m > 0) parts.push(`${m}m`);
        if (s > 0 || parts.length === 0) parts.push(`${s}d`);
        return parts.join(' ');
    }

    function formatPomoTime(totalSec) {
        const m = Math.floor(totalSec / 60);
        const s = totalSec % 60;
        return `${String(m).padStart(2, '0')} : ${String(s).padStart(2, '0')}`;
    }

    function renderPomoDisplay() {
        const el = document.getElementById('pomo-countdown-display');
        if (el) el.textContent = formatPomoTime(pomoTimeLeft);
    }

    function savePomoState() {
        const labelEl = document.getElementById('pomo-active-task-label');
        const state = {
            isRunning: pomoIsRunning,
            mode: pomoMode,
            focusMinutes: pomoFocusMinutes,
            breakMinutes: pomoBreakMinutes,
            secondsLeft: pomoTimeLeft,
            targetEndTime: pomoIsRunning ? (Date.now() + (pomoTimeLeft * 1000)) : null,
            selectedTaskId: pomoSelectedTaskId,
            selectedTaskTitle: labelEl ? labelEl.textContent : 'Pilih Tugas',
            totalElapsedFocusSeconds: pomoTotalElapsedFocusSeconds,
            lastSavedAt: Date.now()
        };
        localStorage.setItem('study_planner_pomo_state', JSON.stringify(state));
    }

    function clearPomoState() {
        localStorage.removeItem('study_planner_pomo_state');
    }

    function loadPomoState() {
        const raw = localStorage.getItem('study_planner_pomo_state');
        if (!raw) return false;
        try {
            const state = JSON.parse(raw);
            if (!state) return false;

            pomoFocusMinutes = state.focusMinutes || 25;
            pomoBreakMinutes = state.breakMinutes || 5;
            pomoMode = state.mode || 'focus';
            pomoTotalElapsedFocusSeconds = state.totalElapsedFocusSeconds || 0;

            // Highlight preset button
            document.querySelectorAll('.pomo-preset-btn').forEach(btn => {
                btn.className = 'pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5B1744] transition';
            });
            const activeBtn = document.getElementById(`preset-btn-${pomoFocusMinutes}`);
            if (activeBtn) {
                activeBtn.className = 'pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-[#5B1744] text-white transition shadow-2xs';
            }

            // Restore selected task and steps (only if task is still active!)
            if (state.selectedTaskId) {
                const parsedTaskId = Number(state.selectedTaskId);
                if (!activeTaskIds.has(parsedTaskId)) {
                    // Task is completed or deleted! Reset selection
                    pomoSelectedTaskId = null;
                    currentTaskId = null;
                    state.selectedTaskId = null;
                    state.selectedTaskTitle = 'Pilih Tugas';
                    const label = document.getElementById('pomo-active-task-label');
                    const subtitle = document.getElementById('active-task-subtitle');
                    if (label) label.textContent = 'Pilih Tugas';
                    if (subtitle) subtitle.textContent = 'Belajar umum tanpa tugas spesifik';
                    currentSteps = [];
                    savePomoState();
                    renderActiveChecklist();
                } else {
                    pomoSelectedTaskId = parsedTaskId;
                    currentTaskId = parsedTaskId;
                    const label = document.getElementById('pomo-active-task-label');
                    const subtitle = document.getElementById('active-task-subtitle');
                    if (label) label.textContent = state.selectedTaskTitle || 'Tugas Terpilih';
                    if (subtitle) subtitle.textContent = 'Mengerjakan tugas: ' + (state.selectedTaskTitle || '');

                    apiFetch(`${API_BASE}/tasks/${parsedTaskId}/steps`)
                        .then(res => res.json())
                        .then(data => {
                            currentSteps = data.steps || [];
                            renderActiveChecklist();
                        })
                        .catch(() => {
                            currentSteps = [];
                            renderActiveChecklist();
                        });
                }
            } else {
                renderActiveChecklist();
            }

            setPomoMode(pomoMode, false);

            if (state.isRunning && state.targetEndTime) {
                const remaining = Math.round((state.targetEndTime - Date.now()) / 1000);
                if (remaining > 0) {
                    pomoTimeLeft = remaining;
                    renderPomoDisplay();
                    startPomodoro(false);
                    return true;
                } else {
                    pomoTimeLeft = 0;
                    renderPomoDisplay();
                    handlePomodoroEnded();
                    return true;
                }
            } else {
                pomoTimeLeft = typeof state.secondsLeft === 'number' ? state.secondsLeft : (pomoMode === 'focus' ? pomoFocusMinutes : pomoBreakMinutes) * 60;
                renderPomoDisplay();
                pausePomodoro(false);
                return true;
            }
        } catch(e) {
            console.error('Failed to load pomo state', e);
            return false;
        }
    }

    function setPomoMode(mode, resetTime = true) {
        pomoMode = mode;
        const badge = document.getElementById('pomo-mode-badge');
        const icon = document.getElementById('pomo-mode-icon');
        const title = document.getElementById('pomo-mode-title');
        const durLabel = document.getElementById('pomo-focus-tab-min');

        if (mode === 'focus') {
            if (resetTime) pomoTimeLeft = pomoFocusMinutes * 60;
            if (badge) badge.className = "inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition bg-[#5B1744] text-white shadow-2xs";
            if (icon) icon.className = "fa-solid fa-bullseye text-[11px]";
            if (title) title.textContent = "Sesi Fokus";
            if (durLabel) durLabel.textContent = pomoFocusMinutes + "m";
        } else {
            if (resetTime) pomoTimeLeft = pomoBreakMinutes * 60;
            if (badge) badge.className = "inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold transition bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB]";
            if (icon) icon.className = "fa-solid fa-mug-hot text-[11px]";
            if (title) title.textContent = "Sesi Istirahat";
            if (durLabel) durLabel.textContent = pomoBreakMinutes + "m";
        }
        renderPomoDisplay();
        savePomoState();
    }

    function handlePomodoroEnded() {
        pausePomodoro(false);

        const soundMode = localStorage.getItem('study_planner_sound_mode') || 'sound';
        if (soundMode === 'sound') {
            playAlarmChime();
        }

        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                new Notification('Waktu Belajar Selesai!', {
                    body: pomoMode === 'focus' 
                        ? 'Sesi fokus telah selesai! Waktunya istirahat sejenak.' 
                        : 'Sesi istirahat telah selesai! Siap untuk kembali fokus.',
                    icon: '{{ asset('logo-chatgpt.png') }}'
                });
            } catch (e) {}
        } else if ('Notification' in window && Notification.permission !== 'denied') {
            Notification.requestPermission();
        }

        if (pomoMode === 'focus') {
            const actualSec = pomoTotalElapsedFocusSeconds > 0 ? pomoTotalElapsedFocusSeconds : (pomoFocusMinutes * 60);
            savePomoSessionToServer(actualSec, false);
            showToast("Alarm: Waktu fokus selesai! Sesi belajar berhasil dicatat dan beralih ke istirahat.", "success");
            setPomoMode('break', true);
            startPomodoro(true); // Otomatis mulai istirahat
        } else {
            showToast("Alarm: Waktu istirahat selesai! Siap untuk kembali fokus.", "success");
            setPomoMode('focus', true);
        }
    }

    let pomoTickCounter = 0;
    function tickPomodoro() {
        if (pomoTimeLeft > 0) {
            pomoTimeLeft--;
            if (pomoMode === 'focus') {
                pomoTotalElapsedFocusSeconds++;
            }
            renderPomoDisplay();
            document.title = `(${formatPomoTime(pomoTimeLeft)}) ${pomoMode === 'focus' ? 'Fokus' : 'Istirahat'} - Study Planner`;
            pomoTickCounter++;
            if (pomoTickCounter % 3 === 0) {
                savePomoState();
            }
        } else {
            handlePomodoroEnded();
        }
    }

    function togglePomodoroPlay() {
        if (pomoIsRunning) {
            pausePomodoro(true);
        } else {
            startPomodoro(true);
        }
    }

    function startPomodoro(save = true) {
        pomoIsRunning = true;
        const icon = document.getElementById('pomo-play-icon');
        if (icon) icon.className = 'fa-solid fa-pause';
        if (pomoTimerInterval) clearInterval(pomoTimerInterval);
        pomoTimerInterval = setInterval(tickPomodoro, 1000);
        if (save) savePomoState();
    }

    function pausePomodoro(save = true) {
        pomoIsRunning = false;
        if (pomoTimerInterval) {
            clearInterval(pomoTimerInterval);
            pomoTimerInterval = null;
        }
        const icon = document.getElementById('pomo-play-icon');
        if (icon) icon.className = 'fa-solid fa-play';
        if (save) savePomoState();
    }

    function resetPomodoro() {
        pausePomodoro(false);
        pomoTimeLeft = (pomoMode === 'focus' ? pomoFocusMinutes : pomoBreakMinutes) * 60;
        renderPomoDisplay();
        savePomoState();
    }

    function skipPomodoroMode() {
        setPomoMode(pomoMode === 'focus' ? 'break' : 'focus', true);
    }

    async function finishPomodoroSession() {
        pausePomodoro(false);
        // Hitung durasi fokus nyata yang berjalan (dalam detik)
        let actualSec = pomoTotalElapsedFocusSeconds;
        if (!actualSec || actualSec <= 0) {
            actualSec = Math.max(0, (pomoFocusMinutes * 60) - pomoTimeLeft);
        }

        if (actualSec <= 0) {
            showToast('Mulai timer terlebih dahulu sebelum menyelesaikan sesi.', 'info');
            return;
        }

        clearPomoState();
        await savePomoSessionToServer(actualSec, true);
    }

    async function savePomoSessionToServer(actualSec, reloadAfter = true) {
        if (!actualSec || actualSec <= 0) return;

        let sessionTitle = 'Sesi Fokus';
        if (pomoSelectedTaskId) {
            const label = document.getElementById('pomo-active-task-label');
            if (label && label.textContent !== 'Pilih Tugas') {
                sessionTitle = label.textContent;
            }
        }

        try {
            const res = await apiFetch(`${API_BASE}/study-sessions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    title: sessionTitle,
                    task_id: pomoSelectedTaskId || null,
                    duration_seconds: actualSec,
                    status: 'completed'
                })
            });
            const data = await res.json();
            if (data.status === 'success') {
                showToast(`Sesi belajar (${formatStudyDuration(actualSec)}) berhasil disimpan!`, 'success');
                currentTodayStudySec += actualSec;
                currentTotalStudySec += actualSec;
                const valEl = document.getElementById('stat-study-time-val');
                const todayEl = document.getElementById('stat-study-time-today');
                if (valEl) valEl.textContent = formatStudyDuration(currentTotalStudySec);
                if (todayEl) todayEl.textContent = formatStudyDuration(currentTodayStudySec);
            } else if (data.message) {
                showToast(data.message, 'info');
            }
            if (reloadAfter) {
                setTimeout(() => window.location.reload(), 1000);
            }
        } catch (e) {
            console.error('Error saving study session:', e);
            if (reloadAfter) {
                setTimeout(() => window.location.reload(), 1000);
            }
        }
    }

    function openPomoTaskPicker() {
        const modal = document.getElementById('pomoTaskPickerModal');
        const box = document.getElementById('pomoTaskPickerModalBox');
        if (!modal || !box) return;
        document.body.classList.add('overflow-hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        void modal.offsetWidth;
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
    }

    function closePomoTaskPicker() {
        const modal = document.getElementById('pomoTaskPickerModal');
        const box = document.getElementById('pomoTaskPickerModalBox');
        if (!modal || !box) return;
        document.body.classList.remove('overflow-hidden');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 250);
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('pomoTaskPickerModal');
            if (modal && !modal.classList.contains('hidden')) {
                closePomoTaskPicker();
            }
        }
    });

    let currentTaskId = null;
    let currentSteps = [];

    function selectPomoTask(taskId, taskTitle, focusMin = 25, breakMin = 5) {
        closePomoTaskPicker();

        if (taskId) {
            if (!activeTaskIds.has(Number(taskId))) {
                showToast('Tugas ini sudah selesai atau tidak aktif.', 'info');
                return;
            }
            pomoSelectedTaskId = taskId;
            currentTaskId = taskId;
        } else {
            pomoSelectedTaskId = null;
            currentTaskId = null;
        }

        const container = document.getElementById('active-checklist-container');
        const label = document.getElementById('pomo-active-task-label');
        const subtitle = document.getElementById('active-task-subtitle');

        if (taskId) {
            if (label) label.textContent = taskTitle.length > 20 ? taskTitle.substring(0, 18) + '...' : taskTitle;
            if (subtitle) subtitle.textContent = 'Mengerjakan tugas: ' + taskTitle;
            
            // Otomatis sinkronkan waktu countdown sesuai waktu di halaman task
            const focus = parseInt(focusMin) || 25;
            const brk = parseInt(breakMin) || 5;
            applyPomoPreset(focus, brk);

            if (container) container.classList.remove('hidden');

            apiFetch(`${API_BASE}/tasks/${taskId}/steps`)
                .then(res => res.json())
                .then(data => {
                    currentSteps = data.steps || [];
                    renderActiveChecklist();
                })
                .catch(() => {
                    currentSteps = [];
                    renderActiveChecklist();
                });
        } else {
            if (label) label.textContent = 'Fokus Umum';
            if (subtitle) subtitle.textContent = 'Mode fokus umum tanpa terhubung ke tugas tertentu.';
            applyPomoPreset(25, 5);
            currentSteps = [];
            renderActiveChecklist();
        }
        savePomoState();
    }

    function applyPomoPreset(focus, breakMin) {
        applyPomoDuration(focus, breakMin);
        document.querySelectorAll('.pomo-preset-btn').forEach(btn => {
            btn.className = 'pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 hover:text-[#5B1744] transition';
        });
        const activeBtn = document.getElementById(`preset-btn-${focus}`);
        if (activeBtn) {
            activeBtn.className = 'pomo-preset-btn px-2.5 py-1 rounded-lg text-xs font-bold bg-[#5B1744] text-white transition shadow-2xs';
        }
    }

    function toggleCustomPomoDuration() {
        const box = document.getElementById('pomo-custom-duration-box');
        if (box) box.classList.toggle('hidden');
    }

    function onPomoCustomApply() {
        const f = parseInt(document.getElementById('pomo-custom-focus').value) || 25;
        const b = parseInt(document.getElementById('pomo-custom-break').value) || 5;
        applyPomoDuration(f, b);
    }

    function applyPomoDuration(focus, breakMin) {
        pomoFocusMinutes = focus;
        pomoBreakMinutes = breakMin;
        const durLabel = document.getElementById('pomo-focus-tab-min');
        if (durLabel) durLabel.textContent = (pomoMode === 'focus' ? focus : breakMin) + 'm';
        pomoTimeLeft = (pomoMode === 'focus' ? pomoFocusMinutes : pomoBreakMinutes) * 60;
        renderPomoDisplay();
        savePomoState();
    }

    function playAlarmChime() {
        if (typeof window.playTimerAlarmChime === 'function') {
            window.playTimerAlarmChime();
            return;
        }
        if (typeof playTimerAlarmChime === 'function') {
            playTimerAlarmChime();
        }
    }

    function testAlarmChime() {
        playAlarmChime();
        showToast('Memutar nada dering alarm selesai...', 'info');
    }

    function formatStudyTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function loadPastSession(session) {
        if (session.task_id) {
            selectPomoTask(session.task_id, session.title, 25, 5);
            window.scrollTo({ top: 0, behavior: 'smooth' });
            showToast(`Tugas "${session.title}" dimuat ke timer fokus.`, 'success');
        } else {
            showToast(`Sesi "${session.title}" berdurasi ${formatStudyTime(session.duration_seconds || 0)}.`, 'info');
        }
    }

    async function deleteStudySession(id) {
        if (!confirm('Yakin ingin menghapus sesi belajar ini dari riwayat?')) return;
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('Sesi riwayat berhasil dihapus.', 'success');
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (e) {
            alert('Gagal menghapus sesi.');
        }
    }

    async function toggleStep(stepId, isCompleted) {
        if (!currentTaskId) return;
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${currentTaskId}/steps/${stepId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_completed: isCompleted })
            });
            const data = await res.json();
            if (data.status === 'success') {
                const step = currentSteps.find(s => s.id === stepId);
                if (step) step.is_completed = isCompleted;
                renderActiveChecklist();

                if (data.task_status === 'completed' && isCompleted) {
                    showToast('Semua langkah selesai! Tugas berhasil diselesaikan.', 'success');
                }
            }
        } catch (e) {
            alert('Gagal memperbarui langkah.');
        }
    }

    async function deleteStep(stepId, event) {
        if (event) event.stopPropagation();
        if (!currentTaskId) return;
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${currentTaskId}/steps/${stepId}`, {
                method: 'DELETE'
            });
            const data = await res.json();
            if (data.status === 'success') {
                currentSteps = currentSteps.filter(s => s.id !== stepId);
                renderActiveChecklist();
                showToast('Langkah berhasil dihapus.', 'info');
            }
        } catch (e) {
            alert('Gagal menghapus langkah.');
        }
    }

    function renderActiveChecklist() {
        const container = document.getElementById('active-checklist-container');
        const list = document.getElementById('active-steps-list');
        if (!container || !list) return;

        container.classList.remove('hidden');

        // State 1: Belum ada tugas yang dipilih -> Tampilkan Panduan Lengkap
        if (!currentTaskId) {
            list.innerHTML = `
                <div class="rounded-2xl border border-[#E7C8DB]/70 bg-gradient-to-br from-[#FAF6F0] via-white to-[#F4E7EF]/30 p-4 sm:p-5 shadow-2xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#5B1744] text-white flex items-center justify-center shrink-0 shadow-xs">
                                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                            </div>
                            <div>
                                <h4 class="text-xs sm:text-sm font-bold text-gray-900">Apa itu Langkah Belajar Step-by-Step?</h4>
                                <p class="text-xs text-gray-600 mt-1 leading-relaxed">
                                    Membantumu memecah tugas belajar (seperti PR, rangkuman, atau persiapan ujian) menjadi target-target mikro yang ringan. Tidak perlu merasa kewalahan—cukup selesaikan satu per satu sambil timer berjalan!
                                </p>
                            </div>
                        </div>
                        <button type="button" onclick="openPomoTaskPicker()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition shadow-xs shrink-0 cursor-pointer">
                            <i class="fa-solid fa-list-check text-[11px]"></i>
                            <span>Pilih Tugas Sekarang</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mt-4 pt-3.5 border-t border-[#E7C8DB]/50">
                        <div class="flex items-center gap-2.5 text-[11px] text-gray-700 bg-white/80 p-2.5 rounded-xl border border-[#F4E7EF]">
                            <span class="w-5 h-5 rounded-full bg-[#5B1744] text-white font-bold flex items-center justify-center text-[10px] shrink-0">1</span>
                            <span>Pilih tugas yang ingin kamu kerjakan</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] text-gray-700 bg-white/80 p-2.5 rounded-xl border border-[#F4E7EF]">
                            <span class="w-5 h-5 rounded-full bg-[#5B1744] text-white font-bold flex items-center justify-center text-[10px] shrink-0">2</span>
                            <span>Buat target kecil (misal: "Baca hal 1-5")</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] text-gray-700 bg-white/80 p-2.5 rounded-xl border border-[#F4E7EF]">
                            <span class="w-5 h-5 rounded-full bg-[#5B1744] text-white font-bold flex items-center justify-center text-[10px] shrink-0">3</span>
                            <span>Centang tiap langkah saat timer jalan!</span>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        list.innerHTML = '';

        let completed = 0;
        const total = currentSteps ? currentSteps.length : 0;

        if (total > 0) {
            let stepsHtml = '';
            currentSteps.forEach(step => {
                if (step.is_completed) completed++;
                const checkedStr = step.is_completed ? 'checked' : '';
                const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-800';

                stepsHtml += `
                    <div class="flex items-center justify-between gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:border-[#E7C8DB] transition shadow-2xs group">
                        <label class="flex items-center gap-3 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox" ${checkedStr} onchange="toggleStep(${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] accent-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744] cursor-pointer shrink-0">
                            <span class="text-xs font-semibold ${lineThrough} truncate select-none">${step.title}</span>
                        </label>
                        <button type="button" onclick="deleteStep(${step.id}, event)" title="Hapus langkah" class="w-6 h-6 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 flex items-center justify-center transition opacity-0 group-hover:opacity-100 shrink-0 cursor-pointer">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </div>
                `;
            });

            const percentage = Math.round((completed / total) * 100);

            list.innerHTML = `
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold text-[#5B1744]">Progress Langkah: ${percentage}%</span>
                        <span class="text-xs text-gray-400 font-medium">${completed}/${total} selesai</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-[#5B1744] h-2 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                    </div>
                </div>
                <div class="space-y-2 mb-3">
                    ${stepsHtml}
                </div>
            `;
        } else {
            list.innerHTML = `
                <div class="rounded-2xl border border-dashed border-[#E7C8DB] bg-[#FAF6F0]/60 p-4 text-center mb-3">
                    <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] mx-auto flex items-center justify-center text-xs mb-2">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Tugas ini belum memiliki langkah belajar</p>
                    <p class="text-[11px] text-gray-500 mt-0.5">Tulis langkah pertama di bawah (misal: <em>Buka catatan hal 12</em> atau <em>Kerjakan soal 1-5</em>) agar lebih bersemangat!</p>
                </div>
            `;
        }

        // Inline input to add steps directly
        list.innerHTML += `
            <div class="flex items-center gap-2 mt-3">
                <input type="text" id="new-active-step-input" placeholder="Tulis langkah belajar baru lalu tekan Enter..." 
                    class="flex-1 text-xs px-3.5 py-2.5 bg-[#FAF6F0]/60 border border-[#E7C8DB] rounded-xl outline-none focus:border-[#5B1744] focus:bg-white text-gray-800 transition placeholder:text-gray-400" 
                    onkeypress="if(event.key === 'Enter') addActiveStep()">
                <button type="button" onclick="addActiveStep()" 
                    class="h-9 px-4 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition shadow-xs flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Tambah</span>
                </button>
            </div>
            <p class="text-[11px] text-gray-400 mt-2.5 flex items-center gap-1.5 justify-center sm:justify-start">
                <i class="fa-regular fa-lightbulb text-amber-500"></i>
                <span>Tips: Fokus selesaikan satu per satu langkah dalam setiap interval timer belajar.</span>
            </p>
        `;
    }

    async function addActiveStep() {
        if (!currentTaskId) return;
        const input = document.getElementById('new-active-step-input');
        if (!input) return;
        const title = input.value.trim();
        if (!title) return;

        input.disabled = true;
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${currentTaskId}/steps`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title })
            });
            const data = await res.json();
            if (data.status === 'success') {
                currentSteps.push(data.step);
                renderActiveChecklist();
                const newInput = document.getElementById('new-active-step-input');
                if (newInput) newInput.focus();
            }
        } catch (e) {
            alert('Gagal menambah langkah.');
        } finally {
            if (input) input.disabled = false;
        }
    }

    // Auto-detect task_id / start_task_id in query string, or restore running Pomodoro
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const taskId = urlParams.get('task_id') || urlParams.get('start_task_id');
        if (taskId) {
            // Find task option in list if available
            const taskEl = document.querySelector(`[onclick*="selectPomoTask(${taskId},"]`);
            if (taskEl) {
                taskEl.click();
            } else {
                apiFetch(`${API_BASE}/tasks/${taskId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.task) {
                            selectPomoTask(data.task.id, data.task.title, data.task.pomodoro_duration || 25, data.task.break_duration || 5);
                        }
                    })
                    .catch(() => {});
            }
            window.history.replaceState({}, document.title, window.location.pathname);
        } else {
            // Restore ongoing session from previous page navigation
            loadPomoState();
            if (!currentTaskId) {
                renderActiveChecklist();
            }
        }
    });

@endpush


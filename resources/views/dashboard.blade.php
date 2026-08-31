@extends('layouts.study')

@section('title', 'Dashboard — Study Planner')
@section('page-label', 'MY STUDY SPACE')

@php
    $activeNav = 'overview';
@endphp

@section('content')

    {{-- Greeting + AI Summary --}}
    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight mt-1 text-gray-900">
                Selamat datang, {{ auth()->user()->name }} 👋
            </h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan ruang belajarmu hari ini.</p>
        </div>
        <a href="{{ route('chat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0">
            <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
            <span>Ask AI</span>
        </a>
    </section>

    {{-- AI Daily Summary Band --}}
    <section class="relative overflow-hidden rounded-3xl bg-[#5B1744] text-white p-6 sm:p-7 soft-shadow fade-up">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="absolute -right-2 -bottom-16 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-300">
                    <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                </div>
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-white/50 font-bold">AI DAILY SUMMARY</p>
                </div>
            </div>

            <p class="text-base sm:text-lg font-medium leading-relaxed text-white/95">
                @if ($todayTotal > 0)
                    Kamu sudah menyelesaikan <span class="text-[#E9C9DA] font-bold">{{ $todayCompleted }} dari {{ $todayTotal }} tugas</span> hari ini.
                    @if ($todayTotal - $todayCompleted > 0)
                        Tinggal {{ $todayTotal - $todayCompleted }} lagi — jangan sampai progress bagus ini berhenti di tengah.
                    @else
                        Luar biasa! Semua tugas hari ini sudah selesai! 🎉
                    @endif
                @else
                    Belum ada tugas untuk hari ini. Mulai tambahkan tugas baru untuk hari yang produktif!
                @endif
            </p>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        @php
            $stats = [
                ['label' => "Today's Tasks", 'value' => (string) $todayTotal, 'icon' => 'fa-regular fa-circle-check', 'description' => "$todayCompleted completed"],
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

    {{-- Today's Tasks Summary --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow fade-up">
        <div class="flex justify-between items-center mb-5">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY'S TASKS</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">My Tasks</h2>
            </div>
            <a href="{{ route('tasks') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5B1744] hover:underline">
                View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div id="task-list" class="space-y-2">
            @forelse ($todayTasks as $task)
                <div class="task-item flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition" data-task-id="{{ $task->id }}">
                    <button onclick="window.location.href='{{ route('tasks') }}'" class="w-5 h-5 rounded-full border-2 {{ $task->status === 'completed' ? 'bg-[#5B1744] border-[#5B1744]' : 'border-gray-300' }} flex items-center justify-center shrink-0 transition"></button>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst($task->category) }} · {{ $task->due_date->format('d M') }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-500') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-regular fa-circle-check text-2xl mb-2"></i>
                    <p class="text-xs">Belum ada tugas hari ini.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Today's Schedule + Progress --}}
    <section class="grid md:grid-cols-12 gap-5">

        {{-- Schedule Summary --}}
        <div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow fade-up">
            <div class="flex justify-between items-center mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY</p>
                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">Your schedule</h2>
                </div>
                <a href="{{ route('schedule') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5B1744] hover:underline">
                    View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-2">
                @forelse ($todaySchedules as $item)
                    @php
                        $statusColors = [
                            'completed' => ['bg-green-50 text-green-600', 'bg-[#5B1744]'],
                            'active' => ['bg-amber-50 text-amber-600 font-bold', 'bg-amber-500'],
                            'pending' => ['bg-gray-50 text-gray-500', 'bg-gray-200'],
                        ];
                        [$statusClass, $lineColor] = $statusColors[$item->status] ?? $statusColors['pending'];
                    @endphp
                    <a href="{{ route('schedule') }}" class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition">
                        <div class="w-12 text-xs font-bold text-gray-500 text-center shrink-0">
                            {{ $item->start_time->format('H:i') }}
                        </div>
                        <div class="w-1 h-8 rounded-full {{ $lineColor }} shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-xs text-gray-900 truncate">{{ $item->title }}</p>
                            <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $item->subject ?? $item->start_time->format('H:i') . ' - ' . $item->end_time->format('H:i') }}</p>
                        </div>
                        <span class="text-[10px] px-2.5 py-1 rounded-full {{ $statusClass }} shrink-0">
                            {{ ucfirst($item->status) }}
                        </span>
                    </a>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        <i class="fa-regular fa-calendar-check text-2xl mb-2"></i>
                        <p class="text-xs">Belum ada jadwal hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Progress Summary --}}
        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between fade-up">
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

            <a href="{{ route('progress') }}" class="w-full py-2.5 rounded-xl bg-[#5B1744] text-white text-xs font-semibold text-center hover:bg-[#481236] transition shadow-xs">
                Lihat progress lengkap
            </a>
        </div>

    </section>

    {{-- Quick actions --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow fade-up">
        <div class="mb-5">
            <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">QUICK ACTIONS</p>
            <h2 class="text-lg font-bold text-gray-900 mt-0.5">Kelola ruang belajarmu</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('tasks') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">
                <div class="w-10 h-10 rounded-xl bg-white text-[#5B1744] flex items-center justify-center shadow-xs">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">My Tasks</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Kelola semua tugas</p>
                </div>
            </a>
            <a href="{{ route('schedule') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">
                <div class="w-10 h-10 rounded-xl bg-white text-[#5B1744] flex items-center justify-center shadow-xs">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Schedule</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Atur jadwal belajar</p>
                </div>
            </a>
            <a href="{{ route('progress') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">
                <div class="w-10 h-10 rounded-xl bg-white text-[#5B1744] flex items-center justify-center shadow-xs">
                    <i class="fa-solid fa-chart-simple"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Progress</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Sesi & statistik belajar</p>
                </div>
            </a>
            <a href="{{ route('integrations') }}" class="flex items-center gap-3 p-4 rounded-2xl bg-[#FAF6F0] hover:bg-[#F4E7EF] transition">
                <div class="w-10 h-10 rounded-xl bg-white text-[#5B1744] flex items-center justify-center shadow-xs">
                    <i class="fa-solid fa-link"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-900">Integrations</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Google Calendar & Tasks</p>
                </div>
            </a>
        </div>
    </section>

@endsection

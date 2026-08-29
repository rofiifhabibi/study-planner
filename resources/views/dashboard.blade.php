<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard — Study Planner</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CDN Tailwind CSS Fallback (Jaga-jaga jika Vite belum di-build) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            scroll-behavior: smooth;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
        }

        .serif {
            font-family: 'Playfair Display', serif;
        }

        .noise {
            background-image: radial-gradient(rgba(91, 23, 68, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .soft-shadow {
            box-shadow: 0 10px 40px rgba(91, 23, 68, 0.05);
        }

        .card-hover {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 45px rgba(91, 23, 68, 0.09);
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-weight: 600;
        }

        .progress-ring {
            transform: rotate(-90deg);
        }

        .fade-up {
            animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-[#FAF6F0] text-[#241C21] antialiased">

    <div class="min-h-screen flex flex-row">

        {{-- ========================================================= --}}
        {{-- SIDEBAR --}}
        {{-- ========================================================= --}}

        <aside
            id="sidebar"
            class="fixed md:sticky top-0 left-0 z-50 h-screen w-[260px]
                   bg-[#5B1744] text-white flex flex-col
                   -translate-x-full md:translate-x-0
                   transition-transform duration-300 ease-in-out shrink-0">

            {{-- Logo --}}
            <div class="p-6 pb-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white text-[#5B1744] flex items-center justify-center font-bold text-lg shadow-sm">
                        S
                    </div>
                    <div>
                        <div class="font-bold text-base tracking-tight">
                            Study Planner
                        </div>
                        <div class="text-[9px] text-white/50 tracking-[.2em] font-semibold">
                            PLAN · STUDY · GROW
                        </div>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <div class="px-4 flex-1 overflow-y-auto space-y-6">

                <div>
                    <p class="px-3 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">
                        Workspace
                    </p>

                    <nav class="space-y-1">
                        <a href="/dashboard" class="sidebar-item active flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-solid fa-border-all w-4 text-center"></i>
                            Overview
                        </a>

                        <a href="#tasks" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-regular fa-circle-check w-4 text-center"></i>
                            My Tasks
                        </a>

                        <a href="#schedule" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-regular fa-calendar-check w-4 text-center"></i>
                            Schedule
                        </a>

                        <a href="/chat" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-solid fa-wand-magic-sparkles w-4 text-center"></i>
                            AI Study
                        </a>

                        <a href="#materials" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-regular fa-folder w-4 text-center"></i>
                            Materials
                        </a>

                        <a href="#progress" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-solid fa-chart-simple w-4 text-center"></i>
                            Progress
                        </a>
                    </nav>
                </div>

                <div>
                    <p class="px-3 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">
                        Personal
                    </p>

                    <nav class="space-y-1">
                        <a href="#" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-solid fa-gear w-4 text-center"></i>
                            Settings
                        </a>

                        <a href="/" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs text-white/70">
                            <i class="fa-solid fa-house w-4 text-center"></i>
                            Back to Home
                        </a>
                    </nav>
                </div>
            </div>

            {{-- Profile --}}
            <div class="p-4 border-t border-white/10">
                <div role="button" tabindex="0" onclick="openProfileModal()"
                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openProfileModal();}"
                    class="w-full flex items-center justify-between gap-3 rounded-xl hover:bg-white/10 transition p-2 -m-2 text-left focus:outline-none cursor-pointer">
                    <span class="flex items-center gap-3 min-w-0">
                        <span class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        </span>
                        <span class="min-w-0">
                            <span class="block font-semibold text-xs text-white truncate">
                                {{ auth()->user()->name ?? 'Killa' }}
                            </span>
                            <span class="block text-[10px] text-white/50 truncate">
                                {{ auth()->user()->email }}
                            </span>
                        </span>
                    </span>

                    <span class="flex items-center gap-1 shrink-0 text-white/50">
                        <i class="fa-solid fa-pen-to-square text-xs" title="Edit profile"></i>

                        <form method="POST" action="{{ route('logout') }}" onclick="event.stopPropagation()">
                            @csrf
                            <button type="submit" title="Logout"
                                class="text-white/50 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </span>
                </div>
            </div>

        </aside>

        {{-- Overlay for Mobile Drawer --}}
        <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40 hidden md:hidden"></div>

        {{-- ========================================================= --}}
        {{-- MAIN CONTENT AREA --}}
        {{-- ========================================================= --}}

        <main class="flex-1 min-w-0 pb-16 md:pb-10">

            {{-- Topbar --}}
            <header class="h-16 md:h-20 bg-[#FAF6F0]/80 backdrop-blur-md border-b border-[#5B1744]/5 sticky top-0 z-30">
                <div class="h-full px-4 sm:px-8 md:px-10 flex items-center justify-between">

                    {{-- Left Mobile Menu Button & Brand --}}
                    <div class="flex items-center gap-3">
                        <button id="menuButton" class="md:hidden w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-700 shadow-xs">
                            <i class="fa-solid fa-bars text-sm"></i>
                        </button>

                        <span class="font-bold text-base text-[#5B1744] md:hidden">Study Planner</span>

                        <div class="hidden md:block">
                            <p class="text-[10px] uppercase tracking-[.2em] font-bold text-[#5B1744]">
                                MY STUDY SPACE
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ date('l, d F Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Right Controls --}}
                    <div class="flex items-center gap-3">
                        <button type="button" class="relative w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:border-[#5B1744]/30 transition shadow-xs">
                            <i class="fa-regular fa-bell text-xs"></i>
                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#5B1744]"></span>
                        </button>
                    </div>

                </div>
            </header>

            {{-- Dashboard Content Container --}}
            <div class="relative min-h-[calc(100vh-80px)]">

                <div class="absolute inset-0 noise opacity-40 pointer-events-none"></div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-8 md:px-10 py-6 sm:py-8 space-y-6">

                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="bg-green-50 border border-green-200 text-green-700 text-xs rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl px-4 py-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Greeting Header --}}
                    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold text-[#5B1744]">
                                Good afternoon 👋
                            </p>
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold tracking-tight mt-1 text-gray-900">
                                Ready to make <span class="serif italic text-[#5B1744]">today count?</span>
                            </h1>
                            <p class="text-xs sm:text-sm text-gray-500 mt-2">
                                Kamu punya beberapa hal yang perlu diselesaikan hari ini. Pelan-pelan, satu per satu.
                            </p>
                        </div>

                        <button onclick="openTaskModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 transition">
                            <i class="fa-solid fa-plus"></i>
                            <span>Add Task</span>
                        </button>
                    </section>

                    {{-- AI Summary & Priority Focus --}}
                    <section class="grid md:grid-cols-12 gap-5">

                        {{-- AI Summary --}}
                        <div class="md:col-span-7 relative overflow-hidden rounded-3xl bg-[#5B1744] text-white p-6 sm:p-7 soft-shadow">
                            <div class="relative z-10 flex flex-col h-full justify-between gap-6">
                                <div>
                                    <div class="flex items-center gap-3 mb-4">
                                        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-300">
                                            <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] uppercase tracking-[.2em] text-white/50 font-bold">
                                                AI DAILY SUMMARY
                                            </p>
                                            <p class="font-semibold text-xs text-white/90">
                                                Your study companion noticed something.
                                            </p>
                                        </div>
                                    </div>

                            <p class="text-base sm:text-lg md:text-xl font-medium leading-relaxed text-white/95">
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

                                <div>
                                    <a href="/chat" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white text-[#5B1744] text-xs font-bold hover:bg-[#FAF6F0] transition shadow-xs">
                                        <span>Ask AI about my day</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Priority Focus --}}
                        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow card-hover flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">
                                            PRIORITY FOCUS
                                        </p>
                                        <h3 class="text-lg font-bold text-gray-900 mt-2">
                                            Finish Database Assignment
                                        </h3>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center font-bold text-xs">
                                        !
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-3 leading-relaxed">
                                    Deadline hari ini. Fokuskan satu sesi belajar khusus untuk menyelesaikan tugas ini.
                                </p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-50">
                                <div class="flex justify-between text-xs mb-1.5">
                                    <span class="text-gray-400 font-medium">Progress</span>
                                    <span class="font-bold text-[#5B1744]">70%</span>
                                </div>
                                <div class="h-2 bg-[#F4E7EF] rounded-full overflow-hidden">
                                    <div class="h-full w-[70%] bg-[#5B1744] rounded-full"></div>
                                </div>
                            </div>
                        </div>

                    </section>

                    {{-- Stats Section --}}
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

                    {{-- Study Session Timer --}}
                    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
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
                    </section>

                    {{-- Tasks + Priority --}}
                    <section id="tasks" class="grid md:grid-cols-12 gap-5">

                        {{-- Tasks List --}}
                        <div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
                            <div class="flex justify-between items-center mb-5">
                                <div>
                                    <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY'S TASKS</p>
                                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">My Tasks</h2>
                                </div>
                                <button onclick="openTaskModal()" class="text-xs font-semibold text-[#5B1744] hover:underline">+ Add Task</button>
                            </div>

                            <div id="task-list" class="space-y-2">
                                @forelse ($todayTasks as $task)
                                    <div class="task-item flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition" data-task-id="{{ $task->id }}">
                                        <button onclick="toggleTask({{ $task->id }})" class="w-5 h-5 rounded-full border-2 {{ $task->status === 'completed' ? 'bg-[#5B1744] border-[#5B1744]' : 'border-gray-300' }} flex items-center justify-center shrink-0 transition">
                                            @if ($task->status === 'completed')
                                                <i class="fa-solid fa-check text-[8px] text-white"></i>
                                            @endif
                                        </button>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-xs text-gray-900 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst($task->category) }} · {{ $task->due_date->format('d M') }}</p>
                                        </div>
                                        <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-500') }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        <button onclick="deleteTask({{ $task->id }})" class="text-gray-400 hover:text-red-500 transition shrink-0">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400">
                                        <i class="fa-regular fa-circle-check text-2xl mb-2"></i>
                                        <p class="text-xs">Belum ada tugas hari ini.</p>
                                        <button onclick="openTaskModal()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Tambah tugas baru</button>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Priority Focus --}}
                        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow card-hover flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">
                                            PRIORITY FOCUS
                                        </p>
                                        @php
                                            $priorityTask = $todayTasks->where('status', 'pending')->sortBy('priority')->first();
                                        @endphp
                                        @if ($priorityTask)
                                            <h3 class="text-lg font-bold text-gray-900 mt-2">
                                                {{ $priorityTask->title }}
                                            </h3>
                                        @else
                                            <h3 class="text-lg font-bold text-gray-900 mt-2">
                                                No pending tasks
                                            </h3>
                                        @endif
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center font-bold text-xs">
                                        !
                                    </div>
                                </div>

                                @if ($priorityTask)
                                    <p class="text-xs text-gray-500 mt-3 leading-relaxed">
                                        {{ $priorityTask->description ?? 'Deadline hari ini. Fokuskan satu sesi belajar khusus untuk menyelesaikan tugas ini.' }}
                                    </p>
                                @else
                                    <p class="text-xs text-gray-500 mt-3 leading-relaxed">
                                        Semua tugas sudah selesai! Kamu hebat hari ini.
                                    </p>
                                @endif
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-50">
                                <div class="flex justify-between text-xs mb-1.5">
                                    <span class="text-gray-400 font-medium">Progress</span>
                                    <span class="font-bold text-[#5B1744]">{{ $completionPercentage }}%</span>
                                </div>
                                <div class="h-2 bg-[#F4E7EF] rounded-full overflow-hidden">
                                    <div class="h-full bg-[#5B1744] rounded-full" style="width: {{ $completionPercentage }}%"></div>
                                </div>
                            </div>
                        </div>

                    </section>

                    {{-- Schedule + Progress Ring --}}
                    <section class="grid md:grid-cols-12 gap-5">

                        {{-- Schedule --}}
                        <div id="schedule" class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
                            <div class="flex justify-between items-center mb-5">
                                <div>
                                    <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY</p>
                                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">Your schedule</h2>
                                </div>
                                <button onclick="openScheduleModal()" class="text-xs font-semibold text-[#5B1744] hover:underline">+ Add Schedule</button>
                            </div>

                            <div class="space-y-2" id="schedule-list">
                                @forelse ($todaySchedules as $item)
                                    @php
                                        $statusColors = [
                                            'completed' => ['bg-green-50 text-green-600', 'bg-[#5B1744]'],
                                            'active' => ['bg-amber-50 text-amber-600 font-bold', 'bg-amber-500'],
                                            'pending' => ['bg-gray-50 text-gray-500', 'bg-gray-200'],
                                        ];
                                        [$statusClass, $lineColor] = $statusColors[$item->status] ?? $statusColors['pending'];
                                        $isActive = $item->status === 'active';
                                    @endphp
                                    <div class="flex items-center gap-3 p-3.5 rounded-2xl {{ $isActive ? 'bg-[#FAF6F0]' : 'hover:bg-[#FAF6F0]/60' }} transition" data-schedule-id="{{ $item->id }}">
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
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-400">
                                        <i class="fa-regular fa-calendar-check text-2xl mb-2"></i>
                                        <p class="text-xs">Belum ada jadwal hari ini.</p>
                                        <button onclick="openScheduleModal()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Tambah jadwal baru</button>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Progress Ring --}}
                        <div id="progress" class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between">
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

                </div>
            </div>

            {{-- Google Integration Panel --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                        <i class="fa-brands fa-google text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">INTEGRATIONS</p>
                        <p class="text-xs font-semibold text-gray-900">Google Calendar & Tasks</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('google.calendar.redirect') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-xs">
                        <i class="fa-brands fa-google text-[10px]"></i>
                        Connect Google
                    </a>
                    <form method="POST" action="{{ route('google.calendar.sync') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                            <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                            Sync to Calendar
                        </button>
                    </form>
                    <form method="POST" action="{{ route('google.calendar.pull') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                            <i class="fa-solid fa-arrow-down-to-bracket text-[10px]"></i>
                            Import from Calendar
                        </button>
                    </form>
                    <form method="POST" action="{{ route('google.tasks.sync') }}" class="inline">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                            <i class="fa-solid fa-list-check text-[10px]"></i>
                            Sync to Google Tasks
                        </button>
                    </form>
                </div>
            </div>

        </main>

        {{-- MOBILE BOTTOM NAVIGATION (Hanya Muncul di Mobile/Layar < 768px) --}}
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-100 py-2 px-6 flex justify-around items-center z-40 shadow-lg">
            <a href="/dashboard" class="flex flex-col items-center gap-1 text-[#5B1744]">
                <div class="bg-[#5B1744] text-white px-3 py-1 rounded-full text-xs">
                    <i class="fa-solid fa-border-all"></i>
                </div>
                <span class="text-[9px] font-bold">Overview</span>
            </a>
            <a href="#schedule" class="flex flex-col items-center gap-1 text-gray-400">
                <i class="fa-regular fa-calendar-check text-sm"></i>
                <span class="text-[9px] font-medium">Schedule</span>
            </a>
            <a href="#tasks" class="flex flex-col items-center gap-1 text-gray-400">
                <i class="fa-regular fa-circle-check text-sm"></i>
                <span class="text-[9px] font-medium">Tasks</span>
            </a>
            <a href="/chat" class="flex flex-col items-center gap-1 text-gray-400">
                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                <span class="text-[9px] font-medium">AI Study</span>
            </a>
        </nav>

    </div>

    {{-- ADD SCHEDULE MODAL --}}
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeScheduleModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">NEW SCHEDULE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Add a schedule</h2>
                </div>
                <button onclick="closeScheduleModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="scheduleForm" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-600">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Review Database" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600">Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Database Systems" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Date</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Start</label>
                        <input type="time" name="start_time" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">End</label>
                        <input type="time" name="end_time" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Add to calendar
                </button>
            </form>
        </div>
    </div>

    {{-- ADD TASK MODAL --}}
    <div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeTaskModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">NEW TASK</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Add something to your day</h2>
                </div>
                <button onclick="closeTaskModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="taskForm" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-600">Task name</label>
                    <input type="text" name="title" required placeholder="e.g. Finish networking assignment" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Deadline</label>
                        <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Category</label>
                        <select name="category" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                            <option value="school">School</option>
                            <option value="project">Project</option>
                            <option value="study">Study</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Priority</label>
                        <select name="priority" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600">Notes</label>
                    <textarea name="description" rows="3" placeholder="Optional notes..." class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition"></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Add to my planner
                </button>
            </form>
        </div>
    </div>

    {{-- PROFILE MODAL --}}
    <div id="profileModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeProfileModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up max-h-[92vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">EDIT PROFILE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Kelola akun Anda</h2>
                </div>
                <button type="button" onclick="closeProfileModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            @if (session('status') === 'profile-updated')
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-xs rounded-xl px-4 py-3 mb-4">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Profil berhasil diperbarui.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" onclick="event.stopPropagation()">
                @csrf
                @method('PUT')

                <div>
                    <label for="profile-name" class="text-xs font-bold text-gray-600">Nama</label>
                    <input id="profile-name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required autofocus
                        class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                    @error('name')
                        <p class="text-[10px] text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile-email" class="text-xs font-bold text-gray-600">Email</label>
                    <input id="profile-email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                        class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                    @error('email')
                        <p class="text-[10px] text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                    <p class="text-[10px] text-gray-400 mt-1.5 flex items-center gap-1">
                        <i class="fa-solid fa-circle-info"></i>
                        Ganti email akan meminta verifikasi ulang.
                    </p>
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Simpan Perubahan
                </button>
            </form>

            <div class="my-6 border-t border-dashed border-gray-300"></div>

            <div onclick="event.stopPropagation()">
                <p class="text-[9px] uppercase tracking-[.2em] text-[#B91C1C] font-bold">DANGER ZONE</p>
                <p class="text-xs text-gray-500 leading-relaxed mt-1.5 mb-3">
                    Akun beserta seluruh riwayat chat dan data planner akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <button type="button" onclick="toggleDeleteConfirm()"
                        class="w-full py-2.5 rounded-xl bg-white border border-[#B91C1C]/30 text-[#B91C1C] text-xs font-bold hover:bg-red-50 transition">
                        <i class="fa-regular fa-trash-can mr-1.5"></i>Hapus Akun Permanen
                    </button>

                    <div id="deleteConfirm" class="hidden space-y-3 mt-3">
                        <label for="delete-account-password" class="sr-only">Konfirmasi password</label>
                        <input id="delete-account-password" type="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password untuk konfirmasi"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#B91C1C] transition">

                        @error('password', 'userDeletion')
                            <p class="text-[10px] text-red-500">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-[#B91C1C] text-white text-xs font-bold hover:bg-[#991B1B] transition shadow-xs">
                            Ya, Saya Yakin Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuButton = document.getElementById('menuButton');
        const taskModal = document.getElementById('taskModal');
        const profileModal = document.getElementById('profileModal');
        const scheduleModal = document.getElementById('scheduleModal');

        const API_BASE = '{{ url("/api") }}';

        function getCsrfToken() {
            const m = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
            return m ? decodeURIComponent(m[1]) : '';
        }
        function apiFetch(url, opts = {}) {
            const h = opts.headers || {};
            h['X-XSRF-TOKEN'] = getCsrfToken();
            h['Accept'] = h['Accept'] || 'application/json';
            opts.headers = h; opts.credentials = opts.credentials || 'same-origin';
            return fetch(url, opts);
        }

        menuButton?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });

        overlay?.addEventListener('click', closeSidebar);

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        function openTaskModal() {
            taskModal.classList.remove('hidden');
            taskModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeTaskModal() {
            taskModal.classList.add('hidden');
            taskModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function openScheduleModal() {
            scheduleModal.classList.remove('hidden');
            scheduleModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeScheduleModal() {
            scheduleModal.classList.add('hidden');
            scheduleModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeTaskModal();
                closeProfileModal();
                closeScheduleModal();
                closeSidebar();
            }
        });

        function openProfileModal() {
            profileModal.classList.remove('hidden');
            profileModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            document.getElementById('profile-name')?.focus();
        }

        function closeProfileModal() {
            profileModal.classList.add('hidden');
            profileModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function toggleDeleteConfirm() {
            const box = document.getElementById('deleteConfirm');
            box.classList.toggle('hidden');
            if (! box.classList.contains('hidden')) {
                document.getElementById('delete-account-password')?.focus();
            }
        }

        // === TASK CRUD ===

        document.getElementById('taskForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Adding...';

            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                const res = await apiFetch(`${API_BASE}/tasks`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await res.json();

                if (result.status === 'success') {
                    closeTaskModal();
                    form.reset();
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal menambahkan tugas.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menambahkan tugas.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        });

        async function toggleTask(id) {
            const item = document.querySelector(`[data-task-id="${id}"]`);
            if (!item) return;

            const btn = item.querySelector('button');
            const titleEl = item.querySelector('.font-semibold');
            const isCompleted = btn.classList.contains('bg-[#5B1744]');

            try {
                const res = await apiFetch(`${API_BASE}/tasks/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: isCompleted ? 'pending' : 'completed' }),
                });
                const result = await res.json();
                if (result.status === 'success') {
                    window.location.reload();
                }
            } catch (err) {
                alert('Gagal mengubah status tugas.');
            }
        }

        async function deleteTask(id) {
            if (!confirm('Hapus tugas ini?')) return;

            try {
                const res = await apiFetch(`${API_BASE}/tasks/${id}`, { method: 'DELETE' });
                const result = await res.json();
                if (result.status === 'success') {
                    window.location.reload();
                }
            } catch (err) {
                alert('Gagal menghapus tugas.');
            }
        }

        // === SCHEDULE CRUD ===

        document.getElementById('scheduleForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Adding...';

            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                const res = await apiFetch(`${API_BASE}/schedules`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await res.json();

                if (result.status === 'success') {
                    closeScheduleModal();
                    form.reset();
                    window.location.reload();
                } else {
                    alert(result.message || 'Gagal menambahkan jadwal.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menambahkan jadwal.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        });

        // === STUDY SESSION TIMER ===

        let studyTimerInterval = null;
        let activeStudySession = null;
        let elapsedAtPause = 0;

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
    </script>

</body>

</html>
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
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-xs text-white truncate">
                                {{ auth()->user()->name ?? 'Killa' }}
                            </p>
                            <p class="text-[10px] text-white/50 truncate">
                                Student Account
                            </p>
                        </div>
                    </div>

                    <a href="#" class="text-white/50 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </a>
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
                        <button class="relative w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:border-[#5B1744]/30 transition shadow-xs">
                            <i class="fa-regular fa-bell text-xs"></i>
                            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#5B1744]"></span>
                        </button>

                        {{-- User Dropdown --}}
                        <div class="relative">
                            <button type="button" onclick="toggleUserMenu(event)" class="flex items-center gap-3 pl-3 border-l border-gray-200 focus:outline-none">
                                <div class="text-right hidden sm:block">
                                    <p class="text-xs font-bold text-gray-800">
                                        {{ auth()->user()->name }}
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        Keep going 🔥
                                    </p>
                                </div>
                                <div class="w-9 h-9 rounded-full bg-[#E9D5E1] text-[#5B1744] flex items-center justify-center font-bold text-xs shadow-xs">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </button>

                            <div id="userMenu" class="hidden absolute right-0 top-full mt-2 w-60 bg-white rounded-2xl border border-[#F0EAE1] shadow-xl shadow-[#5B1744]/10 overflow-hidden z-50">
                                <div class="px-4 py-3 border-b border-[#F0EAE1]">
                                    <p class="text-xs font-bold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full px-4 py-3 flex items-center gap-2 text-left text-xs font-semibold text-[#B91C1C] hover:bg-[#FAF6F0] transition">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            {{-- Dashboard Content Container --}}
            <div class="relative min-h-[calc(100vh-80px)]">

                <div class="absolute inset-0 noise opacity-40 pointer-events-none"></div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-8 md:px-10 py-6 sm:py-8 space-y-6">

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
                                        Kamu sudah menyelesaikan <span class="text-[#E9C9DA] font-bold">4 dari 6 tugas</span> hari ini. Tinggal dua lagi — jangan sampai progress bagus ini berhenti di tengah.
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
                                ['label' => "Today's Tasks", 'value' => '6', 'icon' => 'fa-regular fa-circle-check', 'description' => '4 completed'],
                                ['label' => 'Study Hours', 'value' => '3.5h', 'icon' => 'fa-regular fa-clock', 'description' => '+30 min from yesterday'],
                                ['label' => 'Completed', 'value' => '24', 'icon' => 'fa-solid fa-chart-line', 'description' => 'This month'],
                                ['label' => 'Study Streak', 'value' => '7', 'icon' => 'fa-solid fa-fire text-amber-500', 'description' => 'days in a row'],
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

                    {{-- Schedule + Progress --}}
                    <section class="grid md:grid-cols-12 gap-5">

                        {{-- Schedule --}}
                        <div id="schedule" class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
                            <div class="flex justify-between items-center mb-5">
                                <div>
                                    <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY</p>
                                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">Your schedule</h2>
                                </div>
                                <a href="#" class="text-xs font-semibold text-[#5B1744] hover:underline">View calendar →</a>
                            </div>

                            <div class="space-y-2">
                                @php
                                    $schedule = [
                                        ['time' => '09:00', 'title' => 'Review Database', 'subject' => 'Database Systems · 1h 30m', 'status' => 'Done', 'line' => 'bg-[#5B1744]', 'statusClass' => 'bg-green-50 text-green-600', 'active' => false],
                                        ['time' => '13:00', 'title' => 'Network Assignment', 'subject' => 'Networking · 1h 30m', 'status' => 'Next', 'line' => 'bg-amber-500', 'statusClass' => 'bg-amber-50 text-amber-600 font-bold', 'active' => true],
                                        ['time' => '19:00', 'title' => 'Study Mathematics', 'subject' => 'Mathematics · 1h', 'status' => null, 'line' => 'bg-gray-200', 'statusClass' => '', 'active' => false],
                                    ];
                                @endphp

                                @foreach ($schedule as $item)
                                    <div class="flex items-center gap-3 p-3.5 rounded-2xl {{ $item['active'] ? 'bg-[#FAF6F0]' : 'hover:bg-[#FAF6F0]/60' }} transition">
                                        <div class="w-12 text-xs font-bold text-gray-500 text-center shrink-0">
                                            {{ $item['time'] }}
                                        </div>
                                        <div class="w-1 h-8 rounded-full {{ $item['line'] }} shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-xs text-gray-900 truncate">{{ $item['title'] }}</p>
                                            <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $item['subject'] }}</p>
                                        </div>
                                        @if ($item['status'])
                                            <span class="text-[10px] px-2.5 py-1 rounded-full {{ $item['statusClass'] }} shrink-0">
                                                {{ $item['status'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Progress Ring --}}
                        <div id="progress" class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between">
                            <div>
                                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">YOUR PROGRESS</p>
                                <h2 class="text-lg font-bold text-gray-900 mt-0.5">This week</h2>
                            </div>

                            <div class="relative w-36 h-36 mx-auto my-4">
                                <svg class="w-full h-full progress-ring" viewBox="0 0 160 160">
                                    <circle cx="80" cy="80" r="64" fill="none" stroke="#F4E7EF" stroke-width="12"></circle>
                                    <circle cx="80" cy="80" r="64" fill="none" stroke="#5B1744" stroke-width="12" stroke-linecap="round" stroke-dasharray="402" stroke-dashoffset="112"></circle>
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-2xl font-bold text-[#5B1744]">72%</span>
                                    <span class="text-[9px] text-gray-400 uppercase font-semibold">completed</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                                    <p class="text-[10px] text-gray-400 font-medium">Tasks</p>
                                    <p class="font-bold text-xs mt-0.5 text-gray-800">18 / 25</p>
                                </div>
                                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                                    <p class="text-[10px] text-gray-400 font-medium">Hours</p>
                                    <p class="font-bold text-xs mt-0.5 text-gray-800">12.5h</p>
                                </div>
                            </div>
                        </div>

                    </section>

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

            <form action="#" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-600">Task name</label>
                    <input type="text" name="title" placeholder="e.g. Finish networking assignment" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Deadline</label>
                        <input type="date" name="deadline" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Category</label>
                        <select name="category" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                            <option value="School">School</option>
                            <option value="Project">Project</option>
                            <option value="Study">Study</option>
                            <option value="Personal">Personal</option>
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

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuButton = document.getElementById('menuButton');
        const taskModal = document.getElementById('taskModal');

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

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeTaskModal();
                closeSidebar();
            }
        });

        function toggleUserMenu(event) {
            event.stopPropagation();
            document.getElementById('userMenu').classList.toggle('hidden');
        }

        document.addEventListener('click', (event) => {
            const menu = document.getElementById('userMenu');
            if (menu && ! menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

</body>

</html>
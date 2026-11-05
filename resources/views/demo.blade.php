<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo Website — Study Planner</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Tailwind CSS CDN Fallback & Vite Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            user-select: none;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #120e18;
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .noise {
            background-image: radial-gradient(rgba(91, 23, 68, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .soft-shadow {
            box-shadow: 0 10px 40px rgba(91, 23, 68, 0.05);
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-weight: 600;
        }

        /* Video Stage Frame */
        #demoScreenFrame {
            position: relative;
            width: 1366px;
            height: 768px;
            background: #FAF6F0;
            overflow: hidden;
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.7);
            transform-origin: center center;
            border-radius: 12px;
        }

        /* Camera rig smooth zoom and pan */
        #cameraRig {
            width: 1366px;
            height: 768px;
            position: absolute;
            top: 0;
            left: 0;
            transform-origin: 0 0;
            transition: transform 1.25s cubic-bezier(0.25, 1, 0.35, 1);
            will-change: transform;
        }

        /* Virtual Cursor */
        #virtualCursor {
            position: absolute;
            z-index: 9999;
            pointer-events: none;
            width: 26px;
            height: 26px;
            transition: left 0.85s cubic-bezier(0.25, 1, 0.35, 1),
                        top 0.85s cubic-bezier(0.25, 1, 0.35, 1),
                        transform 0.15s ease;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.35));
            will-change: left, top, transform;
        }

        .cursor-click-active {
            transform: scale(0.8);
        }

        /* Ripple Effect */
        .click-ripple {
            position: absolute;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(91, 23, 68, 0.25);
            border: 2px solid #5B1744;
            transform: translate(-50%, -50%) scale(0);
            pointer-events: none;
            z-index: 9998;
            animation: rippleAnim 0.6s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
        }

        @keyframes rippleAnim {
            0% {
                transform: translate(-50%, -50%) scale(0.2);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(2);
                opacity: 0;
            }
        }

        /* Blinking typing caret */
        .typing-caret {
            display: inline-block;
            width: 2px;
            height: 1.1em;
            background-color: #5B1744;
            vertical-align: middle;
            animation: blink 0.75s infinite;
        }
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Highlighting newly created task */
        @keyframes newTaskPop {
            0% {
                opacity: 0;
                transform: translateY(-16px) scale(0.97);
                background-color: #F4E7EF;
            }
            40% {
                background-color: #E7C8DB;
                box-shadow: 0 0 25px rgba(91, 23, 68, 0.25);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
                background-color: #ffffff;
            }
        }
        .new-task-animated {
            animation: newTaskPop 0.9s cubic-bezier(0.2, 0.9, 0.3, 1) forwards;
        }

        /* Minimal Scrubber */
        .timeline-scrubber {
            -webkit-appearance: none;
            appearance: none;
            background: rgba(255, 255, 255, 0.25);
            height: 4px;
            border-radius: 9999px;
            outline: none;
            cursor: pointer;
        }
        .timeline-scrubber::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #5B1744;
            cursor: pointer;
            box-shadow: 0 0 6px rgba(0,0,0,0.4);
        }
    </style>
</head>
<body>

    <!-- Main Screen Frame (100% Exact Website UI) -->
    <div id="demoScreenFrame">

        <!-- Camera Rig for Zoom & Pan -->
        <div id="cameraRig">

            <div class="w-[1366px] h-[768px] flex flex-row bg-[#FAF6F0] text-[#241C21] antialiased relative overflow-hidden">

                <!-- Real App Desktop Column Spacer & Background Track -->
                <div class="w-[260px] shrink-0 bg-[#5B1744] self-stretch"></div>

                <!-- ========================================================= -->
                <!-- SIDEBAR (Exact Match to layouts/study.blade.php) -->
                <!-- ========================================================= -->
                <aside id="sidebar" class="absolute top-0 left-0 z-30 h-[768px] w-[260px] bg-[#5B1744] text-white flex flex-col shrink-0">

                    <!-- Logo -->
                    <div class="p-6 pb-7">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 overflow-hidden">
                                <img src="/logo-chatgpt.png" alt="Study Planner" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <div class="font-bold text-base tracking-tight text-white leading-tight">
                                    Study Planner
                                </div>
                                <div class="text-[9px] text-white/50 tracking-[.2em] font-semibold mt-0.5">
                                    PLAN · STUDY · GROW
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="px-4 flex-1 space-y-5">
                        <div>
                            <p class="px-3 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">
                                Workspace
                            </p>
                            <nav class="space-y-1">
                                <div id="navItemOverview" class="sidebar-item active flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer">
                                    <i class="fa-solid fa-border-all w-4 text-center"></i>
                                    <span>Overview</span>
                                </div>

                                <div id="navItemTasks" class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer">
                                    <i class="fa-regular fa-circle-check w-4 text-center"></i>
                                    <span>My Tasks</span>
                                </div>

                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-regular fa-calendar-check w-4 text-center"></i>
                                    <span>Schedule</span>
                                </div>

                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-solid fa-graduation-cap w-4 text-center"></i>
                                    <span>Class Schedule</span>
                                </div>

                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-regular fa-note-sticky w-4 text-center"></i>
                                    <span>Notes</span>
                                </div>

                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-solid fa-wand-magic-sparkles w-4 text-center"></i>
                                    <span>AI Study</span>
                                </div>

                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-solid fa-chart-simple w-4 text-center"></i>
                                    <span>Progress</span>
                                </div>
                            </nav>
                        </div>

                        <div>
                            <p class="px-3 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">
                                Personal
                            </p>
                            <nav class="space-y-1">
                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-solid fa-link w-4 text-center"></i>
                                    <span>Integrations</span>
                                </div>
                                <div class="sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs opacity-75">
                                    <i class="fa-solid fa-house w-4 text-center"></i>
                                    <span>Back to Home</span>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <!-- User Profile Card -->
                    <div class="p-4 border-t border-white/10 flex items-center justify-between gap-2">
                        <div class="flex-1 flex items-center gap-3 rounded-xl p-2 -m-2 min-w-0">
                            <span class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                                R
                            </span>
                            <span class="min-w-0">
                                <span class="block font-semibold text-xs text-white truncate">Rofiif</span>
                                <span class="block text-[10px] text-white/50 truncate">rofiifhabibi@gmail.com</span>
                            </span>
                        </div>
                        <div class="text-white/50 p-2.5 rounded-lg bg-black/10">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </div>
                    </div>
                </aside>

                <!-- ========================================================= -->
                <!-- MAIN VIEW AREA (Exact Topbar & Content) -->
                <!-- ========================================================= -->
                <main class="flex-1 min-w-0 flex flex-col relative h-[768px] overflow-hidden bg-[#FAF6F0]">

                    <!-- Topbar (Exact match to study.blade.php) -->
                    <header class="h-20 bg-[#FAF6F0]/80 backdrop-blur-md border-b border-[#5B1744]/5 flex items-center justify-between px-10 shrink-0">
                        <div>
                            <p class="text-[10px] uppercase tracking-[.2em] font-bold text-[#5B1744]" id="topbarPageLabel">
                                MY STUDY SPACE
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                Sabtu, 31 Oktober 2026
                            </p>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <button type="button" class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-[#5B1744] shadow-xs text-xs font-bold">
                                <i class="fa-regular fa-circle-question text-sm text-[#5B1744]"></i>
                                <span>Panduan</span>
                            </button>

                            <button type="button" class="relative w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 shadow-xs">
                                <i class="fa-regular fa-bell text-xs"></i>
                                <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#5B1744]"></span>
                            </button>
                        </div>
                    </header>

                    <!-- Content Body with Noise Texture -->
                    <div class="relative flex-1 overflow-hidden">
                        <div class="absolute inset-0 noise opacity-40 pointer-events-none"></div>

                        <!-- -------------------------------------------------- -->
                        <!-- PAGE 1: OVERVIEW / DASHBOARD -->
                        <!-- -------------------------------------------------- -->
                        <div id="viewOverview" class="absolute inset-0 px-10 py-6 space-y-6 transition-opacity duration-500 overflow-hidden">

                            <!-- Greeting + AI Summary Button -->
                            <section class="flex items-end justify-between gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                                        Selamat datang, Rofiif
                                    </h1>
                                    <p class="text-sm text-gray-500 mt-1">Ringkasan ruang belajarmu hari ini.</p>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 shrink-0">
                                    <i class="fa-solid fa-wand-magic-sparkles text-[10px]"></i>
                                    <span>Ask AI</span>
                                </div>
                            </section>

                            <!-- AI Daily Summary Card -->
                            <section class="relative overflow-hidden rounded-3xl bg-[#5B1744] text-white p-6 soft-shadow">
                                <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>
                                <div class="absolute -right-2 -bottom-16 w-40 h-40 rounded-full bg-white/5 pointer-events-none"></div>
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-amber-300 shrink-0">
                                                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-[9px] uppercase tracking-[.2em] text-white/60 font-bold">AI DAILY SUMMARY</p>
                                            </div>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 text-white/80 text-[11px] font-medium">
                                            <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                            <span>Refresh</span>
                                        </div>
                                    </div>
                                    <p class="text-base font-medium leading-relaxed text-white/95">
                                        Hari ini kamu memiliki 1 tugas penting yang perlu diselesaikan. Fokuslah pada prioritas utama dan manfaatkan timer Pomodoro untuk belajar secara konsisten.
                                    </p>
                                </div>
                            </section>

                            <!-- Stats (Exact 4 Cards) -->
                            <section class="grid grid-cols-4 gap-4">
                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Today's Tasks</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">1</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-regular fa-circle-check"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2">0 completed</p>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Study Hours</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">0h</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2">0 min today</p>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Completed</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">0</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2">All time</p>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Study Streak</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">1</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-fire text-amber-500"></i>
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-2">days in a row</p>
                                </div>
                            </section>

                            <!-- Today's Tasks Summary -->
                            <section class="bg-white rounded-3xl border border-gray-100 p-6 soft-shadow">
                                <div class="flex justify-between items-center mb-4">
                                    <div>
                                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY'S TASKS</p>
                                        <h2 class="text-lg font-bold text-gray-900 mt-0.5">My Tasks</h2>
                                    </div>
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#5B1744]">
                                        View all <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/70 border border-gray-100">
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-xs text-gray-900">Membuat Draft Presentasi Seminar</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">School · 02 Nov</p>
                                        </div>
                                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 font-semibold">
                                            Medium
                                        </span>
                                    </div>
                                </div>
                            </section>

                        </div>

                        <!-- -------------------------------------------------- -->
                        <!-- PAGE 2: MY TASKS (Exact tasks.blade.php) -->
                        <!-- -------------------------------------------------- -->
                        <div id="viewTasks" class="absolute inset-0 px-10 py-6 space-y-5 opacity-0 pointer-events-none transition-opacity duration-500 overflow-hidden">

                            <!-- Header -->
                            <section class="flex items-end justify-between gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold tracking-tight text-gray-900">My Tasks</h1>
                                    <p class="text-sm text-gray-500 mt-1">Kelola semua tugasmu dalam satu tempat.</p>
                                </div>
                                <button id="btnAddTaskTarget" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 shrink-0">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>Add Task</span>
                                </button>
                            </section>

                            <!-- Stats Row -->
                            <section class="grid grid-cols-4 gap-4">
                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Total Tasks</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]" id="statTotalTasks">1</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-regular fa-circle-check"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Pending</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]" id="statPendingTasks">1</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-regular fa-clock"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Completed</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">0</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-check-double"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-xs">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[11px] text-gray-400 font-medium">Completion</p>
                                            <p class="text-2xl font-bold mt-1 text-[#5B1744]">0%</p>
                                        </div>
                                        <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Task List Section -->
                            <section class="bg-white rounded-3xl border border-gray-100 p-6 soft-shadow">
                                <div class="flex justify-between items-center mb-5">
                                    <div>
                                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">ALL TASKS</p>
                                        <h2 class="text-lg font-bold text-gray-900 mt-0.5">Task list</h2>
                                    </div>
                                </div>

                                <div id="demoTaskContainer" class="space-y-2">
                                    <!-- Existing Task from DB -->
                                    <div class="task-item flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition border border-transparent">
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center shrink-0"></div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-semibold text-xs text-gray-900">Membuat Draft Presentasi Seminar</p>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB]/60 font-semibold">
                                                    <i class="fa-solid fa-stopwatch text-[9px] mr-1"></i>25m
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-gray-400 mt-0.5">School · 02 Nov 2026</p>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#F4E7EF] text-[#5B1744] text-[11px] font-semibold border border-[#E7C8DB] shrink-0">
                                            <i class="fa-solid fa-play text-[9px]"></i>
                                            <span>Fokus</span>
                                        </div>
                                        <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 bg-amber-50 text-amber-600 font-medium">
                                            Medium
                                        </span>
                                        <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-400 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </div>
                                    </div>

                                    <!-- Dynamic Slot for Newly Created Task -->
                                    <div id="dynamicTaskWrapper"></div>
                                </div>
                            </section>

                        </div>

                        <!-- -------------------------------------------------- -->
                        <!-- ADD TASK MODAL (Exact match to tasks.blade.php) -->
                        <!-- -------------------------------------------------- -->
                        <div id="demoModalBackdrop" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
                            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs"></div>

                            <div id="demoModalCard" class="relative w-full max-w-lg bg-[#FAF6F0] rounded-[2rem] p-6 sm:p-7 shadow-[0_0_60px_rgba(0,0,0,0.4),0_20px_40px_rgba(91,23,68,0.25)] border-2 border-[#E7C8DB] my-auto transform scale-95 transition-transform duration-300">
                                
                                <!-- Modal Header -->
                                <div class="flex items-center justify-between pb-4 border-b border-[#E7C8DB]/60 mb-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] flex items-center justify-center shrink-0 text-[#5B1744] text-lg">
                                            <i class="fa-solid fa-list-check"></i>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full">Tambah Tugas</span>
                                            <h2 class="text-lg font-bold text-gray-900 mt-1">Buat Rencana Tugas</h2>
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-white border border-[#E7C8DB] flex items-center justify-center text-gray-500">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <!-- 1. Judul Tugas -->
                                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                                        <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                                            <span>Nama Tugas</span>
                                            <span class="text-[10px] text-red-500 font-semibold">*Wajib</span>
                                        </label>
                                        <div class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent text-xs font-medium text-gray-900 min-h-[38px] flex items-center relative" id="modalInputBox">
                                            <span id="typedInputText" class="text-gray-900 font-semibold"></span>
                                            <span id="inputCaret" class="typing-caret ml-0.5"></span>
                                            <span id="inputPlaceholder" class="text-gray-400 absolute left-3">Misal: Rangkuman Biologi Bab 3</span>
                                        </div>
                                    </div>

                                    <!-- 2. Kategori Visual -->
                                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                                        <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                                            <span>Kategori</span>
                                            <span class="text-[10px] text-[#5B1744] font-bold">Sekolah</span>
                                        </label>
                                        <div class="grid grid-cols-4 gap-2 mt-2.5">
                                            <div class="py-2 px-2 rounded-xl text-[11px] font-bold border-2 bg-[#5B1744] text-white border-[#5B1744] shadow-xs flex items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-graduation-cap text-xs"></i> <span>Sekolah</span>
                                            </div>
                                            <div class="py-2 px-2 rounded-xl text-[11px] font-semibold border-2 bg-white text-gray-700 border-gray-200 flex items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-code text-xs"></i> <span>Proyek</span>
                                            </div>
                                            <div class="py-2 px-2 rounded-xl text-[11px] font-semibold border-2 bg-white text-gray-700 border-gray-200 flex items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-book-open text-xs"></i> <span>Belajar</span>
                                            </div>
                                            <div class="py-2 px-2 rounded-xl text-[11px] font-semibold border-2 bg-white text-gray-700 border-gray-200 flex items-center justify-center gap-1.5">
                                                <i class="fa-solid fa-user text-xs"></i> <span>Pribadi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Tenggat Waktu -->
                                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                                        <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                                            <span>Tenggat Waktu</span>
                                            <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                                        </label>
                                        <div class="flex items-center gap-2 mt-2 mb-2.5">
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-[#5B1744] text-white border border-[#5B1744] shadow-xs">Hari Ini</span>
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200">Besok</span>
                                            <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200">3 Hari</span>
                                        </div>
                                    </div>

                                    <!-- 4. Prioritas & Mode Fokus Pomodoro -->
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-white p-3 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                                            <label class="text-[11px] font-bold text-gray-800 block mb-2">Prioritas:</label>
                                            <div class="grid grid-cols-3 gap-1.5">
                                                <div class="py-1.5 rounded-xl text-[10px] font-semibold border-2 flex items-center justify-center gap-1 bg-white text-gray-700 border-gray-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Rendah
                                                </div>
                                                <div id="prioSedangPill" class="py-1.5 rounded-xl text-[10px] font-semibold border-2 flex items-center justify-center gap-1 bg-white text-gray-700 border-gray-200 transition-all">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Sedang
                                                </div>
                                                <div id="prioTinggiPill" class="py-1.5 rounded-xl text-[10px] font-bold border-2 flex items-center justify-center gap-1 bg-[#5B1744] text-white border-[#5B1744] transition-all shadow-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Tinggi
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-white p-3 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                                            <label class="text-[11px] font-bold text-gray-800 block mb-2">Target Sesi Fokus:</label>
                                            <div class="grid grid-cols-3 gap-1.5">
                                                <div class="py-1.5 rounded-xl text-[10px] font-semibold border-2 text-center bg-white text-gray-700 border-gray-200">15 Mnt</div>
                                                <div class="py-1.5 rounded-xl text-[10px] font-bold border-2 text-center bg-[#5B1744] text-white border-[#5B1744]">25 Mnt</div>
                                                <div class="py-1.5 rounded-xl text-[10px] font-semibold border-2 text-center bg-white text-gray-700 border-gray-200">50 Mnt</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="pt-2">
                                        <button id="btnModalSubmit" type="button" class="w-full py-3.5 rounded-2xl bg-[#5B1744] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-lg shadow-[#5B1744]/25 transition-transform">
                                            <i class="fa-solid fa-plus text-xs" id="submitIcon"></i>
                                            <span id="submitText">Simpan Tugas</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </main>

            </div>

        </div> <!-- End #cameraRig -->

        <!-- Virtual Cursor -->
        <div id="virtualCursor" style="left: 650px; top: 380px;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5.5 3.21V20.8c0 .45.54.67.85.35l4.74-4.74a.5.5 0 0 1 .35-.15h6.61c.45 0 .67-.54.35-.85L5.85 2.86a.499.499 0 0 0-.35.35z" fill="#111827"/>
                <path d="M6.5 4.92V19.08l3.74-3.74a1.5 1.5 0 0 1 1.06-.44h5.28L6.5 4.92z" fill="#ffffff"/>
            </svg>
        </div>

        <!-- Ripple Layer -->
        <div id="rippleLayer"></div>

    </div> <!-- End #demoScreenFrame -->

    <!-- Discreet Minimal Floating Controls (Hanya Kontrol Praktis di Pojok Bawah) -->
    <div class="fixed bottom-4 right-4 z-50 bg-[#1e1526]/90 backdrop-blur-md border border-white/15 px-3 py-2 rounded-full shadow-2xl flex items-center gap-3 text-white text-xs select-none">
        <button onclick="togglePlayPause()" id="btnPlayPause" class="w-7 h-7 rounded-full bg-[#5B1744] hover:bg-[#A33878] flex items-center justify-center transition shadow-xs">
            <i class="fa-solid fa-pause text-[10px]" id="playIcon"></i>
        </button>
        <button onclick="restartDemo()" title="Putar Ulang" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
            <i class="fa-solid fa-rotate-right text-[10px]"></i>
        </button>

        <div class="flex items-center gap-2 px-1">
            <input type="range" id="timelineScrubber" min="0" max="19000" value="0" step="50" class="w-24 sm:w-32 timeline-scrubber">
            <span class="font-mono text-[10px] text-gray-300 w-16" id="timeDisplay">00:00</span>
        </div>

        <button onclick="toggleFullscreen()" title="Layar Penuh" class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
            <i class="fa-solid fa-expand text-[10px]" id="fsIcon"></i>
        </button>
    </div>

    <!-- Script Controller -->
    <script>
        const TOTAL_DURATION = 19000;
        let currentTime = 0;
        let isPlaying = true;
        let lastTimestamp = null;

        // Elements
        const demoScreenFrame = document.getElementById('demoScreenFrame');
        const cameraRig = document.getElementById('cameraRig');
        const virtualCursor = document.getElementById('virtualCursor');
        const rippleLayer = document.getElementById('rippleLayer');
        const scrubber = document.getElementById('timelineScrubber');
        const timeDisplay = document.getElementById('timeDisplay');
        const playIcon = document.getElementById('playIcon');

        // App Views & DOM items
        const viewOverview = document.getElementById('viewOverview');
        const viewTasks = document.getElementById('viewTasks');
        const navItemOverview = document.getElementById('navItemOverview');
        const navItemTasks = document.getElementById('navItemTasks');
        const topbarPageLabel = document.getElementById('topbarPageLabel');
        const demoModalBackdrop = document.getElementById('demoModalBackdrop');
        const demoModalCard = document.getElementById('demoModalCard');
        const typedInputText = document.getElementById('typedInputText');
        const inputPlaceholder = document.getElementById('inputPlaceholder');
        const dynamicTaskWrapper = document.getElementById('dynamicTaskWrapper');
        const statTotalTasks = document.getElementById('statTotalTasks');
        const statPendingTasks = document.getElementById('statPendingTasks');
        const prioTinggiPill = document.getElementById('prioTinggiPill');
        const btnModalSubmit = document.getElementById('btnModalSubmit');

        const NEW_TASK_TITLE = "Rangkuman Biologi Bab 3: Genetika";

        // Auto-scale to fill viewport cleanly without clipping
        function resizeFrame() {
            const ww = window.innerWidth;
            const wh = window.innerHeight;
            const targetW = 1366;
            const targetH = 768;
            const scale = Math.min(ww / targetW, wh / targetH);
            demoScreenFrame.style.transform = `scale(${scale})`;
        }
        window.addEventListener('resize', resizeFrame);
        resizeFrame();

        function triggerRipple(x, y) {
            const ripple = document.createElement('div');
            ripple.className = 'click-ripple';
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            rippleLayer.appendChild(ripple);

            virtualCursor.classList.add('cursor-click-active');
            setTimeout(() => virtualCursor.classList.remove('cursor-click-active'), 150);
            setTimeout(() => ripple.remove(), 600);
        }

        let stateTriggers = {
            clickNav: false,
            clickAdd: false,
            clickInput: false,
            clickPrio: false,
            clickSubmit: false
        };

        function resetStateTriggers() {
            stateTriggers = {
                clickNav: false,
                clickAdd: false,
                clickInput: false,
                clickPrio: false,
                clickSubmit: false
            };
        }

        function renderFrame(t) {
            scrubber.value = t;
            const sec = Math.floor(t / 1000);
            const msSec = Math.floor((t % 1000) / 100);
            timeDisplay.innerText = `00:${sec < 10 ? '0' + sec : sec} / 00:19`;

            // SCENE 1: OVERVIEW (0ms - 2800ms)
            if (t < 2800) {
                cameraRig.style.transform = "scale(1) translate(0px, 0px)";
                topbarPageLabel.innerText = "MY STUDY SPACE";

                navItemOverview.className = "sidebar-item active flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer";
                navItemTasks.className = "sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer";

                viewOverview.style.opacity = "1";
                viewTasks.style.opacity = "0";
                demoModalBackdrop.style.display = "none";

                if (t < 1400) {
                    virtualCursor.style.left = "750px";
                    virtualCursor.style.top = "260px";
                } else {
                    // Glide to sidebar My Tasks
                    virtualCursor.style.left = "130px";
                    virtualCursor.style.top = "150px";
                }
            }

            // SCENE 2: CLICK MY TASKS & ZOOM TO SIDEBAR (2800ms - 5800ms)
            else if (t >= 2800 && t < 5800) {
                cameraRig.style.transform = "scale(1.35) translate(-40px, -45px)";
                virtualCursor.style.left = "130px";
                virtualCursor.style.top = "150px";

                if (t >= 3300 && !stateTriggers.clickNav) {
                    stateTriggers.clickNav = true;
                    triggerRipple(130, 150);
                }

                if (t >= 3500) {
                    topbarPageLabel.innerText = "MY TASKS";
                    navItemOverview.className = "sidebar-item text-white/80 hover:bg-white/10 hover:text-white flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer";
                    navItemTasks.className = "sidebar-item active flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs cursor-pointer";

                    viewOverview.style.opacity = "0";
                    viewTasks.style.opacity = "1";
                }

                // Glide cursor towards + Add Task button
                if (t >= 4500) {
                    virtualCursor.style.left = "1040px";
                    virtualCursor.style.top = "105px";
                }
            }

            // SCENE 3: ZOOM TO ADD TASK BUTTON (5800ms - 8800ms)
            else if (t >= 5800 && t < 8800) {
                cameraRig.style.transform = "scale(1.3) translate(-360px, -20px)";
                virtualCursor.style.left = "1040px";
                virtualCursor.style.top = "105px";

                if (t >= 6400 && !stateTriggers.clickAdd) {
                    stateTriggers.clickAdd = true;
                    triggerRipple(1040, 105);
                }

                // Modal pops in
                if (t >= 6600) {
                    demoModalBackdrop.style.display = "flex";
                    demoModalCard.style.transform = "scale(1)";
                }

                if (t >= 7600) {
                    virtualCursor.style.left = "690px";
                    virtualCursor.style.top = "260px";
                }
            }

            // SCENE 4: ZOOM MODAL, TYPE & CHOOSE PRIORITY (8800ms - 13500ms)
            else if (t >= 8800 && t < 13500) {
                cameraRig.style.transform = "scale(1.42) translate(-270px, -100px)";
                demoModalBackdrop.style.display = "flex";

                if (t < 12000) {
                    virtualCursor.style.left = "690px";
                    virtualCursor.style.top = "260px";

                    if (t >= 9000 && !stateTriggers.clickInput) {
                        stateTriggers.clickInput = true;
                        triggerRipple(690, 260);
                    }

                    // Typing simulation
                    const typeStart = 9200;
                    const typeDuration = 2200;
                    const progress = Math.min(1, Math.max(0, (t - typeStart) / typeDuration));
                    const chars = Math.floor(progress * NEW_TASK_TITLE.length);
                    typedInputText.innerText = NEW_TASK_TITLE.substring(0, chars);

                    if (chars > 0) {
                        inputPlaceholder.style.display = "none";
                    } else {
                        inputPlaceholder.style.display = "block";
                    }
                } else {
                    typedInputText.innerText = NEW_TASK_TITLE;
                    inputPlaceholder.style.display = "none";

                    // Move to priority High button
                    virtualCursor.style.left = "805px";
                    virtualCursor.style.top = "475px";

                    if (t >= 12300 && !stateTriggers.clickPrio) {
                        stateTriggers.clickPrio = true;
                        triggerRipple(805, 475);
                    }

                    // Glide to submit
                    if (t >= 12900) {
                        virtualCursor.style.left = "760px";
                        virtualCursor.style.top = "560px";
                    }
                }
            }

            // SCENE 5: SUBMIT & APPEAR IN LIST (13500ms - 19000ms)
            else if (t >= 13500) {
                if (t < 14500) {
                    cameraRig.style.transform = "scale(1.4) translate(-270px, -100px)";
                    virtualCursor.style.left = "760px";
                    virtualCursor.style.top = "560px";

                    if (t >= 13800 && !stateTriggers.clickSubmit) {
                        stateTriggers.clickSubmit = true;
                        triggerRipple(760, 560);
                        btnModalSubmit.style.transform = "scale(0.97)";
                        setTimeout(() => btnModalSubmit.style.transform = "scale(1)", 150);
                    }
                }

                // Modal closes & focus goes to task list
                if (t >= 14500 && t < 17000) {
                    demoModalBackdrop.style.display = "none";
                    cameraRig.style.transform = "scale(1.3) translate(-280px, -150px)";
                    virtualCursor.style.left = "780px";
                    virtualCursor.style.top = "360px";

                    if (!dynamicTaskWrapper.innerHTML.trim()) {
                        dynamicTaskWrapper.innerHTML = `
                            <div class="new-task-animated task-item flex items-center gap-3 p-3.5 rounded-2xl border-2 border-[#5B1744] shadow-md bg-white">
                                <div class="w-5 h-5 rounded-full border-2 border-[#5B1744] bg-[#5B1744] flex items-center justify-center text-white text-[9px] shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-semibold text-xs text-gray-900">${NEW_TASK_TITLE}</p>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB]/60 font-semibold">
                                            <i class="fa-solid fa-stopwatch text-[9px] mr-1"></i>25m
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-0.5">School · Hari Ini</p>
                                </div>
                                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#F4E7EF] text-[#5B1744] text-[11px] font-semibold border border-[#E7C8DB] shrink-0">
                                    <i class="fa-solid fa-play text-[9px]"></i>
                                    <span>Fokus</span>
                                </div>
                                <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 bg-red-50 text-red-600 font-bold border border-red-200">
                                    Tinggi
                                </span>
                                <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 text-gray-400 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </div>
                            </div>
                        `;
                        statTotalTasks.innerText = "2";
                        statPendingTasks.innerText = "2";
                    }
                }

                // Final Pullback to Wide Shot
                if (t >= 17000) {
                    cameraRig.style.transform = "scale(1) translate(0px, 0px)";
                    virtualCursor.style.left = "650px";
                    virtualCursor.style.top = "380px";
                }
            }

            // Loop smoothly at the end
            if (t >= TOTAL_DURATION) {
                currentTime = 0;
                resetStateTriggers();
                dynamicTaskWrapper.innerHTML = "";
                typedInputText.innerText = "";
                inputPlaceholder.style.display = "block";
                statTotalTasks.innerText = "1";
                statPendingTasks.innerText = "1";
            }
        }

        function step(timestamp) {
            if (!lastTimestamp) lastTimestamp = timestamp;
            const delta = timestamp - lastTimestamp;
            lastTimestamp = timestamp;

            if (isPlaying) {
                currentTime += delta;
                if (currentTime > TOTAL_DURATION) {
                    currentTime = TOTAL_DURATION;
                }
                renderFrame(currentTime);
            }
            requestAnimationFrame(step);
        }
        requestAnimationFrame(step);

        function togglePlayPause() {
            isPlaying = !isPlaying;
            playIcon.className = isPlaying ? "fa-solid fa-pause text-[10px]" : "fa-solid fa-play text-[10px]";
        }

        function restartDemo() {
            currentTime = 0;
            resetStateTriggers();
            dynamicTaskWrapper.innerHTML = "";
            typedInputText.innerText = "";
            inputPlaceholder.style.display = "block";
            statTotalTasks.innerText = "1";
            statPendingTasks.innerText = "1";
            isPlaying = true;
            playIcon.className = "fa-solid fa-pause text-[10px]";
            renderFrame(0);
        }

        scrubber.addEventListener('input', (e) => {
            currentTime = parseFloat(e.target.value);
            resetStateTriggers();
            if (currentTime < 14500) {
                dynamicTaskWrapper.innerHTML = "";
                statTotalTasks.innerText = "1";
                statPendingTasks.innerText = "1";
            }
            renderFrame(currentTime);
        });

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
                document.getElementById('fsIcon').className = "fa-solid fa-compress text-[10px]";
            } else {
                document.exitFullscreen();
                document.getElementById('fsIcon').className = "fa-solid fa-expand text-[10px]";
            }
        }
    </script>
</body>
</html>

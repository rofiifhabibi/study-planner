<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study — Study Planner</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        * { scroll-behavior: smooth; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .noise { background-image: radial-gradient(rgba(91, 23, 68, 0.05) 1px, transparent 1px); background-size: 20px 20px; }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.1); color: #ffffff; }
        .sidebar-item.active { background-color: rgba(255, 255, 255, 0.18); color: #ffffff; font-weight: 600; }
        #messages-wrap::-webkit-scrollbar { width: 6px; }
        #messages-wrap::-webkit-scrollbar-thumb { background: #d3c2ca; border-radius: 4px; }
        .typing-dots span { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #9ca3af; margin: 0 2px; animation: bounce 1.4s infinite ease-in-out both; }
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        .typing-dots span:nth-child(3) { animation-delay: 0s; }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; } 40% { transform: scale(1); opacity: 1; } }

        @keyframes tourGuideGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(163, 56, 120, 0.75), 0 2px 4px rgba(0, 0, 0, 0.08);
                border-color: #5B1744;
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(163, 56, 120, 0), 0 4px 14px rgba(91, 23, 68, 0.3);
                border-color: #A33878;
                transform: scale(1.05);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(163, 56, 120, 0), 0 2px 4px rgba(0, 0, 0, 0.08);
                border-color: #5B1744;
                transform: scale(1);
            }
        }

        .tour-guide-glowing {
            animation: tourGuideGlow 1.6s infinite ease-in-out !important;
            background-color: #F4E7EF !important;
            color: #5B1744 !important;
            border-color: #5B1744 !important;
        }

        .msg-fade {
            animation: msgFade .5s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        @keyframes msgFade {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .ai-stream-cursor {
            display: inline-block;
            width: 2.5px;
            height: 1.15em;
            background-color: #5B1744;
            margin-left: 2px;
            vertical-align: -0.12em;
            border-radius: 1px;
            animation: streamBlink 0.7s infinite ease-in-out;
        }
        @keyframes streamBlink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .ai-content p { margin: 0 0 0.85rem 0; line-height: 1.75; }
        .ai-content p:last-child { margin-bottom: 0; }
        .ai-content ul, .ai-content ol { margin: 0.5rem 0; padding-left: 1.25rem; }
        .ai-content ul { list-style-type: disc; }
        .ai-content ol { list-style-type: decimal; }
        .ai-content li { margin: 0.25rem 0; line-height: 1.5; }
        .ai-content li > p { margin: 0; }
        .ai-content strong { font-weight: 700; }
        .ai-content em { font-style: italic; }
        .ai-content code { background: #f4e7ef; color: #5B1744; padding: 0.15rem 0.4rem; border-radius: 0.375rem; font-size: 0.8em; }
        .ai-content pre { background: #1e1e2e; color: #cdd6f4; padding: 1rem; border-radius: 0.75rem; overflow-x: auto; margin: 0.5rem 0; font-size: 0.8rem; line-height: 1.5; }
        .ai-content pre code { background: none; color: inherit; padding: 0; font-size: 0.8rem; }
        .ai-content h1, .ai-content h2, .ai-content h3 { font-weight: 700; margin: 0.75rem 0 0.5rem 0; }
        .ai-content h1 { font-size: 1.1em; }
        .ai-content h2 { font-size: 1.05em; }
        .ai-content h3 { font-size: 1em; }
        .ai-content blockquote { border-left: 3px solid #d3c2ca; padding-left: 0.75rem; margin: 0.5rem 0; color: #6b7280; font-style: italic; }
        .ai-content table { border-collapse: collapse; width: 100%; margin: 0.5rem 0; font-size: 0.85em; }
        .ai-content th, .ai-content td { border: 1px solid #e5e7eb; padding: 0.4rem 0.6rem; text-align: left; }
        .ai-content th { background: #f9fafb; font-weight: 600; }
        .ai-content hr { border: none; border-top: 1px solid #e5e7eb; margin: 0.75rem 0; }
    </style>
</head>

<body class="bg-[#FAF6F0] text-[#241C21] antialiased">
    <div class="h-screen h-[100dvh] flex flex-row overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-50 h-screen h-[100dvh] w-[260px] bg-[#5B1744] text-white flex flex-col -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0">
            <div class="p-6 pb-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 overflow-hidden">
                        <img src="{{ asset('logo-chatgpt.png') }}" alt="Study Planner" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-bold text-base tracking-tight">Study Planner</div>
                        <div class="text-[9px] text-white/50 tracking-[.2em] font-semibold">PLAN · STUDY · GROW</div>
                    </div>
                </a>
            </div>

            <div class="px-4 flex-1 overflow-y-auto space-y-6">
                <div>
                    <button type="button" onclick="startNewDraft()" class="flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white cursor-pointer">
                        <i class="fa-solid fa-plus w-4 text-center"></i> New chat
                    </button>
                    <button type="button" onclick="showProjectCreateModal()" class="mt-2 flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white cursor-pointer">
                        <i class="fa-solid fa-folder-plus w-4 text-center"></i> New Project
                    </button>
                    <p class="px-3 mt-6 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">Recent Sessions</p>
                    <nav id="session-list"></nav>
                </div>
                <a href="/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 mt-auto text-white/70 hover:bg-white/10 rounded-xl text-xs">
                    <i class="fa-solid fa-house w-4 text-center"></i> Back to Dashboard
                </a>
            </div>

        <div class="p-4 border-t border-white/10">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#E7C8DB] text-[#5B1744] flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
                    </div>
                    <p class="font-semibold text-xs text-white truncate">{{ auth()->user()->name }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" title="Log out"
                        class="text-white/50 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10 cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Overlay for Mobile Drawer -->
    <div id="overlay" onclick="closeSidebar()" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40 hidden md:hidden"></div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="hideConfirmModal()"></div>
        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 shadow-2xl">
            <h3 id="confirmModalTitle" class="text-lg font-bold text-gray-900 mb-2">Hapus Sesi?</h3>
            <p id="confirmModalBody" class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus sesi percakapan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button type="button" onclick="hideConfirmModal()" class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold transition">Batal</button>
                <button type="button" id="confirmActionBtn" class="flex-1 py-2 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition">Hapus</button>
            </div>
        </div>
    </div>


    <!-- Project Name Modal -->
    <div id="projectModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" onclick="hideProjectModal()"></div>
        <form id="project-form" class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 shadow-2xl">
            <h3 id="projectModalTitle" class="text-lg font-bold text-gray-900 mb-1">Buat Proyek Baru</h3>
            <p class="text-sm text-gray-600 mb-4">Beri nama proyek untuk mengelompokkan sesi percakapan yang berbagi konteks.</p>
            <input type="text" id="project-name-input" placeholder="Contoh: Belajar Biologi" maxlength="100" autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:border-[#5B1744] focus:ring-2 focus:ring-[#5B1744]/20 mb-5">
            <div class="flex gap-3">
                <button type="button" onclick="hideProjectModal()" class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold transition">Batal</button>
                <button type="submit" id="projectSaveBtn" class="flex-1 py-2 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition">Simpan</button>
            </div>
        </form>
    </div>

    <!-- MAIN -->
    <main class="flex-1 flex flex-col h-screen h-[100dvh] overflow-hidden">
        <header class="h-16 bg-[#FAF6F0]/90 backdrop-blur-md border-b border-[#5B1744]/10 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <button type="button" id="menuButton" onclick="openSidebar()" aria-label="Buka Menu Navigasi" 
                    data-tour-step="1" data-tour-title="Menu & Proyek Belajar" data-tour-desc="Buka menu navigasi untuk membuat chat baru, melihat riwayat sesi, atau mengelompokkan materi dalam Proyek Belajar."
                    class="md:hidden w-9 h-9 rounded-xl bg-[#5B1744] text-white flex items-center justify-center shadow-xs active:scale-95 transition cursor-pointer">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-base text-[#5B1744] tracking-tight">AI Study</span>
                    <span class="hidden sm:inline-block text-[10px] px-2 py-0.5 rounded-full bg-[#F4E7EF] text-[#5B1744] font-semibold border border-[#E7C8DB]">Asisten Belajar</span>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-2.5">
                {{-- Global Pomodoro Live Pill --}}
                <a href="{{ url('/progress') }}" id="global-pomo-pill" title="Kembali ke Pomodoro Timer"
                    class="hidden items-center gap-2 px-3 py-1.5 rounded-full bg-[#F4E7EF] border border-[#E7C8DB] text-[#5B1744] hover:bg-[#E7C8DB]/60 transition shadow-xs text-xs font-bold active:scale-95">
                    <span class="w-2 h-2 rounded-full bg-[#5B1744] animate-ping"></span>
                    <span id="global-pomo-time" class="font-mono font-bold text-xs">25:00</span>
                    <span id="global-pomo-mode" class="text-[10px] uppercase tracking-wider text-[#5B1744]/70 font-semibold hidden sm:inline">Fokus</span>
                </a>

                {{-- Panduan Button with Idle Pulse --}}
                <button type="button" onclick="startFeatureTour()" id="tour-guide-btn" title="Panduan Fitur AI Study"
                    class="relative flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-full bg-white border border-gray-200 text-[#5B1744] hover:bg-[#F4E7EF] hover:border-[#E7C8DB] transition shadow-xs text-xs font-bold active:scale-95 cursor-pointer">
                    <span id="tour-guide-ping" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-[#5B1744] ring-2 ring-white animate-ping hidden"></span>
                    <span id="tour-guide-dot" class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-[#5B1744] ring-2 ring-white hidden"></span>
                    <i class="fa-regular fa-circle-question text-sm text-[#5B1744]"></i>
                    <span>Panduan</span>
                </button>

                <button type="button" id="header-notif-btn" onclick="toggleTimerNotificationMode()" title="Pengaturan Notifikasi & Nada Dering Timer"
                    class="relative w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:border-[#5B1744]/30 transition shadow-xs cursor-pointer active:scale-95">
                    <i id="header-notif-icon" class="fa-solid fa-bell text-xs"></i>
                    <span id="header-notif-dot" class="absolute top-2 right-2 w-2 h-2 rounded-full bg-[#5B1744]"></span>
                </button>

                <a href="{{ route('dashboard') }}" title="Kembali ke Dashboard"
                    class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#5B1744] hover:border-[#5B1744]/30 transition shadow-xs">
                    <i class="fa-solid fa-house text-xs"></i>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" title="Log out"
                        class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#B91C1C] hover:border-[#B91C1C]/30 transition shadow-xs cursor-pointer">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </header>

        <div class="flex-1 flex flex-col relative min-h-0" id="main">
            <div class="messages-wrap flex-1 overflow-y-auto min-h-0" id="messages-wrap">
                <div class="messages max-w-3xl mx-auto p-4 sm:p-6 pb-48 sm:pb-56 md:pb-64" id="messages">
                    <div class="text-center mt-8 sm:mt-12 max-w-lg mx-auto" id="welcome-state"
                        data-tour-step="2" data-tour-title="Template Pertanyaan Instan" data-tour-desc="Klik template pertanyaan ini untuk langsung menanyakan tugas deadline, jadwal kelas, atau rencana belajar harian secara otomatis tanpa repot mengetik.">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-3.5 rounded-3xl bg-[#F4E7EF] border border-[#E7C8DB] text-[#5B1744] flex items-center justify-center text-xl sm:text-2xl shadow-xs">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <h2 class="text-xl sm:text-3xl font-bold mb-1.5 text-gray-900 tracking-tight">Mau belajar apa hari ini?</h2>
                        <p class="text-xs sm:text-sm text-gray-500 mb-5 sm:mb-6">Tanyakan tugas, buat atau hapus jadwal kelas, kelola catatan materi belajarmu.</p>

                        <!-- Quick prompt pills -->
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <button type="button" onclick="setQuickPrompt('Ada tugas apa saja yang belum selesai dan kapan deadlinenya?')" 
                                class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs cursor-pointer">
                                <i class="fa-regular fa-circle-check mr-1.5 text-[11px]"></i>Cek tugas & deadline
                            </button>
                            <button type="button" onclick="setQuickPrompt('Apa jadwal kelasku hari ini dan besok?')" 
                                class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-graduation-cap mr-1.5 text-[11px]"></i>Cek jadwal kelas
                            </button>
                            <button type="button" onclick="setQuickPrompt('Bantu buatkan jadwal belajar malam ini yang efektif')" 
                                class="px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs cursor-pointer">
                                <i class="fa-regular fa-clock mr-1.5 text-[11px]"></i>Buat jadwal belajar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Floating Input Bar (Elevated with comfortable breathing room from bottom) --}}
            <div class="absolute bottom-8 sm:bottom-10 md:bottom-12 inset-x-0 z-20 px-4 sm:px-6 pointer-events-none pb-[max(0.5rem,env(safe-area-inset-bottom,0px))]">
                <form id="chat-form" onsubmit="handleChatInput(event)" class="max-w-3xl mx-auto pointer-events-auto">
                    <div id="editing-indicator" class="hidden mb-2 flex items-center justify-between bg-white text-[#5B1744] text-xs font-semibold px-4 py-2.5 rounded-2xl shadow-lg border border-[#E7C8DB]">
                        <span><i class="fa-solid fa-pen mr-2"></i>Mengedit pesan</span>
                        <button type="button" onclick="stopEditing()" class="hover:underline text-gray-500 cursor-pointer">Batal edit</button>
                    </div>
                    <div id="input-shell" 
                        data-tour-step="3" data-tour-title="Tanya AI Seputar Belajar" data-tour-desc="Ketik pertanyaan atau perintah di sini. AI Study terhubung langsung ke jadwal kelas, tugas sekolah, dan catatan pribadimu! Kamu bisa minta AI membuat ataupun menghapus tugas, jadwal, dan catatan secara otomatis."
                        class="flex items-end sm:items-center gap-2.5 sm:gap-3 bg-white p-2 sm:p-2.5 pl-3.5 sm:pl-4 rounded-3xl border-2 border-[#E7C8DB] shadow-xl shadow-[#5B1744]/10 focus-within:border-[#5B1744] focus-within:ring-2 focus-within:ring-[#5B1744]/15 transition-all">
                        <textarea id="chat-input" rows="1" placeholder="Tanyakan tugas, buat/hapus jadwal kelas, kelola catatan..." oninput="autoResize(this);toggleSend()" class="flex-1 bg-transparent border-none focus:ring-0 py-1.5 px-1 resize-none outline-none text-xs sm:text-sm md:text-base leading-relaxed text-gray-800 placeholder:text-gray-400 max-h-40 [overflow-wrap:anywhere]"></textarea>
                        <button type="submit" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-[#5B1744] hover:bg-[#481236] text-white flex items-center justify-center disabled:opacity-40 transition active:scale-95 shadow-md shadow-[#5B1744]/25 shrink-0 cursor-pointer" id="btn-send" disabled title="Kirim Pesan">
                            <i class="fa-solid fa-paper-plane text-xs sm:text-sm"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    </div>

    <script>
        const API_BASE = '{{ url("/api") }}';

        // Mobile drawer
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function openSidebar() {
            sidebar?.classList.remove('-translate-x-full');
            overlay?.classList.remove('hidden');
        }

        document.getElementById('menuButton')?.addEventListener('click', openSidebar);

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }

        overlay?.addEventListener('click', closeSidebar);

        sidebar?.addEventListener('click', (event) => {
            if (window.innerWidth < 768 && event.target.closest('a, button')) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && window.innerWidth < 768) {
                closeSidebar();
            }
        });
        let currentSessionId = new URLSearchParams(window.location.search).get('session');
        let editingMessageId = null;
        let sessionToDelete = null;
        let actionType = null;
        let pendingController = null;
        let draftParentId = null;
        const messageContents = {};
        const expandedProjects = {};

        function setQuickPrompt(text) {
            const input = document.getElementById('chat-input');
            if (!input) return;
            input.value = text;
            autoResize(input);
            toggleSend();
            input.focus();
        }

        function showWelcomeState(hintText = null) {
            const c = document.getElementById('messages');
            c.innerHTML = `
                <div class="text-center mt-12 sm:mt-16 max-w-lg mx-auto" id="welcome-state">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-3xl bg-[#F4E7EF] border border-[#E7C8DB] text-[#5B1744] flex items-center justify-center text-2xl shadow-xs">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold mb-2 text-gray-900 tracking-tight">Mau belajar apa hari ini?</h2>
                    ${hintText ? `<p class="text-xs text-[#5B1744] font-semibold mb-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F4E7EF] border border-[#E7C8DB]"><i class="fa-solid fa-folder text-[11px]"></i>${hintText}</p>` : ''}
                    <p class="text-sm text-gray-500 mb-6">Tanyakan apa saja seputar tugas, jadwal kelas, atau materi pelajaranmu.</p>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        <button type="button" onclick="setQuickPrompt('Ada tugas apa saja yang belum selesai dan kapan deadlinenya?')" 
                            class="px-3.5 py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs">
                            <i class="fa-regular fa-circle-check mr-1.5 text-[11px]"></i>Cek tugas & deadline
                        </button>
                        <button type="button" onclick="setQuickPrompt('Apa jadwal kelasku hari ini dan besok?')" 
                            class="px-3.5 py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs">
                            <i class="fa-solid fa-graduation-cap mr-1.5 text-[11px]"></i>Cek jadwal kelas
                        </button>
                        <button type="button" onclick="setQuickPrompt('Bantu buatkan jadwal belajar malam ini yang efektif')" 
                            class="px-3.5 py-2 rounded-2xl bg-white hover:bg-[#F4E7EF] border border-[#E7C8DB] text-xs font-semibold text-[#5B1744] transition shadow-2xs">
                            <i class="fa-regular fa-clock mr-1.5 text-[11px]"></i>Buat jadwal belajar
                        </button>
                    </div>
                </div>
            `;
        }

        function scrollToBottom() {
            const wrap = document.getElementById('messages-wrap');
            const jump = () => wrap.scrollTo({ top: wrap.scrollHeight, behavior: 'instant' });
            jump();
            requestAnimationFrame(jump);
            setTimeout(jump, 100);
            setTimeout(jump, 300);
            setTimeout(jump, 500);
        }

        function showConfirmModal(id, type) {
            sessionToDelete = id;
            actionType = type;
            const title = document.getElementById('confirmModalTitle');
            const body = document.getElementById('confirmModalBody');
            const actionBtn = document.getElementById('confirmActionBtn');

            if (type === 'deleteSession') {
                title.innerText = 'Hapus Sesi?';
                body.innerText = 'Apakah Anda yakin ingin menghapus sesi percakapan ini? Tindakan ini tidak dapat dibatalkan.';
                actionBtn.innerText = 'Hapus';
            }

            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        function hideConfirmModal() {
            sessionToDelete = null;
            actionType = null;
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }
        document.getElementById('confirmActionBtn').addEventListener('click', async () => {
            if (actionType === 'deleteSession') await deleteSessionConfirmed();
        });
        async function deleteSessionConfirmed() {
            if (!sessionToDelete) return;
            const actionBtn = document.getElementById('confirmActionBtn');
            if (actionBtn.disabled) return;
            actionBtn.disabled = true;
            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions/${sessionToDelete}`, { method: 'DELETE' });
                if (!res.ok) throw new Error('Gagal menghapus sesi.');
                const deletedId = sessionToDelete;
                if (currentSessionId === deletedId) {
                    currentSessionId = null;
                    draftParentId = null;
                    window.history.pushState(null, '', '{{ route('chat') }}');
                    showWelcomeState();
                }
                loadSessions();
            } catch (e) {
                alert('Terjadi kesalahan saat menghapus sesi.');
            } finally {
                actionBtn.disabled = false;
                hideConfirmModal();
            }
        }
        let projectToRename = null;
        const currentSessions = {};

        function openProjectModal() {
            const m = document.getElementById('projectModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
            setTimeout(() => document.getElementById('project-name-input').focus(), 50);
        }

        function hideProjectModal() {
            projectToRename = null;
            const m = document.getElementById('projectModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function showProjectCreateModal() {
            projectToRename = null;
            document.getElementById('projectModalTitle').innerText = 'Buat Proyek Baru';
            document.getElementById('project-name-input').value = '';
            document.getElementById('projectSaveBtn').innerText = 'Buat Proyek';
            openProjectModal();
        }

        function showProjectRenameModal(id, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const session = currentSessions[id];
            if (!session) return;
            projectToRename = id;
            document.getElementById('projectModalTitle').innerText = 'Ganti Nama Proyek';
            document.getElementById('project-name-input').value = session.title;
            document.getElementById('projectSaveBtn').innerText = 'Simpan';
            openProjectModal();
        }

        async function submitProjectModal() {
            const input = document.getElementById('project-name-input');
            const name = input.value.trim();
            if (!name) {
                input.focus();
                return;
            }
            const btn = document.getElementById('projectSaveBtn');
            if (btn.disabled) return;
            btn.disabled = true;
            try {
                if (projectToRename) {
                    const res = await apiFetch(`${API_BASE}/chat/sessions/${projectToRename}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ title: name }),
                    });
                    if (!res.ok) throw new Error('Gagal mengganti nama proyek.');
                    hideProjectModal();
                    loadSessions();
                } else {
                    const newId = await createSession(name, true);
                    if (!newId) {
                        alert('Gagal membuat sesi proyek baru.');
                    } else {
                        hideProjectModal();
                    }
                }
            } catch (e) {
                alert(projectToRename
                    ? 'Terjadi kesalahan saat mengganti nama proyek.'
                    : 'Terjadi kesalahan saat membuat sesi proyek baru.');
            } finally {
                btn.disabled = false;
            }
        }

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

        function toggleSend() {
            const input = document.getElementById('chat-input');
            const btn = document.getElementById('btn-send');
            btn.disabled = !input.value.trim();
        }

        function sessionRowHtml(s) {
            const isActive = s.id === currentSessionId;
            return `
                <div class="group flex items-center justify-between px-3.5 py-2.5 mb-2 rounded-xl text-xs cursor-pointer ${isActive ? 'active' : 'text-white/70 hover:bg-white/10'}" onclick="selectSession('${s.id}')">
                    <span class="flex items-center gap-3 overflow-hidden">
                        <i class="fa-solid fa-message w-4 text-center shrink-0"></i>
                        <span class="truncate">${s.title}</span>
                    </span>
                    <button type="button" title="Hapus sesi" class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 p-1.5 hover:bg-white/20 text-white/70 hover:text-red-400 rounded-lg shrink-0 transition" onclick="deleteSession('${s.id}', event)">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            `;
        }

        async function loadSessions() {
            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions`);
                const data = await res.json();
                const sessions = data.sessions || [];
                const list = document.getElementById('session-list');
                sessions.forEach(s => { currentSessions[s.id] = s; });
                const roots = sessions.filter(s => !s.parent_id);
                const byParent = {};
                sessions.forEach(s => {
                    if (s.parent_id) (byParent[s.parent_id] = byParent[s.parent_id] || []).push(s);
                });

                list.innerHTML = roots.map(s => {
                    if (!s.is_project) return sessionRowHtml(s);

                    const kids = byParent[s.id] || [];
                    const expanded = Boolean(expandedProjects[s.id]);
                    return `
                        <div class="mb-2">
                            <div class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs cursor-pointer ${s.id === currentSessionId ? 'active' : 'text-white/70 hover:bg-white/10'}" onclick="openProject('${s.id}')">
                                <span class="flex items-center gap-2 overflow-hidden">
                                    <i class="fa-solid fa-chevron-right w-2 text-[8px] transition-transform duration-150 ${expanded ? 'rotate-90' : ''}"></i>
                                    <i class="fa-solid fa-folder w-4 text-center shrink-0"></i>
                                    <span class="truncate font-semibold">${s.title}</span>
                                </span>
                                <span class="flex items-center gap-1 shrink-0 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                    <button type="button" title="Ganti nama proyek" class="p-1.5 hover:bg-white/20 text-white/80 hover:text-white rounded-lg transition" onclick="showProjectRenameModal('${s.id}', event)">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                    <button type="button" title="Sesi baru dalam proyek ini" class="p-1.5 hover:bg-white/20 text-white/80 hover:text-white rounded-lg transition" onclick="startNewDraft('${s.id}', event)">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                    <button type="button" title="Hapus proyek" class="p-1.5 hover:bg-white/20 text-white/70 hover:text-red-400 rounded-lg transition" onclick="deleteSession('${s.id}', event)">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </span>
                            </div>
                            <div class="${expanded ? '' : 'hidden'} ml-4 border-l border-white/15 pl-1 my-1 space-y-0.5">
                                ${kids.length ? kids.map(k => sessionRowHtml(k)).join('') : '<div class="px-3 py-1.5 text-[11px] text-white/40 italic">Belum ada sesi.</div>'}
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (e) {}
        }

        async function openProject(id) {
            expandedProjects[id] = true;
            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions`);
                const data = await res.json();
                const kids = (data.sessions || [])
                    .filter(s => s.parent_id === id)
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                await loadSessions();
                if (kids.length) {
                    await selectSession(kids[0].id);
                } else {
                    startNewDraft(id);
                }
            } catch (e) {}
        }

        function startNewDraft(projectId = null, event = null) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (pendingController) pendingController.abort();
            stopEditing();
            currentSessionId = null;
            draftParentId = projectId;
            if (projectId) expandedProjects[projectId] = true;
            window.history.pushState(null, '', '{{ route('chat') }}');
            const hint = projectId && currentSessions[projectId]
                ? `Sesi baru dalam proyek "${currentSessions[projectId].title}"`
                : null;
            showWelcomeState(hint);
            loadSessions();
        }

        async function selectSession(id) {
            if (pendingController) pendingController.abort();
            stopEditing();
            draftParentId = null;
            currentSessionId = id;
            window.history.pushState(null, '', `{{ route('chat') }}?session=${id}`);
            const ws = document.getElementById('welcome-state');
            if (ws) ws.remove();
            const c = document.getElementById('messages');
            c.innerHTML = '<div class="p-8 text-center text-gray-400">Loading...</div>';

            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions/${id}/messages`);
                const data = await res.json();
                if (data.session && data.session.parent_id) {
                    expandedProjects[data.session.parent_id] = true;
                }
                renderMsgs(data.messages || []);
                loadSessions();
            } catch (e) {
                c.innerHTML = '<div class="p-8"><div class="bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 text-sm text-center">Gagal memuat percakapan. Silakan coba lagi.</div></div>';
            }
        }

        function renderMsgs(msgs) {
            const c = document.getElementById('messages');
            if (!msgs.length) { c.innerHTML = '<div class="p-8 text-center text-gray-400 text-sm">Belum ada pesan.</div>'; return; }
            c.innerHTML = '';
            msgs.forEach(m => appendMessage(m.id, m.content, m.role, m.role === 'user' && !m.is_canceled, m.is_canceled));
            scrollToBottom();
        }

        function scrollToMessageTop(element) {
            const wrap = document.getElementById('messages-wrap');
            if (!element || !wrap) return;

            // Anchor so the user question or top of AI message is nicely visible near top
            const prevEl = element.previousElementSibling;
            const targetEl = (prevEl && prevEl.id && prevEl.id.startsWith('message-')) ? prevEl : element;

            const wrapRect = wrap.getBoundingClientRect();
            const elRect = targetEl.getBoundingClientRect();
            const targetScroll = wrap.scrollTop + (elRect.top - wrapRect.top) - 24;

            wrap.scrollTo({
                top: Math.max(0, targetScroll),
                behavior: 'smooth'
            });
        }

        function streamAssistantMessage(div, content, actionsHtml) {
            const c = document.getElementById('messages');
            const wrap = document.getElementById('messages-wrap');

            div.innerHTML = `
                <div class="ai-content leading-relaxed inline-block p-5 sm:p-6 rounded-3xl rounded-tl-xs bg-white border border-[#E7C8DB]/60 shadow-sm text-sm sm:text-base text-left max-w-full break-words [overflow-wrap:anywhere] text-gray-800 transition-all cursor-pointer" title="Klik untuk menampilkan seluruh pesan langsung">
                    <div class="ai-rendered-body"></div>
                </div>
                ${actionsHtml}
            `;
            c.appendChild(div);

            const bodyContainer = div.querySelector('.ai-rendered-body');
            const aiBubble = div.querySelector('.ai-content');

            // 1. Immediately position viewport to the START of this new response so user can read from line 1!
            scrollToMessageTop(div);

            const tokens = content.split(/(\s+)/);
            const totalTokens = tokens.length;
            let index = 0;
            let accumulated = '';
            let isFinished = false;
            let userScrolledManually = false;

            // Detect if user scrolls up away from bottom so we don't yank their reading view
            const onScroll = () => {
                const threshold = 160;
                const distanceToBottom = wrap.scrollHeight - wrap.scrollTop - wrap.clientHeight;
                if (distanceToBottom > threshold) {
                    userScrolledManually = true;
                } else {
                    userScrolledManually = false;
                }
            };
            wrap.addEventListener('scroll', onScroll, { passive: true });

            // Dynamically pace the streaming so it feels natural, smooth and comfortable to read
            let chunkSize = 1;
            let intervalMs = 28;

            if (totalTokens > 300) {
                chunkSize = 3;
                intervalMs = 20;
            } else if (totalTokens > 120) {
                chunkSize = 2;
                intervalMs = 24;
            }

            const finish = () => {
                if (isFinished) return;
                isFinished = true;
                clearInterval(streamTimer);
                wrap.removeEventListener('scroll', onScroll);
                bodyContainer.innerHTML = marked.parse(content);
                if (aiBubble) {
                    aiBubble.classList.remove('cursor-pointer');
                    aiBubble.removeAttribute('title');
                    aiBubble.onclick = null;
                }
            };

            // Allow instant skip on click
            if (aiBubble) {
                aiBubble.onclick = finish;
            }

            const streamTimer = setInterval(() => {
                if (index >= totalTokens) {
                    finish();
                    return;
                }

                for (let i = 0; i < chunkSize && index < totalTokens; i++) {
                    accumulated += tokens[index++];
                }

                bodyContainer.innerHTML = marked.parse(accumulated) + '<span class="ai-stream-cursor"></span>';

                // If message expands beyond viewport, gently follow down WITHOUT jumping past unread text
                if (!userScrolledManually) {
                    const wrapRect = wrap.getBoundingClientRect();
                    const divRect = div.getBoundingClientRect();
                    const safeBottomMargin = 140; // buffer above floating input bar
                    if (divRect.bottom > wrapRect.bottom - safeBottomMargin) {
                        const overflow = divRect.bottom - (wrapRect.bottom - safeBottomMargin);
                        wrap.scrollBy({ top: overflow, behavior: 'smooth' });
                    }
                }
            }, intervalMs);
        }

        function appendMessage(id, content, role, canEdit = false, isCanceled = false, isPending = false, animate = false) {
            const c = document.getElementById('messages');
            const div = document.createElement('div');
            if (id !== null && id !== undefined) div.id = `message-${id}`;
            div.className = `msg-fade mb-5 sm:mb-6 ${role === 'user' ? 'text-right' : 'text-left'}`;

            let messageContent = content;
            let actionsHtml = '';

            if (isCanceled) {
                messageContent = 'Anda membatalkan pesan.';
            } else if (role === 'user' && isPending) {
                actionsHtml = `
                    <div class="message-actions mt-1.5 text-right">
                        <button type="button" onclick="cancelPendingMessage('${id}')" class="text-xs text-red-500 hover:underline font-semibold">Batalkan</button>
                    </div>
                `;
            } else if (role === 'user' && canEdit) {
                if (id !== null && id !== undefined) messageContents[String(id)] = content;
                actionsHtml = `
                    <div class="message-actions mt-1.5 text-right">
                        <button type="button" onclick="editMessage('${id}')" class="text-xs text-gray-400 hover:text-[#5B1744] hover:underline font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-pen text-[10px]"></i>Edit</button>
                    </div>
                `;
            }

            if (role === 'assistant' && animate) {
                streamAssistantMessage(div, messageContent, actionsHtml);
            } else {
                const rendered = role === 'assistant'
                    ? `<div class="ai-content leading-relaxed inline-block p-5 sm:p-6 rounded-3xl rounded-tl-xs bg-white border border-[#E7C8DB]/60 shadow-sm text-sm sm:text-base text-left max-w-full break-words [overflow-wrap:anywhere] text-gray-800">${marked.parse(messageContent)}</div>`
                    : `<div class="inline-block px-5 py-3.5 rounded-3xl rounded-tr-xs bg-[#5B1744] text-white text-sm sm:text-base leading-relaxed text-left max-w-[85%] break-words [overflow-wrap:anywhere] shadow-xs">${messageContent}</div>`;
                div.innerHTML = rendered + actionsHtml;
                c.appendChild(div);
                scrollToBottom();
            }
        }

        function appendLoadingMessage() {
            const c = document.getElementById('messages');
            const div = document.createElement('div');
            div.id = 'loading-message';
            div.className = 'mb-5 sm:mb-6 text-left';
            div.innerHTML = `
                <div class="inline-block p-4 sm:p-5 rounded-3xl rounded-tl-xs bg-white border border-[#E7C8DB]/60 shadow-sm">
                    <div class="typing-dots flex items-center gap-1">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            c.appendChild(div);
            scrollToBottom();
        }

        function removeLoadingMessage() {
            const loading = document.getElementById('loading-message');
            if (loading) loading.remove();
        }

        async function createSession(title = null, isProject = false, parentId = null, openAfter = true) {
            try {
                const body = {};
                if (title) body.title = title;
                body.is_project = isProject;
                if (parentId) body.parent_id = parentId;
                const res = await apiFetch(`${API_BASE}/chat/session`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    currentSessionId = data.session.id;
                    if (openAfter) {
                        await selectSession(data.session.id);
                    }
                    return data.session.id;
                }
            } catch (e) {}
            return null;
        }

        async         function deleteSession(id, event) {
            event.preventDefault();
            event.stopPropagation();
            showConfirmModal(id, 'deleteSession');
        }

        document.getElementById('project-form').addEventListener('submit', (e) => {
            e.preventDefault();
            submitProjectModal();
        });

        document.getElementById('project-name-input').addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                hideProjectModal();
                document.getElementById('chat-input').focus();
            }
        });

        function editMessage(messageId) {
            const content = messageContents[String(messageId)];
            if (typeof content !== 'string') return;
            editingMessageId = messageId;
            const input = document.getElementById('chat-input');
            input.value = content;
            autoResize(input);
            toggleSend();
            document.getElementById('editing-indicator').classList.remove('hidden');
            input.focus();
        }

        function stopEditing() {
            editingMessageId = null;
            const input = document.getElementById('chat-input');
            input.value = '';
            autoResize(input);
            toggleSend();
            document.getElementById('editing-indicator').classList.add('hidden');
        }

        async function cancelPendingMessage(tempId) {
            if (!pendingController) return;
            pendingController.abort();
        }

        function handleChatInput(e) {
            e.preventDefault();
            sendMessage();
        }

        document.getElementById('chat-input').addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                handleChatInput(event);
            }
            if (event.key === 'Escape' && editingMessageId) {
                stopEditing();
            }
        });

        async function sendMessage() {
            const input = document.getElementById('chat-input');
            const msg = input.value.trim();
            if (!msg) return;

            const userMsg = msg;
            const wasEditing = Boolean(editingMessageId);
            const userMessageId = editingMessageId || `temp-${Date.now()}`;

            if (!wasEditing) {
                appendMessage(userMessageId, userMsg, 'user', false, false, true);
            }

            input.value = '';
            autoResize(input);
            input.disabled = true;
            toggleSend();

            if (wasEditing) {
                document.getElementById('editing-indicator').classList.add('hidden');
                editingMessageId = null;
            }

            appendLoadingMessage();
            pendingController = new AbortController();

            try {
                if (!currentSessionId) {
                    const title = userMsg.length > 50 ? userMsg.substring(0, 50) + '...' : userMsg;
                    const newId = await createSession(title, false, draftParentId, false);
                    if (!newId) {
                        removeLoadingMessage();
                        removeMessageElement(userMessageId);
                        appendMessage(null, 'Gagal membuat sesi baru.', 'assistant');
                        return;
                    }
                    draftParentId = null;
                    window.history.pushState(null, '', `{{ route('chat') }}?session=${newId}`);
                    document.getElementById('welcome-state')?.remove();
                    loadSessions();
                }

                let url = `${API_BASE}/chat/send`;
                let method = 'POST';

                if (wasEditing) {
                    url = `${API_BASE}/chat/messages/${userMessageId}`;
                    method = 'PUT';
                }

                const res = await apiFetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: userMsg, chat_session_id: currentSessionId }),
                    signal: pendingController.signal
                });
                const data = await res.json();
                removeLoadingMessage();

                if (data.status === 'success') {
                    if (data.pomo_state) {
                        if (data.pomo_state.action === 'start') {
                            localStorage.setItem('study_planner_pomo_state', JSON.stringify(data.pomo_state.state));
                        } else if (data.pomo_state.action === 'stop') {
                            localStorage.removeItem('study_planner_pomo_state');
                        }
                        if (typeof updateGlobalPomoPill === 'function') {
                            updateGlobalPomoPill();
                        }
                    }
                    removeMessageElement(userMessageId);
                    appendMessage(data.message_id || userMessageId, userMsg, 'user', true);
                    appendMessage(`reply-${Date.now()}`, data.reply, 'assistant', false, false, false, true);
                } else if (wasEditing) {
                    appendMessage(userMessageId, messageContents[String(userMessageId)] || userMsg, 'user', true);
                    appendMessage(null, data.message || 'Maaf, terjadi kesalahan saat memproses pesan.', 'assistant');
                } else {
                    removeMessageElement(userMessageId);
                    appendMessage(null, data.message || 'Maaf, terjadi kesalahan saat memproses pesan.', 'assistant');
                }
            } catch (e) {
                removeLoadingMessage();
                if (e.name === 'AbortError') {
                    removeMessageElement(userMessageId);
                    appendMessage(null, null, 'user', false, true);
                } else if (wasEditing) {
                    appendMessage(userMessageId, messageContents[String(userMessageId)] || userMsg, 'user', true);
                } else {
                    removeMessageElement(userMessageId);
                    appendMessage(null, 'Gagal terhubung ke AI.', 'assistant');
                }
            } finally {
                pendingController = null;
                input.disabled = false;
                input.focus();
            }
        }

        function removeMessageElement(id) {
            const el = document.getElementById(`message-${id}`);
            if (el) el.remove();
        }

        function autoResize(t) {
            t.style.height = 'auto';
            const h = Math.min(t.scrollHeight, 160);
            t.style.height = h + 'px';
            const shell = document.getElementById('input-shell');
            const isTall = h > 44;
            shell.classList.toggle('rounded-full', !isTall);
            shell.classList.toggle('rounded-3xl', isTall);
            shell.classList.toggle('items-center', !isTall);
            shell.classList.toggle('items-end', isTall);
        }

        marked.setOptions({ breaks: true, gfm: true });

        loadSessions();

        if (currentSessionId) {
            selectSession(currentSessionId);
        }

        // ==========================================
        // ROBUST WEB AUDIO API SOUND ENGINE & ALARMS
        // ==========================================
        function withStudyAudioContext(callback) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                if (!window.__studyAudioCtx) {
                    window.__studyAudioCtx = new AudioCtx();
                }
                const ctx = window.__studyAudioCtx;
                if (ctx.state === 'suspended') {
                    ctx.resume().then(() => {
                        if (callback) callback(ctx);
                    }).catch(() => {
                        if (callback) callback(ctx);
                    });
                } else {
                    if (callback) callback(ctx);
                }
            } catch (e) {
                console.warn('AudioContext error:', e);
            }
        }

        // Global unlock on first user gesture anywhere
        const _unlockChatAudioOnInteraction = () => {
            withStudyAudioContext(ctx => {
                try {
                    const buf = ctx.createBuffer(1, 1, 22050);
                    const src = ctx.createBufferSource();
                    src.buffer = buf;
                    src.connect(ctx.destination);
                    src.start(0);
                } catch (e) {}
            });
            window.removeEventListener('pointerdown', _unlockChatAudioOnInteraction);
            window.removeEventListener('keydown', _unlockChatAudioOnInteraction);
        };
        window.addEventListener('pointerdown', _unlockChatAudioOnInteraction, { passive: true, once: true });
        window.addEventListener('keydown', _unlockChatAudioOnInteraction, { passive: true, once: true });

        function playTimerAlarmChime() {
            withStudyAudioContext(ctx => {
                const burstOffsets = [0.0, 0.85, 1.7];
                const melody = [
                    { f: 659.25, time: 0.00, dur: 0.22 },
                    { f: 830.61, time: 0.12, dur: 0.22 },
                    { f: 987.77, time: 0.24, dur: 0.25 },
                    { f: 1318.51, time: 0.38, dur: 0.50 }
                ];
                burstOffsets.forEach(burstTime => {
                    melody.forEach(note => {
                        const start = ctx.currentTime + burstTime + note.time;
                        const stop = start + note.dur;
                        const osc1 = ctx.createOscillator();
                        const osc2 = ctx.createOscillator();
                        const gain = ctx.createGain();

                        osc1.type = 'sine'; osc1.frequency.setValueAtTime(note.f, start);
                        osc2.type = 'triangle'; osc2.frequency.setValueAtTime(note.f * 2, start);

                        gain.gain.setValueAtTime(0.001, start);
                        gain.gain.exponentialRampToValueAtTime(0.75, start + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, stop);

                        osc1.connect(gain); osc2.connect(gain); gain.connect(ctx.destination);
                        osc1.start(start); osc2.start(start);
                        osc1.stop(stop); osc2.stop(stop);
                    });
                });
            });
        }
        window.playTimerAlarmChime = playTimerAlarmChime;

        function playTourOpenSound() {
            withStudyAudioContext(ctx => {
                const notes = [
                    { f: 523.25, time: 0.00, dur: 0.18 },
                    { f: 659.25, time: 0.07, dur: 0.20 },
                    { f: 783.99, time: 0.14, dur: 0.22 },
                    { f: 1046.50, time: 0.21, dur: 0.35 }
                ];
                notes.forEach(n => {
                    const start = ctx.currentTime + n.time;
                    const stop = start + n.dur;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine'; osc.frequency.setValueAtTime(n.f, start);
                    gain.gain.setValueAtTime(0.001, start);
                    gain.gain.exponentialRampToValueAtTime(0.45, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, stop);
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.start(start); osc.stop(stop);
                });
            });
        }
        window.playTourOpenSound = playTourOpenSound;

        function playTourStepSound() {
            withStudyAudioContext(ctx => {
                const start = ctx.currentTime;
                const stop = start + 0.08;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(580, start);
                osc.frequency.exponentialRampToValueAtTime(950, stop);
                gain.gain.setValueAtTime(0.001, start);
                gain.gain.exponentialRampToValueAtTime(0.35, start + 0.01);
                gain.gain.exponentialRampToValueAtTime(0.0001, stop);
                osc.connect(gain); gain.connect(ctx.destination);
                osc.start(start); osc.stop(stop);
            });
        }
        window.playTourStepSound = playTourStepSound;

        function playTourCompleteSound() {
            withStudyAudioContext(ctx => {
                const notes = [
                    { f: 783.99, time: 0.00, dur: 0.22 },
                    { f: 1046.50, time: 0.09, dur: 0.25 },
                    { f: 1318.51, time: 0.18, dur: 0.40 }
                ];
                notes.forEach(n => {
                    const start = ctx.currentTime + n.time;
                    const stop = start + n.dur;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle'; osc.frequency.setValueAtTime(n.f, start);
                    gain.gain.setValueAtTime(0.001, start);
                    gain.gain.exponentialRampToValueAtTime(0.55, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, stop);
                    osc.connect(gain); gain.connect(ctx.destination);
                    osc.start(start); osc.stop(stop);
                });
            });
        }
        window.playTourCompleteSound = playTourCompleteSound;

        function getTimerSoundMode() {
            return localStorage.getItem('study_planner_sound_mode') || 'sound';
        }

        function updateNotificationButtonUI() {
            const btn = document.getElementById('header-notif-btn');
            const icon = document.getElementById('header-notif-icon');
            const dot = document.getElementById('header-notif-dot');
            if (!btn || !icon) return;

            const mode = getTimerSoundMode();
            if (mode === 'sound') {
                icon.className = 'fa-solid fa-bell text-xs text-[#5B1744]';
                if (dot) dot.classList.remove('hidden');
                btn.title = 'Nada Dering Timer Aktif (Klik untuk ubah ke mode Notifikasi Browser)';
                btn.classList.add('border-[#E7C8DB]', 'bg-[#F4E7EF]');
                btn.classList.remove('border-gray-200', 'bg-white');
            } else {
                icon.className = 'fa-regular fa-bell-slash text-xs text-gray-400';
                if (dot) dot.classList.add('hidden');
                btn.title = 'Hanya Notifikasi Browser (Klik untuk aktifkan Nada Dering)';
                btn.classList.remove('border-[#E7C8DB]', 'bg-[#F4E7EF]');
                btn.classList.add('border-gray-200', 'bg-white');
            }
        }

        function toggleTimerNotificationMode() {
            const currentMode = getTimerSoundMode();
            if (currentMode === 'sound') {
                localStorage.setItem('study_planner_sound_mode', 'browser');
                updateNotificationButtonUI();

                if ('Notification' in window) {
                    if (Notification.permission === 'granted') {
                        try {
                            new Notification('🔔 Study Planner - Notifikasi Browser Aktif', {
                                body: 'Nada dering dimatikan. Saat timer selesai, notifikasi browser ini yang akan muncul!',
                                icon: '{{ asset('logo-chatgpt.png') }}'
                            });
                        } catch (e) {}
                        showToast('🔕 Nada dering dinonaktifkan. Mode notifikasi browser aktif!', 'info');
                    } else if (Notification.permission !== 'denied') {
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                try {
                                    new Notification('🔔 Study Planner - Notifikasi Browser Aktif', {
                                        body: 'Izin diberikan! Notifikasi browser ini akan muncul saat timer selesai.',
                                        icon: '{{ asset('logo-chatgpt.png') }}'
                                    });
                                } catch (e) {}
                                showToast('🔕 Izin diberikan! Mode notifikasi browser aktif.', 'success');
                            } else {
                                showToast('⚠️ Izin notifikasi browser belum diizinkan.', 'info');
                            }
                        });
                    } else {
                        showToast('⚠️ Izin notifikasi browser diblokir di pengaturan browser Anda.', 'error');
                    }
                } else {
                    showToast('⚠️ Browser Anda tidak mendukung Web Notification.', 'info');
                }
            } else {
                localStorage.setItem('study_planner_sound_mode', 'sound');
                updateNotificationButtonUI();
                playTimerAlarmChime();
                showToast('🔔 Nada dering timer aktif! Memutar contoh suara alarm...', 'success');
            }
        }

        updateNotificationButtonUI();

        // ==========================================
        // INTERACTIVE SPOTLIGHT FEATURE TOUR & IDLE PULSE
        // ==========================================
        let tourSteps = [];
        let currentTourIndex = 0;
        let tourRafId = null;

        // Continuously track element position during smooth scroll and layout changes
        function syncTourPositionContinuously(durationMs = 650) {
            if (tourRafId) cancelAnimationFrame(tourRafId);
            const startTime = performance.now();
            function loop(currentTime) {
                const container = document.getElementById('tour-overlay-container');
                if (!container || container.classList.contains('hidden') || !tourSteps[currentTourIndex]) {
                    return;
                }
                positionTourSpotlight(tourSteps[currentTourIndex]);
                if (currentTime - startTime < durationMs) {
                    tourRafId = requestAnimationFrame(loop);
                }
            }
            tourRafId = requestAnimationFrame(loop);
        }

        function startFeatureTour() {
            resetIdleTimer();
            tourSteps = Array.from(document.querySelectorAll('[data-tour-step]'))
                .filter(el => {
                    const style = window.getComputedStyle(el);
                    return style.display !== 'none' && style.visibility !== 'hidden' && (el.offsetWidth > 0 || el.offsetHeight > 0);
                })
                .sort((a, b) => parseInt(a.getAttribute('data-tour-step')) - parseInt(b.getAttribute('data-tour-step')));

            if (tourSteps.length === 0) {
                return;
            }

            playTourOpenSound();

            currentTourIndex = 0;
            const container = document.getElementById('tour-overlay-container');
            if (container) {
                container.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            renderTourStep();
        }

        function positionTourSpotlight(el) {
            if (!el) return;
            const rect = el.getBoundingClientRect();
            const spotlight = document.getElementById('tour-spotlight-box');
            const card = document.getElementById('tour-card');
            if (!spotlight || !card) return;

            const vw = window.innerWidth;
            const vh = window.innerHeight;

            // 1. Spotlight Box Calculation (clamp within viewport with padding)
            const pad = 8;
            const spotTop = Math.max(0, rect.top - pad);
            const spotLeft = Math.max(0, rect.left - pad);
            const spotWidth = Math.min(vw - spotLeft, rect.width + pad * 2);
            const spotHeight = Math.min(vh - spotTop, rect.height + pad * 2);

            spotlight.style.top = `${spotTop}px`;
            spotlight.style.left = `${spotLeft}px`;
            spotlight.style.width = `${Math.max(20, spotWidth)}px`;
            spotlight.style.height = `${Math.max(20, spotHeight)}px`;

            // 2. Card Dimensions
            const cardWidth = Math.min(350, vw * 0.92);
            const cardHeight = card.offsetHeight || 210;

            // 3. Vertical Positioning: avoid overlapping target element
            const spaceBelow = vh - rect.bottom;
            const spaceAbove = rect.top;

            let cardTop;
            const isMobile = vw < 640;

            if (isMobile) {
                const elCenterY = rect.top + (rect.height / 2);
                if (elCenterY > vh / 2) {
                    cardTop = Math.max(12, Math.min(rect.top - cardHeight - 12, 16));
                } else {
                    cardTop = Math.min(vh - cardHeight - 16, Math.max(rect.bottom + 12, vh - cardHeight - 20));
                }
            } else {
                if (spaceBelow >= cardHeight + 16) {
                    cardTop = rect.bottom + 14;
                } else if (spaceAbove >= cardHeight + 16) {
                    cardTop = rect.top - cardHeight - 14;
                } else {
                    if (spaceBelow >= spaceAbove) {
                        cardTop = Math.min(vh - cardHeight - 12, rect.bottom + 12);
                    } else {
                        cardTop = Math.max(12, rect.top - cardHeight - 12);
                    }
                }
            }

            // 4. Horizontal Positioning
            let cardLeft = rect.left + (rect.width / 2) - (cardWidth / 2);
            cardLeft = Math.max(12, Math.min(vw - cardWidth - 12, cardLeft));

            card.style.top = `${Math.round(cardTop)}px`;
            card.style.left = `${Math.round(cardLeft)}px`;
            card.style.width = `${Math.round(cardWidth)}px`;
        }

        function renderTourStep() {
            if (currentTourIndex < 0 || currentTourIndex >= tourSteps.length) return;
            const el = tourSteps[currentTourIndex];
            const title = el.getAttribute('data-tour-title') || 'Panduan Fitur';
            const desc = el.getAttribute('data-tour-desc') || '';
            const total = tourSteps.length;

            const rect = el.getBoundingClientRect();
            const needsScroll = (rect.top < 60 || rect.bottom > window.innerHeight - 60);
            if (needsScroll) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
            }

            const counterEl = document.getElementById('tour-step-counter');
            if (counterEl) counterEl.textContent = `PANDUAN ${currentTourIndex + 1}/${total}`;
            const titleEl = document.getElementById('tour-title');
            if (titleEl) titleEl.textContent = title;
            const descEl = document.getElementById('tour-desc');
            if (descEl) descEl.textContent = desc;

            const dotsBox = document.getElementById('tour-dots');
            if (dotsBox) {
                dotsBox.innerHTML = '';
                for (let i = 0; i < total; i++) {
                    const dot = document.createElement('span');
                    dot.className = i === currentTourIndex 
                        ? 'w-2 h-2 rounded-full bg-[#E7C8DB] transition-all duration-300' 
                        : 'w-1.5 h-1.5 rounded-full bg-white/25 transition-all';
                    dotsBox.appendChild(dot);
                }
            }

            const prevBtn = document.getElementById('tour-prev-btn');
            if (prevBtn) prevBtn.disabled = (currentTourIndex === 0);

            const nextBtnText = document.getElementById('tour-next-btn-text');
            const nextBtnIcon = document.getElementById('tour-next-btn-icon');
            if (currentTourIndex === total - 1) {
                if (nextBtnText) nextBtnText.textContent = 'Selesai';
                if (nextBtnIcon) nextBtnIcon.className = 'fa-solid fa-check text-[10px]';
            } else {
                if (nextBtnText) nextBtnText.textContent = 'Berikutnya';
                if (nextBtnIcon) nextBtnIcon.className = 'fa-solid fa-arrow-right text-[10px]';
            }

            positionTourSpotlight(el);
            syncTourPositionContinuously(650);
        }

        function nextFeatureTourStep() {
            if (currentTourIndex < tourSteps.length - 1) {
                currentTourIndex++;
                playTourStepSound();
                renderTourStep();
            } else {
                playTourCompleteSound();
                endFeatureTour();
            }
        }

        function prevFeatureTourStep() {
            if (currentTourIndex > 0) {
                currentTourIndex--;
                playTourStepSound();
                renderTourStep();
            }
        }

        function endFeatureTour() {
            const container = document.getElementById('tour-overlay-container');
            if (container) container.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            if (tourRafId) cancelAnimationFrame(tourRafId);
            resetIdleTimer();
        }

        function handleTourBackdropClick(e) {
            if (e.target.id === 'tour-overlay-container') {
                endFeatureTour();
            }
        }

        window.addEventListener('resize', () => {
            const container = document.getElementById('tour-overlay-container');
            if (container && !container.classList.contains('hidden') && tourSteps[currentTourIndex]) {
                positionTourSpotlight(tourSteps[currentTourIndex]);
            }
        });

        window.addEventListener('scroll', () => {
            const container = document.getElementById('tour-overlay-container');
            if (container && !container.classList.contains('hidden') && tourSteps[currentTourIndex]) {
                positionTourSpotlight(tourSteps[currentTourIndex]);
            }
        }, { passive: true });

        document.addEventListener('keydown', (e) => {
            const container = document.getElementById('tour-overlay-container');
            if (container && !container.classList.contains('hidden')) {
                if (e.key === 'Escape') endFeatureTour();
                if (e.key === 'ArrowRight') nextFeatureTourStep();
                if (e.key === 'ArrowLeft') prevFeatureTourStep();
            }
        });

        // 10-Second Inactivity Detector: Panduan button glows/pulses when user is idle
        let idleGuideTimer = null;
        const IDLE_GUIDE_DELAY_MS = 10000; // 10 detik

        function resetIdleTimer() {
            const guideBtn = document.getElementById('tour-guide-btn');
            const pingEl = document.getElementById('tour-guide-ping');
            const dotEl = document.getElementById('tour-guide-dot');

            if (guideBtn && guideBtn.classList.contains('tour-guide-glowing')) {
                guideBtn.classList.remove('tour-guide-glowing');
                if (pingEl) pingEl.classList.add('hidden');
                if (dotEl) dotEl.classList.add('hidden');
            }

            if (idleGuideTimer) clearTimeout(idleGuideTimer);

            const container = document.getElementById('tour-overlay-container');
            if (container && !container.classList.contains('hidden')) {
                return;
            }

            idleGuideTimer = setTimeout(() => {
                const btn = document.getElementById('tour-guide-btn');
                const ping = document.getElementById('tour-guide-ping');
                const dot = document.getElementById('tour-guide-dot');
                if (btn) {
                    btn.classList.add('tour-guide-glowing');
                    if (ping) ping.classList.remove('hidden');
                    if (dot) dot.classList.remove('hidden');
                }
            }, IDLE_GUIDE_DELAY_MS);
        }

        ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll'].forEach(evt => {
            window.addEventListener(evt, resetIdleTimer, { passive: true });
        });

        function updateGlobalPomoPill() {
            const pill = document.getElementById('global-pomo-pill');
            const timeEl = document.getElementById('global-pomo-time');
            const modeEl = document.getElementById('global-pomo-mode');
            if (!pill || !timeEl) return;

            const raw = localStorage.getItem('study_planner_pomo_state');
            if (!raw) {
                pill.classList.add('hidden');
                pill.classList.remove('inline-flex');
                return;
            }

            try {
                const state = JSON.parse(raw);
                if (!state || (!state.isRunning && !state.targetEndTime)) {
                    pill.classList.add('hidden');
                    pill.classList.remove('inline-flex');
                    return;
                }

                if (state.isRunning && state.targetEndTime) {
                    const remaining = Math.max(0, Math.round((state.targetEndTime - Date.now()) / 1000));
                    const m = Math.floor(remaining / 60);
                    const s = remaining % 60;
                    timeEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                    if (modeEl) modeEl.textContent = state.mode === 'focus' ? 'Fokus' : 'Istirahat';
                    pill.classList.remove('hidden');
                    pill.classList.add('inline-flex');

                    if (remaining === 0 && state.isRunning) {
                        state.isRunning = false;
                        localStorage.setItem('study_planner_pomo_state', JSON.stringify(state));
                        playGlobalPomoAlarm();
                        alert('⏰ Waktu belajar selesai!');
                    }
                } else {
                    pill.classList.add('hidden');
                    pill.classList.remove('inline-flex');
                }
            } catch (e) {
                pill.classList.add('hidden');
                pill.classList.remove('inline-flex');
            }
        }

        function playGlobalPomoAlarm() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                if (ctx.state === 'suspended') ctx.resume();
                const burstOffsets = [0.0, 0.95, 1.9];
                const melody = [
                    { f: 659.25, time: 0.00, dur: 0.22 },
                    { f: 830.61, time: 0.12, dur: 0.22 },
                    { f: 987.77, time: 0.24, dur: 0.25 },
                    { f: 1318.51, time: 0.38, dur: 0.45 }
                ];
                burstOffsets.forEach(burstTime => {
                    melody.forEach(note => {
                        const start = ctx.currentTime + burstTime + note.time;
                        const stop = start + note.dur;
                        const osc1 = ctx.createOscillator();
                        const osc2 = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc1.type = 'sine'; osc1.frequency.setValueAtTime(note.f, start);
                        osc2.type = 'triangle'; osc2.frequency.setValueAtTime(note.f * 2, start);
                        gain.gain.setValueAtTime(0.001, start);
                        gain.gain.exponentialRampToValueAtTime(0.35, start + 0.02);
                        gain.gain.exponentialRampToValueAtTime(0.0001, stop);
                        osc1.connect(gain); osc2.connect(gain); gain.connect(ctx.destination);
                        osc1.start(start); osc2.start(start);
                        osc1.stop(stop); osc2.stop(stop);
                    });
                });
            } catch (e) {}
        }

        setInterval(updateGlobalPomoPill, 1000);

        document.addEventListener('DOMContentLoaded', () => {
            resetIdleTimer();
            updateGlobalPomoPill();
        });
    </script>

    {{-- FEATURE TOUR SPOTLIGHT & MODAL CARD --}}
    <div id="tour-overlay-container" class="fixed inset-0 z-[9990] hidden select-none" onclick="handleTourBackdropClick(event)">
        {{-- Spotlight Frame with Cutout Box Shadow --}}
        <div id="tour-spotlight-box" class="fixed pointer-events-none transition-all duration-300 ease-out z-[9992] rounded-2xl"
            style="box-shadow: 0 0 0 9999px rgba(35, 8, 26, 0.82); border: 2.5px solid #E7C8DB;"></div>

        {{-- Guide Card Modal in Brand Color Palette --}}
        <div id="tour-card" onclick="event.stopPropagation()" class="fixed z-[9999] w-[350px] max-w-[92vw] bg-[#481236] text-white rounded-3xl p-5 sm:p-6 shadow-2xl transition-all duration-300 ease-out border border-[#E7C8DB]/40">
            <div class="flex items-center gap-2.5 mb-2.5">
                <div class="w-8 h-8 rounded-xl bg-white/10 text-[#E7C8DB] flex items-center justify-center text-sm shadow-xs border border-white/15">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <span id="tour-badge" class="text-[10px] uppercase tracking-wider text-[#E7C8DB] font-extrabold flex items-center gap-1.5">
                        <span class="w-2.5 h-0.5 bg-[#E7C8DB] rounded-full inline-block"></span>
                        <span id="tour-step-counter">PANDUAN 1/3</span>
                    </span>
                </div>
            </div>

            <h4 id="tour-title" class="text-base sm:text-lg font-bold text-white mb-2 leading-snug">Judul Panduan</h4>
            <p id="tour-desc" class="text-xs sm:text-sm text-white/80 leading-relaxed mb-5 font-normal">Penjelasan fitur yang ringkas dan jelas.</p>

            <div class="flex items-center justify-between pt-3 border-t border-white/15">
                <div id="tour-dots" class="flex items-center gap-1.5"></div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="endFeatureTour()" class="text-xs font-semibold text-white/60 hover:text-white px-2 py-1.5 transition cursor-pointer">
                        Lewati
                    </button>
                    <button type="button" onclick="prevFeatureTourStep()" id="tour-prev-btn" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xs transition border border-white/15 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <button type="button" onclick="nextFeatureTourStep()" id="tour-next-btn" class="px-4 py-2 rounded-xl bg-[#E7C8DB] hover:bg-[#F4E7EF] text-[#5B1744] text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-black/20 active:scale-95 cursor-pointer">
                        <span id="tour-next-btn-text">Berikutnya</span>
                        <i id="tour-next-btn-icon" class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

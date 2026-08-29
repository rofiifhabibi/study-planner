<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Study — Study Planner</title>

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

        #mobile-float-burger {
            position: fixed;
            top: 12px;
            left: 12px;
            z-index: 50;
            display: none;
        }
        @media (max-width: 767px) {
            #mobile-float-burger {
                display: flex;
            }
        }

        .typewriter-cursor {
            display: inline-block;
            width: 2px;
            height: 1em;
            background: #5B1744;
            margin-left: 2px;
            vertical-align: text-bottom;
            animation: blink-cursor 0.8s step-end infinite;
        }
        @keyframes blink-cursor {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .ai-content p { margin: 0 0 0.5rem 0; line-height: 1.6; }
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
    <div class="h-screen flex flex-row overflow-hidden">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-50 h-screen w-[260px] bg-[#5B1744] text-white flex flex-col -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0">
            <div class="p-6 pb-8">
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white text-[#5B1744] flex items-center justify-center font-bold text-lg shadow-sm">S</div>
                    <div>
                        <div class="font-bold text-base tracking-tight">Study Planner</div>
                        <div class="text-[9px] text-white/50 tracking-[.2em] font-semibold">PLAN · STUDY · GROW</div>
                    </div>
                </a>
            </div>

            <div class="px-4 flex-1 overflow-y-auto space-y-6">
                <div>
                    <button type="button" onclick="startNewDraft()" class="flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white">
                        <i class="fa-solid fa-plus w-4 text-center"></i> New chat
                    </button>
                    <button type="button" onclick="showProjectCreateModal()" class="mt-2 flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white">
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
                        class="text-white/50 hover:text-white transition p-1.5 rounded-lg hover:bg-white/10">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Overlay for Mobile Drawer -->
    <div id="overlay" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-40 hidden md:hidden"></div>

    <!-- Floating Burger Button (Mobile) -->
    <button type="button" id="mobile-float-burger" onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('overlay').classList.remove('hidden');" class="md:hidden w-11 h-11 rounded-full bg-[#5B1744] text-white items-center justify-center shadow-lg shadow-[#5B1744]/30 active:scale-95 transition">
        <i class="fa-solid fa-bars text-sm"></i>
    </button>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>
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
    <div id="projectModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>
        <form id="project-form" class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 shadow-2xl">
            <h3 id="projectModalTitle" class="text-lg font-bold text-gray-900 mb-1">Buat Proyek Baru</h3>
            <p class="text-sm text-gray-600 mb-4">Beri nama proyek untuk mengelompokkan sesi percakapan yang berbagi konteks.</p>
            <input type="text" id="project-name-input" placeholder="Contoh: Belajar n8n" maxlength="100" autocomplete="off" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm outline-none focus:border-[#5B1744] focus:ring-2 focus:ring-[#5B1744]/20 mb-5">
            <div class="flex gap-3">
                <button type="button" onclick="hideProjectModal()" class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold transition">Batal</button>
                <button type="submit" id="projectSaveBtn" class="flex-1 py-2 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition">Simpan</button>
            </div>
        </form>
    </div>

    <!-- MAIN -->
        <main class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-16 bg-[#FAF6F0]/80 backdrop-blur-md border-b border-[#5B1744]/5 sticky top-0 z-30 px-6 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button" id="menuButton" class="md:hidden w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-700 shadow-xs">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                    <span class="font-bold text-base text-[#5B1744]">AI Study Companion</span>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" title="Log out"
                            class="w-9 h-9 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:text-[#B91C1C] hover:border-[#B91C1C]/30 transition shadow-xs">
                            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                        </button>
                    </form>
                </div>
            </header>

            <div class="flex-1 flex flex-col relative min-h-0" id="main">
                <div class="messages-wrap flex-1 overflow-y-auto min-h-0" id="messages-wrap">
                    <div class="messages max-w-3xl mx-auto p-6 pb-36" id="messages">
                        <div class="text-center mt-20" id="welcome-state">
                            <h2 class="text-3xl font-bold mb-3 text-gray-900">What's on the agenda today?</h2>
                            <p class="text-gray-500">Study Planner AI siap membantu Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="absolute bottom-10 inset-x-0 z-20 px-6 pointer-events-none">
                    <form id="chat-form" onsubmit="handleChatInput(event)" class="max-w-3xl mx-auto pointer-events-auto">
                        <div id="editing-indicator" class="hidden mb-2 flex items-center justify-between bg-white text-[#5B1744] text-xs font-semibold px-4 py-2 rounded-xl shadow-md">
                            <span><i class="fa-solid fa-pen mr-2"></i>Mengedit pesan</span>
                            <button type="button" onclick="stopEditing()" class="hover:underline">Batal edit</button>
                        </div>
                        <div id="input-shell" class="flex items-center gap-2 bg-white p-2 pl-4 rounded-full border border-[#5B1744]/10 shadow-xl shadow-[#5B1744]/15 focus-within:border-[#5B1744]/40 focus-within:shadow-2xl transition-shadow">
                            <textarea id="chat-input" rows="1" placeholder="Ask anything..." oninput="autoResize(this);toggleSend()" class="flex-1 bg-transparent border-none focus:ring-0 p-1 resize-none outline-none text-sm max-h-40 [overflow-wrap:anywhere]"></textarea>
                            <button type="submit" class="w-10 h-10 rounded-full bg-[#5B1744] text-white flex items-center justify-center disabled:opacity-50 shrink-0" id="btn-send" disabled>
                                <span class="material-symbols-outlined text-sm">send</span>
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

        document.getElementById('menuButton')?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });

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

        function showWelcomeState(hintText = null) {
            const c = document.getElementById('messages');
            c.innerHTML = `
                <div class="text-center mt-20" id="welcome-state">
                    <h2 class="text-3xl font-bold mb-3 text-gray-900">What's on the agenda today?</h2>
                    ${hintText ? `<p class="text-sm text-[#5B1744]/70 font-semibold mb-2"><i class="fa-solid fa-folder mr-1"></i>${hintText}</p>` : ''}
                    <p class="text-gray-500">Study Planner AI siap membantu Anda.</p>
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
                    <button type="button" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-white/20 rounded shrink-0" onclick="deleteSession('${s.id}', event)">
                        <i class="fa-solid fa-trash text-[10px]"></i>
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
                                <span class="flex items-center gap-1 shrink-0">
                                    <button type="button" title="Ganti nama proyek" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-white/20 rounded" onclick="showProjectRenameModal('${s.id}', event)">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </button>
                                    <button type="button" title="Sesi baru dalam proyek ini" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-white/20 rounded" onclick="startNewDraft('${s.id}', event)">
                                        <i class="fa-solid fa-plus text-[10px]"></i>
                                    </button>
                                    <button type="button" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-white/20 rounded" onclick="deleteSession('${s.id}', event)">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
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

        function appendMessage(id, content, role, canEdit = false, isCanceled = false, isPending = false, animate = false) {
            const c = document.getElementById('messages');
            const div = document.createElement('div');
            if (id !== null && id !== undefined) div.id = `message-${id}`;
            div.className = `mb-4 ${role === 'user' ? 'text-right' : 'text-left'}`;

            let messageContent = content;
            let actionsHtml = '';

            if (isCanceled) {
                messageContent = 'Anda membatalkan pesan.';
            } else if (role === 'user' && isPending) {
                actionsHtml = `
                    <div class="message-actions mt-1">
                        <button type="button" onclick="cancelPendingMessage('${id}')" class="text-xs text-red-500 hover:underline">Batalkan</button>
                    </div>
                `;
            } else if (role === 'user' && canEdit) {
                if (id !== null && id !== undefined) messageContents[String(id)] = content;
                actionsHtml = `
                    <div class="message-actions mt-1">
                        <button type="button" onclick="editMessage('${id}')" class="text-xs text-blue-500 hover:underline">Edit</button>
                    </div>
                `;
            }

            if (role === 'assistant' && animate && messageContent) {
                const bubble = document.createElement('div');
                bubble.className = 'ai-content inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm text-sm text-left max-w-full break-words [overflow-wrap:anywhere]';
                const cursor = document.createElement('span');
                cursor.className = 'typewriter-cursor';
                bubble.appendChild(cursor);
                div.appendChild(bubble);
                if (actionsHtml) {
                    const actionsWrap = document.createElement('div');
                    actionsWrap.innerHTML = actionsHtml;
                    div.appendChild(actionsWrap);
                }
                c.appendChild(div);
                scrollToBottom();
                typewriterEffect(bubble, cursor, messageContent);
            } else {
                const rendered = role === 'assistant'
                    ? `<div class="ai-content inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm text-sm text-left max-w-full break-words [overflow-wrap:anywhere]">${marked.parse(messageContent)}</div>`
                    : `<div class="inline-block p-4 rounded-2xl bg-[#5B1744] text-white text-sm text-left max-w-full break-words [overflow-wrap:anywhere]">${messageContent}</div>`;
                div.innerHTML = rendered + actionsHtml;
                c.appendChild(div);
                scrollToBottom();
            }
        }

        function typewriterEffect(container, cursor, fullText, index = 0) {
            const plainText = stripMarkdown(fullText);
            const chunkSize = 3;
            const delay = 18;
            if (index < plainText.length) {
                const end = Math.min(index + chunkSize, plainText.length);
                const textNode = document.createTextNode(plainText.substring(index, end));
                container.insertBefore(textNode, cursor);
                scrollToBottom();
                setTimeout(() => typewriterEffect(container, cursor, fullText, end), delay);
            } else {
                container.innerHTML = marked.parse(fullText);
                scrollToBottom();
            }
        }

        function stripMarkdown(md) {
            return md
                .replace(/^#{1,6}\s+/gm, '')
                .replace(/\*\*(.+?)\*\*/g, '$1')
                .replace(/\*(.+?)\*/g, '$1')
                .replace(/__(.+?)__/g, '$1')
                .replace(/_(.+?)_/g, '$1')
                .replace(/~~(.+?)~~/g, '$1')
                .replace(/`{3,}[\s\S]*?`{3,}/g, (m) => m.replace(/`{3,}\w*\n?/g, '').replace(/`{3,}/g, ''))
                .replace(/`(.+?)`/g, '$1')
                .replace(/^\s*[-*+]\s+/gm, '• ')
                .replace(/^\s*\d+\.\s+/gm, (m) => m)
                .replace(/^\s*>\s+/gm, '')
                .replace(/!\[.*?\]\(.*?\)/g, '')
                .replace(/\[(.+?)\]\(.*?\)/g, '$1')
                .replace(/---+/g, '')
                .replace(/\|/g, ' ')
                .replace(/\n{3,}/g, '\n\n')
                .trim();
        }

        function appendLoadingMessage() {
            const c = document.getElementById('messages');
            const div = document.createElement('div');
            div.id = 'loading-message';
            div.className = 'mb-4 text-left';
            div.innerHTML = `
                <div class="inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm">
                    <div class="typing-dots flex items-center gap-0">
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
    </script>
</body>
</html>

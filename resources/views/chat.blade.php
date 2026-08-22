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
    <div class="min-h-screen flex flex-row">
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
                    <button onclick="createSession()" class="flex items-center gap-3 w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 transition text-white">
                        <i class="fa-solid fa-plus w-4 text-center"></i> New chat
                    </button>
                    <p class="px-3 mt-6 mb-2 text-[10px] uppercase tracking-[.2em] text-white/40 font-bold">Recent Sessions</p>
                    <nav class="space-y-1" id="session-list"></nav>
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
            </div>
        </div>
    </aside>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>
        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Sesi?</h3>
            <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin menghapus sesi percakapan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3">
                <button onclick="hideConfirmModal()" class="flex-1 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold transition">Batal</button>
                <button id="confirmDeleteBtn" class="flex-1 py-2 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition">Hapus</button>
            </div>
        </div>
    </div>


        <!-- MAIN -->
        <main class="flex-1 flex flex-col min-h-screen">
            <header class="h-16 bg-[#FAF6F0]/80 backdrop-blur-md border-b border-[#5B1744]/5 sticky top-0 z-30 px-6 flex items-center justify-between">
                <span class="font-bold text-base text-[#5B1744]">AI Study Companion</span>
            </header>

            <div class="flex-1 flex flex-col relative" id="main">
                <div class="messages-wrap flex-1 overflow-y-auto" id="messages-wrap">
                    <div class="messages max-w-3xl mx-auto p-6" id="messages">
                        <div class="text-center mt-20" id="welcome-state">
                            <h2 class="text-3xl font-bold mb-3 text-gray-900">What's on the agenda today?</h2>
                            <p class="text-gray-500">Study Planner AI siap membantu Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-[#FAF6F0]">
                    <form id="chat-form" onsubmit="sendMessage(event)" class="max-w-3xl mx-auto">
                        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-gray-200 shadow-sm focus-within:border-[#5B1744]">
                            <textarea id="chat-input" rows="1" placeholder="Ask anything..." oninput="autoResize(this);toggleSend()" class="flex-1 bg-transparent border-none focus:ring-0 p-2 resize-none outline-none text-sm"></textarea>
                            <button type="submit" class="w-10 h-10 rounded-xl bg-[#5B1744] text-white flex items-center justify-center disabled:opacity-50" id="btn-send" disabled>
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
        let currentSessionId = null;
        let sessionToDelete = null;

        function showConfirmModal(id) {
            sessionToDelete = id;
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        function hideConfirmModal() {
            sessionToDelete = null;
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }
        document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
            if (!sessionToDelete) return;
            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions/${sessionToDelete}`, { method: 'DELETE' });
                if (!res.ok) throw new Error('Gagal menghapus sesi.');
                if (currentSessionId === sessionToDelete) {
                    location.reload();
                } else {
                    loadSessions();
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menghapus sesi.');
            } finally {
                hideConfirmModal();
            }
        });

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

        async function loadSessions() {
            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions`);
                const data = await res.json();
                const list = document.getElementById('session-list');
                list.innerHTML = (data.sessions || []).map(s => `
                    <a href="#" class="group sidebar-item flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs ${s.id === currentSessionId ? 'active' : 'text-white/70'}" onclick="selectSession('${s.id}'); return false;">
                        <span class="flex items-center gap-3 overflow-hidden">
                            <i class="fa-solid fa-message w-4 text-center shrink-0"></i>
                            <span class="truncate">${s.title}</span>
                        </span>
                        <button class="opacity-0 group-hover:opacity-100 p-1 hover:bg-white/20 rounded shrink-0" onclick="deleteSession('${s.id}', event)">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                        </button>
                    </a>
                `).join('');
            } catch (e) {}
        }

        async function selectSession(id) {
            currentSessionId = id;
            const ws = document.getElementById('welcome-state');
            if (ws) ws.remove();
            const c = document.getElementById('messages');
            c.innerHTML = '<div class="p-8 text-center text-gray-400">Loading...</div>';

            try {
                const res = await apiFetch(`${API_BASE}/chat/sessions/${id}/messages`);
                const data = await res.json();
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
            msgs.forEach(m => appendMessage(m.content, m.role));
        }

        function appendMessage(content, role) {
            const c = document.getElementById('messages');
            const div = document.createElement('div');
            div.className = `mb-4 ${role === 'user' ? 'text-right' : 'text-left'}`;

            const rendered = role === 'assistant'
                ? `<div class="ai-content inline-block p-4 rounded-2xl bg-white border border-gray-100 shadow-sm text-sm text-left max-w-full">${marked.parse(content)}</div>`
                : `<div class="inline-block p-4 rounded-2xl bg-[#5B1744] text-white text-sm text-left">${content}</div>`;

            div.innerHTML = rendered;
            c.appendChild(div);
            const wrap = document.getElementById('messages-wrap');
            wrap.scrollTop = wrap.scrollHeight;
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
            const wrap = document.getElementById('messages-wrap');
            wrap.scrollTop = wrap.scrollHeight;
        }

        function removeLoadingMessage() {
            const loading = document.getElementById('loading-message');
            if (loading) loading.remove();
        }

        async function createSession(title = null) {
            try {
                const body = {};
                if (title) body.title = title;
                const res = await apiFetch(`${API_BASE}/chat/session`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    await selectSession(data.session.id);
                    return data.session.id;
                }
            } catch (e) {}
            return null;
        }

        async function deleteSession(id, event) {
            event.stopPropagation();
            showConfirmModal(id);
        }

        async function sendMessage(e) {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const msg = input.value.trim();
            if (!msg) return;

            const userMsg = msg;
            appendMessage(userMsg, 'user');
            input.value = '';
            input.disabled = true;
            toggleSend();
            appendLoadingMessage();

            try {
                if (!currentSessionId) {
                    const title = userMsg.length > 50 ? userMsg.substring(0, 50) + '...' : userMsg;
                    const newId = await createSession(title);
                    if (!newId) {
                        removeLoadingMessage();
                        appendMessage('Gagal membuat sesi baru.', 'assistant');
                        return;
                    }
                }

                const res = await apiFetch(`${API_BASE}/chat/send`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: userMsg, chat_session_id: currentSessionId })
                });
                const data = await res.json();
                removeLoadingMessage();
                if (data.status === 'success') {
                    appendMessage(data.reply, 'assistant');
                } else {
                    appendMessage('Maaf, terjadi kesalahan saat memproses pesan.', 'assistant');
                }
            } catch (e) {
                removeLoadingMessage();
                appendMessage('Gagal terhubung ke AI.', 'assistant');
            } finally {
                input.disabled = false;
                input.focus();
            }
        }

        function autoResize(t) { t.style.height = 'auto'; t.style.height = Math.min(t.scrollHeight, 160) + 'px'; }

        marked.setOptions({ breaks: true, gfm: true });

        loadSessions();
    </script>
</body>
</html>

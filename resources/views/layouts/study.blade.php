<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Study Planner')</title>

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

    @yield('styles')
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
                    <div class="w-10 h-10 overflow-hidden">
                        <img src="{{ asset('logo-chatgpt.png') }}" alt="Study Planner" class="w-full h-full object-contain">
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
                        <a href="{{ route('dashboard') }}" class="sidebar-item {{ ($activeNav ?? '') === 'overview' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-solid fa-border-all w-4 text-center"></i>
                            Overview
                        </a>

                        <a href="{{ url('/tasks') }}" class="sidebar-item {{ ($activeNav ?? '') === 'tasks' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-regular fa-circle-check w-4 text-center"></i>
                            My Tasks
                        </a>

                        <a href="{{ url('/schedule') }}" class="sidebar-item {{ ($activeNav ?? '') === 'schedule' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-regular fa-calendar-check w-4 text-center"></i>
                            Schedule
                        </a>

                        <a href="{{ route('chat') }}" class="sidebar-item {{ ($activeNav ?? '') === 'chat' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-solid fa-wand-magic-sparkles w-4 text-center"></i>
                            AI Study
                        </a>

                        <a href="{{ url('/progress') }}" class="sidebar-item {{ ($activeNav ?? '') === 'progress' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
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
                        <a href="{{ url('/integrations') }}" class="sidebar-item {{ ($activeNav ?? '') === 'integrations' ? 'active' : '' }} flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
                            <i class="fa-solid fa-link w-4 text-center"></i>
                            Integrations
                        </a>

                        <a href="/" class="sidebar-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs">
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
                                @yield('page-label', 'MY STUDY SPACE')
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

            {{-- Content Container --}}
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

                    @yield('content')

                </div>
            </div>

        </main>

        {{-- MOBILE BOTTOM NAVIGATION (Hanya Muncul di Mobile/Layar < 768px) --}}
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-gray-100 py-2 px-6 flex justify-around items-center z-40 shadow-lg">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ ($activeNav ?? '') === 'overview' ? 'text-[#5B1744]' : 'text-gray-400' }}">
                <div class="{{ ($activeNav ?? '') === 'overview' ? 'bg-[#5B1744] text-white' : '' }} px-3 py-1 rounded-full text-xs">
                    <i class="fa-solid fa-border-all"></i>
                </div>
                <span class="text-[9px] {{ ($activeNav ?? '') === 'overview' ? 'font-bold' : 'font-medium' }}">Overview</span>
            </a>
            <a href="{{ url('/schedule') }}" class="flex flex-col items-center gap-1 {{ ($activeNav ?? '') === 'schedule' ? 'text-[#5B1744]' : 'text-gray-400' }}">
                <i class="fa-regular fa-calendar-check text-sm"></i>
                <span class="text-[9px] font-medium">Schedule</span>
            </a>
            <a href="{{ url('/tasks') }}" class="flex flex-col items-center gap-1 {{ ($activeNav ?? '') === 'tasks' ? 'text-[#5B1744]' : 'text-gray-400' }}">
                <i class="fa-regular fa-circle-check text-sm"></i>
                <span class="text-[9px] font-medium">Tasks</span>
            </a>
            <a href="{{ route('chat') }}" class="flex flex-col items-center gap-1 {{ ($activeNav ?? '') === 'chat' ? 'text-[#5B1744]' : 'text-gray-400' }}">
                <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                <span class="text-[9px] font-medium">AI Study</span>
            </a>
        </nav>

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

    <div id="toastContainer" class="fixed top-4 right-4 z-[60] flex flex-col gap-2"></div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuButton = document.getElementById('menuButton');
        const profileModal = document.getElementById('profileModal');

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

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (! container) return;

            const isSuccess = type !== 'error';
            const el = document.createElement('div');
            el.className = 'flex items-center gap-2 px-4 py-3 rounded-2xl shadow-lg border text-xs font-semibold bg-white ' +
                (isSuccess ? 'border-green-200 text-green-700' : 'border-red-200 text-red-700');
            el.innerHTML = '<i class="fa-solid ' + (isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation') + '"></i><span></span>';
            el.querySelector('span').textContent = message;
            container.appendChild(el);

            setTimeout(() => {
                el.style.transition = 'opacity .3s ease, transform .3s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(() => el.remove(), 300);
            }, 3200);
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

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeProfileModal();
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

        @stack('scripts')
    </script>

</body>

</html>

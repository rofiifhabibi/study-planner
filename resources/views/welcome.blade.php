<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Study Planner — Plan Better. Study Smarter.</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;1,500;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            scroll-behavior: smooth;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF6F0;
            color: #241C21;
        }

        .serif {
            font-family: 'Playfair Display', serif;
        }

        .noise {
            background-image: radial-gradient(rgba(91, 23, 68, 0.06) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .blob {
            filter: blur(80px);
        }

        .gradient-text {
            background: linear-gradient(135deg, #5B1744, #8B416F);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-hover {
            transition: all 0.25s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(91, 23, 68, 0.08);
        }
    </style>
</head>

<body class="bg-[#FAF6F0] text-[#241C21] overflow-x-hidden">

    <!-- ========================================================= -->
    <!-- NAVBAR -->
    <!-- ========================================================= -->
    <header id="navbar" class="fixed top-0 left-0 w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <nav class="h-20 flex items-center justify-between">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-3">
                    <div class="w-10 h-10 overflow-hidden shrink-0">
                        <img src="{{ asset('logo-chatgpt.png') }}" alt="Study Planner" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-bold text-lg leading-tight text-[#241C21]">
                            Study Planner
                        </div>
                        <div class="text-[10px] text-gray-500 font-semibold tracking-[.2em] mt-0.5">
                            PLAN · STUDY · GROW
                        </div>
                    </div>
                </a>

                <!-- Nav Menu -->
                <div class="hidden md:flex items-center gap-9 text-sm font-medium text-gray-600">
                    <a href="#home" class="hover:text-[#5B1744] transition">Home</a>
                    <a href="#features" class="hover:text-[#5B1744] transition">Fitur</a>
                    <a href="#how" class="hover:text-[#5B1744] transition">Cara Kerja</a>
                    <a href="#about" class="hover:text-[#5B1744] transition">Tentang</a>
                </div>

                <!-- Action Button -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-full bg-[#5B1744] text-white text-sm font-semibold shadow-md shadow-[#5B1744]/20 hover:-translate-y-0.5 transition">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-block px-4 py-2 text-sm font-semibold text-gray-600 hover:text-[#5B1744] transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full bg-[#5B1744] text-white text-sm font-semibold shadow-md shadow-[#5B1744]/20 hover:-translate-y-0.5 transition">
                            Sign Up
                        </a>
                    @endauth
                </div>

            </nav>
        </div>
    </header>

    <!-- ========================================================= -->
    <!-- HERO -->
    <!-- ========================================================= -->
    <section id="home" class="relative pt-32 pb-20 sm:pt-40 sm:pb-24 overflow-hidden">

        <!-- Background Ambient Blobs -->
        <div class="absolute inset-0 noise opacity-40 pointer-events-none"></div>
        <div class="absolute w-[450px] h-[450px] bg-[#E7C8DB] rounded-full blob -top-40 -right-20 opacity-50 pointer-events-none"></div>
        <div class="absolute w-[350px] h-[350px] bg-[#EADCCF] rounded-full blob bottom-0 -left-28 opacity-60 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-10">
            <div class="grid lg:grid-cols-12 gap-12 items-center">

                <!-- Left Text Column -->
                <div class="lg:col-span-7">

                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#5B1744]/15 bg-white/70 backdrop-blur text-[#5B1744] text-xs font-semibold mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#5B1744]"></span>
                        <span>Ruang Belajar Mandiri Siswa</span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl sm:text-5xl lg:text-[64px] leading-[1.08] tracking-tight font-bold text-gray-900">
                        Make time
                        <br>
                        for what
                        <span class="serif italic gradient-text">
                            matters.
                        </span>
                    </h1>

                    <!-- Description -->
                    <p class="mt-6 max-w-xl text-base sm:text-lg leading-relaxed text-gray-600">
                        Study Planner adalah website untuk mengatur jadwal pelajaran, mencatat tugas harian, dan melatih fokus belajar dengan timer Pomodoro serta bantuan AI yang siap menjawab kapan saja.
                    </p>

                    <!-- Buttons -->
                    <div class="mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-[#5B1744] text-white font-semibold text-sm shadow-lg shadow-[#5B1744]/20 hover:-translate-y-0.5 transition">
                                <span>Buka Dashboard</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="flex items-center gap-2.5 px-6 py-3.5 rounded-full bg-[#5B1744] text-white font-semibold text-sm shadow-lg shadow-[#5B1744]/20 hover:-translate-y-0.5 transition">
                                <span>Mulai Belajar Sekarang</span>
                                <span>&rarr;</span>
                            </a>
                        @endauth

                        <a href="{{ route('chat') }}" class="px-6 py-3.5 rounded-full border border-gray-300 bg-white/70 backdrop-blur font-semibold text-sm text-gray-700 hover:bg-white transition">
                            Tanya AI Study
                        </a>
                    </div>

                    <!-- Mini Info -->
                    <div class="mt-10 flex items-center gap-4 text-xs text-gray-500">
                        <div class="w-8 h-8 rounded-full bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center font-bold">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <div class="font-bold text-gray-800">Dibuat Simpel untuk Pelajar</div>
                            <div class="text-gray-500">Fokus memahami materi, bukan pusing mengatur jadwal.</div>
                        </div>
                    </div>

                </div>

                <!-- Right Visual Column (Clean & Compact Preview Card) -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-[#E7C8DB]/70 shadow-xl shadow-[#5B1744]/5 relative">

                        <!-- Window Dots & Date -->
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ff5f56]"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-[#ffbd2e]"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-[#27c93f]"></span>
                            </div>
                            <span class="text-[11px] font-semibold text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full">
                                Workspace Hari Ini
                            </span>
                        </div>

                        <!-- Card 1: Today's Focus -->
                        <div class="bg-[#5B1744] text-white rounded-2xl p-4 mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-stopwatch text-amber-300 text-xs"></i>
                                    <span class="text-[10px] uppercase tracking-wider font-bold text-pink-200">POMODORO FOKUS</span>
                                </div>
                                <span class="text-xs font-mono font-bold bg-white/15 px-2 py-0.5 rounded-md">25:00</span>
                            </div>
                            <p class="text-xs text-white/90 font-medium">Latihan Soal Matematika & Fisika</p>
                        </div>

                        <!-- Card 2: Simple Task Preview -->
                        <div class="space-y-2.5">
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tugas Prioritas</div>
                            
                            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-4 h-4 rounded-full border-2 border-[#5B1744] bg-[#5B1744] text-white flex items-center justify-center text-[7px]">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                    <span class="text-gray-400 line-through">Rangkuman Bab 2 Biologi</span>
                                </div>
                                <span class="text-[10px] text-gray-400">Selesai</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-[#FAF6F0] border border-[#E7C8DB]/60 text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-4 h-4 rounded-full border-2 border-gray-300"></span>
                                    <span class="font-medium text-gray-800">Draft Presentasi Bahasa Inggris</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-bold">Besok</span>
                            </div>
                        </div>

                        <!-- Card 3: Timetable Hint -->
                        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-[#5B1744]"></i>
                                <span>Jadwal Kelas: 07:30 WIB</span>
                            </span>
                            <span class="text-[#5B1744] font-semibold">Tersinkronisasi</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </section>

    <!-- ========================================================= -->
    <!-- FEATURES (4 Core Clean Cards) -->
    <!-- ========================================================= -->
    <section id="features" class="py-24 border-t border-[#5B1744]/10 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">

            <div class="max-w-2xl">
                <p class="text-xs font-bold tracking-[.2em] uppercase text-[#5B1744]">
                    FITUR UTAMA
                </p>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold tracking-tight text-gray-900">
                    Semua yang Kamu Butuhkan,
                    <span class="serif italic text-[#5B1744] block">
                        Dibuat Sederhana.
                    </span>
                </h2>
                <p class="mt-3 text-gray-600 text-base">
                    Nggak perlu buka banyak aplikasi berbeda. Cukup satu tempat untuk tugas, jadwal, waktu fokus, dan teman belajar.
                </p>
            </div>

            <!-- 4 Cards Grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">

                <!-- Card 1: Schedule -->
                <div class="bg-[#FAF6F0] rounded-3xl p-7 border border-[#E7C8DB]/60 card-hover flex flex-col justify-between">
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-base mb-5">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Jadwal Pelajaran</h3>
                        <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">
                            Simpan jadwal mata pelajaran mingguanmu. Pantau pelajaran hari ini dan persiapan besok secara otomatis.
                        </p>
                    </div>
                </div>

                <!-- Card 2: Tasks -->
                <div class="bg-[#FAF6F0] rounded-3xl p-7 border border-[#E7C8DB]/60 card-hover flex flex-col justify-between">
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-base mb-5">
                            <i class="fa-regular fa-circle-check"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Daftar Tugas</h3>
                        <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">
                            Catat tugas dan PR sekolah dengan tenggat waktu serta tingkat prioritas agar tahu mana yang harus dikerjakan dulu.
                        </p>
                    </div>
                </div>

                <!-- Card 3: Pomodoro -->
                <div class="bg-[#FAF6F0] rounded-3xl p-7 border border-[#E7C8DB]/60 card-hover flex flex-col justify-between">
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-base mb-5">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Fokus Pomodoro</h3>
                        <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">
                            Timer fokus belajar terhubung ke tugasmu, lengkap dengan nada dering melodi saat waktu habis agar belajar tidak jenuh.
                        </p>
                    </div>
                </div>

                <!-- Card 4: AI Study -->
                <div class="bg-[#FAF6F0] rounded-3xl p-7 border border-[#E7C8DB]/60 card-hover flex flex-col justify-between">
                    <div>
                        <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-base mb-5">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900">Asisten AI</h3>
                        <p class="text-gray-500 mt-2 text-xs sm:text-sm leading-relaxed">
                            Tanyakan materi pelajaran yang membingungkan atau minta AI menyetel timer belajar secara langsung lewat chat.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================= -->
    <!-- HOW IT WORKS -->
    <!-- ========================================================= -->
    <section id="how" class="py-24 bg-[#5B1744] text-white relative overflow-hidden">

        <div class="relative max-w-7xl mx-auto px-6 lg:px-10">

            <div class="text-center max-w-xl mx-auto">
                <p class="text-xs tracking-[.25em] uppercase text-white/50 font-bold">
                    CARA KERJA
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold mt-3">
                    Mudah dan Praktis.
                </h2>
                <p class="text-white/70 mt-3 text-sm sm:text-base leading-relaxed">
                    Tiga langkah sederhana dari tugas yang menumpuk hingga jadi rencana belajar yang teratur.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 mt-14">

                <!-- Step 1 -->
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8">
                    <span class="text-5xl font-bold text-white/20">01</span>
                    <h3 class="text-lg font-bold mt-6 text-white">Masukkan Tugas</h3>
                    <p class="text-white/70 mt-2 text-xs sm:text-sm leading-relaxed">
                        Tuliskan tugas sekolah, PR, atau materi yang ingin kamu pelajari beserta tanggal deadline-nya.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8">
                    <span class="text-5xl font-bold text-white/20">02</span>
                    <h3 class="text-lg font-bold mt-6 text-white">Mulai Sesi Fokus</h3>
                    <p class="text-white/70 mt-2 text-xs sm:text-sm leading-relaxed">
                        Nyalakan timer Pomodoro 25 menit. Saat waktu habis, nada dering akan berbunyi dan istirahatlah sejenak.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="rounded-3xl border border-white/10 bg-white/5 p-8">
                    <span class="text-5xl font-bold text-white/20">03</span>
                    <h3 class="text-lg font-bold mt-6 text-white">Tanya AI Bila Stuck</h3>
                    <p class="text-white/70 mt-2 text-xs sm:text-sm leading-relaxed">
                        Jika ada materi atau soal yang sulit dipahami, buka AI Study untuk meminta penjelasan dengan cepat.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!-- ========================================================= -->
    <!-- ABOUT -->
    <!-- ========================================================= -->
    <section id="about" class="py-24 bg-[#FAF6F0]">
        <div class="max-w-5xl mx-auto px-6 lg:px-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                <div>
                    <p class="text-xs font-bold tracking-[.2em] uppercase text-[#5B1744]">
                        TENTANG STUDY PLANNER
                    </p>
                    <h2 class="mt-3 text-3xl sm:text-4xl font-bold leading-tight text-gray-900">
                        Belajar Lebih Terarah,
                        <br>
                        <span class="serif italic gradient-text">
                            Tanpa Ada yang Terlewat.
                        </span>
                    </h2>
                </div>

                <div class="text-gray-600 text-sm sm:text-base leading-relaxed space-y-4">
                    <p>
                        Banyak tugas dan jadwal terlewat hanya karena tidak tercatat di satu tempat yang jelas.
                    </p>
                    <p>
                        Di Study Planner, semua PR, jadwal kelas, dan sesi belajarmu tersusun rapi dalam satu dashboard sederhana. Tidak ada lagi tugas yang kelupaan.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- CTA -->
    <!-- ========================================================= -->
    <section class="px-6 pb-20 bg-[#FAF6F0]">
        <div class="max-w-5xl mx-auto rounded-[32px] bg-[#E9D5E1] p-8 sm:p-14 text-center sm:text-left relative overflow-hidden">
            <div class="max-w-xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-[#5B1744]">
                    MULAI HARI INI
                </p>
                <h2 class="text-3xl sm:text-4xl font-bold mt-2 text-gray-900">
                    Rencanakan lebih tenang.
                    <br>
                    <span class="serif italic text-[#5B1744]">
                        Selesaikan lebih banyak.
                    </span>
                </h2>
                <p class="mt-3 text-gray-700 text-sm sm:text-base">
                    Mulai bangun kebiasaan belajar yang lebih teratur tanpa repot.
                </p>

                <div class="mt-7">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#5B1744] text-white text-sm font-semibold hover:-translate-y-0.5 transition shadow-lg shadow-[#5B1744]/20">
                            <span>Buka Dashboard Pribadi</span>
                            <span>&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#5B1744] text-white text-sm font-semibold hover:-translate-y-0.5 transition shadow-lg shadow-[#5B1744]/20">
                            <span>Sign Up</span>
                            <span>&rarr;</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- FOOTER -->
    <!-- ========================================================= -->
    <footer class="border-t border-gray-200 bg-white py-8">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 overflow-hidden">
                    <img src="{{ asset('logo-chatgpt.png') }}" alt="Study Planner" class="w-full h-full object-contain">
                </div>
                <span class="font-bold text-gray-800">Study Planner</span>
            </div>

            <p class="text-gray-400">
                &copy; {{ date('Y') }} Study Planner. Dibuat untuk belajar yang lebih teratur.
            </p>

            <div class="flex flex-wrap items-center gap-4 sm:gap-5 font-medium">
                <a href="{{ route('privacy') }}" class="hover:text-[#5B1744] transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-[#5B1744] transition">Ketentuan Layanan</a>
                <a href="{{ route('chat') }}" class="hover:text-[#5B1744] transition">AI Study</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-[#5B1744] transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-[#5B1744] transition">Login</a>
                @endauth
            </div>
        </div>
    </footer>

    <!-- Navbar Scroll Script -->
    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 25) {
                navbar.classList.add('bg-[#FAF6F0]/90', 'backdrop-blur-md', 'shadow-xs', 'border-b', 'border-[#5B1744]/5');
            } else {
                navbar.classList.remove('bg-[#FAF6F0]/90', 'backdrop-blur-md', 'shadow-xs', 'border-b', 'border-[#5B1744]/5');
            }
        });
    </script>

</body>
</html>
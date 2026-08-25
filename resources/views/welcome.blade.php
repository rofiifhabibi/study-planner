<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Study Planner — Plan Better. Study Smarter.</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
        }

        .serif {
            font-family: 'Playfair Display', serif;
        }

        .noise {
            background-image:
                radial-gradient(rgba(91,23,68,.08) 1px, transparent 1px);
            background-size: 22px 22px;
        }

        .blob {
            filter: blur(70px);
        }

        .float {
            animation: float 5s ease-in-out infinite;
        }

        .float-delay {
            animation: float 6s ease-in-out infinite 1s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-12px);
            }
        }

        .fade-up {
            animation: fadeUp .8s ease forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .gradient-text {
            background: linear-gradient(
                135deg,
                #5B1744,
                #8B416F
            );

            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>


<body class="bg-[#FBF7F3] text-[#241C21] overflow-x-hidden">


<!-- ========================================================= -->
<!-- NAVBAR -->
<!-- ========================================================= -->

<header
    id="navbar"
    class="fixed top-0 left-0 w-full z-50 transition-all duration-500">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        <nav class="h-20 flex items-center justify-between">

            <!-- LOGO -->

            <a href="/" class="flex items-center gap-3">

                <div
                    class="w-10 h-10 rounded-xl
                    bg-[#5B1744]
                    text-white
                    flex items-center justify-center
                    font-bold text-lg">

                    S
                </div>

                <div>
                    <div class="font-bold text-lg leading-none">
                        Study Planner
                    </div>

                    <div class="text-[11px] text-gray-500 mt-1">
                        PLAN · STUDY · GROW
                    </div>
                </div>

            </a>


            <!-- MENU -->

            <div class="hidden md:flex items-center gap-9 text-sm font-medium">

                <a
                    href="#home"
                    class="nav-link hover:text-[#5B1744] transition">

                    Home
                </a>

                <a
                    href="#features"
                    class="nav-link hover:text-[#5B1744] transition">

                    Features
                </a>

                <a
                    href="#how"
                    class="nav-link hover:text-[#5B1744] transition">

                    How It Works
                </a>

                <a
                    href="#about"
                    class="nav-link hover:text-[#5B1744] transition">

                    About
                </a>

            </div>


            <!-- ACTION -->

            <div class="flex items-center gap-3">

                <a
                    href="/login"
                    class="hidden sm:block px-5 py-2.5 text-sm font-semibold
                    hover:text-[#5B1744] transition">

                    Login
                </a>

                <a
                    href="/register"
                    class="px-5 py-2.5 rounded-full
                    bg-[#5B1744] text-white
                    text-sm font-semibold
                    shadow-lg shadow-[#5B1744]/20
                    hover:shadow-xl
                    hover:-translate-y-0.5
                    transition">

                    Sign Up
                </a>

            </div>

        </nav>

    </div>

</header>



<!-- ========================================================= -->
<!-- HERO -->
<!-- ========================================================= -->

<section
    id="home"
    class="relative min-h-screen pt-32 pb-20 overflow-hidden">


    <!-- BACKGROUND -->

    <div class="absolute inset-0 noise opacity-40"></div>

    <div
        class="absolute
        w-[500px] h-[500px]
        bg-[#E7C8DB]
        rounded-full
        blob
        -top-40
        -right-40
        opacity-60">
    </div>

    <div
        class="absolute
        w-[350px] h-[350px]
        bg-[#EADCCF]
        rounded-full
        blob
        bottom-0
        left-[-150px]
        opacity-70">
    </div>


    <div class="relative max-w-7xl mx-auto px-6 lg:px-10">

        <div class="grid lg:grid-cols-[1fr_0.9fr] gap-16 items-center">


            <!-- LEFT -->

            <div class="fade-up">


                <!-- BADGE -->

                <div
                    class="inline-flex items-center gap-2
                    px-4 py-2
                    rounded-full
                    border border-[#5B1744]/10
                    bg-white/70
                    backdrop-blur
                    text-[#5B1744]
                    text-xs
                    font-semibold">

                    <span
                        class="w-2 h-2 rounded-full bg-[#5B1744]">
                    </span>

                    Your smarter study companion

                </div>


                <!-- TITLE -->

                <h1
                    class="mt-7
                    text-5xl
                    sm:text-6xl
                    lg:text-[76px]
                    leading-[1.02]
                    tracking-tight
                    font-bold">

                    Make time
                    <br>

                    for what
                    <span class="serif italic gradient-text">
                        matters.
                    </span>

                </h1>


                <!-- DESCRIPTION -->

                <p
                    class="mt-7
                    max-w-xl
                    text-lg
                    leading-8
                    text-gray-600">

                    Study Planner membantu kamu mengatur tugas,
                    menyusun jadwal belajar, dan menggunakan AI
                    untuk memahami materi tanpa bikin semuanya terasa ribet.

                </p>


                <!-- BUTTON -->

                <div class="mt-9 flex flex-wrap gap-4">

                    <a
                        href="/register"
                        class="group
                        flex items-center gap-3
                        px-7 py-4
                        rounded-full
                        bg-[#5B1744]
                        text-white
                        font-semibold
                        shadow-xl
                        shadow-[#5B1744]/20
                        hover:-translate-y-1
                        transition">

                        Start Planning

                        <span
                            class="w-7 h-7
                            rounded-full
                            bg-white/15
                            flex items-center justify-center
                            group-hover:translate-x-1
                            transition">

                            →
                        </span>

                    </a>


                    <a
                        href="/chat"
                        class="px-7 py-4
                        rounded-full
                        border border-gray-300
                        bg-white/60
                        backdrop-blur
                        font-semibold
                        hover:bg-white
                        transition">

                        Try AI Assistant

                    </a>

                </div>


                <!-- MINI INFO -->

                <div class="mt-10 flex items-center gap-6">

                    <div class="flex -space-x-2">

                        <div
                            class="w-9 h-9 rounded-full
                            bg-[#D8B9C9]
                            border-2 border-[#FBF7F3]
                            flex items-center justify-center
                            text-xs font-bold">

                            A
                        </div>

                        <div
                            class="w-9 h-9 rounded-full
                            bg-[#CBB8A9]
                            border-2 border-[#FBF7F3]
                            flex items-center justify-center
                            text-xs font-bold">

                            R
                        </div>

                        <div
                            class="w-9 h-9 rounded-full
                            bg-[#E5D5DF]
                            border-2 border-[#FBF7F3]
                            flex items-center justify-center
                            text-xs font-bold">

                            N
                        </div>

                    </div>

                    <div class="text-sm">

                        <div class="font-semibold">
                            Built for students
                        </div>

                        <div class="text-gray-500">
                            Focus on studying, not organizing.
                        </div>

                    </div>

                </div>

            </div>



            <!-- RIGHT / DASHBOARD MOCKUP -->

            <div class="relative">


                <!-- DECORATION -->

                <div
                    class="absolute
                    -top-10
                    -right-5
                    w-24 h-24
                    rounded-full
                    border border-[#5B1744]/10">
                </div>


                <!-- MAIN CARD -->

                <div
                    class="relative
                    bg-white
                    rounded-[32px]
                    border border-gray-100
                    shadow-[0_30px_80px_rgba(91,23,68,0.12)]
                    p-5
                    float">


                    <!-- TOP BAR -->

                    <div
                        class="flex items-center justify-between
                        px-3 py-2 mb-5">

                        <div>

                            <div class="text-xs text-gray-400">
                                Tuesday, 18 August
                            </div>

                            <div class="font-bold text-lg mt-1">
                                Good afternoon.
                            </div>

                        </div>

                        <div
                            class="w-10 h-10 rounded-full
                            bg-[#F4E7EF]
                            flex items-center justify-center
                            text-[#5B1744]">

                            N
                        </div>

                    </div>


                    <!-- PROGRESS -->

                    <div
                        class="rounded-2xl
                        bg-[#FBF7F3]
                        p-5">

                        <div class="flex justify-between items-center">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Today's progress
                                </p>

                                <p class="text-3xl font-bold mt-1">
                                    72%
                                </p>

                            </div>

                            <div
                                class="w-16 h-16 rounded-full
                                border-[6px]
                                border-[#5B1744]
                                border-r-gray-200
                                flex items-center justify-center
                                text-sm font-bold">

                                4/6

                            </div>

                        </div>

                    </div>


                    <!-- TASK TITLE -->

                    <div class="flex justify-between items-center mt-6 mb-3">

                        <h3 class="font-bold">
                            Today's tasks
                        </h3>

                        <span class="text-xs text-[#5B1744]">
                            View all →
                        </span>

                    </div>


                    <!-- TASKS -->

                    <div class="space-y-3">


                        <div
                            class="flex items-center gap-4
                            p-4 rounded-2xl
                            border border-gray-100
                            hover:border-[#5B1744]/20
                            transition">

                            <div
                                class="w-6 h-6 rounded-full
                                bg-[#5B1744]
                                text-white
                                flex items-center justify-center
                                text-xs">

                                ✓
                            </div>

                            <div class="flex-1">

                                <p class="text-sm font-semibold">
                                    Review Database
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    09:00 — 10:30
                                </p>

                            </div>

                            <span
                                class="text-[10px]
                                px-2 py-1
                                rounded-full
                                bg-green-50
                                text-green-600">

                                Done
                            </span>

                        </div>


                        <div
                            class="flex items-center gap-4
                            p-4 rounded-2xl
                            border border-gray-100
                            hover:border-[#5B1744]/20
                            transition">

                            <div
                                class="w-6 h-6 rounded-full
                                border-2 border-gray-300">
                            </div>

                            <div class="flex-1">

                                <p class="text-sm font-semibold">
                                    Network Assignment
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    13:00 — 14:30
                                </p>

                            </div>

                            <span
                                class="text-[10px]
                                px-2 py-1
                                rounded-full
                                bg-orange-50
                                text-orange-600">

                                Today
                            </span>

                        </div>


                        <div
                            class="flex items-center gap-4
                            p-4 rounded-2xl
                            border border-gray-100">

                            <div
                                class="w-6 h-6 rounded-full
                                border-2 border-gray-300">
                            </div>

                            <div class="flex-1">

                                <p class="text-sm font-semibold">
                                    Study Mathematics
                                </p>

                                <p class="text-xs text-gray-400 mt-1">
                                    19:00 — 20:00
                                </p>

                            </div>

                        </div>

                    </div>

                </div>



            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- STATS -->
<!-- ========================================================= -->

<section class="py-10 border-y border-[#5B1744]/10 bg-white/60">

    <div
        class="max-w-5xl mx-auto px-6
        grid grid-cols-2 md:grid-cols-4
        gap-8 text-center">

        <div>

            <div class="text-3xl font-bold text-[#5B1744]">
                24/7
            </div>

            <p class="text-sm text-gray-500 mt-1">
                AI Assistance
            </p>

        </div>

        <div>

            <div class="text-3xl font-bold text-[#5B1744]">
                4+
            </div>

            <p class="text-sm text-gray-500 mt-1">
                Core Features
            </p>

        </div>

        <div>

            <div class="text-3xl font-bold text-[#5B1744]">
                1
            </div>

            <p class="text-sm text-gray-500 mt-1">
                Smart Dashboard
            </p>

        </div>

        <div>

            <div class="text-3xl font-bold text-[#5B1744]">
                100%
            </div>

            <p class="text-sm text-gray-500 mt-1">
                Student Focused
            </p>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- FEATURES -->
<!-- ========================================================= -->

<section
    id="features"
    class="py-28">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">


        <div class="max-w-2xl">

            <p
                class="text-sm font-bold
                tracking-[.2em]
                uppercase
                text-[#5B1744]">

                Everything in one place
            </p>

            <h2
                class="mt-4
                text-4xl
                md:text-5xl
                font-bold
                tracking-tight">

                Your study life,
                <span class="serif italic font-medium">
                    simplified.
                </span>

            </h2>

            <p class="mt-5 text-gray-600 text-lg">

                Nggak perlu pindah-pindah aplikasi.
                Semua yang kamu butuhkan untuk belajar ada di satu tempat.

            </p>

        </div>


        <!-- FEATURE GRID -->

        <div
            class="grid md:grid-cols-2 lg:grid-cols-4
            gap-5 mt-14">


            <!-- CARD 1 -->

            <div
                class="group
                bg-white
                rounded-3xl
                p-7
                border border-gray-100
                hover:-translate-y-2
                hover:shadow-xl
                hover:shadow-[#5B1744]/10
                transition duration-300">

                <div
                    class="w-12 h-12
                    rounded-2xl
                    bg-[#F4E7EF]
                    text-[#5B1744]
                    flex items-center justify-center
                    text-xl
                    group-hover:scale-110
                    transition">

                    ◷
                </div>

                <h3 class="font-bold text-xl mt-6">
                    Smart Schedule
                </h3>

                <p class="text-gray-500 mt-3 leading-6 text-sm">

                    Atur waktu belajar, tugas,
                    dan deadline dengan lebih terstruktur.

                </p>

            </div>


            <!-- CARD 2 -->

            <div
                class="group
                bg-white
                rounded-3xl
                p-7
                border border-gray-100
                hover:-translate-y-2
                hover:shadow-xl
                hover:shadow-[#5B1744]/10
                transition duration-300">

                <div
                    class="w-12 h-12
                    rounded-2xl
                    bg-[#F4E7EF]
                    text-[#5B1744]
                    flex items-center justify-center
                    text-xl
                    group-hover:scale-110
                    transition">

                    ✓
                </div>

                <h3 class="font-bold text-xl mt-6">
                    Task Manager
                </h3>

                <p class="text-gray-500 mt-3 leading-6 text-sm">

                    Catat tugas sekolah dan pantau
                    mana yang sudah atau belum selesai.

                </p>

            </div>


            <!-- CARD 3 -->

            <div
                class="group
                bg-white
                rounded-3xl
                p-7
                border border-gray-100
                hover:-translate-y-2
                hover:shadow-xl
                hover:shadow-[#5B1744]/10
                transition duration-300">

                <div
                    class="w-12 h-12
                    rounded-2xl
                    bg-[#F4E7EF]
                    text-[#5B1744]
                    flex items-center justify-center
                    text-xl
                    group-hover:scale-110
                    transition">

                    ✦
                </div>

                <h3 class="font-bold text-xl mt-6">
                    AI Assistant
                </h3>

                <p class="text-gray-500 mt-3 leading-6 text-sm">

                    Tanya materi, minta penjelasan,
                    dan belajar bersama AI.

                </p>

            </div>


            <!-- CARD 4 -->

            <div
                class="group
                bg-white
                rounded-3xl
                p-7
                border border-gray-100
                hover:-translate-y-2
                hover:shadow-xl
                hover:shadow-[#5B1744]/10
                transition duration-300">

                <div
                    class="w-12 h-12
                    rounded-2xl
                    bg-[#F4E7EF]
                    text-[#5B1744]
                    flex items-center justify-center
                    text-xl
                    group-hover:scale-110
                    transition">

                    ↗
                </div>

                <h3 class="font-bold text-xl mt-6">
                    Track Progress
                </h3>

                <p class="text-gray-500 mt-3 leading-6 text-sm">

                    Lihat perkembangan belajar
                    dan tetap tahu targetmu.

                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- HOW IT WORKS -->
<!-- ========================================================= -->

<section
    id="how"
    class="py-28 bg-[#5B1744] text-white relative overflow-hidden">

    <div
        class="absolute
        w-[500px] h-[500px]
        rounded-full
        border border-white/10
        -right-40
        -top-40">
    </div>

    <div
        class="absolute
        w-[300px] h-[300px]
        rounded-full
        border border-white/10
        -left-40
        bottom-[-150px]">
    </div>


    <div class="relative max-w-7xl mx-auto px-6 lg:px-10">


        <div class="text-center max-w-2xl mx-auto">

            <p
                class="text-xs
                tracking-[.25em]
                uppercase
                text-white/50
                font-bold">

                HOW IT WORKS
            </p>

            <h2
                class="text-4xl md:text-5xl
                font-bold mt-4">

                Simple by design.

            </h2>

            <p class="text-white/60 mt-5 text-lg">

                Mulai dari tugas yang berantakan
                sampai jadi rencana belajar yang jelas.

            </p>

        </div>


        <div
            class="grid md:grid-cols-3
            gap-6 mt-16">


            <div
                class="relative
                rounded-3xl
                border border-white/10
                bg-white/5
                p-8">

                <span
                    class="text-6xl
                    font-bold
                    text-white/10">

                    01
                </span>

                <h3 class="text-xl font-bold mt-8">
                    Add Your Tasks
                </h3>

                <p class="text-white/60 mt-3 leading-6">
                    Masukkan tugas, deadline,
                    dan aktivitas belajar yang harus kamu lakukan.
                </p>

            </div>


            <div
                class="relative
                rounded-3xl
                border border-white/10
                bg-white/5
                p-8">

                <span
                    class="text-6xl
                    font-bold
                    text-white/10">

                    02
                </span>

                <h3 class="text-xl font-bold mt-8">
                    Plan Your Day
                </h3>

                <p class="text-white/60 mt-3 leading-6">
                    Susun semuanya menjadi jadwal
                    belajar yang lebih teratur.
                </p>

            </div>


            <div
                class="relative
                rounded-3xl
                border border-white/10
                bg-white/5
                p-8">

                <span
                    class="text-6xl
                    font-bold
                    text-white/10">

                    03
                </span>

                <h3 class="text-xl font-bold mt-8">
                    Learn With AI
                </h3>

                <p class="text-white/60 mt-3 leading-6">
                    Ketika stuck, langsung tanyakan
                    materi yang belum kamu pahami.
                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- ABOUT -->
<!-- ========================================================= -->

<section
    id="about"
    class="py-28">

    <div
        class="max-w-6xl mx-auto px-6 lg:px-10">

        <div
            class="grid md:grid-cols-2
            gap-16 items-center">


            <div>

                <p
                    class="text-sm font-bold
                    tracking-[.2em]
                    uppercase
                    text-[#5B1744]">

                    ABOUT
                </p>

                <h2
                    class="mt-5
                    text-4xl md:text-5xl
                    font-bold
                    leading-tight">

                    Because studying
                    shouldn't feel
                    <span class="serif italic gradient-text">
                        chaotic.
                    </span>

                </h2>

            </div>


            <div>

                <p
                    class="text-gray-600
                    text-lg
                    leading-8">

                    Study Planner dibuat untuk membantu siswa
                    mengelola aktivitas belajar dengan cara yang
                    lebih sederhana.

                </p>

                <p
                    class="text-gray-600
                    text-lg
                    leading-8
                    mt-5">

                    Daripada mengingat semuanya sendiri,
                    kamu bisa menyimpan tugas, mengatur jadwal,
                    melihat progress, dan menggunakan AI
                    sebagai teman belajar.

                </p>

            </div>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- CTA -->
<!-- ========================================================= -->

<section class="px-6 pb-24">

    <div
        class="max-w-6xl mx-auto
        relative overflow-hidden
        rounded-[40px]
        bg-[#E9D5E1]
        p-10 md:p-16">


        <div
            class="absolute
            w-80 h-80
            rounded-full
            bg-white/30
            blur-3xl
            -right-20
            -top-20">
        </div>


        <div class="relative max-w-2xl">

            <p
                class="text-sm
                font-bold
                uppercase
                tracking-[.2em]
                text-[#5B1744]">

                READY?
            </p>

            <h2
                class="text-4xl md:text-5xl
                font-bold
                mt-4
                leading-tight">

                Plan less.
                <br>

                <span class="serif italic">
                    Accomplish more.
                </span>

            </h2>

            <p
                class="mt-5
                text-gray-600
                text-lg">

                Mulai bangun kebiasaan belajar
                yang lebih teratur hari ini.

            </p>

            <a
                href="/register"
                class="inline-flex
                items-center gap-3
                mt-8
                px-7 py-4
                rounded-full
                bg-[#5B1744]
                text-white
                font-semibold
                hover:-translate-y-1
                shadow-xl
                shadow-[#5B1744]/20
                transition">

                Create Your Planner

                <span>→</span>

            </a>

        </div>

    </div>

</section>



<!-- ========================================================= -->
<!-- FOOTER -->
<!-- ========================================================= -->

<footer class="border-t border-gray-200">

    <div
        class="max-w-7xl mx-auto
        px-6 lg:px-10
        py-8
        flex flex-col md:flex-row
        justify-between
        items-center
        gap-4">

        <div class="flex items-center gap-3">

            <div
                class="w-8 h-8
                rounded-lg
                bg-[#5B1744]
                text-white
                flex items-center justify-center
                font-bold text-sm">

                S
            </div>

            <span class="font-semibold">
                Study Planner
            </span>

        </div>


        <p class="text-sm text-gray-400">

            © {{ date('Y') }} Study Planner AI.
            Built for better learning.

        </p>


        <div class="flex gap-5 text-sm text-gray-500">

            <a
                href="/chat"
                class="hover:text-[#5B1744] transition">

                AI Assistant
            </a>

            <a
                href="/login"
                class="hover:text-[#5B1744] transition">

                Login
            </a>

        </div>

    </div>

</footer>



<!-- ========================================================= -->
<!-- NAVBAR SCRIPT -->
<!-- ========================================================= -->

<script>

    const navbar = document.getElementById('navbar');

    window.addEventListener('scroll', () => {

        if (window.scrollY > 30) {

            navbar.classList.add(
                'bg-[#FBF7F3]/90',
                'backdrop-blur-xl',
                'shadow-sm',
                'border-b',
                'border-gray-200/50'
            );

        } else {

            navbar.classList.remove(
                'bg-[#FBF7F3]/90',
                'backdrop-blur-xl',
                'shadow-sm',
                'border-b',
                'border-gray-200/50'
            );

        }

    });

</script>


</body>
</html>
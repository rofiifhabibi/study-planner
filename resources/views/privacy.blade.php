<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kebijakan Privasi (Privacy Policy) — Study Planner</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAF6F0;
            color: #241C21;
        }
        .serif {
            font-family: 'Playfair Display', serif;
        }
        .prose-legal h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #5B1744;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .prose-legal h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #241C21;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .prose-legal p, .prose-legal li {
            font-size: 0.925rem;
            line-height: 1.7;
            color: #4B5563;
        }
        .prose-legal ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-top: 0.5rem;
            margin-bottom: 0.75rem;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">

    <!-- NAVBAR -->
    <header class="bg-[#FAF6F0]/90 backdrop-blur-md border-b border-[#5B1744]/10 sticky top-0 z-30 px-6 lg:px-10 py-4">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 overflow-hidden rounded-xl bg-white p-1 border border-gray-200 shadow-xs">
                    <img src="{{ asset('logo-chatgpt.png') }}" alt="Study Planner" class="w-full h-full object-contain">
                </div>
                <span class="font-bold text-lg text-gray-900 group-hover:text-[#5B1744] transition">Study Planner</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="text-xs font-semibold text-gray-600 hover:text-[#5B1744] px-3 py-2 rounded-xl transition">
                    &larr; Beranda
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-xs font-semibold bg-[#5B1744] text-white px-4 py-2 rounded-full hover:bg-[#481236] transition shadow-xs">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-semibold bg-[#5B1744] text-white px-4 py-2 rounded-full hover:bg-[#481236] transition shadow-xs">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-5 sm:px-8 py-10 sm:py-14">
        <div class="bg-white rounded-3xl p-7 sm:p-12 border border-[#5B1744]/10 shadow-xl shadow-[#5B1744]/5 prose-legal">
            
            <div class="border-b border-gray-100 pb-6 mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#F4E7EF] text-[#5B1744] text-xs font-semibold mb-3">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Kebijakan Privasi Resmi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 serif tracking-tight">Kebijakan Privasi (Privacy Policy)</h1>
                <p class="text-xs text-gray-400 mt-2">Terakhir diperbarui: 3 November 2026</p>
            </div>

            <p>
                Selamat datang di <strong>Study Planner</strong> (dapat diakses melalui <a href="https://studyplanner.web.id" class="text-[#5B1744] font-medium hover:underline">https://studyplanner.web.id</a>). Kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, serta menjaga kerahasiaan data Anda saat menggunakan layanan Study Planner.
            </p>

            <h2><i class="fa-solid fa-user-check text-base"></i> 1. Informasi yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan data berikut untuk menyediakan fungsionalitas manajemen belajar:</p>
            <ul>
                <li><strong>Informasi Akun:</strong> Nama lengkap, alamat email, dan kata sandi terenkripsi saat Anda mendaftar langsung atau melalui Google Sign-In.</li>
                <li><strong>Data Rencana Belajar (Akademik):</strong> Jadwal belajar (schedule), daftar tugas dan sub-langkah (tasks), jadwal pelajaran kelas (timetables), catatan materi (notes), serta sesi fokus belajar (Pomodoro timer logs).</li>
                <li><strong>Data Otentikasi Pihak Ketiga (OAuth):</strong> Token akses (access token) dan token penyegaran (refresh token) saat Anda menghubungkan akun Google (Google Calendar & Google Tasks) atau Telegram.</li>
            </ul>

            <h2><i class="fa-brands fa-google text-base"></i> 2. Penggunaan Data Google & Kepatuhan Terhadap Kebijakan Google</h2>
            <p>
                Aplikasi Study Planner meminta izin akses ke layanan Google (Google Calendar API dan Google Tasks API) semata-mata untuk menyediakan fitur integrasi berikut:
            </p>
            <ul>
                <li><strong>Google Calendar (Scope: <code>calendar</code>, <code>calendar.events</code>):</strong> Digunakan untuk menyinkronkan agenda sesi belajar dan jadwal pelajaran yang Anda buat di Study Planner ke kalender utama Google Anda, serta mengimpor event belajar dari kalender Anda agar tidak terjadi bentrok jadwal.</li>
                <li><strong>Google Tasks (Scope: <code>tasks</code>):</strong> Digunakan untuk menyinkronkan daftar to-do tugas sekolah atau tugas belajar Anda antara Study Planner dan Google Tasks secara dua arah.</li>
            </ul>

            <div class="my-5 p-4 rounded-2xl bg-[#F4E7EF]/50 border border-[#E7C8DB] text-xs leading-relaxed text-[#5B1744]">
                <strong class="font-bold flex items-center gap-1.5 mb-1 text-sm">
                    <i class="fa-solid fa-circle-check"></i> Kepatuhan Kebijakan Penggunaan Terbatas Google (Google Limited Use Disclosure)
                </strong>
                Penggunaan dan transfer informasi yang diterima dari Google API oleh Study Planner ke aplikasi lain akan sepenuhnya mematuhi 
                <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" class="underline font-semibold hover:text-[#481236]">Google API Services User Data Policy</a>, termasuk persyaratan Penggunaan Terbatas (Limited Use requirements).
            </div>

            <p>Secara khusus, kami menegaskan komitmen berikut:</p>
            <ul>
                <li><strong>Bukan untuk Iklan:</strong> Data pengguna Google Anda tidak akan pernah digunakan untuk melayani iklan bertarget atau periklanan apa pun.</li>
                <li><strong>Tidak Dijual / Dialihkan:</strong> Kami tidak pernah menjual, menyewakan, atau mentransfer data pengguna Google Anda kepada pihak ketiga.</li>
                <li><strong>Bukan untuk Melatih AI Umum:</strong> Data Google Anda tidak digunakan untuk melatih model pembelajaran mesin (AI) umum pihak ketiga. Interaksi AI di Study Planner hanya memproses konteks belajar secara real-time dan aman.</li>
                <li><strong>Akses Manusia Terbatas:</strong> Tidak ada staf atau manusia yang membaca data Google Anda kecuali Anda memberikan izin tertulis untuk keperluan pemecahan masalah teknis, atau jika diwajibkan oleh hukum yang berlaku.</li>
            </ul>

            <h2><i class="fa-solid fa-lock text-base"></i> 3. Penyimpanan & Keamanan Data</h2>
            <p>
                Kami menerapkan langkah-langkah keamanan teknis standar industri untuk melindungi data Anda. Kata sandi di-hash menggunakan algoritma Bcrypt yang aman. Token akses OAuth disimpan secara aman dalam database terlindungi dengan pembatasan hak akses yang ketat. Semua transmisi data antara peramban Anda dan server kami dienkripsi menggunakan protokol HTTPS/TLS modern.
            </p>

            <h2><i class="fa-solid fa-user-gear text-base"></i> 4. Kontrol Pengguna & Pencabutan Izin</h2>
            <p>Anda memegang kendali penuh atas data dan integrasi akun Anda:</p>
            <ul>
                <li><strong>Memutuskan Integrasi:</strong> Anda dapat mencabut atau memutuskan koneksi integrasi Google Calendar & Tasks kapan saja melalui menu <em>Integrations</em> di Study Planner.</li>
                <li><strong>Mencabut Akses di Google:</strong> Anda juga dapat mencabut izin Study Planner langsung melalui pengaturan akun Google Anda di <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener noreferrer" class="text-[#5B1744] underline font-medium">https://myaccount.google.com/permissions</a>.</li>
                <li><strong>Penghapusan Akun & Data:</strong> Anda dapat meminta penghapusan akun serta seluruh data terkait secara permanen dengan menghubungi kami melalui email dukungan.</li>
            </ul>

            <h2><i class="fa-solid fa-cookie-bite text-base"></i> 5. Penggunaan Cookie</h2>
            <p>
                Study Planner menggunakan cookie sesi (session cookies) murni untuk keperluan otentikasi login, menjaga keamanan sesi pengguna, dan menyimpan preferensi tampilan. Kami tidak menggunakan cookie pelacak pihak ketiga untuk keperluan periklanan.
            </p>

            <h2><i class="fa-solid fa-pen-nib text-base"></i> 6. Perubahan Kebijakan Privasi</h2>
            <p>
                Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu agar selaras dengan pembaruan fitur atau regulasi hukum. Versi terbaru akan selalu dipublikasikan di halaman ini dengan tanggal pembaruan yang tercantum di bagian atas.
            </p>

            <h2><i class="fa-solid fa-envelope text-base"></i> 7. Kontak Kami</h2>
            <p>
                Jika Anda memiliki pertanyaan, saran, atau permintaan terkait privasi data Anda, silakan hubungi kami melalui:
            </p>
            <ul class="list-none !pl-0 mt-3 space-y-1.5 font-medium text-gray-700">
                <li><i class="fa-solid fa-envelope mr-2 text-[#5B1744]"></i> Email: <a href="mailto:studyplannerlearnify@gmail.com" class="text-[#5B1744] hover:underline">studyplannerlearnify@gmail.com</a></li>
                <li><i class="fa-solid fa-globe mr-2 text-[#5B1744]"></i> Website: <a href="https://studyplanner.web.id" class="text-[#5B1744] hover:underline">https://studyplanner.web.id</a></li>
            </ul>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-gray-200 bg-white py-6 mt-10">
        <div class="max-w-5xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} Study Planner. Hak Cipta Dilindungi.</p>
            <div class="flex gap-4">
                <a href="{{ route('privacy') }}" class="text-[#5B1744] font-semibold">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="hover:text-[#5B1744] transition">Ketentuan Layanan</a>
                <a href="{{ url('/') }}" class="hover:text-[#5B1744] transition">Beranda</a>
            </div>
        </div>
    </footer>

</body>
</html>

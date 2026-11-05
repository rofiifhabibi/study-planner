<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ketentuan Layanan (Terms of Service) — Study Planner</title>

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
                    <i class="fa-solid fa-file-contract"></i>
                    <span>Ketentuan Layanan Resmi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 serif tracking-tight">Ketentuan Layanan (Terms of Service)</h1>
                <p class="text-xs text-gray-400 mt-2">Terakhir diperbarui: 3 November 2026</p>
            </div>

            <p>
                Selamat datang di <strong>Study Planner</strong>. Dengan mendaftar, mengakses, atau menggunakan layanan kami di <a href="https://studyplanner.web.id" class="text-[#5B1744] font-medium hover:underline">https://studyplanner.web.id</a>, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat oleh Syarat dan Ketentuan Layanan ini. Jika Anda tidak menyetujui ketentuan ini, Anda disarankan untuk tidak menggunakan layanan kami.
            </p>

            <h2><i class="fa-solid fa-graduation-cap text-base"></i> 1. Deskripsi Layanan</h2>
            <p>
                Study Planner adalah platform aplikasi produktivitas dan perencanaan belajar mandiri yang menyediakan berbagai fitur, antara lain:
            </p>
            <ul>
                <li>Manajemen tugas akademik dan daftar checklist (My Tasks).</li>
                <li>Penyusunan jadwal agenda belajar harian dan mingguan (Study Schedule).</li>
                <li>Pencatatan jadwal mata pelajaran sekolah (Class Schedule / Timetable).</li>
                <li>Pencatatan materi dan ringkasan belajar (My Notes).</li>
                <li>Timer fokus Pomodoro dengan pencatatan durasi belajar (Study Progress).</li>
                <li>Asisten belajar berbasis kecerdasan buatan (AI Study).</li>
                <li>Integrasi kalender dan to-do eksternal (Google Calendar & Google Tasks).</li>
            </ul>

            <h2><i class="fa-solid fa-id-card text-base"></i> 2. Akun Pengguna & Keamanan</h2>
            <p>Untuk menggunakan fitur Study Planner secara penuh, Anda wajib membuat akun:</p>
            <ul>
                <li>Anda setuju untuk memberikan informasi pendaftaran yang benar, akurat, dan terkini.</li>
                <li>Anda bertanggung jawab penuh untuk menjaga kerahasiaan kata sandi akun Anda dan seluruh aktivitas yang terjadi di bawah akun Anda.</li>
                <li>Segera beri tahu kami jika Anda mencurigai adanya penggunaan akun tanpa izin atau pelanggaran keamanan lainnya.</li>
            </ul>

            <h2><i class="fa-solid fa-shield-virus text-base"></i> 3. Penggunaan yang Diperbolehkan & Larangan</h2>
            <p>Saat menggunakan Study Planner, Anda setuju untuk TIDAK:</p>
            <ul>
                <li>Menggunakan layanan untuk tujuan apa pun yang melanggar hukum atau peraturan perundang-undangan yang berlaku.</li>
                <li>Mengunggah atau membagikan konten yang mengandung materi berbahaya, virus, malware, pelecehan, atau ujaran kebencian.</li>
                <li>Mencoba mendapatkan akses tidak sah ke sistem, server, jaringan, atau basis data Study Planner.</li>
                <li>Mengganggu kinerja atau membebani infrastruktur server secara tidak wajar melalui tindakan otomatisasi (bot/scraping) tanpa izin.</li>
            </ul>

            <h2><i class="fa-brands fa-google text-base"></i> 4. Integrasi Layanan Pihak Ketiga (Google & Telegram)</h2>
            <p>
                Study Planner menyediakan integrasi opsional dengan layanan pihak ketiga, termasuk Google Calendar API, Google Tasks API, dan Bot Telegram:
            </p>
            <ul>
                <li>Koneksi ke layanan pihak ketiga memerlukan otorisasi eksplisit dari Anda melalui protokol OAuth 2.0.</li>
                <li>Penggunaan data dari API Google mematuhi secara ketat <em>Google API Services User Data Policy</em> dan hanya dimanfaatkan untuk menyinkronkan jadwal belajar serta daftar to-do Anda.</li>
                <li>Anda dapat memutuskan koneksi layanan pihak ketiga ini kapan saja melalui menu Integrasi di Study Planner atau mencabut izin di pengaturan akun Google Anda.</li>
            </ul>

            <h2><i class="fa-solid fa-copyright text-base"></i> 5. Hak Kekayaan Intelektual</h2>
            <p>
                Semua konten, logo, desain antarmuka, kode sumber, dan hak kekayaan intelektual platform Study Planner adalah milik pengembang Study Planner. Konten materi belajar, tugas, dan catatan yang Anda masukkan ke dalam sistem tetap menjadi hak milik Anda sepenuhnya.
            </p>

            <h2><i class="fa-solid fa-triangle-exclamation text-base"></i> 6. Penafian & Batasan Tanggung Jawab</h2>
            <p>
                Layanan Study Planner disediakan sebagaimana adanya (<em>"as is"</em>) dan sebagaimana tersedia (<em>"as available"</em>). Kami berusaha sebaik mungkin menjaga keandalan dan ketersediaan sistem, namun kami tidak menjamin bahwa layanan akan bebas dari gangguan teknis setiap saat. Kami tidak bertanggung jawab atas kerugian tidak langsung atau kehilangan data akibat gangguan jaringan atau kegagalan pihak ketiga di luar kendali wajar kami.
            </p>

            <h2><i class="fa-solid fa-clock-rotate-left text-base"></i> 7. Penghentian Akun</h2>
            <p>
                Kami berhak menangguhkan atau menghentikan akun Anda jika ditemukan pelanggaran serius terhadap Ketentuan Layanan ini. Anda juga berhak menghapus akun Anda sewaktu-waktu jika tidak lagi ingin menggunakan platform Study Planner.
            </p>

            <h2><i class="fa-solid fa-pen-to-square text-base"></i> 8. Perubahan Ketentuan Layanan</h2>
            <p>
                Kami dapat merevisi Syarat dan Ketentuan Layanan ini sewaktu-waktu. Perubahan akan berlaku segera setelah dipublikasikan pada halaman ini. Penggunaan berkelanjutan atas layanan setelah perubahan dipublikasikan dianggap sebagai persetujuan Anda terhadap ketentuan yang diperbarui.
            </p>

            <h2><i class="fa-solid fa-headset text-base"></i> 9. Kontak & Informasi</h2>
            <p>
                Jika Anda memiliki pertanyaan mengenai Ketentuan Layanan ini, silakan hubungi tim kami melalui:
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
                <a href="{{ route('privacy') }}" class="hover:text-[#5B1744] transition">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="text-[#5B1744] font-semibold">Ketentuan Layanan</a>
                <a href="{{ url('/') }}" class="hover:text-[#5B1744] transition">Beranda</a>
            </div>
        </div>
    </footer>

</body>
</html>

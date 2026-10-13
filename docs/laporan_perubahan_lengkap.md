# 📄 LAPORAN PERUBAHAN TEKNIS PROYEK STUDY PLANNER

**Tanggal Dokumen:** 13 Oktober 2026  
**Aplikasi:** Study Planner (Laravel 11/12 Web App)  
**Pengembang:** Tim Pengembang Study Planner  

---

## 1. RINGKASAN EKSEKUTIF (EXECUTIVE SUMMARY)

Proyek **Study Planner** telah mengalami modernisasi dan perombakan arsitektur besar dari sistem berbasis *middleware self-hosted* menuju arsitektur *modern cloud serverless*. Tiga pilar utama perubahan yang dilakukan meliputi:

1. **Migrasi AI Engine**: Mengganti alur integrasi chatbot yang sebelumnya bergantung pada workflow eksternal **n8n** menjadi **Direct API Integration** menggunakan **Groq Cloud API** (`qwen/qwen3.8-27b`) dengan kecepatan inferensi ultra-cepat (*sub-second*).
2. **Migrasi Database**: Beralih dari basis data lokal (SQLite/MySQL) ke basis data cloud terkelola **Supabase PostgreSQL** dengan konfigurasi *Connection Pooling* (port 6543 / PgBouncer).
3. **Arsitektur Deployment Serverless**: Menyiapkan konfigurasi deployment ke platform **Vercel** menggunakan runtime serverless PHP (`vercel-community/php`), memindahkan sesi ke basis data (*stateless filesystem*), dan merapikan Google OAuth 2.0.

---

## 2. DETAIL PERUBAHAN SISTEM & KODE PROGRAM

### A. Komponen Kecerdasan Buatan (AI Engine)

#### Kondisi Sebelumnya:
* Aplikasi Laravel mengirim pesan pengguna melalui HTTP POST webhook ke server **n8n**.
* n8n memproses memory chat menggunakan Redis, melakukan *load balancing* acak ke beberapa provider AI, lalu memanggil endpoint `AiIntegrationController` kembali melalui HTTP callback dengan secret token khusus (`X-API-KEY`).
* **Kelemahan:** Latensi tinggi (banyak lompatan jaringan), overhead server n8n yang berat, dan rawan *timeout*.

#### Kondisi Sesudah (Baru):
* **Layanan Baru (`app/Services/GroqAiService.php`)**:
  * Berkomunikasi langsung via HTTPS ke endpoint resmi Groq API (`https://api.groq.com/openai/v1/chat/completions`).
  * Menggunakan model berkinerja tinggi yang stabil dan fasih berbahasa Indonesia: **`qwen/qwen3.8-27b`** (menggantikan `llama-3.1-70b-versatile` yang telah *decommissioned* oleh penyedia).
  * System prompt bertindak sebagai **StudyBot**—tutor dan asisten studi siswa/mahasiswa Indonesia dengan gaya bahasa terstruktur, ramah, dan solutif.
* **Refactor `app/Http/Controllers/ChatController.php`**:
  * Menghapus seluruh dependensi webhook n8n (`services.n8n.webhook_url`, `X-API-KEY`).
  * Riwayat percakapan (*chat context*) diambil langsung dari tabel database `chat_messages` melalui method `buildHistoryFromSession()` dan digabungkan otomatis jika percakapan berada di dalam *project session*.
* **Refactor `app/Http/Controllers/AiIntegrationController.php`**:
  * Mengganti validasi header kustom n8n menjadi standar industri **Bearer Token** (`AI_API_SECRET`).
* **Pembersihan Rute & View**:
  * Di [`routes/api.php`](file:///home/Astraa/Documents/study-planner/routes/api.php), anotasi n8n dibersihkan menjadi endpoint AI Tool universal.

---

### B. Komponen Basis Data (Supabase PostgreSQL)

#### Kondisi Sebelumnya:
* Menggunakan basis data lokal SQLite (`database.sqlite`) / MySQL lokal. Tidak dapat diakses secara persisten oleh platform cloud serverless seperti Vercel yang bersifat *read-only filesystem*.

#### Kondisi Sesudah (Baru):
* **Penyedia Cloud:** Supabase PostgreSQL 17 (Region: `ap-southeast-1` Singapore).
* **Konfigurasi Pooling:** Menggunakan port pooler PgBouncer **6543** (`aws-0-ap-southeast-1.pooler.supabase.com:6543`) untuk mencegah kehabisan koneksi (*connection exhaustion*) saat diakses oleh fungsi serverless Vercel.
* **Session Management:** Variabel `SESSION_DRIVER` diatur ke **`database`**, menyimpan session state pengguna langsung ke tabel PostgreSQL, bukan ke disk server.

---

### C. Komponen Deployment Cloud (Vercel Serverless)

File-file baru yang ditambahkan ke repositori:

1. **[`vercel.json`](file:///home/Astraa/Documents/study-planner/vercel.json)**:
   * Mengatur fungsi runtime PHP komunitas: `vercel-community/php@0.0.8`.
   * Mengarahkan asset statis (`/build/*`, CSS, JS, font, gambar) langsung disajikan oleh CDN Edge Vercel.
   * Mengarahkan seluruh lalu lintas aplikasi web ke entry point `api/index.php`.
2. **[`api/index.php`](file:///home/Astraa/Documents/study-planner/api/index.php)**:
   * File jembatan (*serverless handler*) yang memanggil `public/index.php` milik Laravel.
3. **[`.vercelignore`](file:///home/Astraa/Documents/study-planner/.vercelignore)**:
   * Mengabaikan folder `.git`, `node_modules`, `tests`, dan file log agar ukuran deployment ringan dan proses build cepat.

---

## 3. DAFTAR FILE YANG DIBUAT DAN DIUBAH

| No | File Path | Tipe Perubahan | Penjelasan Perubahan |
|---|---|---|---|
| 1 | `app/Services/GroqAiService.php` | **File Baru** | Service class untuk direct chat completion dan penyusun riwayat pesan via Groq API. |
| 2 | `app/Http/Controllers/ChatController.php` | **Dimodifikasi** | Menghapus integrasi n8n, memanggil `GroqAiService`, dan menangani pesan chat langsung. |
| 3 | `app/Http/Controllers/AiIntegrationController.php` | **Dimodifikasi** | Memperbarui autentikasi dari `X-API-KEY` n8n ke Bearer Token (`AI_API_SECRET`). |
| 4 | `config/services.php` | **Dimodifikasi** | Menghapus blok konfigurasi `n8n`; menambahkan blok `groq` dan `ai`. |
| 5 | `routes/api.php` | **Dimodifikasi** | Merapikan penamaan grup rute AI integrasi. |
| 6 | `.env.example` & `.env` | **Dimodifikasi / Baru** | Mengganti variabel n8n dengan `GROQ_API_KEY`, `GROQ_MODEL`, `DB_URL` (Supabase), dan `SESSION_DRIVER=database`. |
| 7 | `vercel.json` | **File Baru** | Konfigurasi runtime PHP dan routing Vercel. |
| 8 | `api/index.php` | **File Baru** | Entry point serverless untuk Vercel. |
| 9 | `.vercelignore` | **File Baru** | Daftar file yang dikecualikan dari proses upload Vercel. |
| 10 | `tests/Unit/GroqAiServiceTest.php` | **File Baru** | Pengujian unit otomatis untuk respon Groq API, error handling, dan session history. |
| 11 | `tests/Feature/ChatTest.php` | **Dimodifikasi** | Mengubah pengujian fungsional dari simulasi n8n menjadi mock `GroqAiService`. |
| 12 | `tests/Pest.php` | **Dimodifikasi** | Menyesuaikan binding `TestCase` untuk suite test Pest. |

---

## 4. HASIL VERIFIKASI & PENGUJIAN SISTEM (TESTING EVIDENCE)

### 1. Uji Koneksi Database Supabase
* **Status:** ✅ **LULUS (Passed)**
* **Hasil:**
  ```text
  KONEKSI SUPABASE BERHASIL!
  PostgreSQL Version: PostgreSQL 17.11 on aarch64-unknown-linux-gnu, compiled by gcc (GCC) 15.2.0, 64-bit
  Koneksi TCP ke host aws-0-ap-southeast-1.pooler.supabase.com:6543 terverifikasi terbuka.
  ```

### 2. Uji Langsung Integrasi Groq AI
* **Status:** ✅ **LULUS (Passed)**
* **Prompt Uji:** *"Halo StudyBot! Jelaskan secara singkat apa itu fotosintesis."*
* **Respon Nyata:** AI merespons secara instan dalam bahasa Indonesia terstruktur lengkap dengan analogi memasak, bahan baku ($H_2O$ & $CO_2$), hasil reaksi ($C_6H_{12}O_6$ & $O_2$), dan rumus kimia yang rapi.

### 3. Uji Standardisasi Kode (Pint Linter)
* **Status:** ✅ **LULUS (Passed)**
* Seluruh file PHP telah diformat menggunakan Laravel Pint (`vendor/bin/pint --dirty --format agent`) dan dinyatakan bersih tanpa error sintaks (`php -l` lulus 100%).

---

## 5. REVISI NASKAH LAPORAN AKADEMIK (GOOGLE DOCS)

Untuk memperbarui isi dokumen laporan ilmiah proyek kamu di Google Docs (*A Simple Report: Study Planner*), berikut adalah poin-poin yang perlu disesuaikan:

1. **Bagian 1 (Introduction):**
   * *Revisi:* Menghapus penyebutan "workflow automation powered by n8n multi-agent".
   * *Baru:* Disebutkan menggunakan **Direct API Integration via Groq Cloud LLM engine** dan **Serverless Cloud Deployment (Vercel & Supabase)**.
2. **Bagian 2 (Method/Procedure - Development):**
   * *Revisi Poin 1:* Basis data yang dirancang dan digunakan adalah relasional **PostgreSQL pada Supabase Cloud**.
   * *Revisi Poin 5:* Mengganti n8n workflow pipeline dengan pembuatan service internal **`GroqAiService`** dan konfigurasi serverless runtime **`vercel.json`**.
3. **Bagian 3 (Findings / Evidence):**
   * *Tabel 1 (Baris 2 & 8):* Status pengujian Add Task mencatat penyimpanan di Supabase PostgreSQL, dan pengujian AI Chatbot mencatat perutean langsung ke Groq API dengan context session database.
   * *Keterangan Figure 5:* Diperbarui menjadi "AI Study Virtual Assistant Page connected directly to Groq AI inference engine".
4. **Bagian 4 & 5 (Discussion & Conclusion):**
   * Pembahasan mencatat keuntungan transisi arsitektur: pengurangan latensi respon secara drastis, penghapusan ketergantungan server perantara (*middleware elimination*), dan skalabilitas otomatis melalui hosting serverless di Vercel.

---

Dokumen laporan lengkap ini merangkum seluruh pekerjaan teknis yang telah dikerjakan dan siap digunakan sebagai bukti pertanggungjawaban proyek.

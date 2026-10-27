@extends('layouts.study')

@section('title', 'Schedule — Study Planner')
@section('page-label', 'MY SCHEDULE')

@php
    $activeNav = 'schedule';
@endphp

@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <style>
        .fc-event {
            cursor: pointer;
            border-radius: 6px;
            padding: 2px 4px;
            border: none !important;
            background-color: #F4E7EF !important;
            color: #5B1744 !important;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .fc-event:hover {
            background-color: #5B1744 !important;
            color: #ffffff !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #111827;
        }
        .fc-button-primary {
            background-color: #5B1744 !important;
            border-color: #5B1744 !important;
            border-radius: 9999px !important;
            font-weight: 600 !important;
            font-size: 0.78rem !important;
            padding: 0.4rem 0.85rem !important;
            transition: all 0.2s ease !important;
        }
        .fc-button-primary:hover, .fc-button-primary:focus {
            background-color: #481236 !important;
            border-color: #481236 !important;
        }
        .fc-button-primary:disabled {
            background-color: #FAF6F0 !important;
            border-color: #E7C8DB !important;
            color: #9CA3AF !important;
            opacity: 0.65;
        }
        .fc-day-today {
            background-color: #FAF6F0 !important;
        }
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #F3F4F6 !important;
        }
        .fc-col-header-cell {
            background-color: #FAFAFA !important;
            padding: 6px 0 !important;
        }
        .fc-col-header-cell-cushion {
            font-size: 0.78rem !important;
            font-weight: 700 !important;
            color: #4B5563 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }

        /* Mobile Adjustments */
        @media (max-width: 640px) {
            .fc .fc-toolbar {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.65rem !important;
                align-items: center !important;
                margin-bottom: 0.85rem !important;
            }
            .fc .fc-toolbar-title {
                font-size: 1.05rem !important;
                text-align: center !important;
            }
            .fc .fc-toolbar-chunk {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                flex-wrap: wrap !important;
                gap: 0.35rem !important;
                width: 100% !important;
            }
            .fc .fc-button {
                padding: 0.35rem 0.65rem !important;
                font-size: 0.72rem !important;
            }
            .fc .fc-col-header-cell-cushion {
                font-size: 0.7rem !important;
                padding: 4px 1px !important;
            }
            .fc .fc-daygrid-day-number {
                font-size: 0.72rem !important;
                padding: 3px 4px !important;
            }
            .fc .fc-daygrid-day-frame {
                min-height: 52px !important;
            }
            .fc .fc-daygrid-event {
                font-size: 0.65rem !important;
                padding: 1px 3px !important;
                margin-top: 1px !important;
            }
            .fc .fc-more-link {
                font-size: 0.65rem !important;
                color: #5B1744 !important;
                font-weight: 700 !important;
            }
        }
    </style>
@endsection

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Your schedule</h1>
            <p class="text-sm text-gray-500 mt-1">Atur jadwal belajarmu dalam tampilan kalender.</p>
        </div>
        <button onclick="openScheduleModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0"
            data-tour-step="1" data-tour-title="Tambah Agenda Belajar" data-tour-desc="Jadwalkan sesi belajar, ulangan, atau target belajar tertentu ke kalendermu.">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Schedule</span>
        </button>
    </section>

    <section class="bg-white rounded-2xl sm:rounded-3xl border border-gray-100 p-3.5 sm:p-6 md:p-7 soft-shadow"
        data-tour-step="2" data-tour-title="Kalender Jadwal Interaktif" data-tour-desc="Pantau seluruh agenda belajar bulanan atau mingguan, klik tanggal atau agenda untuk melihat detail.">
        <div id="calendar"></div>
    </section>

@endsection

@push('modals')
    {{-- ADD SCHEDULE MODAL (Cartoonish & Simple Redesign) --}}
    <div id="scheduleModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div onclick="closeScheduleModal()" class="absolute inset-0 bg-black/50 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-lg bg-[#FAF6F0] rounded-[2rem] p-6 sm:p-7 shadow-[0_0_60px_rgba(0,0,0,0.4),0_20px_40px_rgba(91,23,68,0.25)] border-2 border-[#E7C8DB] max-h-[90vh] overflow-y-auto my-auto">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-[#E7C8DB]/60 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] flex items-center justify-center shrink-0 text-[#5B1744] text-lg">
                        <i class="fa-regular fa-calendar-plus"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full">Jadwal Belajar</span>
                        <h2 class="text-lg font-bold text-gray-900 mt-1">Tambah Agenda Belajar</h2>
                    </div>
                </div>
                <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-full bg-white border border-[#E7C8DB] flex items-center justify-center text-gray-500 hover:bg-[#F4E7EF] hover:text-[#5B1744] transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="scheduleForm" class="space-y-4">
                @csrf
                {{-- 1. Judul Jadwal --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Materi / Judul</span>
                        <span class="text-[10px] text-red-500 font-semibold">*Wajib</span>
                    </label>
                    <input type="text" name="title" required placeholder="Contoh: Belajar UTBK / Review Database" 
                        class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                </div>

                {{-- 2. Mata Pelajaran / Topik --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Mata Pelajaran</span>
                        <span class="text-[10px] text-gray-400">Opsional</span>
                    </label>
                    <input type="text" name="subject" placeholder="Contoh: Matematika Diskrit / Sistem Operasi" 
                        class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                </div>

                {{-- 3. Kapan Belajarnya? --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Tanggal</span>
                        <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                    </label>
                    <div class="flex items-center gap-2 mt-2 mb-2.5" id="scheduleQuickDateGroup">
                        <button type="button" onclick="setScheduleQuickDate(0, this)" class="sched-quick-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-[#5B1744] text-white border border-[#5B1744] shadow-xs transition">Hari Ini</button>
                        <button type="button" onclick="setScheduleQuickDate(1, this)" class="sched-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition">Besok</button>
                        <button type="button" onclick="setScheduleQuickDate(2, this)" class="sched-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition">Lusa</button>
                    </div>
                    <div>
                        <input type="date" name="date" id="scheduleDate" required value="{{ date('Y-m-d') }}" 
                            class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                    </div>
                </div>

                {{-- 4. Jam Belajar --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Waktu Sesi Belajar</span>
                        <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                    </label>

                    {{-- Preset Jam Belajar --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 mt-2 mb-3">
                        <button type="button" onclick="setScheduleTimePreset('08:00', '09:30', this)" class="time-preset-chip py-1.5 px-2 rounded-xl text-xs font-bold border transition text-center bg-[#5B1744] text-white border-[#5B1744] shadow-xs">
                            Pagi (08:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('13:00', '14:30', this)" class="time-preset-chip py-1.5 px-2 rounded-xl text-xs font-semibold border transition text-center bg-white text-gray-700 border-gray-200 hover:bg-[#F4E7EF]">
                            Siang (13:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('16:00', '17:30', this)" class="time-preset-chip py-1.5 px-2 rounded-xl text-xs font-semibold border transition text-center bg-white text-gray-700 border-gray-200 hover:bg-[#F4E7EF]">
                            Sore (16:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('19:30', '21:00', this)" class="time-preset-chip py-1.5 px-2 rounded-xl text-xs font-semibold border transition text-center bg-white text-gray-700 border-gray-200 hover:bg-[#F4E7EF]">
                            Malam (19:30)
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-gray-500 font-medium block mb-1">Mulai:</span>
                            <input type="time" name="start_time" id="startTime" required value="08:00" 
                                class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-500 font-medium block mb-1">Selesai:</span>
                            <input type="time" name="end_time" id="endTime" required value="09:30" 
                                class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                    class="w-full py-3.5 rounded-2xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs sm:text-sm font-bold transition shadow-lg shadow-[#5B1744]/20 flex items-center justify-center gap-2 mt-2 transform active:scale-98">
                    <i class="fa-solid fa-check text-xs"></i> <span>Simpan ke Jadwal</span>
                </button>
            </form>
        </div>
    </div>

    {{-- DETAIL / DELETE SCHEDULE MODAL --}}
    <div id="deleteScheduleModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div onclick="closeDeleteScheduleModal()" class="absolute inset-0 bg-black/50 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-[0_0_60px_rgba(0,0,0,0.4),0_20px_40px_rgba(91,23,68,0.25)] border-2 border-[#E7C8DB] my-auto">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">DETAIL AGENDA</p>
                    <h2 class="text-lg font-bold text-gray-900 mt-0.5">Informasi Jadwal</h2>
                </div>
                <button type="button" onclick="closeDeleteScheduleModal()" class="w-8 h-8 rounded-full bg-white border border-[#E7C8DB] flex items-center justify-center text-gray-500 hover:bg-[#F4E7EF] hover:text-[#5B1744] transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <div class="p-3.5 rounded-2xl bg-white border border-[#E7C8DB] mb-4">
                <p class="text-sm font-bold text-gray-900" id="deleteScheduleTitle">Agenda Belajar</p>
                <p class="text-xs text-[#5B1744] font-medium mt-1 flex items-center gap-1.5" id="deleteScheduleTime">
                    <i class="fa-regular fa-clock text-[10px]"></i> <span>-</span>
                </p>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed mb-5">
                Ingin menghapus jadwal ini dari kalender dan Google Calendar?
            </p>

            <div class="flex gap-2.5">
                <button type="button" onclick="closeDeleteScheduleModal()"
                    class="flex-1 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition">
                    Tutup
                </button>
                <button type="button" id="confirmDeleteScheduleBtn" onclick="confirmDeleteSchedule()"
                    class="flex-1 py-2.5 rounded-xl bg-[#B91C1C] hover:bg-[#991B1B] text-white text-xs font-bold transition shadow-xs flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-trash text-[10px]"></i> <span>Hapus</span>
                </button>
            </div>
        </div>
    </div>

@push('scripts')

    const scheduleModal = document.getElementById('scheduleModal');
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        const isMobile = window.innerWidth < 768;
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: isMobile ? 'prev,next' : 'prev,next today',
                center: 'title',
                right: isMobile ? 'dayGridMonth,timeGridDay' : 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hari ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            dayMaxEvents: isMobile ? 2 : 4,
            aspectRatio: isMobile ? 1.05 : 1.45,
            contentHeight: isMobile ? 480 : undefined,
            titleFormat: isMobile ? { month: 'short', year: 'numeric' } : { month: 'long', year: 'numeric' },
            events: `${API_BASE}/schedules`,
            dateClick: function(info) {
                document.getElementById('scheduleDate').value = info.dateStr;
                openScheduleModal();
            },
            eventClick: function(info) {
                const title = info.event.title;
                const start = info.event.start ? info.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                const end = info.event.end ? info.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                const dateStr = info.event.start ? info.event.start.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '';
                const timeStr = dateStr + (start ? ` · ${start}${end ? ' - ' + end : ''}` : '');
                deleteSchedule(info.event.id, title, timeStr);
            }
        });
        calendar.render();
    });

    function setScheduleQuickDate(offsetDays, btn) {
        const d = new Date();
        d.setDate(d.getDate() + offsetDays);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const inp = document.getElementById('scheduleDate');
        if (inp) inp.value = `${yyyy}-${mm}-${dd}`;

        document.querySelectorAll('#scheduleQuickDateGroup .sched-quick-btn').forEach(b => {
            b.className = 'sched-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition';
        });
        if (btn) {
            btn.className = 'sched-quick-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-[#5B1744] text-white border border-[#5B1744] shadow-xs transition';
        }
    }

    function setScheduleTimePreset(start, end, btn) {
        const startInp = document.getElementById('startTime');
        const endInp = document.getElementById('endTime');
        if (startInp) startInp.value = start;
        if (endInp) endInp.value = end;

        document.querySelectorAll('.time-preset-chip').forEach(b => {
            b.className = 'time-preset-chip py-1.5 px-2 rounded-xl text-xs font-semibold border transition text-center bg-white text-gray-700 border-gray-200 hover:bg-[#F4E7EF]';
        });
        if (btn) {
            btn.className = 'time-preset-chip py-1.5 px-2 rounded-xl text-xs font-bold border transition text-center bg-[#5B1744] text-white border-[#5B1744] shadow-xs';
        }
    }

    function openScheduleModal() {
        const todayBtn = document.querySelector('#scheduleQuickDateGroup .sched-quick-btn:first-child');
        if (todayBtn) {
            setScheduleQuickDate(0, todayBtn);
        }
        scheduleModal.classList.remove('hidden');
        scheduleModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeScheduleModal() {
        scheduleModal.classList.add('hidden');
        scheduleModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('scheduleForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Adding...';

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await apiFetch(`${API_BASE}/schedules`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const result = await res.json();

            if (result.status === 'success') {
                closeScheduleModal();
                form.reset();
                calendar.refetchEvents();
            } else {
                alert(result.message || 'Gagal menambahkan jadwal.');
            }
        } catch (err) {
            alert('Terjadi kesalahan saat menambahkan jadwal.');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    const deleteScheduleModal = document.getElementById('deleteScheduleModal');
    let pendingDeleteId = null;

    function deleteSchedule(id, title = '', timeStr = '') {
        pendingDeleteId = id;
        const titleEl = document.getElementById('deleteScheduleTitle');
        const timeEl = document.getElementById('deleteScheduleTime');
        if (titleEl) titleEl.textContent = title || 'Agenda Belajar';
        if (timeEl) timeEl.innerHTML = `<i class="fa-regular fa-clock text-[10px]"></i> <span>${timeStr || '-'}</span>`;
        deleteScheduleModal.classList.remove('hidden');
        deleteScheduleModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteScheduleModal() {
        pendingDeleteId = null;
        deleteScheduleModal.classList.add('hidden');
        deleteScheduleModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    async function confirmDeleteSchedule() {
        const id = pendingDeleteId;
        if (id === null) return;

        const btn = document.getElementById('confirmDeleteScheduleBtn');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        try {
            const res = await apiFetch(`${API_BASE}/schedules/${id}`, { method: 'DELETE' });
            const result = await res.json().catch(() => ({ status: 'error', message: 'Hapus jadwal gagal.' }));
            if (result.status === 'success') {
                closeDeleteScheduleModal();
                showToast(result.message || 'Jadwal berhasil dihapus.', 'success');
                calendar.refetchEvents();
                return;
            }
            closeDeleteScheduleModal();
            showToast(result.message || 'Gagal menghapus jadwal.', 'error');
        } catch (err) {
            closeDeleteScheduleModal();
            showToast('Gagal menghapus jadwal.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

@endpush

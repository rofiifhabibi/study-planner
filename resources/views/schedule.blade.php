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
            border-radius: 4px;
            padding: 2px 4px;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #111827;
        }
        .fc-button-primary {
            background-color: #5B1744 !important;
            border-color: #5B1744 !important;
        }
        .fc-button-primary:hover {
            background-color: #481236 !important;
            border-color: #481236 !important;
        }
        .fc-day-today {
            background-color: #FAF6F0 !important;
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

    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow"
        data-tour-step="2" data-tour-title="Kalender Jadwal Interaktif" data-tour-desc="Pantau seluruh agenda belajar bulanan atau mingguan, klik tanggal atau agenda untuk melihat detail.">
        <div id="calendar"></div>
    </section>

    {{-- ADD SCHEDULE MODAL (Cartoonish & Simple Redesign) --}}
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeScheduleModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-lg bg-[#FAF6F0] rounded-[2rem] p-6 sm:p-7 shadow-2xl border-2 border-[#E7C8DB] fade-up max-h-[92vh] overflow-y-auto">
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
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-bookmark text-[#5B1744]"></i> Materi / Judul</span>
                        <span class="text-[10px] text-red-500 font-semibold">*Wajib</span>
                    </label>
                    <input type="text" name="title" required placeholder="Contoh: Belajar UTBK / Review Database" 
                        class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                </div>

                {{-- 2. Mata Pelajaran / Topik --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-book-open text-[#5B1744]"></i> Mata Pelajaran</span>
                        <span class="text-[10px] text-gray-400">Opsional</span>
                    </label>
                    <input type="text" name="subject" placeholder="Contoh: Matematika Diskrit / Sistem Operasi" 
                        class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                </div>

                {{-- 3. Kapan Belajarnya? --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar text-[#5B1744]"></i> Tanggal</span>
                        <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                    </label>
                    <div class="flex items-center gap-2 mt-2 mb-2.5">
                        <button type="button" onclick="setScheduleQuickDate(0)" class="px-3 py-1 rounded-lg text-[10px] font-bold bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB] hover:bg-[#E7C8DB] transition">Hari Ini</button>
                        <button type="button" onclick="setScheduleQuickDate(1)" class="px-3 py-1 rounded-lg text-[10px] font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-[#F4E7EF] transition">Besok</button>
                        <button type="button" onclick="setScheduleQuickDate(2)" class="px-3 py-1 rounded-lg text-[10px] font-semibold bg-gray-50 text-gray-700 border border-gray-200 hover:bg-[#F4E7EF] transition">Lusa</button>
                    </div>
                    <div>
                        <input type="date" name="date" id="scheduleDate" required value="{{ date('Y-m-d') }}" 
                            class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                    </div>
                </div>

                {{-- 4. Jam Belajar --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-[#5B1744]"></i> Waktu Sesi Belajar</span>
                        <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                    </label>

                    {{-- Preset Jam Belajar --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5 mt-2 mb-3">
                        <button type="button" onclick="setScheduleTimePreset('08:00', '09:30')" class="time-preset-chip py-1.5 px-2 rounded-xl text-[10px] font-semibold border transition flex items-center justify-center gap-1 bg-[#FAF6F0] text-gray-800 border-[#E7C8DB] hover:bg-[#F4E7EF]">
                            <i class="fa-regular fa-sun text-[10px]"></i> Pagi (08:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('13:00', '14:30')" class="time-preset-chip py-1.5 px-2 rounded-xl text-[10px] font-semibold border transition flex items-center justify-center gap-1 bg-[#FAF6F0] text-gray-800 border-[#E7C8DB] hover:bg-[#F4E7EF]">
                            <i class="fa-solid fa-sun text-[10px]"></i> Siang (13:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('16:00', '17:30')" class="time-preset-chip py-1.5 px-2 rounded-xl text-[10px] font-semibold border transition flex items-center justify-center gap-1 bg-[#FAF6F0] text-gray-800 border-[#E7C8DB] hover:bg-[#F4E7EF]">
                            <i class="fa-regular fa-compass text-[10px]"></i> Sore (16:00)
                        </button>
                        <button type="button" onclick="setScheduleTimePreset('19:30', '21:00')" class="time-preset-chip py-1.5 px-2 rounded-xl text-[10px] font-semibold border transition flex items-center justify-center gap-1 bg-[#FAF6F0] text-gray-800 border-[#E7C8DB] hover:bg-[#F4E7EF]">
                            <i class="fa-regular fa-moon text-[10px]"></i> Malam (19:30)
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

    {{-- DELETE SCHEDULE CONFIRM MODAL --}}
    <div id="deleteScheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeDeleteScheduleModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#B91C1C] font-bold">DELETE SCHEDULE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Hapus jadwal ini?</h2>
                </div>
                <button type="button" onclick="closeDeleteScheduleModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed mb-5">
                Jadwal akan dihapus. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteScheduleModal()"
                    class="flex-1 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" id="confirmDeleteScheduleBtn" onclick="confirmDeleteSchedule()"
                    class="flex-1 py-2.5 rounded-xl bg-[#B91C1C] text-white text-xs font-bold hover:bg-[#991B1B] transition shadow-xs">
                    Hapus
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    const scheduleModal = document.getElementById('scheduleModal');
    let calendar;

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        const isMobile = window.innerWidth < 768;
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? 'timeGridDay' : 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: isMobile ? 'timeGridDay,dayGridMonth' : 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            titleFormat: isMobile ? { month: 'short', day: 'numeric' } : { month: 'long', year: 'numeric' },
            events: `${API_BASE}/schedules`,
            dateClick: function(info) {
                document.getElementById('scheduleDate').value = info.dateStr;
                openScheduleModal();
            },
            eventClick: function(info) {
                deleteSchedule(info.event.id);
            }
        });
        calendar.render();
    });

    function setScheduleQuickDate(offsetDays) {
        const d = new Date();
        d.setDate(d.getDate() + offsetDays);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const inp = document.getElementById('scheduleDate');
        if (inp) inp.value = `${yyyy}-${mm}-${dd}`;
    }

    function setScheduleTimePreset(start, end) {
        const startInp = document.getElementById('startTime');
        const endInp = document.getElementById('endTime');
        if (startInp) startInp.value = start;
        if (endInp) endInp.value = end;
    }

    function openScheduleModal() {
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

    function deleteSchedule(id) {
        pendingDeleteId = id;
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

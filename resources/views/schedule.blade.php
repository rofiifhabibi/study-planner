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
        <button onclick="openScheduleModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Schedule</span>
        </button>
    </section>

    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div id="calendar"></div>
    </section>

    {{-- ADD SCHEDULE MODAL --}}
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeScheduleModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">NEW SCHEDULE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Add a schedule</h2>
                </div>
                <button onclick="closeScheduleModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="scheduleForm" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-600">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Review Database" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600">Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Database Systems" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Date</label>
                        <input type="date" name="date" id="scheduleDate" required value="{{ date('Y-m-d') }}" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Start</label>
                        <input type="time" name="start_time" id="startTime" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">End</label>
                        <input type="time" name="end_time" id="endTime" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Add to calendar
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

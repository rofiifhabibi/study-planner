@extends('layouts.study')

@section('title', 'Schedule — Study Planner')
@section('page-label', 'MY SCHEDULE')

@php
    $activeNav = 'schedule';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Your schedule</h1>
            <p class="text-sm text-gray-500 mt-1">Atur jadwal belajarmu hari ini dan yang akan datang.</p>
        </div>
        <button onclick="openScheduleModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Schedule</span>
        </button>
    </section>

    {{-- Today's highlight --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                <i class="fa-regular fa-calendar-check text-sm"></i>
            </div>
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">TODAY</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">{{ date('l, d F Y') }}</h2>
            </div>
        </div>

        <div class="space-y-2" id="schedule-list">
            @forelse ($todaySchedules as $item)
                @php
                    $statusColors = [
                        'completed' => ['bg-green-50 text-green-600', 'bg-[#5B1744]'],
                        'active' => ['bg-amber-50 text-amber-600 font-bold', 'bg-amber-500'],
                        'pending' => ['bg-gray-50 text-gray-500', 'bg-gray-200'],
                    ];
                    [$statusClass, $lineColor] = $statusColors[$item->status] ?? $statusColors['pending'];
                    $isActive = $item->status === 'active';
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl {{ $isActive ? 'bg-[#FAF6F0]' : 'hover:bg-[#FAF6F0]/60' }} transition" data-schedule-id="{{ $item->id }}">
                    <div class="w-12 text-xs font-bold text-gray-500 text-center shrink-0">
                        {{ $item->start_time->format('H:i') }}
                    </div>
                    <div class="w-1 h-8 rounded-full {{ $lineColor }} shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 truncate">{{ $item->title }}</p>
                        <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $item->subject ?? $item->start_time->format('H:i') . ' - ' . $item->end_time->format('H:i') }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full {{ $statusClass }} shrink-0">
                        {{ ucfirst($item->status) }}
                    </span>
                    <button onclick="deleteSchedule({{ $item->id }})" class="text-gray-400 hover:text-red-500 transition shrink-0 ml-1">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                    </button>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-regular fa-calendar-check text-2xl mb-2"></i>
                    <p class="text-xs">Belum ada jadwal hari ini.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Upcoming schedules --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="mb-5">
            <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">UPCOMING</p>
            <h2 class="text-lg font-bold text-gray-900 mt-0.5">Coming up</h2>
        </div>

        <div class="space-y-2">
            @forelse ($upcomingSchedules as $item)
                @php
                    $statusColors = [
                        'completed' => ['bg-green-50 text-green-600', 'bg-[#5B1744]'],
                        'active' => ['bg-amber-50 text-amber-600 font-bold', 'bg-amber-500'],
                        'pending' => ['bg-gray-50 text-gray-500', 'bg-gray-200'],
                    ];
                    [$statusClass, $lineColor] = $statusColors[$item->status] ?? $statusColors['pending'];
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition" data-schedule-id="{{ $item->id }}">
                    <div class="w-16 text-center shrink-0">
                        <p class="text-lg font-bold text-[#5B1744] leading-none">{{ $item->date->format('d') }}</p>
                        <p class="text-[9px] uppercase text-gray-400 font-semibold mt-0.5">{{ $item->date->format('M') }}</p>
                    </div>
                    <div class="w-1 h-8 rounded-full {{ $lineColor }} shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 truncate">{{ $item->title }}</p>
                        <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $item->start_time->format('H:i') }} - {{ $item->end_time->format('H:i') }}{{ $item->subject ? ' · ' . $item->subject : '' }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full {{ $statusClass }} shrink-0">
                        {{ ucfirst($item->status) }}
                    </span>
                    <button onclick="deleteSchedule({{ $item->id }})" class="text-gray-400 hover:text-red-500 transition shrink-0 ml-1">
                        <i class="fa-solid fa-trash text-[10px]"></i>
                    </button>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="fa-regular fa-calendar text-2xl mb-2"></i>
                    <p class="text-xs">Belum ada jadwal mendatang.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ADD SCHEDULE MODAL --}}
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeScheduleModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
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

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Date</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Start</label>
                        <input type="time" name="start_time" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">End</label>
                        <input type="time" name="end_time" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                </div>

                {{-- Recurrence --}}
                <div class="border-t border-gray-200 pt-4">
                    <label class="text-xs font-bold text-gray-600">Ulangi (Repetitif)</label>
                    <select name="recurrence_frequency" id="recurrenceFrequency" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                        <option value="">Tidak berulang</option>
                        <option value="daily">Setiap hari</option>
                        <option value="weekly">Setiap minggu</option>
                        <option value="monthly">Setiap bulan</option>
                    </select>

                    <div id="recurrenceOptions" class="hidden mt-3 space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-bold text-gray-600">Ulang setiap</label>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <input type="number" name="recurrence_interval" id="recurrenceInterval" min="1" max="52" value="1" class="w-16 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                                    <span class="text-xs text-gray-500" id="intervalUnit">hari</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-600">Berakhir</label>
                                <select id="recurrenceEndType" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                                    <option value="never">Tidak pernah</option>
                                    <option value="count">Setelah jumlah kali</option>
                                    <option value="until">Sampai tanggal</option>
                                </select>
                            </div>
                        </div>

                        <div id="recurrenceCountWrap" class="hidden">
                            <label class="text-xs font-bold text-gray-600">Jumlah kali</label>
                            <input type="number" name="recurrence_count" min="1" max="365" placeholder="e.g. 10" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                        </div>

                        <div id="recurrenceUntilWrap" class="hidden">
                            <label class="text-xs font-bold text-gray-600">Sampai tanggal</label>
                            <input type="date" name="recurrence_until" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                        </div>

                        <div id="recurrenceDaysWrap" class="hidden">
                            <label class="text-xs font-bold text-gray-600">Hari</label>
                            <input type="hidden" name="recurrence_days" id="recurrenceDays">
                            <div class="flex flex-wrap gap-2 mt-1.5">
                                @foreach (['MO' => 'Sen', 'TU' => 'Sel', 'WE' => 'Rab', 'TH' => 'Kam', 'FR' => 'Jum', 'SA' => 'Sab', 'SU' => 'Min'] as $code => $label)
                                    <button type="button" data-day="{{ $code }}"
                                        class="day-pill px-3.5 py-2 rounded-xl border border-gray-200 bg-white text-gray-600 text-xs font-bold hover:border-[#5B1744] transition">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
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
                Jadwal dan event di Google Calendar akan dihapus. Tindakan ini tidak bisa dibatalkan.
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

    const recurrenceFrequency = document.getElementById('recurrenceFrequency');
    const recurrenceOptions = document.getElementById('recurrenceOptions');
    const recurrenceDaysWrap = document.getElementById('recurrenceDaysWrap');
    const recurrenceCountWrap = document.getElementById('recurrenceCountWrap');
    const recurrenceUntilWrap = document.getElementById('recurrenceUntilWrap');
    const recurrenceInterval = document.getElementById('recurrenceInterval');
    const intervalUnit = document.getElementById('intervalUnit');
    const recurrenceEndType = document.getElementById('recurrenceEndType');
    const recurrenceDaysInput = document.getElementById('recurrenceDays');
    let selectedDays = [];

    function updateRecurrenceUI() {
        const freq = recurrenceFrequency.value;
        recurrenceOptions.classList.toggle('hidden', !freq);

        if (!freq) {
            clearRecurrenceFields();
            return;
        }

        recurrenceDaysWrap.classList.toggle('hidden', freq !== 'weekly');
        intervalUnit.textContent = freq === 'daily' ? 'hari' : (freq === 'weekly' ? 'minggu' : 'bulan');

        updateRecurrenceEndUI();
    }

    function updateRecurrenceEndUI() {
        const type = recurrenceEndType.value;
        recurrenceCountWrap.classList.toggle('hidden', type !== 'count');
        recurrenceUntilWrap.classList.toggle('hidden', type !== 'until');
    }

    function clearRecurrenceFields() {
        recurrenceInterval.value = 1;
        recurrenceCountWrap.classList.add('hidden');
        recurrenceUntilWrap.classList.add('hidden');
        recurrenceDaysWrap.classList.add('hidden');
        selectedDays = [];
        document.querySelectorAll('.day-pill').forEach((b) => b.classList.remove('bg-[#5B1744]', 'text-white', 'border-[#5B1744]'));
    }

    document.querySelectorAll('.day-pill').forEach((btn) => {
        btn.addEventListener('click', () => {
            const day = btn.dataset.day;
            const idx = selectedDays.indexOf(day);
            if (idx >= 0) {
                selectedDays.splice(idx, 1);
                btn.classList.remove('bg-[#5B1744]', 'text-white', 'border-[#5B1744]');
            } else {
                selectedDays.push(day);
                btn.classList.add('bg-[#5B1744]', 'text-white', 'border-[#5B1744]');
            }
            recurrenceDaysInput.value = selectedDays.join(',');
        });
    });

    recurrenceFrequency.addEventListener('change', updateRecurrenceUI);
    recurrenceEndType.addEventListener('change', updateRecurrenceEndUI);

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
                window.location.reload();
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
                setTimeout(() => window.location.reload(), 600);
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

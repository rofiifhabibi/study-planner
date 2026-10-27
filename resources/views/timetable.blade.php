@extends('layouts.study')

@section('title', 'Class Schedule — Study Planner')
@section('page-label', 'CLASS SCHEDULE')

@php
    $activeNav = 'timetable';
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];
    if (isset($grouped[0]) && count($grouped[0]) > 0) {
        $days[0] = 'Minggu';
    }
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Class Schedule</h1>
            <p class="text-sm text-gray-500 mt-1">Atur jadwal pelajaran dan kelas mingguanmu.</p>
        </div>
        <button onclick="openTimetableModal()" class="inline-flex items-center gap-2 bg-[#5B1744] text-white px-5 py-3 rounded-full text-xs font-bold hover:bg-[#481236] transition shadow-xs"
            data-tour-step="2" data-tour-title="Tambah Jadwal Kelas" data-tour-desc="Klik tombol ini untuk memasukkan mata pelajaran baru beserta hari dan jam pelajarannya.">
            <i class="fa-solid fa-plus w-4 text-center"></i> Tambah Kelas
        </button>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4"
        data-tour-step="1" data-tour-title="Jadwal Mingguan Senin-Jumat" data-tour-desc="Lihat seluruh jadwal kelas rutinmu yang tersusun rapi dari hari Senin hingga Jumat.">
        @foreach($days as $val => $label)
            <div class="bg-white rounded-3xl border border-gray-100 p-4 soft-shadow h-full flex flex-col fade-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="mb-4 pb-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">{{ $label }}</h3>
                    <div class="w-6 h-6 rounded-full bg-[#FAF6F0] flex items-center justify-center text-[10px] font-bold text-[#5B1744]">
                        {{ isset($grouped[$val]) ? count($grouped[$val]) : 0 }}
                    </div>
                </div>
                
                <div class="flex-1 space-y-3">
                    @if(isset($grouped[$val]) && count($grouped[$val]) > 0)
                        @foreach($grouped[$val] as $item)
                            <div class="relative group bg-[#FAF6F0]/60 hover:bg-[#FAF6F0] border border-[#E7C8DB]/50 rounded-2xl p-3 transition overflow-hidden">
                                <div class="absolute top-2 right-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                    <form action="{{ route('timetable.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus jadwal kelas" class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition shadow-2xs" onclick="return confirm('Hapus pelajaran ini?')">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                                <p class="font-bold text-xs text-[#5B1744] pr-7">{{ $item->subject }}</p>
                                <div class="flex items-center gap-1.5 mt-1.5 text-gray-500 text-[10px] font-medium">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} 
                                    {{ $item->end_time ? '- ' . \Carbon\Carbon::parse($item->end_time)->format('H:i') : '' }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center py-6 text-center">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center mb-2">
                                <i class="fa-solid fa-mug-hot text-gray-300 text-sm"></i>
                            </div>
                            <p class="text-[10px] text-gray-400 font-medium">Kosong</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL TAMBAH PELAJARAN (Friendly Cartoonish Redesign) --}}
    <div id="timetableModal" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-[#FAF6F0] w-full max-w-lg rounded-[2rem] border-2 border-[#E7C8DB] shadow-[0_0_50px_rgba(0,0,0,0.35),0_15px_35px_rgba(91,23,68,0.2)] overflow-hidden transform transition-all scale-95 opacity-0 duration-300 my-auto" id="timetableModalInner">
            {{-- Modal Header --}}
            <div class="px-6 py-5 border-b border-[#E7C8DB]/60 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] flex items-center justify-center shrink-0 text-[#5B1744] text-lg">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full">Class Schedule</span>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-1">Tambah Jadwal Kelas</h3>
                    </div>
                </div>
                <button type="button" onclick="closeTimetableModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-[#5B1744] hover:bg-[#F4E7EF] transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('timetable.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="day_of_week" id="inputDayOfWeek" value="1">

                    {{-- 1. Pilih Hari --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                        <label class="text-xs font-bold text-gray-800 flex items-center justify-between mb-2">
                            <span>Hari</span>
                            <span id="selectedDayLabel" class="text-[10px] text-[#5B1744] font-bold">Senin</span>
                        </label>
                        <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                            @foreach($days as $val => $label)
                                <button type="button" onclick="selectTimetableDay({{ $val }}, '{{ $label }}')" id="dayChip-{{ $val }}" 
                                    class="day-chip py-2 rounded-xl text-[11px] font-bold border-2 transition text-center {{ $val === 1 ? 'bg-[#5B1744] text-white border-[#5B1744] shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Mata Pelajaran --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                        <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                            <span>Mata Pelajaran</span>
                            <span class="text-[10px] text-red-500 font-semibold">*Wajib</span>
                        </label>
                        <input type="text" name="subject" required placeholder="Contoh: Matematika, Bahasa Inggris, Fisika" 
                            class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                    </div>

                    {{-- 3. Jam Mulai & Selesai --}}
                    <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                        <label class="text-xs font-bold text-gray-800 block mb-2">
                            Jam Pelajaran
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-[10px] text-gray-500 font-medium block mb-1">Mulai:</span>
                                <input type="time" name="start_time" required value="07:30" 
                                    class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-500 font-medium block mb-1">Selesai (Opsional):</span>
                                <input type="time" name="end_time" value="09:00" 
                                    class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full mt-2 py-3.5 rounded-2xl bg-[#5B1744] text-white text-xs sm:text-sm font-bold hover:bg-[#481236] transition shadow-lg shadow-[#5B1744]/20 flex items-center justify-center gap-2 transform active:scale-98">
                        <i class="fa-solid fa-check text-xs"></i> <span>Simpan Pelajaran</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    const modal = document.getElementById('timetableModal');
    const modalInner = document.getElementById('timetableModalInner');

    function selectTimetableDay(dayVal, dayName) {
        document.getElementById('inputDayOfWeek').value = dayVal;
        const lbl = document.getElementById('selectedDayLabel');
        if (lbl) lbl.textContent = dayName;

        document.querySelectorAll('.day-chip').forEach(btn => {
            btn.className = "day-chip py-2 rounded-xl text-[11px] font-semibold border-2 transition text-center bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]";
        });
        const activeBtn = document.getElementById('dayChip-' + dayVal);
        if (activeBtn) {
            activeBtn.className = "day-chip py-2 rounded-xl text-[11px] font-bold border-2 transition text-center bg-[#5B1744] text-white border-[#5B1744] shadow-xs";
        }
    }

    function openTimetableModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalInner.classList.remove('scale-95', 'opacity-0');
            modalInner.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeTimetableModal() {
        modalInner.classList.remove('scale-100', 'opacity-100');
        modalInner.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
@endpush

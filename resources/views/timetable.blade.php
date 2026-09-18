@extends('layouts.study')

@section('title', 'Jadwal Pelajaran — Study Planner')
@section('page-label', 'JADWAL PELAJARAN')

@php
    $activeNav = 'timetable';
    $days = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat'
    ];
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Jadwal Pelajaran</h1>
            <p class="text-sm text-gray-500 mt-1">Atur jadwal rutin mingguanmu dari Senin sampai Jumat.</p>
        </div>
        <button onclick="openTimetableModal()" class="inline-flex items-center gap-2 bg-[#5B1744] text-white px-5 py-3 rounded-full text-xs font-bold hover:bg-[#481236] transition shadow-xs">
            <i class="fa-solid fa-plus w-4 text-center"></i> Tambah Pelajaran
        </button>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <form action="{{ route('timetable.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-5 h-5 rounded-full bg-white text-red-500 hover:bg-red-50 flex items-center justify-center transition shadow-sm" onclick="return confirm('Hapus pelajaran ini?')">
                                            <i class="fa-solid fa-trash text-[8px]"></i>
                                        </button>
                                    </form>
                                </div>
                                <p class="font-bold text-xs text-[#5B1744] pr-4">{{ $item->subject }}</p>
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

    {{-- MODAL TAMBAH PELAJARAN --}}
    <div id="timetableModal" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-md rounded-3xl soft-shadow overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="timetableModalInner">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-bold text-gray-900">Tambah Pelajaran</h3>
                <button onclick="closeTimetableModal()" class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6">
                <form action="{{ route('timetable.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs font-bold text-gray-600">Hari</label>
                        <select name="day_of_week" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                            @foreach($days as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Mata Pelajaran</label>
                        <input type="text" name="subject" required placeholder="Contoh: Matematika" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-gray-600">Jam Mulai</label>
                            <input type="time" name="start_time" required class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-600">Jam Selesai (Opsional)</label>
                            <input type="time" name="end_time" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                        </div>
                    </div>
                    <button type="submit" class="w-full mt-2 py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs">
                        Simpan Pelajaran
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    const modal = document.getElementById('timetableModal');
    const modalInner = document.getElementById('timetableModalInner');

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

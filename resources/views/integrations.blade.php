@extends('layouts.study')

@section('title', 'Integrations — Study Planner')
@section('page-label', 'INTEGRATIONS')

@php
    $activeNav = 'integrations';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Integrations</h1>
            <p class="text-sm text-gray-500 mt-1">Hubungkan akun Google untuk sinkronisasi kalender dan tugas.</p>
        </div>
    </section>

    {{-- Google Card --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                <i class="fa-brands fa-google text-base"></i>
            </div>
            <div class="flex-1">
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">GOOGLE</p>
                <p class="text-sm font-semibold text-gray-900 mt-0.5">Google Calendar & Tasks</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold {{ $isConnected ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isConnected ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $isConnected ? 'Connected' : 'Not connected' }}
            </span>
        </div>

        @if ($isConnected)
            <p class="text-xs text-gray-500 mb-5">
                Akun Google kamu sudah terhubung. Kamu bisa menyinkronkan jadwal ke Google Calendar, mengimpor event dari kalender, dan menyinkronkan tugas ke Google Tasks.
            </p>

            <div class="grid sm:grid-cols-3 gap-3">
                <form method="POST" action="{{ route('google.calendar.sync') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-xs">
                        <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                        Sync to Calendar
                    </button>
                </form>

                <form method="POST" action="{{ route('google.calendar.pull') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                        <i class="fa-solid fa-arrow-down-to-bracket text-[10px]"></i>
                        Import from Calendar
                    </button>
                </form>

                <form method="POST" action="{{ route('google.tasks.sync') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition">
                        <i class="fa-solid fa-list-check text-[10px]"></i>
                        Sync to Google Tasks
                    </button>
                </form>
            </div>
        @else
            <p class="text-xs text-gray-500 mb-5">
                Hubungkan akun Google kamu untuk menyinkronkan jadwal dan tugas. Koneksi aman dan hanya digunakan untuk sinkronisasi.
            </p>

            <a href="{{ route('google.calendar.redirect') }}"
                class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-xs">
                <i class="fa-brands fa-google text-[10px]"></i>
                Connect Google
            </a>
        @endif
    </section>

@endsection

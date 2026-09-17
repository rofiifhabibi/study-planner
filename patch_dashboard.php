<?php
$file = 'resources/views/dashboard.blade.php';
$content = file_get_contents($file);

// Replace "summary belum siap" with simple sentence
$oldSummary = '                    @if ($todayTotal - $todayCompleted > 0)
                        Tinggal {{ $todayTotal - $todayCompleted }} lagi — jangan sampai progress bagus ini berhenti di tengah.
                    @else
                        Luar biasa! Semua tugas hari ini sudah selesai! 🎉
                    @endif';
$newSummary = '                    @if ($todayTotal - $todayCompleted > 0)
                        Semangat belajar hari ini! Jangan lupa cek daftar tugasmu.
                    @else
                        Luar biasa! Semua tugas selesai dengan baik hari ini.
                    @endif';

$content = str_replace($oldSummary, $newSummary, $content);

// Replace the circular progress ring with a task study list
$oldProgressBlock = '        {{-- Progress Summary --}}
        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between fade-up">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">YOUR PROGRESS</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">This week</h2>
            </div>

            @php
                $dashOffset = 402 - (402 * $completionPercentage / 100);
            @endphp

            <div class="relative w-36 h-36 mx-auto my-4">
                <svg class="w-full h-full progress-ring" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="64" fill="none" stroke="#F4E7EF" stroke-width="12"></circle>
                    <circle cx="80" cy="80" r="64" fill="none" stroke="#5B1744" stroke-width="12" stroke-linecap="round" stroke-dasharray="402" stroke-dashoffset="{{ $dashOffset }}"></circle>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-[#5B1744]">{{ $completionPercentage }}%</span>
                    <span class="text-[9px] text-gray-400 uppercase font-semibold">completed</span>
                </div>
            </div>

            <a href="{{ route(\'progress\') }}" class="w-full py-2.5 rounded-xl bg-[#5B1744] text-white text-xs font-semibold text-center hover:bg-[#481236] transition shadow-xs">
                Lihat progress lengkap
            </a>
        </div>';

$newProgressBlock = '        {{-- Progress Summary (Study Tasks) --}}
        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between fade-up">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">STUDY SESSION</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">Mulai Belajar</h2>
            </div>

            <div class="space-y-3 mt-4 flex-1">
                @forelse($todayTasks->take(3) as $task)
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold text-gray-800">{{ $task->title }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">Bertahap selesaikan tugas</p>
                            </div>
                            <a href="{{ route(\'progress\') }}" class="px-3 py-1.5 bg-[#5B1744] text-white text-[10px] font-bold rounded-lg hover:bg-[#481236] transition shadow-sm whitespace-nowrap">
                                <i class="fa-solid fa-play mr-1"></i> Start
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fa-solid fa-check-circle text-gray-300 text-3xl mb-2"></i>
                        <p class="text-xs text-gray-400">Tidak ada tugas aktif.</p>
                    </div>
                @endforelse
            </div>

            <a href="{{ route(\'progress\') }}" class="w-full mt-4 py-2.5 rounded-xl bg-[#FAF6F0] text-[#5B1744] border border-[#E7C8DB]/50 text-xs font-bold text-center hover:bg-[#F4E7EF] transition shadow-xs">
                Ke Halaman Track Progress
            </a>
        </div>';

$content = str_replace($oldProgressBlock, $newProgressBlock, $content);
file_put_contents($file, $content);
echo "Patched dashboard summary.\n";

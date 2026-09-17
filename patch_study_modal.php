<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

// Replace the Study Session Timer section
$oldTimerBlock = '<div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div id="study-timer-icon" class="w-14 h-14 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">STUDY SESSION</p>
                        <div id="study-timer-display" class="text-3xl font-bold text-[#5B1744] font-mono tabular-nums mt-1">00:00:00</div>
                        <p id="study-timer-status" class="text-xs text-gray-400 mt-0.5">Ready to study</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div id="study-session-controls">
                        <button onclick="startStudySession()" class="flex items-center gap-2 px-5 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 transition">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Start Session</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>';

$newTimerBlock = '<div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div id="study-timer-icon" class="w-14 h-14 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">STUDY SESSION</p>
                        <div id="study-timer-display" class="text-3xl font-bold text-[#5B1744] font-mono tabular-nums mt-1">00:00:00</div>
                        <p id="study-timer-status" class="text-xs text-gray-400 mt-0.5">Ready to study</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div id="study-session-controls">
                        <button onclick="openStartModal()" class="flex items-center gap-2 px-5 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md shadow-[#5B1744]/20 transition">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Start Session</span>
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Active Checklist --}}
            <div id="active-checklist-container" class="hidden mt-4 pt-4 border-t border-gray-100">
                <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-3">CHECKLIST TUGAS</p>
                <div id="active-steps-list" class="space-y-2">
                    <!-- Checklists will be rendered here -->
                </div>
            </div>
        </div>';

$content = str_replace($oldTimerBlock, $newTimerBlock, $content);
file_put_contents($file, $content);
echo "Replaced timer block.\n";

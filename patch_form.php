<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldModal = <<<HTML
    {{-- Start Session Modal --}}
    <div id="start-session-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeStartModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-3xl p-6 shadow-xl">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Mulai Sesi Belajar</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Tugas (Opsional)</label>
                    <select id="task-select" class="w-full rounded-xl border-gray-200 text-sm focus:ring-[#5B1744] focus:border-[#5B1744]" onchange="onTaskSelect(this.value)">
                        <option value="">-- Pilih Tugas --</option>
                        @foreach(\$activeTasks ?? [] as \$t)
                            <option value="{{ \$t->id }}">{{ \$t->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Judul Sesi</label>
                    <input type="text" id="session-title" class="w-full rounded-xl border-gray-200 text-sm focus:ring-[#5B1744] focus:border-[#5B1744]" placeholder="Belajar hari ini...">
                </div>

                <div id="checklist-setup" class="hidden border-t border-gray-100 pt-4 mt-4">
                    <label class="block text-xs font-bold text-gray-700 mb-2">Step-by-step Checklist</label>
                    <div id="setup-steps-list" class="space-y-2 mb-3 max-h-40 overflow-y-auto"></div>
                    <div class="flex gap-2">
                        <input type="text" id="new-step-title" class="flex-1 rounded-xl border-gray-200 text-sm focus:ring-[#5B1744] focus:border-[#5B1744]" placeholder="Langkah baru...">
                        <button onclick="addStep()" class="px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition">Add</button>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-4 border-t border-gray-100">
                    <button onclick="closeStartModal()" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 transition">Batal</button>
                    <button onclick="startSessionSubmit()" class="px-5 py-2 text-sm font-semibold text-white bg-[#5B1744] rounded-xl hover:bg-[#481236] transition shadow-md">Mulai Sekarang</button>
                </div>
            </div>
        </div>
    </div>
HTML;

$newModal = <<<HTML
    {{-- Start Session Modal --}}
    <div id="start-session-modal" class="fixed inset-0 z-50 hidden transition-opacity duration-300">
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeStartModal()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md bg-white rounded-[2rem] p-6 sm:p-8 shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="modal-content-box">
            
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Mulai Sesi</h3>
                </div>
                <button onclick="closeStartModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Pilih Tugas (Opsional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-list-check text-gray-400"></i>
                        </div>
                        <select id="task-select" class="w-full pl-10 pr-10 py-3 rounded-2xl bg-gray-50 border-gray-200 text-sm focus:ring-2 focus:ring-[#5B1744]/20 focus:border-[#5B1744] transition-all cursor-pointer" onchange="onTaskSelect(this.value)">
                            <option value="">-- Pilih tugas yang ingin dikerjakan --</option>
                            @foreach(\$activeTasks ?? [] as \$t)
                                <option value="{{ \$t->id }}">{{ \$t->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Judul Sesi Belajar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-pen text-gray-400"></i>
                        </div>
                        <input type="text" id="session-title" class="w-full pl-10 pr-4 py-3 rounded-2xl bg-gray-50 border-gray-200 text-sm focus:ring-2 focus:ring-[#5B1744]/20 focus:border-[#5B1744] transition-all" placeholder="Misal: Belajar Matematika...">
                    </div>
                </div>

                <div id="checklist-setup" class="hidden">
                    <div class="border-t border-gray-100 my-5"></div>
                    <label class="block text-xs font-bold text-gray-700 mb-2.5 uppercase tracking-wide">Step-by-step Checklist</label>
                    
                    <div id="setup-steps-list" class="space-y-2 mb-3 max-h-40 overflow-y-auto pr-2 custom-scrollbar"></div>
                    
                    <div class="flex items-center gap-2 mt-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-plus text-gray-400 text-xs"></i>
                            </div>
                            <input type="text" id="new-step-title" class="w-full pl-8 pr-3 py-2.5 rounded-xl bg-gray-50 border-dashed border-2 border-gray-200 text-sm focus:bg-white focus:border-solid focus:ring-2 focus:ring-[#5B1744]/20 focus:border-[#5B1744] transition-all" placeholder="Ketik langkah baru...">
                        </div>
                        <button onclick="addStep()" class="h-10 px-4 bg-[#FAF6F0] hover:bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB]/50 text-xs font-bold rounded-xl transition shadow-sm whitespace-nowrap">
                            Tambah
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-5 border-t border-gray-100">
                    <button onclick="closeStartModal()" class="px-5 py-2.5 text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Batal</button>
                    <button onclick="startSessionSubmit()" class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-[#5B1744] rounded-xl hover:bg-[#481236] transition shadow-lg shadow-[#5B1744]/30 transform hover:-translate-y-0.5">
                        <i class="fa-solid fa-play text-xs"></i> Mulai Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
HTML;

$content = str_replace($oldModal, $newModal, $content);
file_put_contents($file, $content);
echo "Patched form HTML.\n";

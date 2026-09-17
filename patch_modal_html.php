<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$modalHtml = <<<HTML
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

$content = str_replace('@endsection', $modalHtml . "\n@endsection", $content);
file_put_contents($file, $content);
echo "Added modal HTML.\n";

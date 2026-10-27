@extends('layouts.study')

@section('title', 'My Tasks — Study Planner')
@section('page-label', 'MY TASKS')

@php
    $activeNav = 'tasks';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">My Tasks</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola semua tugasmu dalam satu tempat.</p>
        </div>
        <button onclick="openTaskModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0"
            data-tour-step="1" data-tour-title="Tambah Tugas Baru" data-tour-desc="Buat tugas baru, tentukan kategori, tenggat waktu, serta durasi fokus Pomodoro khusus untuk tugas tersebut.">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Task</span>
        </button>
    </section>

    {{-- Stats row --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4"
        data-tour-step="2" data-tour-title="Statistik & Status Tugas" data-tour-desc="Pantau jumlah total tugas, tugas yang belum selesai, serta persentase keberhasilan belajarmu.">
        @php
            $statBoxes = [
                ['label' => 'Total Tasks', 'value' => (string) $totalTasks, 'icon' => 'fa-regular fa-circle-check'],
                ['label' => 'Pending', 'value' => (string) ($totalTasks - $totalCompleted), 'icon' => 'fa-regular fa-clock'],
                ['label' => 'Completed', 'value' => (string) $totalCompleted, 'icon' => 'fa-solid fa-check-double'],
                ['label' => 'Completion', 'value' => $completionPercentage . '%', 'icon' => 'fa-solid fa-chart-line'],
            ];
        @endphp
        @foreach ($statBoxes as $stat)
            <div class="bg-white rounded-2xl border border-gray-100 p-4 sm:p-5 card-hover shadow-xs">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium">{{ $stat['label'] }}</p>
                        <p class="text-2xl sm:text-3xl font-bold mt-1 text-[#5B1744]">{{ $stat['value'] }}</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center text-xs">
                        <i class="{{ $stat['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Task list --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow"
        data-tour-step="3" data-tour-title="Daftar Tugas & Checklist" data-tour-desc="Kelola tugasmu, centang jika selesai, buat sub-langkah belajar bertahap, atau mulai timer fokus Pomodoro langsung dari sini.">
        <div class="flex justify-between items-center mb-5">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">ALL TASKS</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">Task list</h2>
            </div>
        </div>

        <div id="task-list" class="space-y-2">
            @forelse ($tasks as $task)
                <div class="task-item flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition" data-task-id="{{ $task->id }}">
                    <button onclick="toggleTask({{ $task->id }})" class="w-5 h-5 rounded-full border-2 {{ $task->status === 'completed' ? 'bg-[#5B1744] border-[#5B1744]' : 'border-gray-300' }} flex items-center justify-center shrink-0 transition">
                        @if ($task->status === 'completed')
                            <i class="fa-solid fa-check text-[8px] text-white"></i>
                        @endif
                    </button>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-xs text-gray-900 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-[#F4E7EF] text-[#5B1744] border border-[#E7C8DB]/60 font-semibold">
                                <i class="fa-solid fa-stopwatch text-[9px] mr-1"></i>{{ $task->pomodoro_duration ?? 25 }}m
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst($task->category) }} · {{ $task->due_date ? $task->due_date->format('d M Y') . ($task->due_time ? ' at ' . \Carbon\Carbon::parse($task->due_time)->format('H:i') : '') : 'No deadline' }}</p>
                        @if ($task->description)
                            <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $task->description }}</p>
                        @endif
                    </div>
                    <a href="{{ route('progress', ['task_id' => $task->id]) }}" title="Mulai Pomodoro untuk tugas ini" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#F4E7EF] hover:bg-[#E7C8DB] text-[#5B1744] text-[11px] font-semibold border border-[#E7C8DB] transition shrink-0">
                        <i class="fa-solid fa-play text-[9px]"></i>
                        <span>Fokus</span>
                    </a>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-500') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                    <button onclick="deleteTask({{ $task->id }})" title="Hapus tugas" class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-white border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 flex items-center justify-center transition shrink-0 shadow-2xs">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-regular fa-circle-check text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada tugas.</p>
                    <button onclick="openTaskModal()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Tambah tugas baru</button>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ADD TASK MODAL (Friendly Cartoonish Redesign) --}}
    <div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeTaskModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-lg bg-[#FAF6F0] rounded-[2rem] p-6 sm:p-7 shadow-[0_0_50px_rgba(0,0,0,0.35),0_15px_35px_rgba(91,23,68,0.2)] border-2 border-[#E7C8DB] fade-up max-h-[92vh] overflow-y-auto">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-[#E7C8DB]/60 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] flex items-center justify-center shrink-0 text-[#5B1744] text-lg">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full">Tambah Tugas</span>
                        <h2 class="text-lg font-bold text-gray-900 mt-1">Buat Rencana Tugas</h2>
                    </div>
                </div>
                <button type="button" onclick="closeTaskModal()" class="w-8 h-8 rounded-full bg-white border border-[#E7C8DB] flex items-center justify-center text-gray-500 hover:bg-[#F4E7EF] hover:text-[#5B1744] transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="taskForm" class="space-y-4">
                @csrf
                {{-- Hidden Inputs for interactive pill choices --}}
                <input type="hidden" name="category" id="inputTaskCategory" value="school">
                <input type="hidden" name="priority" id="inputTaskPriority" value="medium">
                <input type="hidden" name="pomodoro_duration" id="taskPomodoroFocus" value="25">
                <input type="hidden" name="break_duration" id="taskPomodoroBreak" value="5">

                {{-- 1. Judul Tugas --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Nama Tugas</span>
                        <span class="text-[10px] text-red-500 font-semibold">*Wajib</span>
                    </label>
                    <input type="text" name="title" required placeholder="Misal: Rangkuman Biologi Bab 3" 
                        class="w-full mt-2 px-3 py-2.5 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-900 outline-none transition placeholder:text-gray-400">
                </div>

                {{-- 2. Kategori Visual --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Kategori</span>
                        <span id="categoryLabelText" class="text-[10px] text-[#5B1744] font-bold">Sekolah</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2.5">
                        <button type="button" onclick="selectCategory('school')" id="catBtn-school" 
                            class="cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-bold border-2 transition active:scale-95 bg-[#5B1744] text-white border-[#5B1744] shadow-xs">
                            <i class="fa-solid fa-graduation-cap text-xs"></i> <span>Sekolah</span>
                        </button>
                        <button type="button" onclick="selectCategory('project')" id="catBtn-project" 
                            class="cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-semibold border-2 transition active:scale-95 bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]">
                            <i class="fa-solid fa-code text-xs"></i> <span>Proyek</span>
                        </button>
                        <button type="button" onclick="selectCategory('study')" id="catBtn-study" 
                            class="cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-semibold border-2 transition active:scale-95 bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]">
                            <i class="fa-solid fa-book-open text-xs"></i> <span>Belajar</span>
                        </button>
                        <button type="button" onclick="selectCategory('personal')" id="catBtn-personal" 
                            class="cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-semibold border-2 transition active:scale-95 bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]">
                            <i class="fa-solid fa-user text-xs"></i> <span>Pribadi</span>
                        </button>
                    </div>
                </div>

                {{-- 3. Deadline / Waktu Selesai --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Tenggat Waktu</span>
                        <span class="text-[10px] text-gray-400">Pilihan cepat:</span>
                    </label>
                    <div class="flex items-center gap-2 mt-2 mb-2.5" id="taskQuickDateGroup">
                        <button type="button" onclick="setQuickDate(0, this)" class="task-quick-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-[#5B1744] text-white border border-[#5B1744] shadow-xs transition">Hari Ini</button>
                        <button type="button" onclick="setQuickDate(1, this)" class="task-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition">Besok</button>
                        <button type="button" onclick="setQuickDate(3, this)" class="task-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition">3 Hari</button>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-gray-500 font-medium block mb-1">Tanggal:</span>
                            <input type="date" name="due_date" id="inputDueDate" required value="{{ date('Y-m-d') }}" 
                                class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-500 font-medium block mb-1">Jam (Opsional):</span>
                            <input type="time" name="due_time" id="inputDueTime" 
                                class="w-full px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-gray-200 text-xs font-semibold text-gray-800 outline-none focus:border-[#5B1744]">
                        </div>
                    </div>
                </div>

                {{-- 4. Prioritas & Mode Fokus Pomodoro --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- Prioritas --}}
                    <div class="bg-white p-3 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                        <label class="text-[11px] font-bold text-gray-800 block mb-2">Prioritas:</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" onclick="selectPriority('low')" id="prioBtn-low" 
                                class="prio-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition flex items-center justify-center gap-1.5 bg-white text-gray-700 border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Rendah
                            </button>
                            <button type="button" onclick="selectPriority('medium')" id="prioBtn-medium" 
                                class="prio-chip py-1.5 rounded-xl text-[10px] font-bold border-2 transition flex items-center justify-center gap-1.5 bg-[#5B1744] text-white border-[#5B1744]">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Sedang
                            </button>
                            <button type="button" onclick="selectPriority('high')" id="prioBtn-high" 
                                class="prio-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition flex items-center justify-center gap-1.5 bg-white text-gray-700 border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Tinggi
                            </button>
                        </div>
                    </div>

                    {{-- Mode Pomodoro --}}
                    <div class="bg-white p-3 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                        <label class="text-[11px] font-bold text-gray-800 block mb-2">Target Sesi Fokus:</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" onclick="selectPomoDuration(15, 3)" id="pomoChip-15" 
                                class="pomo-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition text-center bg-white text-gray-700 border-gray-200">
                                15 Menit
                            </button>
                            <button type="button" onclick="selectPomoDuration(25, 5)" id="pomoChip-25" 
                                class="pomo-chip py-1.5 rounded-xl text-[10px] font-bold border-2 transition text-center bg-[#5B1744] text-white border-[#5B1744]">
                            25 Menit
                            </button>
                            <button type="button" onclick="selectPomoDuration(50, 10)" id="pomoChip-50" 
                                class="pomo-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition text-center bg-white text-gray-700 border-gray-200">
                                50 Menit
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 5. Catatan Tambahan (Opsional) --}}
                <div class="bg-white p-3.5 rounded-2xl border border-[#E7C8DB] shadow-2xs">
                    <label class="text-xs font-bold text-gray-800 flex items-center justify-between">
                        <span>Catatan</span>
                        <span class="text-[10px] text-gray-400">Opsional</span>
                    </label>
                    <textarea name="description" rows="2" placeholder="Catat detail atau instruksi khusus pengerjaan..." 
                        class="w-full mt-2 px-3 py-2 rounded-xl bg-[#FAF6F0]/60 border border-transparent focus:border-[#5B1744] focus:bg-white text-xs font-medium text-gray-800 outline-none transition placeholder:text-gray-400"></textarea>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                    class="w-full py-3.5 rounded-2xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs sm:text-sm font-bold transition shadow-lg shadow-[#5B1744]/20 flex items-center justify-center gap-2 mt-2 transform active:scale-98">
                    <i class="fa-solid fa-check text-xs"></i> <span>Simpan Tugas</span>
                </button>
            </form>
        </div>
    </div>

    {{-- DELETE TASK CONFIRM MODAL --}}
    <div id="deleteTaskModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeDeleteTaskModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-[0_0_50px_rgba(0,0,0,0.35),0_15px_35px_rgba(91,23,68,0.2)] fade-up">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#B91C1C] font-bold">DELETE TASK</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Hapus tugas ini?</h2>
                </div>
                <button type="button" onclick="closeDeleteTaskModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed mb-5">
                Tugas dan item di Google Tasks akan dihapus. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteTaskModal()"
                    class="flex-1 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" id="confirmDeleteTaskBtn" onclick="confirmDeleteTask()"
                    class="flex-1 py-2.5 rounded-xl bg-[#B91C1C] text-white text-xs font-bold hover:bg-[#991B1B] transition shadow-xs">
                    Hapus
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    const taskModal = document.getElementById('taskModal');

    function selectCategory(cat) {
        const inp = document.getElementById('inputTaskCategory');
        if (inp) inp.value = cat;
        const labels = {
            school: 'Sekolah',
            project: 'Proyek',
            study: 'Belajar',
            personal: 'Pribadi'
        };
        const lbl = document.getElementById('categoryLabelText');
        if (lbl) lbl.textContent = labels[cat] || cat;

        document.querySelectorAll('.cat-chip').forEach(btn => {
            btn.className = "cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-semibold border-2 transition active:scale-95 bg-white text-gray-700 border-gray-200 hover:border-[#E7C8DB]";
        });
        const activeBtn = document.getElementById('catBtn-' + cat);
        if (activeBtn) {
            activeBtn.className = "cat-chip flex items-center justify-center gap-1.5 py-2 px-2 rounded-xl text-[11px] font-bold border-2 transition active:scale-95 bg-[#5B1744] text-white border-[#5B1744] shadow-xs";
        }
    }

    function selectPriority(prio) {
        const inp = document.getElementById('inputTaskPriority');
        if (inp) inp.value = prio;
        document.querySelectorAll('.prio-chip').forEach(btn => {
            btn.className = "prio-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition flex items-center justify-center gap-1.5 bg-white text-gray-700 border-gray-200";
        });
        const activeBtn = document.getElementById('prioBtn-' + prio);
        if (activeBtn) {
            activeBtn.className = "prio-chip py-1.5 rounded-xl text-[10px] font-bold border-2 transition flex items-center justify-center gap-1.5 bg-[#5B1744] text-white border-[#5B1744]";
        }
    }

    function selectPomoDuration(focus, breakMin) {
        const fInp = document.getElementById('taskPomodoroFocus');
        const bInp = document.getElementById('taskPomodoroBreak');
        if (fInp) fInp.value = focus;
        if (bInp) bInp.value = breakMin;
        document.querySelectorAll('.pomo-chip').forEach(btn => {
            btn.className = "pomo-chip py-1.5 rounded-xl text-[10px] font-semibold border-2 transition text-center bg-white text-gray-700 border-gray-200";
        });
        const activeBtn = document.getElementById('pomoChip-' + focus);
        if (activeBtn) {
            activeBtn.className = "pomo-chip py-1.5 rounded-xl text-[10px] font-bold border-2 transition text-center bg-[#5B1744] text-white border-[#5B1744]";
        }
    }

    function setQuickDate(offsetDays, btn) {
        const d = new Date();
        d.setDate(d.getDate() + offsetDays);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const inp = document.getElementById('inputDueDate');
        if (inp) inp.value = `${yyyy}-${mm}-${dd}`;

        document.querySelectorAll('#taskQuickDateGroup .task-quick-btn').forEach(b => {
            b.className = 'task-quick-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-gray-700 border border-gray-200 hover:border-[#E7C8DB] hover:bg-[#F4E7EF] transition';
        });
        if (btn) {
            btn.className = 'task-quick-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-[#5B1744] text-white border border-[#5B1744] shadow-xs transition';
        }
    }

    function openTaskModal() {
        const todayBtn = document.querySelector('#taskQuickDateGroup .task-quick-btn:first-child');
        if (todayBtn) {
            setQuickDate(0, todayBtn);
        }
        taskModal.classList.remove('hidden');
        taskModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeTaskModal() {
        taskModal.classList.add('hidden');
        taskModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('taskForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Adding...';

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const res = await apiFetch(`${API_BASE}/tasks`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const result = await res.json();

            if (result.status === 'success') {
                closeTaskModal();
                form.reset();
                window.location.reload();
            } else {
                alert(result.message || 'Gagal menambahkan tugas.');
            }
        } catch (err) {
            alert('Terjadi kesalahan saat menambahkan tugas.');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    async function toggleTask(id) {
        const item = document.querySelector(`[data-task-id="${id}"]`);
        if (!item) return;

        const btn = item.querySelector('button');
        const isCompleted = btn.classList.contains('bg-[#5B1744]');

        try {
            const res = await apiFetch(`${API_BASE}/tasks/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: isCompleted ? 'pending' : 'completed' }),
            });
            const result = await res.json();
            if (result.status === 'success') {
                window.location.reload();
            }
        } catch (err) {
            alert('Gagal mengubah status tugas.');
        }
    }

    const deleteTaskModal = document.getElementById('deleteTaskModal');
    let pendingDeleteTaskId = null;

    function deleteTask(id) {
        pendingDeleteTaskId = id;
        deleteTaskModal.classList.remove('hidden');
        deleteTaskModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteTaskModal() {
        pendingDeleteTaskId = null;
        deleteTaskModal.classList.add('hidden');
        deleteTaskModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    async function confirmDeleteTask() {
        const id = pendingDeleteTaskId;
        if (id === null) return;

        const btn = document.getElementById('confirmDeleteTaskBtn');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        try {
            const res = await apiFetch(`${API_BASE}/tasks/${id}`, { method: 'DELETE' });
            const result = await res.json().catch(() => ({ status: 'error', message: 'Hapus tugas gagal.' }));
            if (result.status === 'success') {
                closeDeleteTaskModal();
                showToast(result.message || 'Tugas berhasil dihapus.', 'success');
                setTimeout(() => window.location.reload(), 600);
                return;
            }
            closeDeleteTaskModal();
            showToast(result.message || 'Gagal menghapus tugas.', 'error');
        } catch (err) {
            closeDeleteTaskModal();
            showToast('Gagal menghapus tugas.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

@endpush

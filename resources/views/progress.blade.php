@extends('layouts.study')

@section('title', 'Progress — Study Planner')
@section('page-label', 'MY PROGRESS')

@php
    $activeNav = 'progress';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Your progress</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau pencapaian dan kebiasaan belajarmu.</p>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        @php
            $stats = [
                ['label' => "Today's Tasks", 'value' => "$todayCompleted/$todayTotal", 'icon' => 'fa-regular fa-circle-check', 'description' => "$todayCompleted completed"],
                ['label' => 'Study Hours', 'value' => $totalStudyHours . 'h', 'icon' => 'fa-regular fa-clock', 'description' => "$todayStudyMinutes min today"],
                ['label' => 'Completed', 'value' => (string) $totalCompleted, 'icon' => 'fa-solid fa-chart-line', 'description' => 'All time'],
                ['label' => 'Study Streak', 'value' => (string) $streak, 'icon' => 'fa-solid fa-fire text-amber-500', 'description' => 'days in a row'],
            ];
        @endphp
        @foreach ($stats as $stat)
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
                <p class="text-[10px] sm:text-[11px] text-gray-400 mt-2">{{ $stat['description'] }}</p>
            </div>
        @endforeach
    </section>

    {{-- Study Session Timer + Progress Ring --}}
    <section class="grid md:grid-cols-12 gap-5">

        <div class="md:col-span-7 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col">
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
        </div>

        <div class="md:col-span-5 bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow flex flex-col justify-between">
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

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                    <p class="text-[10px] text-gray-400 font-medium">Tasks</p>
                    <p class="font-bold text-xs mt-0.5 text-gray-800">{{ $totalCompleted }} / {{ $totalTasks }}</p>
                </div>
                <div class="rounded-xl bg-[#FAF6F0] p-3 text-center">
                    <p class="text-[10px] text-gray-400 font-medium">Hours</p>
                    <p class="font-bold text-xs mt-0.5 text-gray-800">{{ $totalStudyHours }}h</p>
                </div>
            </div>
        </div>

    </section>

    {{-- Study history --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="mb-5">
            <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">HISTORY</p>
            <h2 class="text-lg font-bold text-gray-900 mt-0.5">Sesi belajar terbaru</h2>
        </div>

        <div class="space-y-2">
            @forelse ($recentSessions as $s)
                @php
                    $dur = $s->duration_seconds ?? 0;
                    $hours = str_pad(floor($dur / 3600), 2, '0', STR_PAD_LEFT);
                    $mins = str_pad(floor(($dur % 3600) / 60), 2, '0', STR_PAD_LEFT);
                    $secs = str_pad($dur % 60, 2, '0', STR_PAD_LEFT);
                    $durLabel = "{$hours}:{$mins}:{$secs}";
                    $statusLabel = ucfirst($s->status);
                    $statusClass = $s->status === 'completed' ? 'bg-green-50 text-green-600' : ($s->status === 'running' ? 'bg-amber-50 text-amber-600 font-bold' : 'bg-gray-50 text-gray-500');
                @endphp
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition cursor-pointer" onclick="loadPastSession({{ json_encode($s) }})">
                    <div class="w-9 h-9 rounded-xl bg-[#F4E7EF] text-[#5B1744] flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-[12px]"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-gray-900 truncate">{{ $s->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $s->started_at ? $s->started_at->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <span class="text-xs font-bold text-gray-700 shrink-0 w-20 text-right">{{ $durLabel }}</span>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-solid fa-book-open text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada sesi belajar.</p>
                    <button onclick="startStudySession()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Mulai sesi sekarang</button>
                </div>
            @endforelse
        </div>
    </section>

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
                            @foreach($activeTasks ?? [] as $t)
                                <option value="{{ $t->id }}">{{ $t->title }}</option>
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
@endsection

@push('scripts')

    // === STUDY SESSION TIMER ===

    let studyTimerInterval = null;
    let activeStudySession = null;

    function formatStudyTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function startStudyTimer(session) {
        activeStudySession = session;
        const baseSeconds = session.duration_seconds || 0;
        const startTs = new Date(session.started_at).getTime();

        function tick() {
            const now = Date.now();
            const elapsed = baseSeconds + Math.floor((now - startTs) / 1000);
            document.getElementById('study-timer-display').textContent = formatStudyTime(elapsed);
        }

        tick();
        studyTimerInterval = setInterval(tick, 1000);

        document.getElementById('study-session-controls').innerHTML = `
            <div class="flex items-center gap-2">
                <button onclick="pauseStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-pause text-[10px]"></i>
                    <span>Pause</span>
                </button>
                <button onclick="stopStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-stop text-[10px]"></i>
                    <span>Stop</span>
                </button>
            </div>
        `;
        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
        document.getElementById('study-timer-status').setAttribute('data-title', session.title);
        document.getElementById('study-timer-icon').innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
        renderActiveChecklist();
    }


    let currentTaskId = null;
    let currentSteps = [];

    function loadPastSession(session) {
        activeStudySession = session;
        clearInterval(studyTimerInterval);
        
        document.getElementById('study-timer-status').textContent = session.status.toUpperCase() + ': ' + session.title;
        document.getElementById('study-timer-status').setAttribute('data-title', session.title);
        document.getElementById('study-timer-display').textContent = formatStudyTime(session.duration_seconds || 0);
        
        if (session.status === 'running') {
            startStudyTimer(session);
            return;
        }
        
        document.getElementById('study-timer-icon').innerHTML = session.status === 'completed' ? '<i class="fa-solid fa-check-circle text-green-500"></i>' : '<i class="fa-solid fa-pause text-amber-500"></i>';
        
        document.getElementById('study-session-controls').innerHTML = `
            <div class="flex items-center gap-2">
                <button onclick="resumeStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-play text-[10px]"></i>
                    <span>Lanjutkan</span>
                </button>
                <button onclick="deleteStudySession(${session.id})" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-100 hover:bg-red-200 text-red-600 text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-trash text-[10px]"></i>
                    <span>Hapus</span>
                </button>
            </div>
        `;
        
        if (session.task_id) {
            currentTaskId = session.task_id;
            apiFetch(`${API_BASE}/tasks/${session.task_id}/steps`)
                .then(res => res.json())
                .then(data => {
                    currentSteps = data.steps || [];
                    renderActiveChecklist();
                });
        } else {
            currentTaskId = null;
            currentSteps = [];
            renderActiveChecklist();
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function deleteStudySession(id) {
        if (!confirm('Yakin ingin menghapus sesi belajar ini?')) return;
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.status === 'success') {
                window.location.reload();
            }
        } catch (e) {
            alert('Gagal menghapus sesi.');
        }
    }

    function openStartModal(taskId = null) {
        const modal = document.getElementById('start-session-modal');
        const box = document.getElementById('modal-content-box');
        
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');

        if (taskId) {
            const select = document.getElementById('task-select');
            select.value = taskId;
            if (select.selectedIndex >= 0) {
                document.getElementById('session-title').value = select.options[select.selectedIndex].text;
                onTaskSelect(taskId);
            }
        } else {
            document.getElementById('session-title').value = 'Belajar hari ini';
        }
    }

    function closeStartModal() {
        const modal = document.getElementById('start-session-modal');
        const box = document.getElementById('modal-content-box');
        
        modal.classList.remove('opacity-100');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('checklist-setup').classList.add('hidden');
            currentTaskId = null;
            currentSteps = [];
        }, 300);
    }

    async function onTaskSelect(taskId) {
        currentTaskId = taskId;
        const titleInput = document.getElementById('session-title');
        const setupDiv = document.getElementById('checklist-setup');
        
        if (!taskId) {
            setupDiv.classList.add('hidden');
            titleInput.value = 'Belajar hari ini';
            currentSteps = [];
            return;
        }
        
        const select = document.getElementById('task-select');
        titleInput.value = select.options[select.selectedIndex].text;
        
        setupDiv.classList.remove('hidden');
        
        // Fetch steps
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${taskId}/steps`);
            const data = await res.json();
            currentSteps = data.steps || [];
            renderSetupSteps();
        } catch (e) {
            console.error('Failed to load steps');
        }
    }

    function renderSetupSteps() {
        const list = document.getElementById('setup-steps-list');
        list.innerHTML = '';
        currentSteps.forEach(step => {
            list.innerHTML += `
                <div class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-circle-check ${step.is_completed ? 'text-green-500' : 'text-gray-200'}"></i>
                    <span class="text-sm font-semibold ${step.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}">${step.title}</span>
                </div>
            `;
        });
    }

    async function addStep() {
        if (!currentTaskId) return;
        const input = document.getElementById('new-step-title');
        const title = input.value.trim();
        if (!title) return;
        
        input.disabled = true;
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${currentTaskId}/steps`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title })
            });
            const data = await res.json();
            if (data.status === 'success') {
                currentSteps.push(data.step);
                renderSetupSteps();
                input.value = '';
            }
        } catch(e) {
            alert('Gagal menambah langkah.');
        } finally {
            input.disabled = false;
            input.focus();
        }
    }

    async function toggleStep(stepId, isCompleted) {
        if (!activeStudySession || !activeStudySession.task_id) return;
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${activeStudySession.task_id}/steps/${stepId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ is_completed: isCompleted })
            });
            const data = await res.json();
            if (data.status === 'success') {
                // Update local active steps
                const step = currentSteps.find(s => s.id === stepId);
                if (step) step.is_completed = isCompleted;
                renderActiveChecklist();
                
                if (data.task_status === 'completed' && isCompleted) {
                    showToast('Semua langkah selesai! Tugas otomatis ditandai Selesai & disinkronkan ke Google Tasks.', 'success');
                }
            }
        } catch(e) {
            alert('Gagal mengupdate checklist.');
        }
    }

    function renderActiveChecklist() {
        const container = document.getElementById('active-checklist-container');
        const list = document.getElementById('active-steps-list');
        
        // Always show container if a task is selected, even if steps are empty
        if (!currentTaskId) {
            container.classList.add('hidden');
            return;
        }
        
        container.classList.remove('hidden');
        list.innerHTML = '';
        
        let completed = 0;
        let total = currentSteps ? currentSteps.length : 0;
        
        if (total > 0) {
            currentSteps.forEach(step => {
                if (step.is_completed) completed++;
                const checkedStr = step.is_completed ? 'checked' : '';
                const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
                
                list.innerHTML += `
                    <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" ${checkedStr} onchange="toggleStep(${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]">
                        <span class="text-xs font-semibold ${lineThrough}">${step.title}</span>
                    </label>
                `;
            });
            
            const percentage = Math.round((completed / total) * 100);
            
            list.innerHTML = `
                <div class="mb-3 flex items-center justify-between">
                    <span class="text-xs font-bold text-[#5B1744]">Progress: ${percentage}%</span>
                    <span class="text-xs text-gray-400">${completed}/${total} selesai</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4 overflow-hidden">
                    <div class="bg-[#5B1744] h-1.5 rounded-full transition-all duration-500" style="width: ${percentage}%"></div>
                </div>
            ` + list.innerHTML;
        } else {
            list.innerHTML = `<p class="text-xs text-gray-400 italic mb-3">Belum ada langkah. Silakan tambah di bawah.</p>`;
        }
        
        // Add new step input
        list.innerHTML += `
            <div class="flex items-center gap-2 mt-3">
                <input type="text" id="new-active-step-input" placeholder="Tambah langkah baru..." class="flex-1 text-xs px-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-[#5B1744] focus:border-[#5B1744]" onkeypress="if(event.key === 'Enter') addActiveStep()">
                <button type="button" onclick="addActiveStep()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                </button>
            </div>
        `;
    }

    async function addActiveStep() {
        if (!currentTaskId) return;
        const input = document.getElementById('new-active-step-input');
        const title = input.value.trim();
        if (!title) return;
        
        try {
            const res = await apiFetch(`${API_BASE}/tasks/${currentTaskId}/steps`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title })
            });
            const data = await res.json();
            if (data.status === 'success') {
                currentSteps.push(data.step);
                renderActiveChecklist();
                const newInput = document.getElementById('new-active-step-input');
                if (newInput) newInput.focus();
            }
        } catch(e) {
            alert('Gagal menambah langkah.');
        }
    }

    async function startSessionSubmit() {
        const title = document.getElementById('session-title').value.trim() || 'Belajar hari ini';
        const task_id = document.getElementById('task-select').value || null;

        // Auto-add step if user forgot to click "Tambah"
        if (task_id) {
            const newStepInput = document.getElementById('new-step-title');
            if (newStepInput && newStepInput.value.trim()) {
                await addStep();
            }
        }

        try {
            const res = await apiFetch(`${API_BASE}/study-sessions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, task_id }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                closeStartModal();
                setTimeout(() => startStudyTimer(data.session), 300);
            } else {
                alert(data.message || 'Gagal memulai sesi.');
            }
        } catch (err) {
            alert('Gagal memulai sesi belajar.');
        }
    }


    async function pauseStudySession() {
        if (!activeStudySession) return;
        try {
            clearInterval(studyTimerInterval);
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/pause`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                activeStudySession = data.session;
                const sessionTitle = document.getElementById('study-timer-status').getAttribute('data-title') || 'Belajar';
                document.getElementById('study-timer-status').textContent = 'Paused: ' + sessionTitle;
                document.getElementById('study-timer-icon').innerHTML = '<i class="fa-solid fa-pause"></i>';
                document.getElementById('study-session-controls').innerHTML = `
                    <div class="flex items-center gap-2">
                        <button onclick="resumeStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold shadow-md transition">
                            <i class="fa-solid fa-play text-[10px]"></i>
                            <span>Resume</span>
                        </button>
                        <button onclick="stopStudySession()" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-500 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition">
                            <i class="fa-solid fa-stop text-[10px]"></i>
                            <span>Stop</span>
                        </button>
                    </div>
                `;
            }
        } catch (err) {
            alert('Gagal pause sesi.');
        }
    }

    async function resumeStudySession() {
        if (!activeStudySession) return;
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/resume`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            } else {
                alert(data.message || 'Gagal melanjutkan sesi.');
            }
        } catch (err) {
            alert('Gagal resume sesi.');
        }
    }

    async function stopStudySession() {
        if (!activeStudySession) return;
        if (!confirm('Akhiri sesi belajar ini?')) return;
        try {
            clearInterval(studyTimerInterval);
            const res = await apiFetch(`${API_BASE}/study-sessions/${activeStudySession.id}/stop`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                const dur = data.session.formatted_duration || formatStudyTime(data.session.duration_seconds);
                alert('Sesi belajar selesai! Durasi: ' + dur);
                window.location.reload();
            }
        } catch (err) {
            alert('Gagal menghentikan sesi.');
        }
    }

    async function checkActiveStudySession() {
        try {
            const res = await apiFetch(`${API_BASE}/study-sessions/active`);
            const data = await res.json();
            if (data.session) {
                if (data.session.task_id) {
                    currentTaskId = data.session.task_id;
                    const stepRes = await apiFetch(`${API_BASE}/tasks/${data.session.task_id}/steps`);
                    const stepData = await stepRes.json();
                    currentSteps = stepData.steps || [];
                    renderActiveChecklist();
                }
                startStudyTimer(data.session);
            }
        } catch (err) {}
    }

    checkActiveStudySession().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_task_id')) {
            const taskId = urlParams.get('start_task_id');
            if (!activeStudySession) {
                setTimeout(() => openStartModal(taskId), 500);
            }
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });

@endpush

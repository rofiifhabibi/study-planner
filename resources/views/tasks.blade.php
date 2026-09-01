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
        <button onclick="openTaskModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Task</span>
        </button>
    </section>

    {{-- Stats row --}}
    <section class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
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
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
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
                        <p class="font-semibold text-xs text-gray-900 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst($task->category) }} · {{ $task->due_date ? $task->due_date->format('d M Y') : 'No deadline' }}</p>
                        @if ($task->description)
                            <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $task->description }}</p>
                        @endif
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full shrink-0 {{ $task->priority === 'high' ? 'bg-red-50 text-red-600' : ($task->priority === 'medium' ? 'bg-amber-50 text-amber-600' : 'bg-gray-50 text-gray-500') }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                    <button onclick="deleteTask({{ $task->id }})" class="text-gray-400 hover:text-red-500 transition shrink-0">
                        <i class="fa-solid fa-trash text-[10px]"></i>
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

    {{-- ADD TASK MODAL --}}
    <div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeTaskModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-md bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold">NEW TASK</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Add something to your day</h2>
                </div>
                <button onclick="closeTaskModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="taskForm" class="space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-bold text-gray-600">Task name</label>
                    <input type="text" name="title" required placeholder="e.g. Finish networking assignment" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Deadline</label>
                        <input type="date" name="due_date" required value="{{ date('Y-m-d') }}" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-600">Category</label>
                        <select name="category" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                            <option value="school">School</option>
                            <option value="project">Project</option>
                            <option value="study">Study</option>
                            <option value="personal">Personal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-bold text-gray-600">Priority</label>
                        <select name="priority" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744]">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600">Notes</label>
                    <textarea name="description" rows="3" placeholder="Optional notes..." class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition"></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Add to my planner
                </button>
            </form>
        </div>
    </div>

    {{-- DELETE TASK CONFIRM MODAL --}}
    <div id="deleteTaskModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeDeleteTaskModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
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

    function openTaskModal() {
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

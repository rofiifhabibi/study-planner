@extends('layouts.study')

@section('title', 'Notes — Study Planner')
@section('page-label', 'MY NOTES')

@php
    $activeNav = 'notes';
@endphp

@section('content')

    <section class="fade-up flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight mt-1 text-gray-900">Notes</h1>
            <p class="text-sm text-gray-500 mt-1">Simpan catatan belajarmu di satu tempat.</p>
        </div>
        <button onclick="openNoteModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Add Note</span>
        </button>
    </section>

    {{-- Stats row --}}
    <section class="grid grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
        @php
            $statBoxes = [
                ['label' => 'Total Notes', 'value' => (string) $notes->count(), 'icon' => 'fa-regular fa-note-sticky'],
                ['label' => 'With Title', 'value' => (string) $notes->filter(fn ($n) => ! empty($n->title))->count(), 'icon' => 'fa-solid fa-heading'],
                ['label' => 'Last Updated', 'value' => $notes->isNotEmpty() ? $notes->first()->updated_at->format('d M') : '-', 'icon' => 'fa-regular fa-clock'],
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

    {{-- Notes grid --}}
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow">
        <div class="flex justify-between items-center mb-5">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">ALL NOTES</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">Notes list</h2>
            </div>
        </div>

        <div id="note-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @forelse ($notes as $note)
                <div class="note-item group flex flex-col p-4 rounded-2xl bg-[#FAF6F0]/60 hover:bg-[#F4E7EF]/60 border border-gray-100 transition" data-note-id="{{ $note->id }}">
                    <div class="flex items-start justify-between gap-3">
                        <button onclick="editNote(this)" class="flex-1 min-w-0 text-left" data-title="{{ $note->title }}" data-content="{{ $note->content }}">
                            <p class="font-semibold text-xs text-gray-900 line-clamp-1">{{ $note->title ?: 'Untitled' }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $note->updated_at->format('d M Y, H:i') }}</p>
                        </button>
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="editNote(this)" class="text-gray-400 hover:text-[#5B1744] transition" data-title="{{ $note->title }}" data-content="{{ $note->content }}">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </button>
                            <button onclick="deleteNote({{ $note->id }})" class="text-gray-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-trash text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed mt-2 line-clamp-3 whitespace-pre-line">{{ $note->content }}</p>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400 col-span-full">
                    <i class="fa-regular fa-note-sticky text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada catatan.</p>
                    <button onclick="openNoteModal()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Tambah catatan baru</button>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ADD / EDIT NOTE MODAL --}}
    <div id="noteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeNoteModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-lg bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-5">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#5B1744] font-bold" id="noteModalLabel">NEW NOTE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5" id="noteModalTitle">Tulis catatanmu</h2>
                </div>
                <button onclick="closeNoteModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="noteForm" class="space-y-4">
                @csrf
                <input type="hidden" name="id" id="noteId">
                <div>
                    <label class="text-xs font-bold text-gray-600">Judul (opsional)</label>
                    <input type="text" name="title" id="noteTitle" placeholder="e.g. Ringkasan materi jaringan" class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition">
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-600">Isi catatan</label>
                    <textarea name="content" id="noteContent" rows="6" required placeholder="Tulis catatanmu di sini..." class="w-full mt-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white text-xs outline-none focus:border-[#5B1744] transition"></textarea>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-[#5B1744] text-white text-xs font-bold hover:bg-[#481236] transition shadow-xs mt-2">
                    Save Note
                </button>
            </form>
        </div>
    </div>

    {{-- DELETE NOTE CONFIRM MODAL --}}
    <div id="deleteNoteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeDeleteNoteModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-2xl fade-up">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-[9px] uppercase tracking-[.2em] text-[#B91C1C] font-bold">DELETE NOTE</p>
                    <h2 class="text-xl font-bold text-gray-900 mt-0.5">Hapus catatan ini?</h2>
                </div>
                <button type="button" onclick="closeDeleteNoteModal()" class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed mb-5">
                Catatan akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteNoteModal()"
                    class="flex-1 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="button" id="confirmDeleteNoteBtn" onclick="confirmDeleteNote()"
                    class="flex-1 py-2.5 rounded-xl bg-[#B91C1C] text-white text-xs font-bold hover:bg-[#991B1B] transition shadow-xs">
                    Hapus
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')

    const noteModal = document.getElementById('noteModal');

    function openNoteModal() {
        document.getElementById('noteModalLabel').textContent = 'NEW NOTE';
        document.getElementById('noteModalTitle').textContent = 'Tulis catatanmu';
        document.getElementById('noteId').value = '';
        document.getElementById('noteTitle').value = '';
        document.getElementById('noteContent').value = '';
        noteModal.classList.remove('hidden');
        noteModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function editNote(btn) {
        const title = btn.getAttribute('data-title') || '';
        const content = btn.getAttribute('data-content') || '';
        document.getElementById('noteModalLabel').textContent = 'EDIT NOTE';
        document.getElementById('noteModalTitle').textContent = 'Perbarui catatan';
        document.getElementById('noteId').value = btn.closest('.note-item').dataset.noteId;
        document.getElementById('noteTitle').value = title;
        document.getElementById('noteContent').value = content;
        noteModal.classList.remove('hidden');
        noteModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeNoteModal() {
        noteModal.classList.add('hidden');
        noteModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    document.getElementById('noteForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const id = document.getElementById('noteId').value;
        const payload = { title: document.getElementById('noteTitle').value, content: document.getElementById('noteContent').value };

        try {
            const res = await apiFetch(`${API_BASE}/notes${id ? '/' + id : ''}`, {
                method: id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const result = await res.json();

            if (result.status === 'success') {
                closeNoteModal();
                form.reset();
                showToast(id ? 'Catatan diperbarui.' : 'Catatan berhasil ditambahkan.', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(result.message || 'Gagal menyimpan catatan.', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan saat menyimpan catatan.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });

    const deleteNoteModal = document.getElementById('deleteNoteModal');
    let pendingDeleteNoteId = null;

    function deleteNote(id) {
        pendingDeleteNoteId = id;
        deleteNoteModal.classList.remove('hidden');
        deleteNoteModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeDeleteNoteModal() {
        pendingDeleteNoteId = null;
        deleteNoteModal.classList.add('hidden');
        deleteNoteModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    async function confirmDeleteNote() {
        const id = pendingDeleteNoteId;
        if (id === null) return;

        const btn = document.getElementById('confirmDeleteNoteBtn');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Menghapus...';

        try {
            const res = await apiFetch(`${API_BASE}/notes/${id}`, { method: 'DELETE' });
            const result = await res.json().catch(() => ({ status: 'error', message: 'Hapus catatan gagal.' }));
            if (result.status === 'success') {
                closeDeleteNoteModal();
                showToast(result.message || 'Catatan berhasil dihapus.', 'success');
                setTimeout(() => window.location.reload(), 600);
                return;
            }
            closeDeleteNoteModal();
            showToast(result.message || 'Gagal menghapus catatan.', 'error');
        } catch (err) {
            closeDeleteNoteModal();
            showToast('Gagal menghapus catatan.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

@endpush

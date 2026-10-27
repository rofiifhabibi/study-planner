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
        <button onclick="openNoteModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-semibold transition shadow-md shadow-[#5B1744]/20 shrink-0"
            data-tour-step="1" data-tour-title="Buat Catatan Baru" data-tour-desc="Tulis materi pelajaran, rangkuman, atau poin penting dengan cepat.">
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
    <section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow"
        data-tour-step="2" data-tour-title="Daftar & Edit Catatan" data-tour-desc="Lihat semua catatan yang tersimpan, klik untuk menyunting langsung, atau hapus saat sudah tidak diperlukan.">
        <div class="flex justify-between items-center mb-5">
            <div>
                <p class="text-[9px] uppercase tracking-[.2em] text-gray-400 font-bold">ALL NOTES</p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5">Notes list</h2>
            </div>
        </div>

        <div id="note-list" class="space-y-3">
            @forelse ($notes as $note)
                <div class="note-item group w-full p-4 sm:p-5 rounded-2xl bg-[#FAF6F0]/60 hover:bg-[#F4E7EF]/50 border border-gray-200 hover:border-[#E7C8DB] transition flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-6 shadow-2xs" data-note-id="{{ $note->id }}">
                    <button type="button" onclick="editNote(this)" class="flex-1 min-w-0 text-left cursor-pointer" data-title="{{ $note->title }}" data-content="{{ $note->content }}">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="font-bold text-sm text-gray-900 group-hover:text-[#5B1744] transition">{{ $note->title ?: 'Untitled' }}</h3>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full bg-white border border-gray-200 text-gray-500 font-medium">
                                {{ $note->updated_at->format('d M Y, H:i') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed mt-1.5 line-clamp-2 whitespace-pre-line">{{ $note->content }}</p>
                    </button>
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <button type="button" onclick="editNote(this)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-[#E7C8DB] text-[#5B1744] hover:bg-[#5B1744] hover:text-white hover:border-[#5B1744] text-xs font-semibold shadow-2xs transition" data-title="{{ $note->title }}" data-content="{{ $note->content }}">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span>Edit</span>
                        </button>
                        <button type="button" onclick="deleteNote({{ $note->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-red-200 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500 text-xs font-semibold shadow-2xs transition">
                            <i class="fa-solid fa-trash text-[10px]"></i>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-regular fa-note-sticky text-3xl mb-3"></i>
                    <p class="text-sm">Belum ada catatan.</p>
                    <button onclick="openNoteModal()" class="mt-2 text-xs text-[#5B1744] font-semibold hover:underline">Tambah catatan baru</button>
                </div>
            @endforelse
        </div>
    </section>

    {{-- ADD / EDIT NOTE MODAL (Enlarged & Comfortable Reading) --}}
    <div id="noteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-6">
        <div onclick="closeNoteModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-3xl sm:max-w-4xl bg-[#FAF6F0] rounded-[2rem] p-5 sm:p-7 shadow-[0_0_60px_rgba(0,0,0,0.35),0_15px_35px_rgba(91,23,68,0.2)] border-2 border-[#E7C8DB] fade-up max-h-[92vh] flex flex-col my-auto">
            {{-- Header --}}
            <div class="flex items-center justify-between pb-4 border-b border-[#E7C8DB]/60 mb-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-[#F4E7EF] border border-[#E7C8DB] flex items-center justify-center shrink-0 text-[#5B1744] text-lg">
                        <i class="fa-regular fa-note-sticky"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] bg-[#F4E7EF] px-2.5 py-0.5 rounded-full" id="noteModalLabel">NEW NOTE</span>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mt-1" id="noteModalTitle">Tulis Catatan Belajar</h2>
                    </div>
                </div>
                <button type="button" onclick="closeNoteModal()" class="w-8 h-8 rounded-full bg-white border border-[#E7C8DB] flex items-center justify-center text-gray-500 hover:bg-[#F4E7EF] hover:text-[#5B1744] transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form id="noteForm" class="flex-1 flex flex-col gap-3 min-h-0">
                @csrf
                <input type="hidden" name="id" id="noteId">

                {{-- Judul Catatan --}}
                <div class="bg-white px-4 py-3 rounded-2xl border border-[#E7C8DB] shadow-2xs shrink-0">
                    <label class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] block mb-1">
                        Judul Catatan
                    </label>
                    <input type="text" name="title" id="noteTitle" placeholder="Contoh: Rangkuman Materi Fisika Kuantum / Rumus Trigonometri" 
                        class="w-full text-sm sm:text-base font-bold text-gray-900 placeholder:text-gray-300 outline-none bg-transparent">
                </div>

                {{-- Isi Catatan --}}
                <div class="bg-white p-4 sm:p-5 rounded-2xl border border-[#E7C8DB] shadow-2xs flex-1 flex flex-col min-h-0">
                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100 shrink-0">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-[#5B1744] block">
                            Isi Catatan / Materi Lengkap
                        </label>
                        <span id="noteCharCount" class="text-[10px] text-gray-400 font-semibold">0 karakter</span>
                    </div>
                    <textarea name="content" id="noteContent" required placeholder="Tuliskan catatan, rumus penting, konsep kunci, atau rangkuman materi di sini... Lebih leluasa dan nyaman dibaca!" 
                        class="w-full flex-1 min-h-[280px] sm:min-h-[380px] text-sm sm:text-base leading-relaxed text-gray-800 placeholder:text-gray-300 outline-none resize-none bg-transparent overflow-y-auto"
                        oninput="updateNoteCharCount()"></textarea>
                </div>

                {{-- Action Footer --}}
                <div class="flex items-center justify-end gap-2.5 pt-2 shrink-0">
                    <button type="button" onclick="closeNoteModal()" class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 text-xs font-bold hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" id="saveNoteBtn" class="px-6 py-2.5 rounded-xl bg-[#5B1744] hover:bg-[#481236] text-white text-xs font-bold transition shadow-md shadow-[#5B1744]/20 flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span id="saveNoteBtnText">Simpan Catatan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- DELETE NOTE CONFIRM MODAL --}}
    <div id="deleteNoteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div onclick="closeDeleteNoteModal()" class="absolute inset-0 bg-black/40 backdrop-blur-xs"></div>

        <div class="relative w-full max-w-sm bg-[#FAF6F0] rounded-3xl p-6 sm:p-7 shadow-[0_0_50px_rgba(0,0,0,0.35),0_15px_35px_rgba(91,23,68,0.2)] fade-up my-auto">
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

    function updateNoteCharCount() {
        const content = document.getElementById('noteContent')?.value || '';
        const countEl = document.getElementById('noteCharCount');
        if (countEl) {
            countEl.textContent = `${content.length.toLocaleString('id-ID')} karakter`;
        }
    }

    function openNoteModal() {
        document.getElementById('noteModalLabel').textContent = 'NEW NOTE';
        document.getElementById('noteModalTitle').textContent = 'Tulis Catatan Belajar';
        document.getElementById('saveNoteBtnText').textContent = 'Simpan Catatan';
        document.getElementById('noteId').value = '';
        document.getElementById('noteTitle').value = '';
        document.getElementById('noteContent').value = '';
        updateNoteCharCount();
        noteModal.classList.remove('hidden');
        noteModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function editNote(btn) {
        const title = btn.getAttribute('data-title') || '';
        const content = btn.getAttribute('data-content') || '';
        document.getElementById('noteModalLabel').textContent = 'EDIT NOTE';
        document.getElementById('noteModalTitle').textContent = 'Perbarui Catatan Belajar';
        document.getElementById('saveNoteBtnText').textContent = 'Simpan Perubahan';
        document.getElementById('noteId').value = btn.closest('.note-item').dataset.noteId;
        document.getElementById('noteTitle').value = title;
        document.getElementById('noteContent').value = content;
        updateNoteCharCount();
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

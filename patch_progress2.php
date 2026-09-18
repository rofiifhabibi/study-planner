<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldDiv = <<<HTML
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition">
HTML;
$newDiv = <<<HTML
                <div class="flex items-center gap-3 p-3.5 rounded-2xl hover:bg-[#FAF6F0]/60 transition cursor-pointer" onclick="loadPastSession({{ json_encode(\$s) }})">
HTML;
$content = str_replace($oldDiv, $newDiv, $content);

$oldJS = <<<JS
    let currentTaskId = null;
    let currentSteps = [];
JS;
$newJS = <<<JS
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
                <button onclick="deleteStudySession(\${session.id})" class="flex items-center gap-2 px-4 py-3 rounded-full bg-red-100 hover:bg-red-200 text-red-600 text-xs font-semibold shadow-md transition">
                    <i class="fa-solid fa-trash text-[10px]"></i>
                    <span>Hapus</span>
                </button>
            </div>
        `;
        
        if (session.task_id) {
            currentTaskId = session.task_id;
            apiFetch(`\${API_BASE}/tasks/\${session.task_id}/steps`)
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
            const res = await apiFetch(`\${API_BASE}/study-sessions/\${id}`, { method: 'DELETE' });
            const data = await res.json();
            if (data.status === 'success') {
                window.location.reload();
            }
        } catch (e) {
            alert('Gagal menghapus sesi.');
        }
    }
JS;
$content = str_replace($oldJS, $newJS, $content);

$oldResume = <<<JS
            const res = await apiFetch(`\${API_BASE}/study-sessions/\${activeStudySession.id}/resume`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            }
        } catch (err) {
            alert('Gagal resume sesi.');
        }
JS;
$newResume = <<<JS
            const res = await apiFetch(`\${API_BASE}/study-sessions/\${activeStudySession.id}/resume`, { method: 'POST' });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            } else {
                alert(data.message || 'Gagal melanjutkan sesi.');
            }
        } catch (err) {
            alert('Gagal resume sesi.');
        }
JS;
$content = str_replace($oldResume, $newResume, $content);

file_put_contents($file, $content);
echo "Patched progress ui for past sessions.\n";

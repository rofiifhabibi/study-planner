<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldLogic = <<<JS
        currentSteps.forEach(step => {
            const checkedStr = step.is_completed ? 'checked' : '';
            const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
            
            list.innerHTML += `
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" \${checkedStr} onchange="toggleStep(\${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]">
                    <span class="text-xs font-semibold \${lineThrough}">\${step.title}</span>
                </label>
            `;
        });
    }

    async function startSessionSubmit() {
JS;

$newLogic = <<<JS
        currentSteps.forEach(step => {
            const checkedStr = step.is_completed ? 'checked' : '';
            const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
            
            list.innerHTML += `
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" \${checkedStr} onchange="toggleStep(\${step.id}, this.checked)" class="w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]">
                    <span class="text-xs font-semibold \${lineThrough}">\${step.title}</span>
                </label>
            `;
        });
        
        if (currentTaskId) {
            list.innerHTML += `
                <div class="flex items-center gap-2 mt-2">
                    <input type="text" id="new-active-step-input" placeholder="Tambah langkah baru..." class="flex-1 text-xs px-3 py-2.5 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-[#5B1744] focus:border-[#5B1744]" onkeypress="if(event.key === 'Enter') addActiveStep()">
                    <button type="button" onclick="addActiveStep()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
            `;
        }
    }

    async function addActiveStep() {
        if (!currentTaskId) return;
        const input = document.getElementById('new-active-step-input');
        const title = input.value.trim();
        if (!title) return;
        
        try {
            const res = await apiFetch(`\${API_BASE}/tasks/\${currentTaskId}/steps`, {
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
JS;

$content = str_replace($oldLogic, $newLogic, $content);

// Also fix the session title to default to task title and stay visible when paused
$oldTitleLogic = <<<JS
        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
JS;
$newTitleLogic = <<<JS
        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
        document.getElementById('study-timer-status').setAttribute('data-title', session.title);
JS;
$content = str_replace($oldTitleLogic, $newTitleLogic, $content);

$oldPauseLogic = <<<JS
                document.getElementById('study-timer-status').textContent = 'Paused';
JS;
$newPauseLogic = <<<JS
                const sessionTitle = document.getElementById('study-timer-status').getAttribute('data-title') || 'Belajar';
                document.getElementById('study-timer-status').textContent = 'Paused: ' + sessionTitle;
JS;
$content = str_replace($oldPauseLogic, $newPauseLogic, $content);

file_put_contents($file, $content);
echo "Patched active checklist.\n";

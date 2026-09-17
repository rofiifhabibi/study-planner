<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

// Remove old startStudySession
$oldJs = "    async function startStudySession(defaultTitle = 'Belajar hari ini') {
        const title = prompt('Judul sesi belajar:', defaultTitle);
        if (!title) return;

        try {
            const res = await apiFetch(`\${API_BASE}/study-sessions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                startStudyTimer(data.session);
            } else {
                alert(data.message || 'Gagal memulai sesi.');
            }
        } catch (err) {
            alert('Gagal memulai sesi belajar.');
        }
    }";

$newJs = "
    let currentTaskId = null;
    let currentSteps = [];

    function openStartModal(taskTitle = null) {
        document.getElementById('start-session-modal').classList.remove('hidden');
        if (taskTitle) {
            const select = document.getElementById('task-select');
            for(let i=0; i<select.options.length; i++) {
                if (select.options[i].text === taskTitle) {
                    select.selectedIndex = i;
                    onTaskSelect(select.options[i].value);
                    break;
                }
            }
            document.getElementById('session-title').value = taskTitle;
        } else {
            document.getElementById('session-title').value = 'Belajar hari ini';
        }
    }

    function closeStartModal() {
        document.getElementById('start-session-modal').classList.add('hidden');
        document.getElementById('checklist-setup').classList.add('hidden');
        currentTaskId = null;
        currentSteps = [];
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
            const res = await apiFetch(`\${API_BASE}/tasks/\${taskId}/steps`);
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
                <div class=\"flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100\">
                    <i class=\"fa-regular fa-circle-check \${step.is_completed ? 'text-green-500' : 'text-gray-300'}\"></i>
                    <span class=\"text-xs font-medium \${step.is_completed ? 'line-through text-gray-400' : 'text-gray-700'}\">\${step.title}</span>
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
            const res = await apiFetch(`\${API_BASE}/tasks/\${currentTaskId}/steps`, {
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
            const res = await apiFetch(`\${API_BASE}/tasks/\${activeStudySession.task_id}/steps/\${stepId}`, {
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
            }
        } catch(e) {
            alert('Gagal mengupdate checklist.');
        }
    }

    function renderActiveChecklist() {
        const container = document.getElementById('active-checklist-container');
        const list = document.getElementById('active-steps-list');
        
        if (!currentSteps || currentSteps.length === 0) {
            container.classList.add('hidden');
            return;
        }
        
        container.classList.remove('hidden');
        list.innerHTML = '';
        
        currentSteps.forEach(step => {
            const checkedStr = step.is_completed ? 'checked' : '';
            const lineThrough = step.is_completed ? 'line-through text-gray-400' : 'text-gray-700';
            
            list.innerHTML += `
                <label class=\"flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 cursor-pointer hover:bg-gray-100 transition\">
                    <input type=\"checkbox\" \${checkedStr} onchange=\"toggleStep(\${step.id}, this.checked)\" class=\"w-4 h-4 text-[#5B1744] rounded border-gray-300 focus:ring-[#5B1744]\">
                    <span class=\"text-xs font-semibold \${lineThrough}\">\${step.title}</span>
                </label>
            `;
        });
    }

    async function startSessionSubmit() {
        const title = document.getElementById('session-title').value.trim() || 'Belajar hari ini';
        const task_id = document.getElementById('task-select').value || null;

        try {
            const res = await apiFetch(`\${API_BASE}/study-sessions`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ title, task_id }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                closeStartModal();
                startStudyTimer(data.session);
            } else {
                alert(data.message || 'Gagal memulai sesi.');
            }
        } catch (err) {
            alert('Gagal memulai sesi belajar.');
        }
    }
";

$content = str_replace($oldJs, $newJs, $content);
file_put_contents($file, $content);
echo "Replaced startStudySession with modal logic.\n";

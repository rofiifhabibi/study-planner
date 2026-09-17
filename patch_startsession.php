<?php
\$file = 'resources/views/progress.blade.php';
\$content = file_get_contents(\$file);

\$oldLogic = <<<JS
    async function startSessionSubmit() {
        const title = document.getElementById('session-title').value.trim() || 'Belajar hari ini';
        const task_id = document.getElementById('task-select').value || null;

        try {
            const res = await apiFetch(`\${API_BASE}/study-sessions`, {
JS;

\$newLogic = <<<JS
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
            const res = await apiFetch(`\${API_BASE}/study-sessions`, {
JS;

\$content = str_replace(\$oldLogic, \$newLogic, \$content);

// Also let's check addActiveStep (auto-add if they pause/stop while typing?)
// Probably not needed for pause/stop. But for Enter key:
\$oldAddStep = "onkeypress=\"if(event.key === 'Enter') addStep()\""; // wait, the modal input uses what?

file_put_contents(\$file, \$content);
echo "Patched startSessionSubmit.\n";

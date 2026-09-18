const fs = require('fs');
let content = fs.readFileSync('resources/views/progress.blade.php', 'utf8');

const oldLogic = `    async function startSessionSubmit() {
        const title = document.getElementById('session-title').value.trim() || 'Belajar hari ini';
        const task_id = document.getElementById('task-select').value || null;

        try {
            const res = await apiFetch(\`\${API_BASE}/study-sessions\`, {`;

const newLogic = `    async function startSessionSubmit() {
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
            const res = await apiFetch(\`\${API_BASE}/study-sessions\`, {`;

content = content.replace(oldLogic, newLogic);

fs.writeFileSync('resources/views/progress.blade.php', content);
console.log('patched successfully');

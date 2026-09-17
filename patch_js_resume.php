<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldCheck = "    async function checkActiveStudySession() {
        try {
            const res = await apiFetch(`\${API_BASE}/study-sessions/active`);
            const data = await res.json();
            if (data.session) {
                startStudyTimer(data.session);
            }
        } catch (err) {}
    }";

$newCheck = "    async function checkActiveStudySession() {
        try {
            const res = await apiFetch(`\${API_BASE}/study-sessions/active`);
            const data = await res.json();
            if (data.session) {
                if (data.session.task_id) {
                    const stepRes = await apiFetch(`\${API_BASE}/tasks/\${data.session.task_id}/steps`);
                    const stepData = await stepRes.json();
                    currentSteps = stepData.steps || [];
                    renderActiveChecklist();
                }
                startStudyTimer(data.session);
            }
        } catch (err) {}
    }";

$content = str_replace($oldCheck, $newCheck, $content);

// Also modify urlParams parsing to use openStartModal
$oldUrl = "            if (!activeStudySession) {
                setTimeout(() => startStudySession(taskTitle), 500);
            }";
$newUrl = "            if (!activeStudySession) {
                setTimeout(() => openStartModal(taskTitle), 500);
            }";
$content = str_replace($oldUrl, $newUrl, $content);

file_put_contents($file, $content);
echo "Patched resume JS.\n";

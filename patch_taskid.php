<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldLogic = <<<JS
                if (data.session.task_id) {
                    const stepRes = await apiFetch(`\${API_BASE}/tasks/\${data.session.task_id}/steps`);
                    const stepData = await stepRes.json();
                    currentSteps = stepData.steps || [];
                    renderActiveChecklist();
                }
JS;

$newLogic = <<<JS
                if (data.session.task_id) {
                    currentTaskId = data.session.task_id;
                    const stepRes = await apiFetch(`\${API_BASE}/tasks/\${data.session.task_id}/steps`);
                    const stepData = await stepRes.json();
                    currentSteps = stepData.steps || [];
                    renderActiveChecklist();
                }
JS;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched currentTaskId.\n";

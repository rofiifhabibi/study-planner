<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldLogic = <<<JS
            const data = await res.json();
            if (data.status === 'success') {
                // Update local active steps
                const step = currentSteps.find(s => s.id === stepId);
                if (step) step.is_completed = isCompleted;
                renderActiveChecklist();
            }
JS;

$newLogic = <<<JS
            const data = await res.json();
            if (data.status === 'success') {
                // Update local active steps
                const step = currentSteps.find(s => s.id === stepId);
                if (step) step.is_completed = isCompleted;
                renderActiveChecklist();
                
                if (data.task_status === 'completed' && isCompleted) {
                    showToast('Semua langkah selesai! Tugas otomatis ditandai Selesai & disinkronkan ke Google Tasks.', 'success');
                }
            }
JS;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched progress.blade.php.\n";

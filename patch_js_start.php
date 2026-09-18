<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

// Replace openStartModal definition
$oldOpen = "    function openStartModal(taskTitle = null) {
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
    }";

$newOpen = "    function openStartModal(taskId = null) {
        document.getElementById('start-session-modal').classList.remove('hidden');
        if (taskId) {
            const select = document.getElementById('task-select');
            select.value = taskId;
            if (select.selectedIndex >= 0) {
                document.getElementById('session-title').value = select.options[select.selectedIndex].text;
                onTaskSelect(taskId);
            }
        } else {
            document.getElementById('session-title').value = 'Belajar hari ini';
        }
    }";
$content = str_replace($oldOpen, $newOpen, $content);

// Replace checkActiveStudySession url parsing
$oldUrl = "        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_task')) {
            const taskTitle = urlParams.get('start_task');
            if (!activeStudySession) {
                setTimeout(() => openStartModal(taskTitle), 500);
            }";
            
$newUrl = "        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_task_id')) {
            const taskId = urlParams.get('start_task_id');
            if (!activeStudySession) {
                setTimeout(() => openStartModal(taskId), 500);
            }";
$content = str_replace($oldUrl, $newUrl, $content);

file_put_contents($file, $content);
echo "Patched progress js for taskId.\n";

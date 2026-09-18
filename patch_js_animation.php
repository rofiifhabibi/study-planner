<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldOpen = "    function openStartModal(taskId = null) {
        document.getElementById('start-session-modal').classList.remove('hidden');";

$newOpen = "    function openStartModal(taskId = null) {
        const modal = document.getElementById('start-session-modal');
        const box = document.getElementById('modal-content-box');
        
        modal.classList.remove('hidden');
        // trigger reflow
        void modal.offsetWidth;
        
        modal.classList.add('opacity-100');
        box.classList.remove('scale-95', 'opacity-0');
        box.classList.add('scale-100', 'opacity-100');
";
$content = str_replace($oldOpen, $newOpen, $content);

$oldClose = "    function closeStartModal() {
        document.getElementById('start-session-modal').classList.add('hidden');
        document.getElementById('checklist-setup').classList.add('hidden');
        currentTaskId = null;
        currentSteps = [];
    }";

$newClose = "    function closeStartModal() {
        const modal = document.getElementById('start-session-modal');
        const box = document.getElementById('modal-content-box');
        
        modal.classList.remove('opacity-100');
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            document.getElementById('checklist-setup').classList.add('hidden');
            currentTaskId = null;
            currentSteps = [];
        }, 300);
    }";
$content = str_replace($oldClose, $newClose, $content);

$oldSubmit = "            if (data.status === 'success') {
                document.getElementById('start-session-modal').classList.add('hidden');
                startStudyTimer(data.session);
            }";
$newSubmit = "            if (data.status === 'success') {
                closeStartModal();
                setTimeout(() => startStudyTimer(data.session), 300);
            }";
$content = str_replace($oldSubmit, $newSubmit, $content);

file_put_contents($file, $content);
echo "Patched form animation.\n";

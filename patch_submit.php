<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldLogic = "            if (data.status === 'success') {
                closeStartModal();
                startStudyTimer(data.session);
            }";

$newLogic = "            if (data.status === 'success') {
                document.getElementById('start-session-modal').classList.add('hidden');
                startStudyTimer(data.session);
            }";

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched start session submit.\n";

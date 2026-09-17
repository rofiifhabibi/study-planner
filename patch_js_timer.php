<?php
$file = 'resources/views/progress.blade.php';
$content = file_get_contents($file);

$oldStart = "        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
        document.getElementById('study-timer-icon').innerHTML = '<i class=\"fa-solid fa-spinner fa-spin\"></i>';
    }";

$newStart = "        document.getElementById('study-timer-status').textContent = 'Studying: ' + session.title;
        document.getElementById('study-timer-icon').innerHTML = '<i class=\"fa-solid fa-spinner fa-spin\"></i>';
        renderActiveChecklist();
    }";

$content = str_replace($oldStart, $newStart, $content);
file_put_contents($file, $content);
echo "Patched start timer.\n";

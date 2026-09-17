<?php
$file = 'resources/views/dashboard.blade.php';
$content = file_get_contents($file);

$content = str_replace(
    "route('progress') . '?start_task=' . urlencode(\$task->title)",
    "route('progress') . '?start_task_id=' . \$task->id",
    $content
);
file_put_contents($file, $content);
echo "Updated start link in dashboard.\n";

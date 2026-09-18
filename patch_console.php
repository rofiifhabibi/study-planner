<?php
$file = 'routes/console.php';
$content = file_get_contents($file);

$oldSchedule = "Schedule::command('agenda:send-daily')->dailyAt('20:00');";
$newSchedule = "Schedule::command('agenda:send-daily', ['--target=tomorrow'])->dailyAt('20:00');\nSchedule::command('agenda:send-daily', ['--target=today'])->dailyAt('05:00');";

if (strpos($content, "Schedule::command('agenda:send-daily', ['--target=tomorrow'])") === false) {
    $content = str_replace($oldSchedule, $newSchedule, $content);
    file_put_contents($file, $content);
    echo "Patched console.\n";
} else {
    echo "Already patched.\n";
}

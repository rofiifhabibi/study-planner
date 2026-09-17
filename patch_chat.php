<?php
$file = 'app/Http/Controllers/ChatController.php';
$content = file_get_contents($file);

$oldSchedules = "        \$todaySchedules = Schedule::where('user_id', \$user->id)\n            ->whereDate('date', today())\n            ->orderBy('start_time')\n            ->get();";
$newSchedules = "        \$todaySchedules = Schedule::where('user_id', \$user->id)\n            ->whereDate('date', '>=', today())\n            ->orderBy('date')\n            ->orderBy('start_time')\n            ->limit(5)\n            ->get();";

$content = str_replace($oldSchedules, $newSchedules, $content);
file_put_contents($file, $content);
echo "Patched schedules in ChatController.\n";

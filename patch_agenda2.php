<?php
$file = 'app/Console/Commands/SendDailyAgendaEmail.php';
$content = file_get_contents($file);

$oldCall = "Mail::to(\$user->email)->send(new DailyTimetableAgenda(\$timetables, \$dayName));";
$newCall = "Mail::to(\$user->email)->send(new DailyTimetableAgenda(\$timetables, \$dayName, \$this->option('target')));";

$content = str_replace($oldCall, $newCall, $content);
file_put_contents($file, $content);
echo "Patched command parameter passing.\n";

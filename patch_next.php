<?php
$content = file_get_contents('app/Services/GoogleCalendarService.php');

$search = <<<'HTML'
        $carbonDay = $timetable->day_of_week == 0 ? 0 : $timetable->day_of_week;
        $nextDate = now()->next($carbonDay);
HTML;

$replace = <<<'HTML'
        $carbonDay = $timetable->day_of_week == 0 ? 0 : $timetable->day_of_week;
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $nextDate = now()->next($dayNames[$carbonDay]);
HTML;

$content = str_replace($search, $replace, $content);
file_put_contents('app/Services/GoogleCalendarService.php', $content);

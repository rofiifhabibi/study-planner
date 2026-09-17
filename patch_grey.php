<?php
$content = file_get_contents('app/Services/GoogleCalendarService.php');

$search = <<<'HTML'
        // Assign a consistent color (1-11) based on the subject name
        $colorId = (string) ((crc32(strtolower(trim($timetable->subject))) % 11) + 1);
HTML;

$replace = <<<'HTML'
        // Graphite / Grey color in Google Calendar
        $colorId = '8';
HTML;

$content = str_replace($search, $replace, $content);
file_put_contents('app/Services/GoogleCalendarService.php', $content);

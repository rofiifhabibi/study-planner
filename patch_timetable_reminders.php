<?php
$file = 'app/Services/GoogleCalendarService.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
            'recurrence' => [
                'RRULE:FREQ=WEEKLY;BYDAY=' . \$dayMap[\$timetable->day_of_week]
            ],
        ]);

        if (! empty(\$timetable->google_event_id)) {
PHP;

$newLogic = <<<PHP
            'recurrence' => [
                'RRULE:FREQ=WEEKLY;BYDAY=' . \$dayMap[\$timetable->day_of_week]
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 30],
                    ['method' => 'popup', 'minutes' => 10],
                ],
            ],
        ]);

        if (! empty(\$timetable->google_event_id)) {
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched reminders.\n";

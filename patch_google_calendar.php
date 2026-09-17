<?php
$content = file_get_contents('app/Services/GoogleCalendarService.php');

// Add SchoolTimetable import
$content = str_replace(
    "use App\Models\Task;",
    "use App\Models\Task;\nuse App\Models\SchoolTimetable;",
    $content
);

// Add the new methods before the closing brace
$methods = <<<'HTML'
    // === School Timetable ===

    public function syncSchoolTimetable(SchoolTimetable $timetable): bool
    {
        if (! $this->isConnected()) {
            return false;
        }

        try {
            $this->upsertSchoolTimetable($timetable);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar timetable sync error', [
                'timetable_id' => $timetable->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function deleteSchoolTimetableEvent(SchoolTimetable $timetable): bool
    {
        if (! $this->isConnected() || empty($timetable->google_event_id)) {
            return false;
        }

        try {
            $calendar = new GoogleCalendar($this->client);
            $calendar->events->delete('primary', $timetable->google_event_id);
            return true;
        } catch (\Exception $e) {
            if ($e->getCode() == 404 || str_contains($e->getMessage(), 'Not Found')) {
                return true;
            }
            Log::error('Google Calendar timetable delete error', [
                'timetable_id' => $timetable->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function upsertSchoolTimetable(SchoolTimetable $timetable): void
    {
        $calendar = new GoogleCalendar($this->client);

        // Find the next occurrence of the day_of_week
        // 1=Senin, 0/7=Minggu. Laravel Carbon dayOfWeek: 0 (Sunday) - 6 (Saturday).
        // Our app: 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat.
        $dayMap = [
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
            0 => 'SU'
        ];
        
        $carbonDay = $timetable->day_of_week == 0 ? 0 : $timetable->day_of_week;
        $nextDate = now()->next($carbonDay);
        
        $startTime = Carbon::parse($timetable->start_time);
        $endTime = $timetable->end_time ? Carbon::parse($timetable->end_time) : $startTime->copy()->addHour();
        
        $event = new Event([
            'summary' => '[Pelajaran] ' . $timetable->subject,
            'start' => [
                'dateTime' => $nextDate->format('Y-m-d') . 'T' . $startTime->format('H:i') . ':00',
                'timeZone' => $this->calendarTimezone(),
            ],
            'end' => [
                'dateTime' => $nextDate->format('Y-m-d') . 'T' . $endTime->format('H:i') . ':00',
                'timeZone' => $this->calendarTimezone(),
            ],
            'recurrence' => [
                'RRULE:FREQ=WEEKLY;BYDAY=' . $dayMap[$timetable->day_of_week]
            ],
        ]);

        if (! empty($timetable->google_event_id)) {
            $calendar->events->update('primary', $timetable->google_event_id, $event);
            return;
        }

        $createdEvent = $calendar->events->insert('primary', $event);
        $timetable->update(['google_event_id' => $createdEvent->getId()]);
    }
HTML;

$content = preg_replace('/}\s*$/', "\n$methods\n}", $content);
file_put_contents('app/Services/GoogleCalendarService.php', $content);

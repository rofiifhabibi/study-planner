<?php
$file = 'app/Services/GoogleCalendarService.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
                \$existing = Schedule::where('user_id', \$this->user->id)
                    ->where('google_event_id', \$event->getId())
                    ->first();

                if (\$existing) {
                    continue;
                }

                \$startDate = Carbon::parse(\$startDateTime);
                \$endDate = Carbon::parse(\$endDateTime);

                Schedule::create([
                    'user_id' => \$this->user->id,
                    'title' => \$event->getSummary() ?? 'Untitled Event',
                    'subject' => \$event->getDescription(),
                    'date' => \$startDate->format('Y-m-d'),
                    'start_time' => \$startDate->format('H:i'),
                    'end_time' => \$endDate->format('H:i'),
                    'status' => 'pending',
                    'google_event_id' => \$event->getId(),
                ]);
                \$imported++;
PHP;

$newLogic = <<<PHP
                \$title = \$event->getSummary() ?? 'Untitled Event';
                \$startDate = Carbon::parse(\$startDateTime);
                \$endDate = Carbon::parse(\$endDateTime);
                
                // If title contains [Pelajaran], it belongs to SchoolTimetable
                if (stripos(\$title, '[Pelajaran]') !== false) {
                    \$subject = trim(str_ireplace('[Pelajaran]', '', \$title));
                    // 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat
                    \$dayOfWeek = \$startDate->dayOfWeek; 
                    
                    \$existingTimetable = \\App\\Models\\SchoolTimetable::where('user_id', \$this->user->id)
                        ->where('google_event_id', \$event->getId())
                        ->first();
                        
                    if (!\$existingTimetable) {
                        \\App\\Models\\SchoolTimetable::create([
                            'user_id' => \$this->user->id,
                            'subject' => \$subject,
                            'day_of_week' => \$dayOfWeek,
                            'start_time' => \$startDate->format('H:i'),
                            'end_time' => \$endDate->format('H:i'),
                            'google_event_id' => \$event->getId(),
                        ]);
                        \$imported++;
                    }
                    continue;
                }

                \$existing = Schedule::where('user_id', \$this->user->id)
                    ->where('google_event_id', \$event->getId())
                    ->first();

                if (\$existing) {
                    continue;
                }

                Schedule::create([
                    'user_id' => \$this->user->id,
                    'title' => \$title,
                    'subject' => \$event->getDescription(),
                    'date' => \$startDate->format('Y-m-d'),
                    'start_time' => \$startDate->format('H:i'),
                    'end_time' => \$endDate->format('H:i'),
                    'status' => 'pending',
                    'google_event_id' => \$event->getId(),
                ]);
                \$imported++;
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched importFromGoogleCalendar.\n";

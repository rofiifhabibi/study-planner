<?php
$file = 'app/Services/GoogleCalendarService.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
                    \$existingTimetable = \App\Models\SchoolTimetable::where('user_id', \$this->user->id)
                        ->where('google_event_id', \$event->getId())
                        ->first();
                        
                    if (!\$existingTimetable) {
                        \App\Models\SchoolTimetable::create([
                            'user_id' => \$this->user->id,
                            'subject' => \$subject,
                            'day_of_week' => \$dayOfWeek,
                            'start_time' => \$startDate->format('H:i'),
                            'end_time' => \$endDate->format('H:i'),
                            'google_event_id' => \$event->getId(),
                        ]);
                        \$imported++;
                    }
PHP;

$newLogic = <<<PHP
                    // Check by google_event_id OR by subject, day, and time to prevent duplicates from recurring instances
                    \$existingTimetable = \App\Models\SchoolTimetable::where('user_id', \$this->user->id)
                        ->where(function(\$q) use (\$event, \$subject, \$dayOfWeek, \$startDate) {
                            \$q->where('google_event_id', \$event->getId())
                              ->orWhere(function(\$subQ) use (\$subject, \$dayOfWeek, \$startDate) {
                                  \$subQ->where('subject', \$subject)
                                        ->where('day_of_week', \$dayOfWeek)
                                        ->where('start_time', \$startDate->format('H:i').':00');
                              });
                        })
                        ->first();
                        
                    if (!\$existingTimetable) {
                        \App\Models\SchoolTimetable::create([
                            'user_id' => \$this->user->id,
                            'subject' => \$subject,
                            'day_of_week' => \$dayOfWeek,
                            'start_time' => \$startDate->format('H:i'),
                            'end_time' => \$endDate->format('H:i'),
                            'google_event_id' => \$event->getId(), // Store the first instance ID
                        ]);
                        \$imported++;
                    }
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched import logic.\n";

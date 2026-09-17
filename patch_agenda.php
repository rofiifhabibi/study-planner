<?php
$file = 'app/Console/Commands/SendDailyAgendaEmail.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
use App\Models\SchoolTimetable;
use App\Mail\DailyTimetableAgenda;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
PHP;

$newLogic = <<<PHP
use App\Models\SchoolTimetable;
use App\Models\Task;
use App\Models\Schedule;
use App\Mail\DailyTimetableAgenda;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
PHP;

$content = str_replace($oldLogic, $newLogic, $content);

$oldHandle = <<<PHP
        foreach (\$users as \$user) {
            \$timetables = SchoolTimetable::where('user_id', \$user->id)
                ->where('day_of_week', \$targetDayOfWeek)
                ->orderBy('start_time')
                ->get();

            // Send to everyone so they receive a daily recap (empty or not)
            Mail::to(\$user->email)->send(new DailyTimetableAgenda(\$timetables, \$dayName, \$this->option('target')));
            \$this->info("Sent agenda to {\$user->email}");
        }
PHP;

$newHandle = <<<PHP
        foreach (\$users as \$user) {
            \$timetables = SchoolTimetable::where('user_id', \$user->id)
                ->where('day_of_week', \$targetDayOfWeek)
                ->orderBy('start_time')
                ->get();
                
            \$tasks = Task::where('user_id', \$user->id)
                ->where('status', 'pending')
                ->where('due_date', \$target->toDateString())
                ->get();
                
            \$schedules = Schedule::where('user_id', \$user->id)
                ->where('date', \$target->toDateString())
                ->orderBy('start_time')
                ->get();

            // Send to everyone so they receive a daily recap (empty or not)
            Mail::to(\$user->email)->send(new DailyTimetableAgenda(\$timetables, \$tasks, \$schedules, \$dayName, \$this->option('target')));
            \$this->info("Sent agenda to {\$user->email}");
        }
PHP;

$content = str_replace($oldHandle, $newHandle, $content);
file_put_contents($file, $content);
echo "Patched SendDailyAgendaEmail command.\n";

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SchoolTimetable;
use App\Models\Task;
use App\Models\Schedule;
use App\Mail\DailyTimetableAgenda;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDailyAgendaEmail extends Command
{
    protected $signature = 'agenda:send-daily {--target=tomorrow : The target day to send agenda for (today or tomorrow)}';
    protected $description = 'Send daily agenda email for today or tomorrow\'s school timetable to all users';

    public function handle()
    {
        $target = $this->option('target') === 'today' ? Carbon::today() : Carbon::tomorrow();
        $targetDayOfWeek = $target->dayOfWeek;
        $dayName = $target->translatedFormat('l');

        $users = User::all();

        foreach ($users as $user) {
            $timetables = SchoolTimetable::where('user_id', $user->id)
                ->where('day_of_week', $targetDayOfWeek)
                ->orderBy('start_time')
                ->get();
                
            $tasks = Task::where('user_id', $user->id)
                ->where('status', 'pending')
                ->where('due_date', $target->toDateString())
                ->get();
                
            $schedules = Schedule::where('user_id', $user->id)
                ->where('date', $target->toDateString())
                ->orderBy('start_time')
                ->get();

            // Send to everyone so they receive a daily recap (empty or not)
            Mail::to($user->email)->send(new DailyTimetableAgenda($timetables, $tasks, $schedules, $dayName, $this->option('target')));
            $this->info("Sent agenda to {$user->email}");
        }
        
        $this->info('Daily agenda emails dispatched!');
    }
}

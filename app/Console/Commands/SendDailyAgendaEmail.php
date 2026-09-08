<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\SchoolTimetable;
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

            // We only send the email if they have classes tomorrow
            // But if they want a daily summary, we can send it either way.
            // Let's only send if there are classes, or send anyway?
            // "kalau jam 8 itu pakai email aja" -> Let's send anyway so they know it's empty.
            if ($timetables->count() > 0) {
                Mail::to($user->email)->send(new DailyTimetableAgenda($timetables, $dayName, $this->option('target')));
                $this->info("Sent agenda to {$user->email}");
            }
        }
        
        $this->info('Daily agenda emails dispatched!');
    }
}

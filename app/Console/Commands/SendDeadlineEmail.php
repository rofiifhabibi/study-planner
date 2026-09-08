<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Task;
use App\Mail\DeadlineReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendDeadlineEmail extends Command
{
    protected \$signature = 'agenda:send-deadline';
    protected \$description = 'Send email reminder for tasks that are approaching deadline (due today or tomorrow)';

    public function handle()
    {
        \$users = User::all();
        \$targetDate = Carbon::tomorrow()->toDateString();
        \$todayDate = Carbon::today()->toDateString();

        foreach (\$users as \$user) {
            // Find tasks due today or tomorrow that are still pending
            \$tasks = Task::where('user_id', \$user->id)
                ->where('status', 'pending')
                ->where(function(\$q) use (\$targetDate, \$todayDate) {
                    \$q->whereDate('due_date', \$todayDate)
                      ->orWhereDate('due_date', \$targetDate)
                      ->orWhereDate('due_date', '<', \$todayDate); // Also include overdue
                })
                ->orderBy('due_date')
                ->get();

            if (\$tasks->count() > 0) {
                Mail::to(\$user->email)->send(new DeadlineReminder(\$tasks, \$user));
                \$this->info("Sent deadline reminder to {\$user->email} for {\$tasks->count()} tasks.");
            } else {
                \$this->info("No upcoming deadlines for {\$user->email}");
            }
        }
        
        \$this->info('Deadline reminder emails dispatched!');
    }
}

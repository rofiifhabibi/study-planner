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
    protected $signature = 'agenda:send-deadline';
    protected $description = 'Send email reminder for all pending tasks and upcoming deadlines';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            // Find ALL pending tasks for this user
            $tasks = Task::where('user_id', $user->id)
                ->where('status', 'pending')
                ->orderBy('due_date')
                ->get();

            if ($tasks->count() > 0) {
                Mail::to($user->email)->send(new DeadlineReminder($tasks, $user));
                $this->info("Sent task reminder to {$user->email} for {$tasks->count()} tasks.");
            } else {
                $this->info("No pending tasks for {$user->email}");
            }
        }
        
        $this->info('Task reminder emails dispatched!');
    }
}

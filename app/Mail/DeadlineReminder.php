<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DeadlineReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $tasks;

    public User $user;

    public function __construct(Collection $tasks, User $user)
    {
        $this->tasks = $tasks;
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        $count = $this->tasks->count();

        return new Envelope(
            subject: "Rekap Tugas: Ada $count Tugas yang Menunggumu!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deadline_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

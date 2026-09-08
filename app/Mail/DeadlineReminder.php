<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\User;

class DeadlineReminder extends Mailable
{
    use Queueable, SerializesModels;

    public Collection \$tasks;
    public User \$user;

    public function __construct(Collection \$tasks, User \$user)
    {
        \$this->tasks = \$tasks;
        \$this->user = \$user;
    }

    public function envelope(): Envelope
    {
        \$count = \$this->tasks->count();
        return new Envelope(
            subject: "Peringatan: Ada \$count Tugas Hampir Deadline!",
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

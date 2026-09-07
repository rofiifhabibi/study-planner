<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyTimetableAgenda extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $timetables;
    public string $dayName;

    public function __construct(Collection $timetables, string $dayName)
    {
        $this->timetables = $timetables;
        $this->dayName = $dayName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Agenda: Jadwal Pelajaran Besok (' . $this->dayName . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_agenda',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

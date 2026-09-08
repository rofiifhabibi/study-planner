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
    public Collection $tasks;
    public Collection $schedules;
    public string $dayName;
    public string $targetType;

    public function __construct(Collection $timetables, Collection $tasks, Collection $schedules, string $dayName, string $targetType = 'tomorrow')
    {
        $this->timetables = $timetables;
        $this->tasks = $tasks;
        $this->schedules = $schedules;
        $this->dayName = $dayName;
        $this->targetType = $targetType;
    }

    public function envelope(): Envelope
    {
        $targetWord = $this->targetType === 'today' ? 'Hari Ini' : 'Besok';
        return new Envelope(
            subject: 'Daily Agenda: Jadwal Pelajaran ' . $targetWord . ' (' . $this->dayName . ')',
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

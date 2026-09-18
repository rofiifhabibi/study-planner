<?php
$file = 'app/Mail/DailyTimetableAgenda.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
    public Collection \$timetables;
    public string \$dayName;

    public function __construct(Collection \$timetables, string \$dayName)
    {
        \$this->timetables = \$timetables;
        \$this->dayName = \$dayName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Agenda: Jadwal Pelajaran Besok (' . \$this->dayName . ')',
        );
    }
PHP;

$newLogic = <<<PHP
    public Collection \$timetables;
    public string \$dayName;
    public string \$targetType;

    public function __construct(Collection \$timetables, string \$dayName, string \$targetType = 'tomorrow')
    {
        \$this->timetables = \$timetables;
        \$this->dayName = \$dayName;
        \$this->targetType = \$targetType;
    }

    public function envelope(): Envelope
    {
        \$targetWord = \$this->targetType === 'today' ? 'Hari Ini' : 'Besok';
        return new Envelope(
            subject: 'Daily Agenda: Jadwal Pelajaran ' . \$targetWord . ' (' . \$this->dayName . ')',
        );
    }
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched mail class.\n";

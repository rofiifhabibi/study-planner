<?php
$file = 'app/Mail/DailyTimetableAgenda.php';
$content = file_get_contents($file);

$oldLogic = <<<PHP
    public Collection \$timetables;
    public string \$dayName;
    public string \$targetType;

    public function __construct(Collection \$timetables, string \$dayName, string \$targetType = 'tomorrow')
    {
        \$this->timetables = \$timetables;
        \$this->dayName = \$dayName;
        \$this->targetType = \$targetType;
    }
PHP;

$newLogic = <<<PHP
    public Collection \$timetables;
    public Collection \$tasks;
    public Collection \$schedules;
    public string \$dayName;
    public string \$targetType;

    public function __construct(Collection \$timetables, Collection \$tasks, Collection \$schedules, string \$dayName, string \$targetType = 'tomorrow')
    {
        \$this->timetables = \$timetables;
        \$this->tasks = \$tasks;
        \$this->schedules = \$schedules;
        \$this->dayName = \$dayName;
        \$this->targetType = \$targetType;
    }
PHP;

$content = str_replace($oldLogic, $newLogic, $content);
file_put_contents($file, $content);
echo "Patched mailable properties.\n";

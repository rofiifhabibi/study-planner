<?php
$content = file_get_contents('app/Services/GoogleCalendarService.php');

// 1. Patch upsertTask for priority emojis
$searchTask = <<<'HTML'
        $googleTask = new GoogleTasks\Task([
            'title' => $task->title,
            'notes' => $task->description,
HTML;

$replaceTask = <<<'HTML'
        $priorityEmoji = ['high' => '🔴', 'medium' => '🟡', 'low' => '🟢'][$task->priority] ?? '';
        $taskTitle = trim($priorityEmoji . ' ' . $task->title);

        $googleTask = new GoogleTasks\Task([
            'title' => $taskTitle,
            'notes' => $task->description,
HTML;

$content = str_replace($searchTask, $replaceTask, $content);

// 2. Patch upsertSchoolTimetable for colors
$searchTimetable = <<<'HTML'
        $event = new Event([
            'summary' => '[Pelajaran] ' . $timetable->subject,
            'start' => [
HTML;

$replaceTimetable = <<<'HTML'
        // Assign a consistent color (1-11) based on the subject name
        $colorId = (string) ((crc32(strtolower(trim($timetable->subject))) % 11) + 1);

        $event = new Event([
            'summary' => '[Pelajaran] ' . $timetable->subject,
            'colorId' => $colorId,
            'start' => [
HTML;

$content = str_replace($searchTimetable, $replaceTimetable, $content);

file_put_contents('app/Services/GoogleCalendarService.php', $content);

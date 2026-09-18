<?php
$file = 'resources/views/schedule.blade.php';
$content = file_get_contents($file);

// 1. Fix grid-cols-3 in the schedule form modal
$content = str_replace(
    '<div class="grid grid-cols-3 gap-3">',
    '<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">',
    $content
);

// 2. Add x-overflow auto to calendar wrapper
$oldCalendarWrap = '<section class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-7 soft-shadow h-[70vh] flex flex-col fade-up mt-5">
        <div id="calendar" class="flex-1 w-full h-full overflow-y-auto"></div>
    </section>';

$newCalendarWrap = '<section class="bg-white rounded-3xl border border-gray-100 p-3 sm:p-7 soft-shadow h-[70vh] flex flex-col fade-up mt-5">
        <div id="calendar" class="flex-1 w-full h-full overflow-y-auto overflow-x-auto min-w-0"></div>
    </section>';
$content = str_replace($oldCalendarWrap, $newCalendarWrap, $content);

// 3. Make FullCalendar's header Toolbar responsive by adding some JS logic
$oldCalendarInit = <<<JS
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
JS;

$newCalendarInit = <<<JS
        const isMobile = window.innerWidth < 768;
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? 'timeGridDay' : 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next',
                center: 'title',
                right: isMobile ? 'timeGridDay,dayGridMonth' : 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            titleFormat: isMobile ? { month: 'short', day: 'numeric' } : { month: 'long', year: 'numeric' },
JS;

$content = str_replace($oldCalendarInit, $newCalendarInit, $content);

file_put_contents($file, $content);
echo "Patched schedule mobile view.\n";

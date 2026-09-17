<?php
$file = 'resources/views/schedule.blade.php';
$content = file_get_contents($file);

$oldStyles = <<<CSS
    <style>
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
        }
        .fc .fc-button-primary {
            background-color: #5B1744 !important;
            border-color: #5B1744 !important;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #481236 !important;
            border-color: #481236 !important;
        }
        .fc-theme-standard th, .fc-theme-standard td {
            border-color: #f3f4f6;
        }
    </style>
CSS;

$newStyles = <<<CSS
    <style>
        .fc-event {
            cursor: pointer;
            border-radius: 4px;
            padding: 2px 4px;
        }
        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
        }
        .fc .fc-button-primary {
            background-color: #5B1744 !important;
            border-color: #5B1744 !important;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #481236 !important;
            border-color: #481236 !important;
        }
        .fc-theme-standard th, .fc-theme-standard td {
            border-color: #f3f4f6;
        }
        /* Mobile specific fixes */
        @media (max-width: 640px) {
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 10px;
            }
            .fc .fc-toolbar-title {
                font-size: 1.1rem;
            }
            .fc .fc-button {
                padding: 0.2rem 0.5rem;
                font-size: 0.8rem;
            }
        }
    </style>
CSS;

$content = str_replace($oldStyles, $newStyles, $content);
file_put_contents($file, $content);
echo "Patched schedule styles.\n";

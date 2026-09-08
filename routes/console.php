<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('agenda:send-daily', ['--target=tomorrow'])->dailyAt('20:00');
Schedule::command('agenda:send-daily', ['--target=today'])->dailyAt('05:00');

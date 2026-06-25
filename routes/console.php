<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('appointments:remind')->dailyAt('08:00');
Schedule::command('health:weekly-report')->weeklyOn(1, '09:00');
Schedule::command('medications:remind')->everyMinute();

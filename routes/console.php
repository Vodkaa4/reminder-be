<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app(Schedule::class)->command('contracts:send-expiring')->dailyAt('08:00');
app(Schedule::class)->command('permits:send-expiring')->dailyAt('08:00');
app(Schedule::class)->command('reminders:run')->everyMinute();
app(Schedule::class)->command('permits:update-statuses')->dailyAt('00:00');
    
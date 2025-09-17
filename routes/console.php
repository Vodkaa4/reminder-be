<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Schedule::command('reminders:run')->dailyAt('08:00');
Schedule::command('permits:send-expiring --days=30')->dailyAt('09:00');
Schedule::command('permits:send-expiring --days=15')->dailyAt('09:30');

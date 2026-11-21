<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('db:backup')
    ->dailyAt('02:00')
    ->description('Automated daily database backup')
    ->withoutOverlapping();

Schedule::command('auth:clear-resets')
    ->dailyAt('03:00')
    ->description('Clear expired password reset tokens')
    ->withoutOverlapping();

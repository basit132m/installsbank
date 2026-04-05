<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Credit fixed-rate publishers their daily fixed amount every day at midnight
Schedule::command('publishers:credit-fixed-rate')->dailyAt('00:05');

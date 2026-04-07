<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\FraudAlert;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Credit fixed-rate publishers their daily fixed amount every day at midnight
Schedule::command('publishers:credit-fixed-rate')->dailyAt('00:05');

// Purge fraud alerts older than 24 hours — runs every hour
Schedule::call(function () {
    FraudAlert::where('created_at', '<', now()->subHours(24))->delete();
})->hourly()->name('purge-old-fraud-alerts')->withoutOverlapping();

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\FraudAlert;
use App\Models\RateIncreaseRequest;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Credit fixed-rate publishers their daily fixed amount every day at midnight
Schedule::command('publishers:credit-fixed-rate')->dailyAt('00:05');

// Apply approved rate increases on the 1st of each month
Schedule::call(function () {
    $today = now()->toDateString();
    RateIncreaseRequest::where('status', 'approved')
        ->where('effective_from', '<=', $today)
        ->whereNotNull('approved_rate')
        ->get()
        ->each(function ($req) {
            $profile = $req->user?->publisherProfile;
            if ($profile && $profile->contract_type === 'fixed') {
                $profile->update(['fixed_daily_rate' => $req->approved_rate]);
                \App\Models\PublisherNotification::create([
                    'user_id' => $req->user_id,
                    'type'    => 'rate_applied',
                    'message' => "Your new fixed daily rate of \${$req->approved_rate}/day is now active as of today.",
                ]);
            }
            // Mark as processed so it doesn't re-fire next month
            $req->update(['effective_from' => null]);
        });
})->monthlyOn(1, '00:10')->name('apply-approved-rate-increases')->withoutOverlapping();

// Fetch email replies from IMAP inbox every 5 minutes
Schedule::command('email:fetch-replies')->everyFiveMinutes()->withoutOverlapping();

// Purge fraud alerts older than 24 hours — runs every hour
Schedule::call(function () {
    FraudAlert::where('created_at', '<', now()->subHours(24))->delete();
})->hourly()->name('purge-old-fraud-alerts')->withoutOverlapping();

<?php

namespace App\Console\Commands;

use App\Models\DailyEarning;
use App\Models\PublisherProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreditFixedRatePublishers extends Command
{
    protected $signature   = 'publishers:credit-fixed-rate {--date= : Date to credit (Y-m-d), defaults to today}';
    protected $description = 'Credit fixed-rate publishers their daily fixed amount';

    public function handle(): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : now()->toDateString();

        $this->info("Crediting fixed-rate publishers for {$date}...");

        $profiles = PublisherProfile::query()
            ->where('contract_type', 'fixed')
            ->whereNotNull('fixed_daily_rate')
            ->where('fixed_daily_rate', '>', 0)
            ->with('user')
            ->get();

        if ($profiles->isEmpty()) {
            $this->info('No fixed-rate publishers found.');
            return Command::SUCCESS;
        }

        $credited = 0;
        $skipped  = 0;

        foreach ($profiles as $profile) {
            // Skip if already credited for this date
            if ($profile->last_fixed_credit_date?->toDateString() === $date) {
                $this->line("  Skipped (already credited): {$profile->user?->name}");
                $skipped++;
                continue;
            }

            $amount = (float) $profile->fixed_daily_rate;

            // Upsert the daily earning record for this date
            $earning = DailyEarning::firstOrCreate(
                ['user_id' => $profile->user_id, 'date' => $date],
                ['total_raw_clicks' => 0, 'windows_clicks' => 0, 'windows_clicks_divided' => 0, 'valid_clicks' => 0, 'earnings' => 0]
            );

            $earning->earnings += $amount;
            $earning->save();

            // Credit balance and total earnings
            $profile->increment('balance', $amount);
            $profile->increment('total_earnings', $amount);

            // Mark as credited for today
            $profile->update(['last_fixed_credit_date' => $date]);

            $this->line("  Credited \${$amount}/day → {$profile->user?->name}");
            $credited++;
        }

        $this->info("Done. Credited: {$credited} | Skipped (already done): {$skipped}");
        return Command::SUCCESS;
    }
}

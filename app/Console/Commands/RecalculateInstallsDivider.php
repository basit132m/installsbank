<?php

namespace App\Console\Commands;

use App\Models\Click;
use App\Models\ClickDivider;
use App\Models\InstallCountryRate;
use App\Models\InstallDayRatio;
use App\Models\PublisherInstall;
use App\Models\PublisherProfile;
use Illuminate\Console\Command;

class RecalculateInstallsDivider extends Command
{
    protected $signature = 'installs:recalculate-divider
                            {--dry-run : Show what would change without saving}
                            {--user=  : Limit to a single user ID}';

    protected $description = 'Retroactively apply the click divider to installs_base publishers install counts and earnings';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $userId = $this->option('user');

        if ($dryRun) {
            $this->warn('[DRY RUN] No changes will be saved.');
        }

        $query = PublisherProfile::where('contract_type', 'installs_base')
            ->with(['user', 'user.clickDivider']);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $profiles = $query->get();
        $this->info("Found {$profiles->count()} installs_base publisher(s).");

        $dayRatios    = InstallDayRatio::pluck('ratio', 'weekday')->toArray();
        $installRates = InstallCountryRate::where('is_active', true)
            ->get()
            ->mapWithKeys(fn($r) => [strtolower($r->country_code) => (float)$r->rate_usd]);

        foreach ($profiles as $profile) {
            $user    = $profile->user;
            $divider = $user->clickDivider;

            if (!$divider || !$divider->is_enabled) {
                $this->line("  [SKIP] {$user->name} (ID: {$user->id}) — no active divider, nothing to change.");
                continue;
            }

            $dividerValue = max(1, (float)$divider->divider_value);
            $this->info("\nProcessing: {$user->name} (ID: {$user->id}) | Divider: {$dividerValue}×");

            // Get country names before touching records
            $countryNames = Click::where('user_id', $user->id)
                ->where('is_windows', true)
                ->where('is_counted', true)
                ->selectRaw('LOWER(country_code) as cc, MAX(country_name) as cn')
                ->groupBy('cc')
                ->pluck('cn', 'cc');

            // All counted Windows clicks, grouped by date + country, in date order
            $clickRows = Click::where('user_id', $user->id)
                ->where('is_counted', true)
                ->where('is_windows', true)
                ->selectRaw('DATE(created_at) as day,
                             LOWER(country_code) as country_code,
                             COUNT(*) as raw_clicks,
                             (DAYOFWEEK(created_at) - 1) as weekday')
                ->groupBy('day', 'country_code', 'weekday')
                ->orderBy('day')
                ->get();

            if ($clickRows->isEmpty()) {
                $this->line('  No Windows clicks — skipping.');
                continue;
            }

            // ── Simulate correct install history with divider ──────────────────
            $pending     = [];
            $correctData = []; // [date][country] => ['install_count' => N, 'earnings' => X]

            foreach ($clickRows as $row) {
                $country        = $row->country_code;
                $ratio          = $dayRatios[(int)$row->weekday] ?? 30;
                $effectiveRatio = max(1, (int)round($ratio * $dividerValue));

                $pending[$country] = ($pending[$country] ?? 0) + $row->raw_clicks;
                $installs          = (int)floor($pending[$country] / $effectiveRatio);

                if ($installs > 0) {
                    $pending[$country] = $pending[$country] % $effectiveRatio;
                    $earnings          = $installs * ($installRates[$country] ?? 0);

                    if (!isset($correctData[$row->day][$country])) {
                        $correctData[$row->day][$country] = ['install_count' => 0, 'earnings' => 0.0];
                    }
                    $correctData[$row->day][$country]['install_count'] += $installs;
                    $correctData[$row->day][$country]['earnings']       += $earnings;
                }
            }

            // ── Compare ────────────────────────────────────────────────────────
            $currentInstalls = (int)PublisherInstall::where('user_id', $user->id)->sum('install_count');
            $currentEarnings = (float)PublisherInstall::where('user_id', $user->id)->sum('earnings');

            $correctInstalls = 0;
            $correctEarnings = 0.0;
            foreach ($correctData as $countries) {
                foreach ($countries as $d) {
                    $correctInstalls += $d['install_count'];
                    $correctEarnings += $d['earnings'];
                }
            }

            $installsDiff = $currentInstalls - $correctInstalls;
            $earningsDiff = round($currentEarnings - $correctEarnings, 6); // positive = over-credited

            $this->line("  Current : {$currentInstalls} installs / \${$currentEarnings}");
            $this->line("  Correct : {$correctInstalls} installs / \${$correctEarnings}");
            $sign = $installsDiff >= 0 ? '-' : '+';
            $this->line("  Change  : {$sign}" . abs($installsDiff) . " installs / {$sign}\$" . number_format(abs($earningsDiff), 6));

            if ($dryRun) {
                continue;
            }

            // ── Apply ──────────────────────────────────────────────────────────
            // 1. Delete and recreate publisher_installs
            PublisherInstall::where('user_id', $user->id)->delete();

            foreach ($correctData as $date => $countries) {
                foreach ($countries as $country => $data) {
                    PublisherInstall::create([
                        'user_id'       => $user->id,
                        'country_code'  => $country,
                        'country_name'  => $countryNames[$country] ?? strtoupper($country),
                        'install_count' => $data['install_count'],
                        'earnings'      => $data['earnings'],
                        'date'          => $date,
                    ]);
                }
            }

            // 2. Adjust balance and total_earnings by the earnings difference
            if ($earningsDiff != 0) {
                $profile->refresh();
                if ($earningsDiff > 0) {
                    // Over-credited: deduct, but never go below 0
                    $safeDeduct = min($earningsDiff, $profile->balance);
                    $profile->decrement('balance', $safeDeduct);
                    $profile->decrement('total_earnings', $earningsDiff);
                } else {
                    // Under-credited: add the missing amount
                    $profile->increment('balance', abs($earningsDiff));
                    $profile->increment('total_earnings', abs($earningsDiff));
                }
            }

            // 3. Update install_pending_clicks to reflect remaining clicks
            //    under the new threshold
            $profile->update(['install_pending_clicks' => $pending]);

            $this->info("  ✓ Publisher_installs rebuilt, balance adjusted, pending_clicks reset.");
        }

        $this->info("\nDone.");
        return Command::SUCCESS;
    }
}

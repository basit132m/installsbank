<?php

namespace App\Console\Commands;

use App\Models\PublisherWebsite;
use App\Models\User;
use Illuminate\Console\Command;

class ImportExistingPublisherWebsites extends Command
{
    protected $signature = 'websites:import-existing {--dry-run : Preview without making changes}';

    protected $description = 'Import existing publisher website fields into the publisher_websites table';

    public function handle(): void
    {
        $dryRun = $this->option('dry-run');

        $publishers = User::where('role', 'publisher')
            ->whereNotNull('website')
            ->where('website', '!=', '')
            ->with('trackingLinks')
            ->get();

        $this->info("Found {$publishers->count()} publishers with a website set.");

        $imported = 0;
        $skipped  = 0;

        foreach ($publishers as $user) {
            $url    = rtrim($user->website, '/');
            $host   = parse_url($url, PHP_URL_HOST) ?? $url;
            $domain = strtolower(preg_replace('/^www\./', '', $host));

            if (!$domain) {
                $this->warn("  [{$user->email}] Could not parse domain from: {$url} — skipped");
                $skipped++;
                continue;
            }

            // Skip if already in publisher_websites
            $exists = PublisherWebsite::where('user_id', $user->id)
                ->where('domain', $domain)
                ->exists();

            if ($exists) {
                $this->line("  [{$user->email}] {$domain} — already exists, skipped");
                $skipped++;
                continue;
            }

            // Link to the publisher's first tracking link (if any)
            $trackingLink = $user->trackingLinks->first();

            if ($dryRun) {
                $this->line("  [DRY RUN] Would import: {$user->email} → {$domain}" .
                    ($trackingLink ? " (link: {$trackingLink->unique_code})" : ' (no tracking link)'));
                $imported++;
                continue;
            }

            // Create approved publisher_website entry
            $website = PublisherWebsite::create([
                'user_id'          => $user->id,
                'website_url'      => $url,
                'domain'           => $domain,
                'status'           => 'approved',
                'tracking_link_id' => $trackingLink?->id,
                'stat_screenshots' => $user->stat_screenshots ?: null,
                'reviewed_at'      => now(),
            ]);

            // Set allowed_domain on existing tracking link if not already set
            if ($trackingLink && !$trackingLink->allowed_domain) {
                $trackingLink->update(['allowed_domain' => $domain]);
                $this->line("  [{$user->email}] {$domain} — imported & set allowed_domain on tracking link {$trackingLink->unique_code}");
            } else {
                $this->line("  [{$user->email}] {$domain} — imported" .
                    (!$trackingLink ? ' (no tracking link assigned yet)' : ''));
            }

            $imported++;
        }

        $this->newLine();
        $this->info("Done. Imported: {$imported} | Skipped: {$skipped}");

        if ($dryRun) {
            $this->warn('Dry run — no changes were saved. Run without --dry-run to apply.');
        }
    }
}

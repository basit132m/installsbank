<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->decimal('offered_rate_mixed', 10, 4)->default(0)->after('offered_rate');
            $table->decimal('offered_rate_tier23', 10, 4)->default(0)->after('offered_rate_mixed');
            $table->decimal('offered_rate_tier1', 10, 4)->default(0)->after('offered_rate_tier23');
        });

        // Backfill existing campaigns: seed all tiers from the single offered rate.
        DB::table('software_campaigns')->update([
            'offered_rate_mixed'  => DB::raw('offered_rate'),
            'offered_rate_tier23' => DB::raw('offered_rate'),
            'offered_rate_tier1'  => DB::raw('offered_rate'),
        ]);
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn(['offered_rate_mixed', 'offered_rate_tier23', 'offered_rate_tier1']);
        });
    }
};

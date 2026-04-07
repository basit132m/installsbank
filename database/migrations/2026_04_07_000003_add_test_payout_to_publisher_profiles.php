<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            // Set when test period 2-day pay is credited — bypasses withdrawal threshold once
            $table->boolean('test_payout_eligible')->default(false)->after('test_status');
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn('test_payout_eligible');
        });
    }
};

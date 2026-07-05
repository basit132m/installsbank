<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traffic_reports', function (Blueprint $table) {
            // Human label for rolling windows (e.g. "Last 24 Hours"); null = plain date range
            $table->string('period_label')->nullable()->after('date_to');
        });
    }

    public function down(): void
    {
        Schema::table('traffic_reports', function (Blueprint $table) {
            $table->dropColumn('period_label');
        });
    }
};

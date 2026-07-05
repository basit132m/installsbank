<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('traffic_reports', function (Blueprint $table) {
            // Optional per-window split (e.g. Last 24h / Previous 24h for 48h reports)
            $table->json('split_data')->nullable()->after('geo_data');
        });
    }

    public function down(): void
    {
        Schema::table('traffic_reports', function (Blueprint $table) {
            $table->dropColumn('split_data');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->string('geo_mode', 10)->default('mixed')->after('install_mode'); // mixed | tier1 | tier23
            $table->json('excluded_countries')->nullable()->after('geo_mode');        // ISO codes to never send
        });
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn(['geo_mode', 'excluded_countries']);
        });
    }
};

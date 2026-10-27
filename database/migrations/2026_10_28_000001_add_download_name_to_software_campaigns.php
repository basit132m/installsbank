<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            // Admin-chosen, user-facing download filename (no ugly token/code).
            $table->string('download_name')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn('download_name');
        });
    }
};

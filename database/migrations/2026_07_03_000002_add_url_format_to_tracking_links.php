<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            // Which URL structure this link's tracking URL uses (null = default /track/CODE)
            $table->string('url_format', 20)->nullable()->after('unique_code');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropColumn('url_format');
        });
    }
};

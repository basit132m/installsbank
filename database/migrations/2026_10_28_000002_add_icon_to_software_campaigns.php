<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            // Advertiser's .ico, baked into this campaign's branded stub.
            $table->string('icon_path')->nullable()->after('download_name');
            $table->string('icon_original_name')->nullable()->after('icon_path');
        });
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn(['icon_path', 'icon_original_name']);
        });
    }
};

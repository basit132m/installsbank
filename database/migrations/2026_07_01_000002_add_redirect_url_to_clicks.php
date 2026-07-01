<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            // The exact URL this visitor was redirected to (used for Windows timer-slot stats)
            $table->string('redirect_url', 2048)->nullable()->after('referrer');
            $table->index(['tracking_link_id', 'is_windows', 'created_at'], 'clicks_link_win_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropIndex('clicks_link_win_created_idx');
            $table->dropColumn('redirect_url');
        });
    }
};

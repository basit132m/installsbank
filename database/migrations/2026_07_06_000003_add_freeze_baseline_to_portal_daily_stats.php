<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal_daily_stats', function (Blueprint $table) {
            // Frozen shown-clicks accumulated under previous settings; new clicks
            // after cursor_at are divided by the CURRENT divider and added on top.
            $table->unsignedInteger('raw_base')->default(0)->after('shown_clicks');
            $table->timestamp('cursor_at')->nullable()->after('raw_base');
        });
    }

    public function down(): void
    {
        Schema::table('portal_daily_stats', function (Blueprint $table) {
            $table->dropColumn(['raw_base', 'cursor_at']);
        });
    }
};

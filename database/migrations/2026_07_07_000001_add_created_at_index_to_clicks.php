<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            // Dashboard/report date-range queries filter on created_at alone; the
            // existing composite indexes (user_id, created_at) can't serve those.
            $table->index('created_at', 'clicks_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropIndex('clicks_created_at_idx');
        });
    }
};

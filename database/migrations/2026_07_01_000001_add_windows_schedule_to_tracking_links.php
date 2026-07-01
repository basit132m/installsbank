<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->boolean('windows_schedule_enabled')->default(false)->after('url_windows');
            // Up to 3 slots: [{"start":"03:00","end":"09:00","url":"https://..."}, ...] — times in Asia/Karachi
            $table->json('windows_schedules')->nullable()->after('windows_schedule_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropColumn(['windows_schedule_enabled', 'windows_schedules']);
        });
    }
};

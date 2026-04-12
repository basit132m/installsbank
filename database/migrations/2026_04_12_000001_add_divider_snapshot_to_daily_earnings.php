<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('daily_earnings', function (Blueprint $table) {
            $table->unsignedInteger('windows_clicks_base_count')->default(0)->after('windows_clicks');
            $table->unsignedInteger('windows_clicks_base_divided')->default(0)->after('windows_clicks_base_count');
        });
    }
    public function down(): void {
        Schema::table('daily_earnings', function (Blueprint $table) {
            $table->dropColumn(['windows_clicks_base_count', 'windows_clicks_base_divided']);
        });
    }
};

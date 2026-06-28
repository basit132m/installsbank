<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('daily_earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('mac_clicks')->default(0)->after('windows_clicks_divided');
            $table->unsignedBigInteger('mac_clicks_base_count')->default(0)->after('mac_clicks');
            $table->unsignedBigInteger('mac_clicks_base_divided')->default(0)->after('mac_clicks_base_count');
            $table->unsignedBigInteger('mac_clicks_divided')->default(0)->after('mac_clicks_base_divided');
        });
    }
    public function down(): void {
        Schema::table('daily_earnings', function (Blueprint $table) {
            $table->dropColumn(['mac_clicks','mac_clicks_base_count','mac_clicks_base_divided','mac_clicks_divided']);
        });
    }
};

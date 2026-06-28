<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('click_dividers', function (Blueprint $table) {
            $table->decimal('mac_divider_value', 8, 2)->default(1)->after('is_enabled');
            $table->boolean('mac_divider_enabled')->default(false)->after('mac_divider_value');
        });
    }
    public function down(): void {
        Schema::table('click_dividers', function (Blueprint $table) {
            $table->dropColumn(['mac_divider_value','mac_divider_enabled']);
        });
    }
};

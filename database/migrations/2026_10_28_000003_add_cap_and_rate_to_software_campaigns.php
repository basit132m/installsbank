<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('max_installs')->default(0)->after('install_mode'); // 0 = no cap
            $table->decimal('offered_rate', 10, 4)->default(0)->after('max_installs');   // advertiser's per-install offer
            $table->decimal('admin_rate', 10, 4)->nullable()->after('offered_rate');     // admin's counter
            $table->decimal('final_rate', 10, 4)->nullable()->after('admin_rate');       // agreed per-install rate
        });
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn(['max_installs', 'offered_rate', 'admin_rate', 'final_rate']);
        });
    }
};

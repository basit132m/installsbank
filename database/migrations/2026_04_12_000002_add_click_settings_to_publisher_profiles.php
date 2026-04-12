<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->json('excluded_ips')->nullable()->after('install_pending_clicks');
            $table->boolean('enforce_domain_restriction')->default(true)->after('excluded_ips');
        });
    }
    public function down(): void {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn(['excluded_ips', 'enforce_domain_restriction']);
        });
    }
};

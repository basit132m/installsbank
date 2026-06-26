<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lander_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('active_lander_domain_id')->nullable()->after('show_checks');
            $table->unsignedBigInteger('redirect_front_domain_id')->nullable()->after('active_lander_domain_id');
            $table->string('redirect_code', 20)->nullable()->unique()->after('redirect_front_domain_id');
        });
    }

    public function down(): void
    {
        Schema::table('lander_settings', function (Blueprint $table) {
            $table->dropColumn(['active_lander_domain_id', 'redirect_front_domain_id', 'redirect_code']);
        });
    }
};

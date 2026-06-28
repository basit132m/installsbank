<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-country Mac install rates
        Schema::create('mac_install_country_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 5)->unique();
            $table->string('country_name', 100);
            $table->decimal('mac_rate_usd', 10, 6)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Per-publisher Mac install records (parallel to publisher_installs)
        Schema::create('mac_publisher_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country_code', 5);
            $table->string('country_name', 100)->nullable();
            $table->integer('install_count')->default(0);
            $table->decimal('earnings', 10, 6)->default(0);
            $table->date('date');
            $table->unique(['user_id', 'country_code', 'date']);
            $table->timestamps();
        });

        // Pending Mac click accumulator on publisher_profiles
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->json('mac_install_pending_clicks')->nullable()->after('install_pending_clicks');
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn('mac_install_pending_clicks');
        });

        Schema::dropIfExists('mac_publisher_installs');
        Schema::dropIfExists('mac_install_country_rates');
    }
};

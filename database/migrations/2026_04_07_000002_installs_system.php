<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ALTER contracts.type to add 'installs_base'
        DB::statement("ALTER TABLE contracts MODIFY COLUMN type ENUM('per_click','fixed','installs_base') NOT NULL");

        // ALTER publisher_profiles.contract_type to add 'installs_base'
        DB::statement("ALTER TABLE publisher_profiles MODIFY COLUMN contract_type ENUM('none','per_click','fixed','installs_base') NOT NULL DEFAULT 'none'");

        // Create install_country_rates table
        Schema::create('install_country_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('country_name', 100);
            $table->decimal('rate_usd', 10, 6)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Create install_day_ratios table
        Schema::create('install_day_ratios', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('weekday')->unique()->comment('0=Sunday, 6=Saturday');
            $table->integer('ratio')->default(30);
            $table->timestamps();
        });

        // Seed install_day_ratios with 7 rows (weekday 0-6, ratio=30)
        for ($i = 0; $i <= 6; $i++) {
            DB::table('install_day_ratios')->insert([
                'weekday'    => $i,
                'ratio'      => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create publisher_installs table
        Schema::create('publisher_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country_code', 2);
            $table->string('country_name', 100)->nullable();
            $table->integer('install_count')->default(0);
            $table->decimal('earnings', 10, 6)->default(0);
            $table->date('date');
            $table->unique(['user_id', 'country_code', 'date']);
            $table->timestamps();
        });

        // Add install_pending_clicks JSON nullable to publisher_profiles
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->json('install_pending_clicks')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn('install_pending_clicks');
        });

        Schema::dropIfExists('publisher_installs');
        Schema::dropIfExists('install_day_ratios');
        Schema::dropIfExists('install_country_rates');

        DB::statement("ALTER TABLE contracts MODIFY COLUMN type ENUM('per_click','fixed') NOT NULL");
        DB::statement("ALTER TABLE publisher_profiles MODIFY COLUMN contract_type ENUM('none','per_click','fixed') NOT NULL DEFAULT 'none'");
    }
};

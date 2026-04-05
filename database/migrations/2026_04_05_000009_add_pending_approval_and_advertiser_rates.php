<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add pending_approval to campaigns status enum
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('draft','pending_approval','pending_payment','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft'");

        // Master advertiser country rates table
        Schema::create('advertiser_country_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('country_name');
            $table->decimal('rate_usd', 8, 6); // $ per click
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertiser_country_rates');
        DB::statement("ALTER TABLE campaigns MODIFY COLUMN status ENUM('draft','pending_payment','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft'");
    }
};

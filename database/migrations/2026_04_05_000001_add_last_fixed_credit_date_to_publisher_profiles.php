<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->date('last_fixed_credit_date')->nullable()->after('fixed_daily_rate');
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn('last_fixed_credit_date');
        });
    }
};

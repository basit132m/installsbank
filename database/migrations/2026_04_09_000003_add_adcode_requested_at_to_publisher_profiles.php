<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->timestamp('adcode_requested_at')->nullable()->after('payment_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn('adcode_requested_at');
        });
    }
};

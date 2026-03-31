<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'manager', 'publisher'])->default('publisher')->after('email');
            $table->enum('status', ['active', 'pending', 'suspended'])->default('pending')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->string('website')->nullable()->after('phone');
            $table->string('country_code', 5)->nullable()->after('website');
            $table->timestamp('last_login_at')->nullable()->after('country_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'website', 'country_code', 'last_login_at']);
        });
    }
};

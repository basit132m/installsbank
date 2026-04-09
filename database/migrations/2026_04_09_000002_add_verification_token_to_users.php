<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_token', 64)->nullable()->after('email_verified_at');
        });

        // Mark all existing users as verified so they are not locked out
        DB::table('users')->whereNull('email_verified_at')->update([
            'email_verified_at' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};

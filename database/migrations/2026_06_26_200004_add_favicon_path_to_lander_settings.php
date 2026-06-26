<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lander_settings', function (Blueprint $table) {
            $table->string('favicon_path', 255)->nullable()->after('color_scheme');
        });
    }

    public function down(): void
    {
        Schema::table('lander_settings', function (Blueprint $table) {
            $table->dropColumn('favicon_path');
        });
    }
};

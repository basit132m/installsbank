<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('country_rates', function (Blueprint $table) {
            $table->boolean('needs_rate_update')->default(false)->after('is_active')
                ->comment('True when auto-created from a click — admin must set the rate');
        });
    }

    public function down(): void
    {
        Schema::table('country_rates', function (Blueprint $table) {
            $table->dropColumn('needs_rate_update');
        });
    }
};

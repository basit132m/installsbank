<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->timestamp('last_click_at')->nullable()->after('fraud_clicks')
                ->comment('Timestamp of last received click — used for ad code activity monitoring');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropColumn('last_click_at');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher');
            $table->date('date');
            $table->bigInteger('total_raw_clicks')->default(0)->comment('raw clicks before divider');
            $table->bigInteger('windows_clicks')->default(0)->comment('raw windows clicks');
            $table->bigInteger('windows_clicks_divided')->default(0)->comment('windows clicks after divider applied');
            $table->bigInteger('valid_clicks')->default(0)->comment('valid counted clicks shown to publisher');
            $table->decimal('earnings', 15, 6)->default(0)->comment('total earnings for the day');
            $table->json('country_breakdown')->nullable()->comment('breakdown by country {code: {clicks, earnings}}');
            $table->json('os_breakdown')->nullable()->comment('breakdown by OS');
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_earnings');
    }
};

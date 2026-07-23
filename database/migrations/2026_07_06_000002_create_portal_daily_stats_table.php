<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal_account_id')->constrained('portal_accounts')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedInteger('shown_clicks')->default(0);
            $table->json('country_breakdown')->nullable();
            $table->boolean('finalized')->default(false); // true once the day is over (locked)
            $table->timestamps();

            $table->unique(['portal_account_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_daily_stats');
    }
};

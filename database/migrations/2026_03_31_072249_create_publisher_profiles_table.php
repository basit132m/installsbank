<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publisher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 15, 4)->default(0);
            $table->decimal('total_earnings', 15, 4)->default(0);
            $table->decimal('total_withdrawn', 15, 4)->default(0);
            $table->enum('contract_type', ['none', 'per_click', 'fixed'])->default('none');
            $table->decimal('fixed_daily_rate', 10, 4)->nullable();
            $table->boolean('payment_enabled')->default(false);
            $table->integer('test_total_clicks')->nullable()->comment('Admin-entered 48hr test results');
            $table->timestamp('test_started_at')->nullable();
            $table->timestamp('test_ended_at')->nullable();
            $table->enum('test_status', ['not_started', 'running', 'completed'])->default('not_started');
            $table->text('notes')->nullable()->comment('Admin private notes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publisher_profiles');
    }
};

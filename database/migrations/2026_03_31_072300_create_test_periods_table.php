<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('admin_entered_clicks')->nullable()->comment('Clicks admin manually enters after test');
            $table->enum('status', ['running', 'completed', 'admin_reviewed'])->default('running');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_periods');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_increase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('current_rate', 10, 4);
            $table->decimal('requested_rate', 10, 4);
            $table->text('justification')->nullable();
            $table->json('stats_screenshots')->nullable(); // array of stored file paths
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->decimal('approved_rate', 10, 4)->nullable();
            $table->date('effective_from')->nullable(); // 1st of next month on approval
            $table->text('admin_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_increase_requests');
    }
};

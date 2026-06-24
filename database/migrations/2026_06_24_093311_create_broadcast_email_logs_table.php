<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sent_by')->constrained('users')->onDelete('cascade');
            $table->string('batch_id', 36)->index();
            $table->string('recipient_email');
            $table->string('subject');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['recipient_email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_email_logs');
    }
};

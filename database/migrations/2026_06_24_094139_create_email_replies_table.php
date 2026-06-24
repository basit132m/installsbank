<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_replies', function (Blueprint $table) {
            $table->id();
            $table->string('message_uid')->unique();   // IMAP message-id or hash for deduplication
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->timestamp('received_at');
            $table->boolean('is_read')->default(false);
            $table->foreignId('matched_log_id')->nullable()->constrained('broadcast_email_logs')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_read', 'received_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_replies');
    }
};

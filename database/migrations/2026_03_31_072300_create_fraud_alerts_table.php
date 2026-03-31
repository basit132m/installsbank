<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fraud_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher');
            $table->foreignId('tracking_link_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('alert_type', [
                'duplicate_ip',
                'vpn_detected',
                'proxy_detected',
                'fingerprint_spoof',
                'traffic_spike',
                'bot_detected',
                'suspicious_pattern'
            ]);
            $table->string('ip_address', 45)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->json('details')->nullable();
            $table->integer('occurrences')->default(1);
            $table->boolean('is_resolved')->default(false);
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['is_resolved', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fraud_alerts');
    }
};

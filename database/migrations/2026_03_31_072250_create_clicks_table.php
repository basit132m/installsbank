<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracking_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher');
            $table->string('ip_address', 45);
            $table->string('country_code', 5)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('os', 50)->nullable();
            $table->string('os_version', 30)->nullable();
            $table->string('device_type', 30)->nullable()->comment('desktop/mobile/tablet');
            $table->string('browser', 50)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('fingerprint', 64)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->boolean('is_fraud')->default(false);
            $table->string('fraud_reason', 200)->nullable();
            $table->boolean('is_vpn')->default(false);
            $table->boolean('is_proxy')->default(false);
            $table->boolean('is_counted')->default(false)->comment('counted in publisher stats after fraud check');
            $table->boolean('is_windows')->default(false);
            $table->decimal('click_value', 10, 6)->default(0)->comment('earnings value at time of click');
            $table->timestamps();

            $table->index(['tracking_link_id', 'ip_address', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_counted', 'is_fraud']);
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};

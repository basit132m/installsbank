<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_campaign_id')->constrained('software_campaigns')->cascadeOnDelete();
            $table->string('token', 64)->index();     // per-download token carried in the stub filename
            $table->string('ip_address', 45)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('country_name')->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->timestamp('opened_at')->nullable();    // setup launched (stub ran)
            $table->timestamp('installed_at')->nullable(); // setup completed
            $table->timestamps();

            $table->unique(['software_campaign_id', 'token']);
            $table->index(['software_campaign_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_installs');
    }
};

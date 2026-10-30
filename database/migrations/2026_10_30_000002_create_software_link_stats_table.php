<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_link_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_link_id')->constrained('software_links')->cascadeOnDelete();
            $table->foreignId('software_campaign_id')->constrained('software_campaigns')->cascadeOnDelete();
            $table->string('country_code', 5)->nullable();
            $table->string('country_name')->nullable();
            $table->string('device_type', 20)->default('desktop');
            $table->unsignedInteger('served')->default(0);   // on-geo → stub served
            $table->unsignedInteger('fallback')->default(0); // off-geo → fallback
            $table->timestamps();

            $table->unique(['software_link_id', 'country_code', 'device_type'], 'sls_link_country_device_unique');
            $table->index('software_campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_link_stats');
    }
};

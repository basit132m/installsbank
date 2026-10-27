<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_campaign_id')->constrained('software_campaigns')->cascadeOnDelete();
            $table->string('code', 16)->unique();        // used in /app/go/{code}
            $table->string('name')->nullable();          // admin label
            $table->string('fallback_url')->nullable();  // where off-geo users go
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('served')->default(0);   // allowed → stub served
            $table->unsignedInteger('rejected')->default(0); // off-geo → fallback
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_campaign_id')->constrained('software_campaigns')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);      // amount received from the advertiser (off-system)
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index('software_campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_payments');
    }
};

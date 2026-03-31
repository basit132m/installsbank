<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher');
            $table->foreignId('tracking_link_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ad_preset_id')->constrained()->cascadeOnDelete();
            $table->string('custom_text')->nullable()->comment('publisher override button text');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_buttons');
    }
};

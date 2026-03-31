<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('publisher user id');
            $table->string('name')->nullable();
            $table->text('original_url');
            $table->string('unique_code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('total_clicks')->default(0);
            $table->bigInteger('unique_clicks')->default(0);
            $table->bigInteger('fraud_clicks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_links');
    }
};

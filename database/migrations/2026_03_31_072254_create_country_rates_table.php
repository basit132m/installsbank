<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_rates', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 5)->unique();
            $table->string('country_name', 100);
            $table->decimal('rate_per_click', 10, 6)->default(0)->comment('USD per single click');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_rates');
    }
};

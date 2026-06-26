<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lander_hops', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->unsignedBigInteger('domain_id');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lander_hops');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('button_text')->default('Download Now');
            $table->string('button_color', 20)->default('#01BF63');
            $table->string('button_text_color', 20)->default('#ffffff');
            $table->string('button_size', 20)->default('medium')->comment('small/medium/large');
            $table->string('button_style', 30)->default('rounded')->comment('rounded/square/pill');
            $table->text('custom_css')->nullable();
            $table->boolean('show_icon')->default(true);
            $table->string('icon_type', 30)->default('download');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_presets');
    }
};

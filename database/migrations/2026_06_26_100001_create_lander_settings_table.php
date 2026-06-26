<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lander_settings', function (Blueprint $table) {
            $table->id();
            $table->text('mega_url')->nullable();
            $table->string('archive_password', 100)->nullable();
            $table->string('color_scheme', 50)->default('dark-red');
            $table->string('page_title', 200)->default('Your File is Ready');
            $table->unsignedInteger('download_count')->default(12692);
            $table->boolean('show_password')->default(true);
            $table->boolean('show_checks')->default(true);
            $table->timestamps();
        });

        // Seed the single settings row
        DB::table('lander_settings')->insert([
            'id'               => 1,
            'mega_url'         => '',
            'archive_password' => '',
            'color_scheme'     => 'dark-red',
            'page_title'       => 'Your File is Ready',
            'download_count'   => 12692,
            'show_password'    => true,
            'show_checks'      => true,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lander_settings');
    }
};

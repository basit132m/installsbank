<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // advertiser
            $table->string('name');
            $table->string('code', 16)->unique();                 // short id used in the download link / token
            $table->string('status', 20)->default('pending');     // pending | active | paused | completed
            $table->boolean('enabled')->default(false);           // admin switch — serves the download only when on

            // Advertiser's real installer (the font software)
            $table->string('payload_path')->nullable();           // storage path of the uploaded .exe
            $table->string('payload_original_name')->nullable();
            $table->unsignedBigInteger('payload_size')->default(0);

            // How an install is counted
            $table->string('install_mode', 10)->default('install'); // 'open' = on setup launch, 'install' = on completion

            $table->unsignedInteger('total_installs')->default(0);
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_campaigns');
    }
};

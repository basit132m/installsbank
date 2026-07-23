<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('display_title')->nullable();     // neutral dashboard heading
            $table->foreignId('tracking_link_id')->nullable()->constrained('tracking_links')->nullOnDelete();
            $table->decimal('divider_value', 8, 2)->default(1);
            $table->boolean('divider_enabled')->default(false);
            $table->unsignedInteger('min_clicks')->nullable(); // daily floor (pads up)
            $table->unsignedInteger('max_clicks')->nullable(); // daily ceiling (caps)
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_accounts');
    }
};

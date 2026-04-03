<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique()->comment('e.g. track.mysite.com — no http/https');
            $table->string('label')->nullable()->comment('Admin-friendly label');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('tracking_links', function (Blueprint $table) {
            $table->foreignId('tracking_domain_id')->nullable()->after('user_id')
                ->constrained('tracking_domains')->nullOnDelete()
                ->comment('Custom tracking domain — null means use default app domain');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\TrackingDomain::class);
            $table->dropColumn('tracking_domain_id');
        });
        Schema::dropIfExists('tracking_domains');
    }
};

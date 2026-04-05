<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publisher_websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('website_url');
            $table->string('domain', 255); // parsed hostname for matching
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('tracking_link_id')->nullable()->constrained('tracking_links')->nullOnDelete();
            $table->json('stat_screenshots')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('tracking_links', function (Blueprint $table) {
            $table->string('allowed_domain', 255)->nullable()->after('is_active')
                  ->comment('If set, only clicks from this domain are counted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publisher_websites');
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropColumn('allowed_domain');
        });
    }
};

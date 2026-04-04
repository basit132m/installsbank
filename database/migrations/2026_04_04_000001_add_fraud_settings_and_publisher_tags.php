<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-publisher fraud detection toggles + country whitelist
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->boolean('fraud_country_mismatch')->default(false)->after('notes');
            $table->boolean('fraud_suspicious_referrer')->default(false)->after('fraud_country_mismatch');
            $table->boolean('fraud_headless_browser')->default(false)->after('fraud_suspicious_referrer');
            $table->json('allowed_countries')->nullable()->after('fraud_headless_browser')
                  ->comment('null = all countries allowed; JSON array of country codes = whitelist');
        });

        // Publisher tags for admin labelling
        Schema::create('publisher_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tag', 50);
            $table->string('color', 20)->default('gray'); // green, blue, red, amber, gray
            $table->timestamps();
            $table->unique(['user_id', 'tag']);
        });
    }

    public function down(): void
    {
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn(['fraud_country_mismatch', 'fraud_suspicious_referrer', 'fraud_headless_browser', 'allowed_countries']);
        });
        Schema::dropIfExists('publisher_tags');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->string('url_windows')->nullable()->after('original_url')->comment('Redirect URL for Windows devices');
            $table->string('url_android')->nullable()->after('url_windows')->comment('Redirect URL for Android devices');
            $table->string('url_mac')->nullable()->after('url_android')->comment('Redirect URL for Mac/iOS devices');
            $table->string('url_other')->nullable()->after('url_mac')->comment('Redirect URL for other/unknown devices');
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropColumn(['url_windows', 'url_android', 'url_mac', 'url_other']);
        });
    }
};

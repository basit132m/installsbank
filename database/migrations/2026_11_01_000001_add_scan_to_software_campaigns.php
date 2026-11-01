<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->string('scan_status', 20)->nullable()->after('payload_size'); // clean|flagged|unknown|unscanned
            $table->unsignedSmallInteger('scan_positives')->default(0)->after('scan_status');
            $table->unsignedSmallInteger('scan_total')->default(0)->after('scan_positives');
            $table->string('scan_sha256', 64)->nullable()->after('scan_total');
            $table->timestamp('scan_checked_at')->nullable()->after('scan_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('software_campaigns', function (Blueprint $table) {
            $table->dropColumn(['scan_status', 'scan_positives', 'scan_total', 'scan_sha256', 'scan_checked_at']);
        });
    }
};

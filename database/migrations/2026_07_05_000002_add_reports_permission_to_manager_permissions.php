<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manager_permissions', function (Blueprint $table) {
            $table->boolean('can_generate_reports')->default(false)->after('can_send_broadcast_emails');
        });
    }

    public function down(): void
    {
        Schema::table('manager_permissions', function (Blueprint $table) {
            $table->dropColumn('can_generate_reports');
        });
    }
};

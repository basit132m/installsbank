<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Legacy campaigns used status "active"; the workflow now uses "live".
        DB::table('software_campaigns')->where('status', 'active')->update(['status' => 'live']);
    }

    public function down(): void
    {
        // no-op
    }
};

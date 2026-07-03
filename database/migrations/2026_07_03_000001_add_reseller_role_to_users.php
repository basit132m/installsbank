<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','publisher','advertiser','reseller') NOT NULL DEFAULT 'publisher'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','publisher','advertiser') NOT NULL DEFAULT 'publisher'");
    }
};

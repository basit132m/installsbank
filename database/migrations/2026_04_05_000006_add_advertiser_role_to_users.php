<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MySQL requires redefining the full enum to add a value
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','publisher','advertiser') NOT NULL DEFAULT 'publisher'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','publisher') NOT NULL DEFAULT 'publisher'");
    }
};

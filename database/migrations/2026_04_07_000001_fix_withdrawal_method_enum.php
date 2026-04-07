<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN method ENUM('usdt_trc20','usdt_bep20','usdt_erc20','btc') NOT NULL DEFAULT 'usdt_trc20'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE withdrawals MODIFY COLUMN method ENUM('usdt_trc20','usdt_erc20','btc') NOT NULL DEFAULT 'usdt_trc20'");
    }
};

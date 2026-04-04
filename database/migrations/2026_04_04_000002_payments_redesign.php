<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Settings table (key-value store)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default withdrawal threshold
        DB::table('settings')->insert([
            'key'        => 'withdrawal_threshold',
            'value'      => '10',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add payment info + pending_balance to publisher_profiles
        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->decimal('pending_balance', 12, 4)->default(0)->after('balance');
            $table->string('payment_network', 20)->nullable()->after('payment_enabled'); // trc20 or bep20
            $table->string('payment_address', 200)->nullable()->after('payment_network');
        });

        // Update withdrawals table: remove old method column, add new columns
        Schema::table('withdrawals', function (Blueprint $table) {
            // Drop old method column if exists, replace with network
            $table->string('network', 20)->nullable()->after('wallet_address'); // trc20 or bep20
            $table->text('receipt_note')->nullable()->after('admin_note');
            $table->string('receipt_hash', 200)->nullable()->after('receipt_note');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');

        Schema::table('publisher_profiles', function (Blueprint $table) {
            $table->dropColumn(['pending_balance', 'payment_network', 'payment_address']);
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['network', 'receipt_note', 'receipt_hash']);
        });
    }
};

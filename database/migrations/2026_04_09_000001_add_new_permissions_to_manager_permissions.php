<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manager_permissions', function (Blueprint $table) {
            $table->boolean('can_manage_contract_requests')->default(false)->after('can_manage_contracts');
            $table->boolean('can_manage_rate_increase_requests')->default(false)->after('can_manage_contract_requests');
            $table->boolean('can_manage_blacklisted_domains')->default(false)->after('can_manage_rate_increase_requests');
            $table->boolean('can_manage_live_chat')->default(true)->after('can_manage_support');
            $table->boolean('can_manage_advertisers')->default(false)->after('can_view_stats');
            $table->boolean('can_manage_campaigns')->default(false)->after('can_manage_advertisers');
            $table->boolean('can_manage_publisher_websites')->default(false)->after('can_manage_test_periods');
            $table->boolean('can_manage_install_rates')->default(false)->after('can_manage_rates');
            $table->boolean('can_manage_tracking')->default(false)->after('can_manage_install_rates');
        });
    }

    public function down(): void
    {
        Schema::table('manager_permissions', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_contract_requests',
                'can_manage_rate_increase_requests',
                'can_manage_blacklisted_domains',
                'can_manage_live_chat',
                'can_manage_advertisers',
                'can_manage_campaigns',
                'can_manage_publisher_websites',
                'can_manage_install_rates',
                'can_manage_tracking',
            ]);
        });
    }
};

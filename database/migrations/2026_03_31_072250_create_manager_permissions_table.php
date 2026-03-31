<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manager_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_manage_publishers')->default(false);
            $table->boolean('can_view_publishers')->default(true);
            $table->boolean('can_manage_contracts')->default(false);
            $table->boolean('can_manage_rates')->default(false);
            $table->boolean('can_manage_withdrawals')->default(false);
            $table->boolean('can_view_withdrawals')->default(true);
            $table->boolean('can_manage_ad_presets')->default(false);
            $table->boolean('can_view_fraud_alerts')->default(true);
            $table->boolean('can_resolve_fraud_alerts')->default(false);
            $table->boolean('can_manage_support')->default(true);
            $table->boolean('can_view_stats')->default(true);
            $table->boolean('can_manage_test_periods')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_permissions');
    }
};

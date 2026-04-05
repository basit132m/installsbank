<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertiser_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company_name')->nullable();
            $table->string('website')->nullable();
            $table->string('telegram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->decimal('balance', 12, 4)->default(0); // prepaid credit balance
            $table->decimal('total_spent', 12, 4)->default(0);
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // advertiser
            $table->string('name');
            $table->string('destination_url');
            $table->string('fallback_url')->nullable(); // used when campaign inactive
            $table->enum('contract_type', ['per_click', 'fixed_rate'])->default('per_click');
            $table->decimal('fixed_rate', 8, 6)->nullable(); // $/click for fixed_rate
            $table->json('country_rates')->nullable(); // {US: 0.0050, PK: 0.0010, ...}
            $table->unsignedInteger('target_clicks');
            $table->unsignedInteger('delivered_clicks')->default(0);
            $table->decimal('total_value', 12, 4)->default(0); // calculated by admin
            $table->decimal('advance_amount', 12, 4)->default(0); // 50% of total_value
            $table->decimal('total_paid', 12, 4)->default(0);
            $table->enum('status', ['draft','pending_payment','active','paused','completed','cancelled'])->default('draft');
            $table->json('click_breakdown')->nullable(); // {country: {US: {clicks: 100}}}
            $table->text('admin_note')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('campaign_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // advertiser
            $table->enum('type', ['advance', 'topup'])->default('advance');
            $table->decimal('amount', 12, 4);
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->string('payment_method')->nullable(); // crypto, bank, etc.
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        // Link tracking links to campaigns
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tracking_links', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Campaign::class);
            $table->dropColumn('campaign_id');
        });
        Schema::dropIfExists('campaign_payments');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('advertiser_profiles');
    }
};

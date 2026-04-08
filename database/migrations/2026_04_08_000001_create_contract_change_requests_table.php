<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contract_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('current_type', 30);
            $table->string('requested_type', 30);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('publisher_contract_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('contract_type', 30);
            $table->decimal('fixed_daily_rate', 12, 6)->nullable();
            $table->unsignedBigInteger('total_clicks')->default(0);
            $table->decimal('total_earnings', 12, 4)->default(0);
            $table->string('changed_to', 30);
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publisher_contract_snapshots');
        Schema::dropIfExists('contract_change_requests');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traffic_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('report_no')->index();
            $table->string('title');
            $table->string('prepared_for')->nullable();
            $table->string('target');
            $table->date('date_from');
            $table->date('date_to');
            $table->string('click_type', 10)->default('valid');
            $table->unsignedBigInteger('total')->default(0);
            $table->json('os_data')->nullable();
            $table->json('geo_data')->nullable();
            $table->string('rate')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('tracking_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traffic_reports');
    }
};

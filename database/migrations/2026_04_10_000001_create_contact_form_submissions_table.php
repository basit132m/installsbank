<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('sender_name');
            $table->string('sender_email');
            $table->string('status'); // sent, failed
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_form_submissions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->string('contact_number', 20);
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->boolean('is_verified')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('contact_number');
            $table->index('expires_at');
            $table->index(['contact_number', 'otp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }
};

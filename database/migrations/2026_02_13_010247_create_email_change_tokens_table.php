<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_change_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('old_email');
            $table->string('new_email');
            $table->string('token', 64)->unique();
            $table->timestamp('created_at');
            
            $table->index(['user_id', 'token']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_change_tokens');
    }
};
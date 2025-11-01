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
        Schema::create('activity_log_views', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to activity_log table
            $table->unsignedBigInteger('activity_id');
            
            // User who viewed the log
            $table->unsignedBigInteger('user_id');
            
            // Security tracking fields (ISO 27001 A.8.16)
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            
            // Location tracking (optional, for advanced security)
            $table->string('location')->nullable();
            
            // Session info
            $table->string('session_id')->nullable();
            
            // View duration
            $table->timestamp('viewed_at');
            $table->timestamp('view_ended_at')->nullable();
            
            // Purpose/reason for viewing
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('activity_id')
                ->references('id')
                ->on('activity_log')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            // Indexes for performance
            $table->index('activity_id');
            $table->index('user_id');
            $table->index('viewed_at');
            $table->index(['activity_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log_views');
    }
};
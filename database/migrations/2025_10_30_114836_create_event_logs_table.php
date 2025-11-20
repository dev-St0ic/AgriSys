<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the event_logs table for audit trail and change tracking
     * Helps track all modifications to events for accountability
     */
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // ========================================
            // FOREIGN KEY REFERENCES
            // ========================================
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();

            // ========================================
            // LOG INFORMATION
            // ========================================
            $table->string('action', 100)->nullable(); // created, updated, deleted, published, unpublished, toggled
            $table->json('changes')->nullable(); // Track what fields changed: {field: {old: val, new: val}}
            $table->text('notes')->nullable(); // Additional context or reason for change

            // ========================================
            // METADATA
            // ========================================
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->string('user_agent', 500)->nullable(); // Browser/client info
            $table->string('method', 10)->nullable(); // HTTP method (POST, PUT, DELETE)
            $table->json('request_data')->nullable(); // Full request payload for debugging

            // ========================================
            // TIMESTAMPS (Immutable - logs are never updated)
            // ========================================
            $table->timestamps(); // created_at, updated_at

            // ========================================
            // FOREIGN KEY CONSTRAINTS
            // ========================================
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade'); // Delete logs when event is permanently deleted

            $table->foreign('performed_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Delete logs when user is deleted

            // ========================================
            // INDEXES FOR QUERY PERFORMANCE
            // ========================================
            // DO NOT add index() to column that already has foreign()
            // Foreign keys automatically create indexes in MySQL

            // Single column indexes
            $table->index('action'); // Query by action type
            $table->index('created_at'); // Time-based queries

            // Composite indexes for common query patterns
            $table->index(['event_id', 'created_at']); // Get event logs in order
            $table->index(['performed_by', 'created_at']); // Get user activity timeline
            $table->index(['event_id', 'action']); // Get specific actions for an event
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};

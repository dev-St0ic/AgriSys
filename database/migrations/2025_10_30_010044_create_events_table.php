<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates the events table for Agricultural Events Management System
     * Supports dynamic event display with categorization and ordering
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            // Primary Key
            $table->id();
            
            // ========================================
            // BASIC INFORMATION
            // ========================================
            $table->string('title', 255)->index();
            $table->longText('description');
            $table->text('short_description')->nullable(); // Optional short desc for cards
            
            // ========================================
            // CATEGORIZATION
            // ========================================
            $table->enum('category', ['announcement', 'ongoing', 'upcoming', 'past'])
                ->default('upcoming')
                ->index();
            $table->string('category_label', 100)->nullable(); // Stores "Ongoing", "Upcoming" etc for frontend
            
            // ========================================
            // MEDIA & VISUAL
            // ========================================
            $table->string('image_path')->nullable()->index();
            $table->string('image_alt_text', 500)->nullable(); // For accessibility
            
            // ========================================
            // EVENT DETAILS
            // ========================================
            $table->string('date', 255)->nullable();
            $table->string('location', 500)->nullable();
            $table->json('details')->nullable(); // Store flexible details (participants, cost, etc)
            
            // ========================================
            // STATUS & DISPLAY CONTROL
            // ========================================
            $table->boolean('is_active')->default(true)->index(); // Controls visibility
            $table->integer('display_order')->default(0)->index(); // Controls ordering on frontend
            $table->boolean('is_featured')->default(false)->index(); // Flag for featured event
            
            // ========================================
            // USER TRACKING (Audit Trail)
            // ========================================
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('published_at')->nullable(); // When event was published
            
            // ========================================
            // SOFT DELETE & TIMESTAMPS
            // ========================================
            $table->softDeletes(); // For archive functionality
            $table->timestamps(); // created_at, updated_at

            // ========================================
            // FOREIGN KEY CONSTRAINTS
            // ========================================
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
                
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // ========================================
            // INDEXES FOR PERFORMANCE
            // ========================================
            $table->index(['is_active', 'category']); // For filtering active events by category
            $table->index(['display_order', 'created_at']); // For sorting
            $table->index('created_at'); // For date range queries
            $table->index(['is_active', 'is_featured']); // For featured events
            $table->index(['category', 'is_active', 'display_order']); // Composite for common queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
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
        Schema::create('fishr_applications', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('cascade');

            // Registration Information
            $table->string('registration_number')->unique()->nullable();
            
            // Personal Information
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say'])->nullable();
            
            // Contact Information
            $table->string('barangay')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->string('contact_number', 20)->nullable();
            
            // Main Livelihood Information
            $table->enum('main_livelihood', ['capture', 'aquaculture', 'vending', 'processing', 'others'])->nullable();
            $table->string('livelihood_description')->nullable();
            $table->string('other_livelihood')->nullable();
            
            // Secondary Livelihood Information (NEW)
            $table->enum('secondary_livelihood', ['capture', 'aquaculture', 'vending', 'processing', 'others'])->nullable();
            $table->string('other_secondary_livelihood')->nullable();
            
            // Supporting Documents
            $table->string('document_path')->nullable();
            
            // Status Management
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            // FishR Number (for approved registrations)
            $table->string('fishr_number')->unique()->nullable();
            $table->timestamp('fishr_number_assigned_at')->nullable();
            $table->unsignedBigInteger('fishr_number_assigned_by')->nullable();
            $table->foreign('fishr_number_assigned_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index(['barangay', 'main_livelihood']);
            $table->index('barangay_id');
            $table->index('registration_number');
            $table->index('contact_number');
            $table->index(['first_name', 'last_name']);
            $table->index('fishr_number');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishr_applications');
    }
};
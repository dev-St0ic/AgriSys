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
        Schema::create('user_registration', function (Blueprint $table) {
            $table->id();
            
            // ===== BASIC SIGNUP INFO (REQUIRED FOR INITIAL REGISTRATION) =====
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['unverified', 'pending', 'approved', 'rejected'])->default('unverified');
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('privacy_accepted')->default(false);
            
            // ===== PROFILE COMPLETION INFO (FILLED LATER - ALL NULLABLE) =====
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('name_extension', 20)->nullable(); // Jr., Sr., III, etc.
            $table->string('contact_number', 20)->nullable(); // UPDATED: contact_number instead of phone
            $table->text('complete_address')->nullable();
            $table->string('barangay', 100)->nullable();
            $table->enum('user_type', ['farmer', 'fisherfolk', 'general'])->nullable();
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            
            // ===== ADDITIONAL PROFILE INFO (OPTIONAL) =====
            $table->string('occupation', 100)->nullable();
            $table->string('organization', 150)->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            
            // ===== DOCUMENT PATHS (UPLOADED DURING VERIFICATION - NULLABLE) =====
            $table->string('location_document_path')->nullable(); // UPDATED: location_document_path instead of place_document_path
            $table->string('id_front_path')->nullable();
            $table->string('id_back_path')->nullable();
            
            // ===== EMAIL VERIFICATION =====
            $table->string('verification_token')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            // ===== ADMIN APPROVAL SYSTEM =====
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // ===== TRACKING & ANALYTICS =====
            $table->string('registration_ip', 45)->nullable(); // IPv6 support
            $table->text('user_agent')->nullable();
            $table->string('referral_source', 50)->nullable(); // direct, facebook, google, etc.
            $table->timestamp('last_login_at')->nullable();
            
            // ===== STANDARD LARAVEL FIELDS =====
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // For soft delete functionality
            
            // ===== FOREIGN KEYS =====
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            // ===== OPTIMIZED INDEXES FOR PERFORMANCE =====
            
            // Login & Authentication (High Priority)
            $table->index(['username'], 'idx_username_login'); // Username login
            $table->index(['email'], 'idx_email_login'); // Email login
            $table->index(['email', 'status'], 'idx_email_status_auth'); // Email + status for login checks
            
            // Admin Dashboard Filtering (Critical for Performance)
            $table->index(['status'], 'idx_status_filter'); // Status filtering
            $table->index(['status', 'created_at'], 'idx_status_date_dashboard'); // Status + date for admin dashboard
            $table->index(['user_type'], 'idx_user_type_filter'); // User type filtering
            $table->index(['user_type', 'status'], 'idx_type_status_filter'); // Combined type + status
            $table->index(['created_at'], 'idx_created_date_sort'); // Date sorting/filtering
            
            // Email Verification System
            $table->index(['email_verified_at'], 'idx_email_verified_filter'); // Email verification status
            $table->index(['verification_token'], 'idx_verification_token'); // Email verification lookup
            
            // Admin Search & Text Queries (For search functionality)
            $table->index(['first_name'], 'idx_first_name_search'); // Name searches
            $table->index(['last_name'], 'idx_last_name_search'); // Name searches  
            $table->index(['contact_number'], 'idx_contact_number_search'); // UPDATED: contact_number index
            $table->index(['barangay'], 'idx_barangay_location'); // Location filtering
            
            // Admin Approval Workflow
            $table->index(['approved_at'], 'idx_approval_date'); // Approval date sorting
            $table->index(['approved_by'], 'idx_approved_by_admin'); // Track who approved
            $table->index(['rejected_at'], 'idx_rejection_date'); // Rejection date sorting
            
            // Analytics & Reporting
            $table->index(['referral_source'], 'idx_referral_analytics'); // Traffic source analysis
            $table->index(['registration_ip'], 'idx_registration_ip'); // IP tracking/security
            $table->index(['last_login_at'], 'idx_last_login_activity'); // Activity tracking
            
            // Composite Indexes for Complex Queries
            $table->index(['status', 'user_type', 'created_at'], 'idx_admin_dashboard_combo'); // Main admin dashboard query
            $table->index(['email_verified_at', 'status'], 'idx_verification_status_combo'); // Verification + status
            $table->index(['created_at', 'status', 'user_type'], 'idx_date_status_type_reports'); // Reporting queries
            
            // Soft Delete Support
            $table->index(['deleted_at'], 'idx_soft_delete'); // For soft delete queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_registration');
    }
};
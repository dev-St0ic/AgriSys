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
            $table->string('username', 50)->unique()->nullable();
            $table->timestamp('username_changed_at')->nullable();
            $table->string('email')->unique()->nullable();

            // ===== FACEBOOK AUTHENTICATION FIELDS =====
            $table->string('facebook_id')->nullable();
            $table->string('profile_image_url')->nullable();
            // ===== END FACEBOOK FIELDS =====

            $table->string('password')->nullable();
            $table->enum('status', ['unverified', 'pending', 'approved', 'rejected'])->default('unverified');
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('privacy_accepted')->default(false);

            // ===== PROFILE COMPLETION INFO (FILLED LATER - ALL NULLABLE) =====
            $table->string('first_name', 100)->nullable();
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('name_extension', 20)->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->text('complete_address')->nullable();
            $table->string('barangay', 100)->nullable();
            $table->enum('user_type', ['farmer', 'fisherfolk', 'general'])->nullable();
            $table->integer('age')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();

            // ===== EMERGENCY CONTACT =====
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();

            // ===== DOCUMENT PATHS (UPLOADED DURING VERIFICATION - NULLABLE) =====
            $table->string('location_document_path')->nullable();
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

            // ===== ACTIVITY TRACKING =====
            $table->timestamp('last_login_at')->nullable();

            // ===== STANDARD LARAVEL FIELDS =====
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // ===== FOREIGN KEYS =====
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // ===== OPTIMIZED INDEXES FOR PERFORMANCE =====

            // Login & Authentication (High Priority)
            $table->index(['username'], 'idx_username_login');
            $table->index(['email'], 'idx_email_login');
            $table->index(['email', 'status'], 'idx_email_status_auth');

            // Admin Dashboard Filtering (Critical for Performance)
            $table->index(['status'], 'idx_status_filter');
            $table->index(['status', 'created_at'], 'idx_status_date_dashboard');
            $table->index(['user_type'], 'idx_user_type_filter');
            $table->index(['user_type', 'status'], 'idx_type_status_filter');
            $table->index(['created_at'], 'idx_created_date_sort');

            // Email Verification System
            $table->index(['email_verified_at'], 'idx_email_verified_filter');
            $table->index(['verification_token'], 'idx_verification_token');

            // Admin Search & Text Queries
            $table->index(['first_name'], 'idx_first_name_search');
            $table->index(['last_name'], 'idx_last_name_search');
            $table->index(['contact_number'], 'idx_contact_number_search');
            $table->index(['barangay'], 'idx_barangay_location');

            // Admin Approval Workflow
            $table->index(['approved_at'], 'idx_approval_date');
            $table->index(['approved_by'], 'idx_approved_by_admin');
            $table->index(['rejected_at'], 'idx_rejection_date');

            // Activity Tracking
            $table->index(['last_login_at'], 'idx_last_login_activity');

            // Facebook ID lookups
            $table->index(['facebook_id'], 'idx_facebook_id');

            // Composite Indexes for Complex Queries
            $table->index(['status', 'user_type', 'created_at'], 'idx_admin_dashboard_combo');
            $table->index(['email_verified_at', 'status'], 'idx_verification_status_combo');
            $table->index(['created_at', 'status', 'user_type'], 'idx_date_status_type_reports');

            // Soft Delete Support
            $table->index(['deleted_at'], 'idx_soft_delete');
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

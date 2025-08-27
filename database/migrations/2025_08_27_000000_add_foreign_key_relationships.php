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
        // 1. Add barangay_id to all application tables (keeping existing barangay string field for backward compatibility)
        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->index('barangay_id');
        });

        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->index('barangay_id');
        });

        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->index('barangay_id');
        });

        // 2. Add missing user foreign keys
        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Create inventory_transactions table for better inventory tracking
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('seedling_request_id')->nullable();
            $table->enum('transaction_type', ['deduction', 'addition', 'adjustment']);
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('seedling_request_id')->references('id')->on('seedling_requests')->onDelete('set null');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['inventory_id', 'created_at']);
            $table->index(['seedling_request_id', 'transaction_type']);
            $table->index(['transaction_type', 'created_at']);
        });

        // 4. Add cross-application relationship (BoatR requires FishR)
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->unsignedBigInteger('fishr_application_id')->nullable()->after('fishr_number');
            $table->foreign('fishr_application_id')->references('id')->on('fishr_applications')->onDelete('set null');
            $table->index('fishr_application_id');
        });

        // 5. Add training applications relationships (only user foreign key - no barangay field exists)
        if (Schema::hasTable('training_applications')) {
            Schema::table('training_applications', function (Blueprint $table) {
                // Add missing user foreign key if updated_by exists
                if (Schema::hasColumn('training_applications', 'updated_by')) {
                    $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order to avoid foreign key constraint issues

        // 5. Remove training applications relationships
        if (Schema::hasTable('training_applications')) {
            Schema::table('training_applications', function (Blueprint $table) {
                if (Schema::hasColumn('training_applications', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                }
            });
        }

        // 4. Remove cross-application relationship
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropForeign(['fishr_application_id']);
            $table->dropColumn('fishr_application_id');
        });

        // 3. Drop inventory_transactions table
        Schema::dropIfExists('inventory_transactions');

        // 2. Remove user foreign keys
        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
        });

        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
        });

        // 1. Remove barangay relationships
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });
    }
};

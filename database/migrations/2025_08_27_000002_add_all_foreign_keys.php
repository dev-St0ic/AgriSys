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
        // Add all foreign key constraints after all tables are created

        // 1. RSBSA Applications foreign keys
        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Seedling Requests foreign keys
        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. FishR Applications foreign keys
        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });

        // 4. BoatR Applications foreign keys
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->foreign('fishr_application_id')->references('id')->on('fishr_applications')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('inspected_by')->references('id')->on('users')->onDelete('set null');
        });

        // 5. Training Applications foreign keys
        Schema::table('training_applications', function (Blueprint $table) {
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order

        Schema::table('training_applications', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
        });

        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropForeign(['fishr_application_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['inspected_by']);
        });

        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropForeign(['updated_by']);
        });

        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropForeign(['reviewed_by']);
        });

        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['assigned_by']);
        });
    }
};

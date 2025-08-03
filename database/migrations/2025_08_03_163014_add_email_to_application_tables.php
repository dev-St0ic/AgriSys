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
        // Add email column to seedling_requests table
        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->string('email')->after('contact_number');
        });

        // Add email column to rsbsa_applications table
        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->string('email')->after('mobile_number');
        });

        // Add email column to fishr_applications table
        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->string('email')->after('contact_number');
        });

        // Add email and mobile columns to boatr_applications table
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->string('mobile')->after('last_name');
            $table->string('email')->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove email column from seedling_requests table
        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        // Remove email column from rsbsa_applications table
        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        // Remove email column from fishr_applications table
        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        // Remove email and mobile columns from boatr_applications table
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropColumn(['mobile', 'email']);
        });
    }
};

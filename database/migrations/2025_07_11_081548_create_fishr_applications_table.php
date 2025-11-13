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

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('registration_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('name_extension')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say']);
            $table->string('barangay');
            $table->unsignedBigInteger('barangay_id')->nullable(); // Foreign key to barangays table
            $table->string('contact_number', 20);
            $table->string('email')->nullable();
            $table->enum('main_livelihood', ['capture', 'aquaculture', 'vending', 'processing', 'others']);
            $table->string('livelihood_description');
            $table->string('other_livelihood')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('status', ['under_review', 'approved', 'rejected'])->default('under_review');

            // Admin management fields
            $table->text('remarks')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['barangay', 'main_livelihood']);
            $table->index('barangay_id');
            $table->index('registration_number');
            $table->index('contact_number'); // Added for search functionality
            $table->index('email'); // Added for email search functionality
            $table->index(['first_name', 'last_name']); // Added for name searches

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('cascade');

            // Foreign key for admin who updated the status and barangay
            // Will be added in separate migration after all tables are created
            // $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            // $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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

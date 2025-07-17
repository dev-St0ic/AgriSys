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
            $table->string('registration_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say']);
            $table->string('barangay');
            $table->string('contact_number', 20);
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
            $table->index('registration_number');
            $table->index('contact_number'); // Added for search functionality
            $table->index(['first_name', 'last_name']); // Added for name searches
            
            // Foreign key for admin who updated the status
            // Uncomment this line if you have a users table for admins
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
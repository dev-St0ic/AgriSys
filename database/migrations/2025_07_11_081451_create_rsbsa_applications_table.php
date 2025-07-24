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
        Schema::create('rsbsa_applications', function (Blueprint $table) {
            $table->id();
            
            // Application identification
            $table->string('application_number')->unique();
            
            // Personal Information (simplified)
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say']);
            
            // Contact Information
            $table->string('mobile_number', 20);
            $table->string('barangay');
            
            // Registration Details
            $table->enum('main_livelihood', ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth']);
            
            // Farm/Livelihood Information
            $table->decimal('land_area', 8, 2)->nullable(); // in hectares
            $table->string('farm_location')->nullable();
            $table->text('commodity')->nullable(); // crops/livestock/fish
            
            // Document Upload
            $table->string('supporting_document_path')->nullable();
            
            // Application Status Management
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Application Number Assignment
            $table->timestamp('number_assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['barangay', 'main_livelihood']);
            $table->index('application_number');
            $table->index('mobile_number'); // For search functionality
            $table->index(['first_name', 'last_name']); // For name searches
            
            // Foreign key constraints
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rsbsa_applications');
    }
};
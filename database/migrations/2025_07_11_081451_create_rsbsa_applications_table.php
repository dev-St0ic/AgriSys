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
            
            // Basic Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say'])->nullable();
            $table->date('date_of_birth')->nullable();
            
            // Contact Information
            $table->string('mobile_number')->nullable();
            $table->string('barangay');
            
            // Registration Type
            $table->enum('registration_type', ['new', 'renewal']);
            
            // For New Registration
            $table->enum('main_livelihood', ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'])->nullable();
            $table->decimal('land_area', 8, 2)->nullable(); // in hectares
            $table->string('farm_location')->nullable();
            $table->text('commodity')->nullable(); // crops/livestock
            
            // For Old Registration (Renewal)
            $table->string('rsbsa_reference_number')->nullable();
            
            // Document Upload
            $table->string('supporting_document_path')->nullable();
            
            // Application Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraint
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['registration_type', 'status']);
            $table->index('barangay');
            $table->index('rsbsa_reference_number');
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

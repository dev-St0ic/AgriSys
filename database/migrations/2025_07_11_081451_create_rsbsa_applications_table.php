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

            // CHANGED: Made user_id nullable
            $table->unsignedBigInteger('user_id')->nullable();

            // Application identification
            $table->string('application_number')->unique()->nullable();

            // Personal Information (simplified)
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say'])->nullable();

            // Contact Information
            $table->string('contact_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('barangay')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();

            // Registration Details
            $table->enum('main_livelihood', ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'])->nullable();

            // Farm/Livelihood Information
            $table->decimal('land_area', 8, 2)->nullable();
            $table->string('farm_location')->nullable();
            $table->text('commodity')->nullable();

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

            // Foreign key constraints
            // CHANGED: Made onDelete('set null') since user_id is now nullable
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('set null');

            // Indexes for better performance
            $table->index('user_id');
            $table->index(['status', 'created_at']);
            $table->index(['barangay', 'main_livelihood']);
            $table->index('barangay_id');
            $table->index('application_number');
            $table->index('contact_number');
            $table->index('email');
            $table->index(['first_name', 'last_name']);
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

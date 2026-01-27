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

            // User relationship
            $table->unsignedBigInteger('user_id')->nullable();

            // Application identification
            $table->string('application_number')->unique()->nullable();

            // ==================== BASIC INFORMATION ====================
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say'])->nullable();

            // ==================== CONTACT & LOCATION ====================
            $table->string('contact_number', 20)->nullable();
            $table->string('barangay')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();
            $table->text('address')->nullable(); // NEW: Complete address field

            // ==================== MAIN LIVELIHOOD ====================
            $table->enum('main_livelihood', ['Farmer', 'Farmworker/Laborer', 'Fisherfolk', 'Agri-youth'])->nullable();

            // ==================== FARMER-SPECIFIC FIELDS ====================
            $table->string('farmer_crops')->nullable(); // Rice, Corn, HVC, Livestock, Poultry, Agri-fishery, Other Crops
            $table->string('farmer_other_crops')->nullable(); // If "Other Crops" selected
            $table->text('farmer_livestock')->nullable(); // Type and number (e.g., "Chickens (50), Pigs (5)")
            $table->decimal('farmer_land_area', 8, 2)->nullable(); // In hectares
            $table->string('farmer_type_of_farm')->nullable(); // Irrigated, Rainfed Upland, Rainfed Lowland
            $table->string('farmer_land_ownership')->nullable(); // Owner, Tenant, Lessee
            $table->string('farmer_special_status')->nullable(); // Ancestral Domain, Agrarian Reform Beneficiary, None
            $table->string('farm_location')->nullable(); // REQUIRED FOR FARMERS ONLY

            // ==================== FARMWORKER/LABORER-SPECIFIC FIELDS ====================
            $table->string('farmworker_type')->nullable(); // Land prep, Planting, Cultivation, Harvesting, Others
            $table->string('farmworker_other_type')->nullable(); // If "Others" selected

            // ==================== FISHERFOLK-SPECIFIC FIELDS ====================
            $table->string('fisherfolk_activity')->nullable(); // Fish capture, Aquaculture, Gleaning, Processing, Vending, Others
            $table->string('fisherfolk_other_activity')->nullable(); // If "Others" selected

            // ==================== AGRI-YOUTH-SPECIFIC FIELDS ====================
            $table->string('agriyouth_farming_household')->nullable(); // Yes, No
            $table->string('agriyouth_training')->nullable(); // Formal agri-fishery course, Non-formal, None
            $table->string('agriyouth_participation')->nullable(); // Participated, Not Participated

            // ==================== GENERAL LIVELIHOOD INFO ====================
            $table->text('commodity')->nullable(); // General commodity field (used by all livelihoods)

            // ==================== DOCUMENT UPLOAD ====================
            $table->string('supporting_document_path')->nullable();

            // ==================== APPLICATION STATUS MANAGEMENT ====================
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // ==================== APPLICATION NUMBER ASSIGNMENT ====================
            $table->timestamp('number_assigned_at')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // ==================== FOREIGN KEYS ====================
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('set null');

            // ==================== INDEXES ====================
            $table->index('user_id');
            $table->index(['status', 'created_at']);
            $table->index(['barangay', 'main_livelihood']);
            $table->index('application_number');
            $table->index('contact_number');
            $table->index(['first_name', 'last_name']);
            $table->index('main_livelihood');
            $table->index('status');
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
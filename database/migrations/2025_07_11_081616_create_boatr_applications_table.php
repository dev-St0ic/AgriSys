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
        Schema::create('boatr_applications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            // Application identification
            $table->string('application_number')->unique();

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('contact_number', 20);
            $table->string('email')->nullable();
            $table->string('barangay'); // Barangay location
            $table->string('fishr_number'); // FishR registration number
            $table->unsignedBigInteger('fishr_application_id')->nullable(); // Foreign key to fishr_applications table

            // Vessel Information
            $table->string('vessel_name');
            $table->enum('boat_type', [
                'Spoon',
                'Plumb',
                'Banca',
                'Rake Stem - Rake Stern',
                'Rake Stem - Transom/Spoon/Plumb Stern',
                'Skiff (Typical Design)'
            ]);

            // Boat Dimensions
            $table->decimal('boat_length', 5, 2); // in feet
            $table->decimal('boat_width', 5, 2);  // in feet
            $table->decimal('boat_depth', 5, 2);  // in feet

            // Engine Information
            $table->string('engine_type');
            $table->integer('engine_horsepower');

            // Fishing Information
            $table->enum('primary_fishing_gear', [
                'Hook and Line',
                'Bottom Set Gill Net',
                'Fish Trap',
                'Fish Coral'
            ]);

            // FIXED: Single User Document (not array)
            $table->string('user_document_path')->nullable();
            $table->string('user_document_name')->nullable();
            $table->string('user_document_type')->nullable();
            $table->bigInteger('user_document_size')->nullable(); // file size in bytes
            $table->timestamp('user_document_uploaded_at')->nullable();

            // Multiple Inspection Documents (JSON)
            $table->json('inspection_documents')->nullable();

            // Inspection Information
            $table->boolean('inspection_completed')->default(false);
            $table->timestamp('inspection_date')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->unsignedBigInteger('inspected_by')->nullable();

            // Document Verification
            $table->boolean('documents_verified')->default(false);
            $table->timestamp('documents_verified_at')->nullable();
            $table->text('document_verification_notes')->nullable();

            // Application Status Management
            $table->enum('status', [
                'pending',
                'under_review',
                'inspection_scheduled',
                'inspection_required',
                'documents_pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();

            // Status History (JSON)
            $table->json('status_history')->nullable();

            // Workflow Timestamps
            $table->timestamp('inspection_scheduled_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['boat_type', 'primary_fishing_gear']);
            $table->index('application_number');
            $table->index('fishr_number');
            $table->index('fishr_application_id');
            $table->index('contact_number');
            $table->index('email');
            $table->index(['first_name', 'last_name']); // For name searches
            $table->index('vessel_name'); // For vessel searches
            $table->index('inspection_completed');
            $table->index('documents_verified');

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('cascade');

            // Foreign key constraints - will be added in separate migration
            // $table->foreign('fishr_application_id')->references('id')->on('fishr_applications')->onDelete('set null');
            // $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('inspected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boatr_applications');
    }
};

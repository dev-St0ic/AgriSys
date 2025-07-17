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
            $table->string('application_number')->unique();
            
            // Applicant Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('fishr_number'); // Required FishR registration number
            
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
            
            // Vessel Dimensions (in feet)
            $table->decimal('boat_length', 8, 2);
            $table->decimal('boat_width', 8, 2);
            $table->decimal('boat_depth', 8, 2);
            
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
            
            // Document and Inspection (handled by admin)
            $table->string('supporting_document_path')->nullable();
            $table->boolean('inspection_completed')->default(false);
            $table->timestamp('inspection_date')->nullable();
            
            // Application Status and Review
            $table->enum('status', ['pending', 'approved', 'rejected', 'inspection_required'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Key Constraints
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index('status');
            $table->index('boat_type');
            $table->index('vessel_name');
            $table->index('fishr_number');
            $table->index('inspection_completed');
            $table->index('application_number');
            $table->index(['first_name', 'last_name']); // For name searches
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
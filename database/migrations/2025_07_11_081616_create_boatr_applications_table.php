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
            
            // Owner Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('mobile_number');
            $table->string('barangay');
            
            // Boat Information
            $table->string('boat_name');
            $table->decimal('boat_length', 8, 2); // in meters
            $table->decimal('boat_width', 8, 2); // in meters
            $table->decimal('boat_depth', 8, 2); // in meters
            $table->enum('hull_material', ['Wood', 'Fiberglass', 'Steel', 'Aluminum']);
            
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
            
            // Document Upload (handled by admin after inspection)
            $table->string('supporting_document_path')->nullable();
            $table->boolean('inspection_completed')->default(false);
            $table->timestamp('inspection_date')->nullable();
            
            // Application Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'inspection_required'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraint
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
            $table->index('barangay');
            $table->index('boat_name');
            $table->index('inspection_completed');
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

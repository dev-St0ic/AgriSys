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
        Schema::create('seedling_requests', function (Blueprint $table) {
            $table->id();
            
            // Applicant Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('mobile_number');
            $table->string('barangay');
            $table->text('address');
            
            // Seedling Selections (JSON to store arrays)
            $table->json('selected_vegetables')->nullable(); // Array of selected vegetables
            $table->json('selected_fruits')->nullable(); // Array of selected fruits
            $table->string('selected_fertilizer')->nullable(); // Single fertilizer selection
            
            // Application Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'distributed'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('distributed_at')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraint
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
            $table->index('barangay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seedling_requests');
    }
};

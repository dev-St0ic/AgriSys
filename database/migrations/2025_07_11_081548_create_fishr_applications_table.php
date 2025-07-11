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
            
            // Basic Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('sex', ['Male', 'Female', 'Preferred not to say']);
            $table->string('barangay');
            $table->string('mobile_number');
            
            // Livelihood Information
            $table->enum('main_livelihood', [
                'capture-fishing', 
                'aquaculture', 
                'fish-processing', 
                'fish-marketing', 
                'others'
            ]);
            $table->string('other_livelihood')->nullable(); // For when 'others' is selected
            
            // Document Upload
            $table->string('supporting_documents_path')->nullable();
            
            // Application Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'under_review'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            
            $table->timestamps();
            
            // Foreign Key Constraint
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
            $table->index('barangay');
            $table->index('main_livelihood');
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

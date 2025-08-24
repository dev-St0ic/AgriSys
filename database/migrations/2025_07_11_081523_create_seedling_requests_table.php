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
            $table->string('request_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('extension_name')->nullable();
            $table->string('contact_number');
            $table->string('address');
            $table->string('barangay');
            $table->string('planting_location')->nullable();
            $table->string('purpose')->nullable();
            $table->string('seedling_type')->nullable();
            
            // Item requests
            $table->json('vegetables')->nullable();           
            $table->json('fruits')->nullable();              
            $table->json('fertilizers')->nullable();         
            $table->integer('requested_quantity')->nullable();
            $table->integer('total_quantity')->default(0);   
            $table->date('preferred_delivery_date')->nullable();
            $table->string('document_path')->nullable();
            
            // Status fields
            $table->string('status')->default('under_review');
            $table->string('vegetables_status')->nullable(); // Added individual status fields
            $table->string('fruits_status')->nullable();
            $table->string('fertilizers_status')->nullable();
            
            // Approved items
            $table->json('vegetables_approved_items')->nullable(); // Added approved items fields
            $table->json('fruits_approved_items')->nullable();
            $table->json('fertilizers_approved_items')->nullable();
            
            // Review information
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('remarks')->nullable(); // Changed to text for longer remarks
            $table->integer('approved_quantity')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('barangay');
            $table->index('status');
            $table->index(['vegetables_status', 'fruits_status', 'fertilizers_status'], 'seedling_requests_category_status_index');
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
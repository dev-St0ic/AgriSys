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
            $table->json('vegetables')->nullable();           
            $table->json('fruits')->nullable();              
            $table->json('fertilizers')->nullable();         
            $table->integer('requested_quantity')->nullable();
            $table->integer('total_quantity')->default(0);   
            $table->date('preferred_delivery_date')->nullable();
            $table->string('document_path')->nullable();
            $table->string('status')->default('under_review');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('remarks')->nullable();
            $table->integer('approved_quantity')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();

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
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seedling_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();

            // Basic request info
            $table->string('request_number')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('extension_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('barangay')->nullable();
            $table->text('planting_location')->nullable();
            $table->text('purpose')->nullable();

            // Quantities
            $table->integer('total_quantity')->default(0);
            $table->integer('approved_quantity')->nullable();

            // Dates
            $table->date('preferred_delivery_date')->nullable();

            // Document
            $table->string('document_path')->nullable();

            // Overall status
            $table->enum('status', ['pending', 'under_review', 'approved', 'partially_approved', 'rejected', 'cancelled'])->default('pending');

            // Review information
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('cascade');

            // Indexes
            $table->index('status');
            $table->index('barangay');
            $table->index('created_at');
            $table->index('request_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seedling_requests');
    }
};

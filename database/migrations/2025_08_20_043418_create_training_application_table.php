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
        Schema::create('training_applications', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('application_number')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name_extension')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('barangay')->nullable();
            $table->enum('training_type', [
                'tilapia_hito',
                'hydroponics',
                'aquaponics',
                'mushrooms',
                'livestock_poultry',
                'high_value_crops',
                'sampaguita_propagation'
            ])->nullable();
            // CHANGED: Single document path instead of JSON array
            $table->string('document_path')->nullable();
            $table->enum('status', ['under_review', 'approved', 'rejected'])->default('under_review');

            // Admin management fields
            $table->text('remarks')->nullable();
            $table->timestamp('status_updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['training_type', 'status']);
            $table->index('application_number');
            $table->index('contact_number');
            $table->index('email');
            $table->index(['first_name', 'last_name']);

            // Foreign key constraints
            $table->foreign('user_id')
                ->references('id')
                ->on('user_registration')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_applications');
    }
};
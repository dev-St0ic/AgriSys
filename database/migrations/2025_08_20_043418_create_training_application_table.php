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
            $table->string('application_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('contact_number', 20);
            $table->string('email');
            $table->string('barangay'); // Barangay location
            $table->enum('training_type', [
                'tilapia_hito',
                'hydroponics',
                'aquaponics',
                'mushrooms',
                'livestock_poultry',
                'high_value_crops',
                'sampaguita_propagation'
            ]);
            $table->json('document_paths')->nullable(); // Store multiple document paths
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
            $table->index(['first_name', 'last_name']); // Added for name searches

            // Foreign key for admin who updated the status - will be added in separate migration
            // $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
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

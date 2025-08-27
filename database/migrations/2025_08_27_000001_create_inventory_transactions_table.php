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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('seedling_request_id')->nullable();
            $table->enum('transaction_type', ['deduction', 'addition', 'adjustment']);
            $table->integer('quantity');
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('seedling_request_id')->references('id')->on('seedling_requests')->onDelete('set null');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for better performance
            $table->index(['inventory_id', 'created_at']);
            $table->index(['seedling_request_id', 'transaction_type']);
            $table->index(['transaction_type', 'created_at']);
            $table->index('performed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

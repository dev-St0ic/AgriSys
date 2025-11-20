<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_supply_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_item_id')->nullable()->constrained('category_items')->onDelete('cascade');

            // Transaction details
            $table->enum('transaction_type', [
                'received',           // New supplies received
                'distributed',        // Supplies given to approved applicants
                'adjustment',         // Manual supply adjustment
                'returned',           // Returned from cancelled/rejected requests
                'loss',              // Expired/damaged/lost
                'initial_supply'     // Initial supply setup
            ])->nullable();

            $table->integer('quantity')->nullable();
            $table->integer('old_supply')->nullable();
            $table->integer('new_supply')->nullable();

            // Who performed the transaction
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('cascade');

            // Additional details
            $table->text('notes')->nullable();
            $table->string('source')->nullable(); // For received supplies (supplier name, etc.)

            // Reference to related entities (polymorphic)
            $table->string('reference_type')->nullable(); // e.g., 'SeedlingRequest'
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('category_item_id');
            $table->index('transaction_type');
            $table->index('performed_by');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_supply_logs');
    }
};

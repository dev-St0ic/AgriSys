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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->enum('category', ['vegetables', 'fruits', 'fertilizers']);
            $table->string('variety')->nullable(); // e.g., 'siling haba', 'eggplant'
            $table->text('description')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(10); // Alert threshold
            $table->integer('maximum_stock')->default(1000);
            $table->string('unit')->default('pieces'); // pieces, kg, sacks, etc.
            $table->date('last_restocked')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['category', 'is_active']);
            $table->index('current_stock');
            $table->index('item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

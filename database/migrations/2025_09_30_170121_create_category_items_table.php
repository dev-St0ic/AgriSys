<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('request_categories')->onDelete('cascade');
            $table->string('name'); // Item name
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs'); // pcs, kg, liters, etc.
            $table->decimal('price', 10, 2)->nullable(); // Optional pricing
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['category_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_items');
    }
};
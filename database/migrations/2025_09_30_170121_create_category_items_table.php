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
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            
            // Supply management fields
            $table->integer('current_supply')->default(0);
            $table->integer('minimum_supply')->default(0);
            $table->integer('maximum_supply')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->boolean('supply_alert_enabled')->default(true);
            $table->timestamp('last_supplied_at')->nullable();
            $table->foreignId('last_supplied_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index('display_order');
            $table->index('current_supply');
            $table->index('reorder_point');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_items');
    }
};
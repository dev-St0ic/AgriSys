<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seedling_request_items', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');

            $table->foreignId('seedling_request_id')->constrained('seedling_requests')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('request_categories')->onDelete('cascade');
            $table->foreignId('category_item_id')->nullable()->constrained('category_items')->onDelete('set null');
            $table->string('item_name');
            $table->integer('requested_quantity');
            $table->integer('approved_quantity')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->index(['seedling_request_id', 'category_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seedling_request_items');
    }
};
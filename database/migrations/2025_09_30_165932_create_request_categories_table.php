<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0); // Changed from sort_order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('display_order');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_categories');
    }
};
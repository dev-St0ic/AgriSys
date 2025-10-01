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
            $table->string('name')->unique(); // e.g., 'seeds', 'seedlings', 'fruits'
            $table->string('display_name'); // e.g., 'Seeds', 'Seedlings', 'Fruit Trees'
            $table->string('icon')->nullable(); // icon class or path
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_categories');
    }
};
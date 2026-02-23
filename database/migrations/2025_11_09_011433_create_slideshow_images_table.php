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
        Schema::create('slideshow_images', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable(); // Path to the uploaded image
            $table->string('title')->nullable(); // Optional title for the slide
            $table->text('description')->nullable(); // Optional description
            $table->integer('order')->default(0); // Order/position in slideshow
            $table->boolean('is_active')->default(true); // Enable/disable slide
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slideshow_images');
    }
};

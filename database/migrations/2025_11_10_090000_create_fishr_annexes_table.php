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
        Schema::create('fishr_annexes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fishr_application_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_extension');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('fishr_application_id')->references('id')->on('fishr_applications')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('fishr_application_id');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fishr_annexes');
    }
};

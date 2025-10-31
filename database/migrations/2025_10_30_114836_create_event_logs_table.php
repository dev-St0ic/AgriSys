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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('action'); // created, updated, deleted, published, unpublished
            $table->unsignedBigInteger('performed_by'); // user who performed the action
            $table->json('changes')->nullable(); // track what changed
            $table->text('notes')->nullable(); // additional notes
            $table->timestamps();

            // Foreign keys
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->onDelete('cascade');

            $table->foreign('performed_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Indexes for better query performance
            $table->index('event_id');
            $table->index('performed_by');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
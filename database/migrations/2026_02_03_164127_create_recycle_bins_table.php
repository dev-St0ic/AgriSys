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
        Schema::create('recycle_bins', function (Blueprint $table) {
            $table->id();
            
            // Model information
            $table->string('model_type')->comment('Full class name of deleted model');
            $table->unsignedBigInteger('model_id')->comment('ID of deleted model');
            
            // Deleted data
            $table->longText('data')->comment('JSON data of deleted item');
            
            // Who deleted it
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            // Why was it deleted
            $table->string('reason')->nullable()->comment('Reason for deletion');
            $table->string('item_name')->nullable()->comment('Human readable name of deleted item');
            
            // Deletion timeline
            $table->timestamp('deleted_at')->comment('When item was deleted');
            
            // Restoration info
            $table->timestamp('restored_at')->nullable()->comment('When item was restored');
            $table->unsignedBigInteger('restored_by')->nullable();
            $table->foreign('restored_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            
            // Timestamps
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['model_type', 'model_id']);
            $table->index('deleted_by');
            $table->index('deleted_at');
            $table->index('restored_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recycle_bins');
    }
};
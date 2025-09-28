<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the complete seedling_requests table with all 6 categories and fixes existing data
     */
    public function up(): void
    {
        Schema::create('seedling_requests', function (Blueprint $table) {
            $table->id();
            
            // Basic request info
            $table->string('request_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('extension_name')->nullable();
            $table->string('contact_number');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('barangay');
            $table->text('planting_location')->nullable();
            $table->text('purpose')->nullable();
            $table->text('seedling_type')->nullable();
            
            // 6 Categories - JSON fields for items with proper defaults
            $table->json('seeds')->nullable()->default(null);
            $table->json('seedlings')->nullable()->default(null);
            $table->json('fruits')->nullable()->default(null);
            $table->json('ornamentals')->nullable()->default(null);
            $table->json('fingerlings')->nullable()->default(null);
            $table->json('fertilizers')->nullable()->default(null);
            
            // Quantities
            $table->integer('requested_quantity')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->integer('approved_quantity')->nullable();
            
            // Dates
            $table->date('preferred_delivery_date')->nullable();
            
            // Document
            $table->string('document_path')->nullable();
            
            // Overall status
            $table->string('status')->default('under_review');
            
            // Category-specific status fields
            $table->string('seeds_status')->nullable();
            $table->string('seedlings_status')->nullable();
            $table->string('fruits_status')->nullable();
            $table->string('ornamentals_status')->nullable();
            $table->string('fingerlings_status')->nullable();
            $table->string('fertilizers_status')->nullable();
            
            // Approved items for each category
            $table->json('seeds_approved_items')->nullable()->default(null);
            $table->json('seedlings_approved_items')->nullable()->default(null);
            $table->json('fruits_approved_items')->nullable()->default(null);
            $table->json('ornamentals_approved_items')->nullable()->default(null);
            $table->json('fingerlings_approved_items')->nullable()->default(null);
            $table->json('fertilizers_approved_items')->nullable()->default(null);
            
            // Rejected items for each category
            $table->json('seeds_rejected_items')->nullable()->default(null);
            $table->json('seedlings_rejected_items')->nullable()->default(null);
            $table->json('fruits_rejected_items')->nullable()->default(null);
            $table->json('ornamentals_rejected_items')->nullable()->default(null);
            $table->json('fingerlings_rejected_items')->nullable()->default(null);
            $table->json('fertilizers_rejected_items')->nullable()->default(null);
            
            // Review information
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('barangay');
            $table->index('created_at');
            $table->index(['seeds_status', 'seedlings_status']);
            $table->index(['fruits_status', 'ornamentals_status']);
            $table->index(['fingerlings_status', 'fertilizers_status']);
        });

        // Fix existing data if this is a modification of existing table
        $this->fixExistingData();
    }

    /**
     * Fix existing data to ensure proper JSON format
     */
    private function fixExistingData(): void
    {
        // Only run if we're updating an existing table
        if (!Schema::hasTable('seedling_requests')) {
            return;
        }

        $categories = ['seeds', 'seedlings', 'fruits', 'ornamentals', 'fingerlings', 'fertilizers'];
        $itemTypes = ['', '_approved_items', '_rejected_items'];

        foreach ($categories as $category) {
            foreach ($itemTypes as $type) {
                $columnName = $category . $type;
                
                // Fix NULL values that might be stored as strings
                DB::statement("UPDATE seedling_requests SET `{$columnName}` = NULL WHERE `{$columnName}` IN ('', 'null', '[]')");
                
                // Fix malformed JSON
                DB::statement("UPDATE seedling_requests SET `{$columnName}` = NULL WHERE `{$columnName}` IS NOT NULL AND JSON_VALID(`{$columnName}`) = 0");
                
                // Ensure empty arrays are stored as NULL for consistency
                DB::statement("UPDATE seedling_requests SET `{$columnName}` = NULL WHERE `{$columnName}` = '[]'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seedling_requests');
    }
};
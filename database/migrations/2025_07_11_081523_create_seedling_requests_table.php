<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration adds the new 6-category fields to existing seedling_requests table
     */
    public function up(): void
    {
        Schema::table('seedling_requests', function (Blueprint $table) {
            // Add new category fields (if they don't exist)
            if (!Schema::hasColumn('seedling_requests', 'seeds')) {
                $table->json('seeds')->nullable()->after('seedling_type');
            }
            if (!Schema::hasColumn('seedling_requests', 'seedlings')) {
                $table->json('seedlings')->nullable()->after('seeds');
            }
            if (!Schema::hasColumn('seedling_requests', 'ornamentals')) {
                $table->json('ornamentals')->nullable()->after('fruits');
            }
            if (!Schema::hasColumn('seedling_requests', 'fingerlings')) {
                $table->json('fingerlings')->nullable()->after('ornamentals');
            }

            // Add new status fields
            if (!Schema::hasColumn('seedling_requests', 'seeds_status')) {
                $table->string('seeds_status')->nullable()->after('status');
            }
            if (!Schema::hasColumn('seedling_requests', 'seedlings_status')) {
                $table->string('seedlings_status')->nullable()->after('seeds_status');
            }
            if (!Schema::hasColumn('seedling_requests', 'ornamentals_status')) {
                $table->string('ornamentals_status')->nullable()->after('fruits_status');
            }
            if (!Schema::hasColumn('seedling_requests', 'fingerlings_status')) {
                $table->string('fingerlings_status')->nullable()->after('ornamentals_status');
            }

            // Add new approved items fields
            if (!Schema::hasColumn('seedling_requests', 'seeds_approved_items')) {
                $table->json('seeds_approved_items')->nullable()->after('fertilizers_status');
            }
            if (!Schema::hasColumn('seedling_requests', 'seedlings_approved_items')) {
                $table->json('seedlings_approved_items')->nullable()->after('seeds_approved_items');
            }
            if (!Schema::hasColumn('seedling_requests', 'ornamentals_approved_items')) {
                $table->json('ornamentals_approved_items')->nullable()->after('fruits_approved_items');
            }
            if (!Schema::hasColumn('seedling_requests', 'fingerlings_approved_items')) {
                $table->json('fingerlings_approved_items')->nullable()->after('ornamentals_approved_items');
            }

            // Add new rejected items fields
            if (!Schema::hasColumn('seedling_requests', 'seeds_rejected_items')) {
                $table->json('seeds_rejected_items')->nullable()->after('fertilizers_approved_items');
            }
            if (!Schema::hasColumn('seedling_requests', 'seedlings_rejected_items')) {
                $table->json('seedlings_rejected_items')->nullable()->after('seeds_rejected_items');
            }
            if (!Schema::hasColumn('seedling_requests', 'ornamentals_rejected_items')) {
                $table->json('ornamentals_rejected_items')->nullable()->after('fruits_rejected_items');
            }
            if (!Schema::hasColumn('seedling_requests', 'fingerlings_rejected_items')) {
                $table->json('fingerlings_rejected_items')->nullable()->after('ornamentals_rejected_items');
            }

            // Update indexes
            $table->index(['seeds_status', 'seedlings_status'], 'seedling_requests_new_status_1');
            $table->index(['ornamentals_status', 'fingerlings_status'], 'seedling_requests_new_status_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seedling_requests', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex('seedling_requests_new_status_1');
            $table->dropIndex('seedling_requests_new_status_2');

            // Drop new category columns
            $table->dropColumn([
                'seeds',
                'seedlings',
                'ornamentals', 
                'fingerlings',
                'seeds_status',
                'seedlings_status',
                'ornamentals_status',
                'fingerlings_status',
                'seeds_approved_items',
                'seedlings_approved_items',
                'ornamentals_approved_items',
                'fingerlings_approved_items',
                'seeds_rejected_items',
                'seedlings_rejected_items',
                'ornamentals_rejected_items',
                'fingerlings_rejected_items'
            ]);
        });
    }
};
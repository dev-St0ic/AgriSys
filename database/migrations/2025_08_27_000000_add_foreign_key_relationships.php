<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function to check if foreign key exists
        $foreignKeyExists = function($table, $foreignKey) {
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
            ", [config('database.connections.mysql.database'), $table, $foreignKey]);
            return !empty($result);
        };

        // Helper function to check if index exists
        $indexExists = function($table, $indexName) {
            $result = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            return !empty($result);
        };

        // 1. Add barangay_id to all application tables
        Schema::table('seedling_requests', function (Blueprint $table) use ($foreignKeyExists, $indexExists) {
            if (!Schema::hasColumn('seedling_requests', 'barangay_id')) {
                $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            }

            if (!$foreignKeyExists('seedling_requests', 'seedling_requests_barangay_id_foreign')) {
                $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            }

            if (!$indexExists('seedling_requests', 'seedling_requests_barangay_id_index')) {
                $table->index('barangay_id');
            }
        });

        Schema::table('rsbsa_applications', function (Blueprint $table) use ($foreignKeyExists, $indexExists) {
            if (!Schema::hasColumn('rsbsa_applications', 'barangay_id')) {
                $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            }

            if (!$foreignKeyExists('rsbsa_applications', 'rsbsa_applications_barangay_id_foreign')) {
                $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            }

            if (!$indexExists('rsbsa_applications', 'rsbsa_applications_barangay_id_index')) {
                $table->index('barangay_id');
            }
        });

        Schema::table('fishr_applications', function (Blueprint $table) use ($foreignKeyExists, $indexExists) {
            if (!Schema::hasColumn('fishr_applications', 'barangay_id')) {
                $table->unsignedBigInteger('barangay_id')->nullable()->after('barangay');
            }

            if (!$foreignKeyExists('fishr_applications', 'fishr_applications_barangay_id_foreign')) {
                $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            }

            if (!$indexExists('fishr_applications', 'fishr_applications_barangay_id_index')) {
                $table->index('barangay_id');
            }
        });

        // 2. Add missing user foreign keys
        Schema::table('seedling_requests', function (Blueprint $table) use ($foreignKeyExists) {
            if (!$foreignKeyExists('seedling_requests', 'seedling_requests_reviewed_by_foreign')) {
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        Schema::table('fishr_applications', function (Blueprint $table) use ($foreignKeyExists) {
            if (!$foreignKeyExists('fishr_applications', 'fishr_applications_updated_by_foreign')) {
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            }
        });

        // 3. Add cross-application relationship (BoatR requires FishR)
        Schema::table('boatr_applications', function (Blueprint $table) use ($foreignKeyExists, $indexExists) {
            if (!Schema::hasColumn('boatr_applications', 'barangay_id')) {
                $table->unsignedBigInteger('barangay_id')->nullable()->after('contact_number');
            }
            if (!$foreignKeyExists('boatr_applications', 'boatr_applications_barangay_id_foreign')) {
                $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
            }

            // fishr_application_id already exists, just add foreign key if missing
            if (!$foreignKeyExists('boatr_applications', 'boatr_applications_fishr_application_id_foreign')) {
                $table->foreign('fishr_application_id')->references('id')->on('fishr_applications')->onDelete('set null');
            }
            if (!$indexExists('boatr_applications', 'boatr_applications_fishr_application_id_index')) {
                $table->index('fishr_application_id');
            }
        });

        // 5. Add training applications relationships
        if (Schema::hasTable('training_applications')) {
            Schema::table('training_applications', function (Blueprint $table) use ($foreignKeyExists) {
                // Add barangay relationship if column doesn't exist
                if (!Schema::hasColumn('training_applications', 'barangay_id')) {
                    $table->unsignedBigInteger('barangay_id')->nullable()->after('contact_number');
                }
                if (!$foreignKeyExists('training_applications', 'training_applications_barangay_id_foreign')) {
                    $table->foreign('barangay_id')->references('id')->on('barangays')->onDelete('set null');
                }

                // Add missing user foreign key if updated_by exists
                if (Schema::hasColumn('training_applications', 'updated_by')) {
                    if (!$foreignKeyExists('training_applications', 'training_applications_updated_by_foreign')) {
                        $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop in reverse order to avoid foreign key constraint issues

        // 5. Remove training applications relationships
        if (Schema::hasTable('training_applications')) {
            Schema::table('training_applications', function (Blueprint $table) {
                if (Schema::hasColumn('training_applications', 'updated_by')) {
                    $table->dropForeign(['updated_by']);
                }
            });
        }

        // 4. Remove cross-application relationship
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropForeign(['fishr_application_id']);
            $table->dropColumn('fishr_application_id');
        });

        // 2. Remove user foreign keys
        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
        });

        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
        });

        // 1. Remove barangay relationships
        Schema::table('boatr_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('fishr_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('rsbsa_applications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });

        Schema::table('seedling_requests', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });
    }
};

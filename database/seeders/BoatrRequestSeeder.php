<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BoatrApplication;
use App\Models\User;
use App\Models\UserRegistration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BoatrRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have users in the user_registration table
        if (UserRegistration::count() < 10) {
            UserRegistration::factory(20)->create();
        }

        // Ensure we have at least one admin user before creating applications
        $this->createAdminUserIfNotExists();

        $this->command->info('Creating BoatR applications...');

        try {
            // Check if table exists and has correct structure
            $this->validateTableStructure();

            // Optional: Clear existing records (comment out if you want to keep existing data)
            // BoatrApplication::truncate();
            // $this->command->info('🗑️ Cleared existing BoatR applications');

            // Create applications with different statuses
            $this->createApplications();

            $total = BoatrApplication::count();
            $this->command->info("🎉 Successfully created BoatR application records! Total: {$total}");

            // Display statistics
            $this->displayStatistics();

        } catch (\Exception $e) {
            $this->command->error('❌ Error creating BoatR applications: ' . $e->getMessage());
            Log::error('BoatR Seeder Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide helpful suggestions
            $this->command->error('💡 Suggestions:');
            $this->command->error('   1. Run: php artisan migrate:fresh');
            $this->command->error('   2. Check if BoatrApplication factory exists');
            $this->command->error('   3. Verify database connection');
        }
    }

    /**
     * Validate table structure
     */
    private function validateTableStructure(): void
    {
        if (!Schema::hasTable('boatr_applications')) {
            throw new \Exception('Table boatr_applications does not exist. Please run migrations first.');
        }

        // Check for required columns based on your ACTUAL migration
        $requiredColumns = [
            'application_number',
            'first_name',
            'last_name',
            'fishr_number',
            'vessel_name',
            'boat_type',
            'status',
            // Updated to match your migration
            'user_document_path',
            'inspection_documents'
        ];

        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('boatr_applications', $column)) {
                throw new \Exception("Required column '{$column}' does not exist in boatr_applications table.");
            }
        }

        $this->command->info('✓ Table structure validated');
    }

    /**
     * Create applications with different statuses
     */
    private function createApplications(): void
    {
        // Create pending applications
        try {
            BoatrApplication::factory(8)->pending()->create();
            $this->command->info('✓ Created 8 pending applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating pending applications: ' . $e->getMessage());
        }

        // Create approved applications (with completed inspections)
        try {
            BoatrApplication::factory(6)->approved()->create();
            $this->command->info('✓ Created 6 approved applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating approved applications: ' . $e->getMessage());
        }

        // Create inspection required applications
        try {
            BoatrApplication::factory(4)->inspectionRequired()->create();
            $this->command->info('✓ Created 4 inspection required applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating inspection required applications: ' . $e->getMessage());
        }

        // Create rejected applications
        try {
            BoatrApplication::factory(3)->rejected()->create();
            $this->command->info('✓ Created 3 rejected applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating rejected applications: ' . $e->getMessage());
        }

        // Create under review applications
        try {
            BoatrApplication::factory(3)->underReview()->create();
            $this->command->info('✓ Created 3 under review applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating under review applications: ' . $e->getMessage());
        }

        // Create inspection scheduled applications
        try {
            BoatrApplication::factory(2)->inspectionScheduled()->create();
            $this->command->info('✓ Created 2 inspection scheduled applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating inspection scheduled applications: ' . $e->getMessage());
        }

        // Create documents pending applications
        try {
            BoatrApplication::factory(2)->documentsPending()->create();
            $this->command->info('✓ Created 2 documents pending applications');
        } catch (\Exception $e) {
            $this->command->error('Error creating documents pending applications: ' . $e->getMessage());
        }
    }

    /**
     * Create admin user if none exists
     */
    private function createAdminUserIfNotExists(): void
    {
        try {
            $adminUser = User::where('role', 'admin')
                            ->first();

            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => 'System Admin',
                    'password' => bcrypt('admin123'),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);

                $this->command->info('✓ Created admin user: ' . $adminUser->name);
            } else {
                $this->command->info('✓ Admin user exists: ' . $adminUser->name);
            }
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Could not verify/create admin user: ' . $e->getMessage());
            // Continue anyway, as this is not critical for creating applications
        }
    }

    /**
     * Display creation statistics
     */
    private function displayStatistics(): void
    {
        try {
            $stats = [
                'pending' => BoatrApplication::where('status', 'pending')->count(),
                'under_review' => BoatrApplication::where('status', 'under_review')->count(),
                'inspection_required' => BoatrApplication::where('status', 'inspection_required')->count(),
                'inspection_scheduled' => BoatrApplication::where('status', 'inspection_scheduled')->count(),
                'documents_pending' => BoatrApplication::where('status', 'documents_pending')->count(),
                'approved' => BoatrApplication::where('status', 'approved')->count(),
                'rejected' => BoatrApplication::where('status', 'rejected')->count(),
            ];

            $this->command->info('📊 Current Statistics:');
            foreach ($stats as $status => $count) {
                if ($count > 0) {
                    $emoji = match($status) {
                        'pending' => '⏳',
                        'under_review' => '🔍',
                        'inspection_required' => '🚤',
                        'inspection_scheduled' => '📅',
                        'documents_pending' => '📄',
                        'approved' => '✅',
                        'rejected' => '❌',
                        default => '📊'
                    };
                    $this->command->info("   {$emoji} " . ucwords(str_replace('_', ' ', $status)) . ": {$count}");
                }
            }

            // Additional statistics - FIXED to use correct column names
            $withUserDocs = BoatrApplication::whereNotNull('user_document_path')->count();
            $withInspectionDocs = BoatrApplication::whereNotNull('inspection_documents')->count();
            $inspectionCompleted = BoatrApplication::where('inspection_completed', true)->count();

            $this->command->info('📊 Additional Statistics:');
            $this->command->info("   📎 With User Documents: {$withUserDocs}");
            $this->command->info("   📋 With Inspection Documents: {$withInspectionDocs}");
            $this->command->info("   ✔️ Inspection Completed: {$inspectionCompleted}");

        } catch (\Exception $e) {
            $this->command->warn('⚠️ Could not generate statistics: ' . $e->getMessage());
        }
    }
}
